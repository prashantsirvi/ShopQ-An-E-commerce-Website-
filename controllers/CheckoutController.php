<?php

declare(strict_types=1);

namespace Controllers;

use Core\Controller;
use Models\Address;
use Models\Cart;
use Models\Coupon;
use Models\Order;

final class CheckoutController extends Controller
{
    public function index(): void
    {
        $cart = new Cart();
        $items = $cart->items();

        if ($items === []) {
            $this->flash('error', 'Your cart is empty.');
            $this->redirect('/cart');
        }

        $customer = auth_customer();
        $summary = $this->buildSummary($items, $_SESSION['checkout_coupon'] ?? null);

        $this->view('checkout/index', [
            'title' => 'Checkout',
            'items' => $items,
            'addresses' => (new Address())->forUser((int) $customer['id']),
            'summary' => $summary,
            'couponCode' => $_SESSION['checkout_coupon'] ?? '',
        ]);
    }

    public function applyCoupon(): void
    {
        $this->validateCsrf();
        $code = strtoupper(trim(sanitize_string($_POST['coupon_code'] ?? '')));
        $coupon = (new Coupon())->findValid($code);

        if ($coupon === null) {
            $this->flash('error', 'Invalid or expired coupon code.');
            $this->redirect('/checkout');
        }

        $_SESSION['checkout_coupon'] = $code;
        $this->flash('success', 'Coupon applied successfully.');
        $this->redirect('/checkout');
    }

    public function placeOrder(): void
    {
        $this->validateCsrf();

        $cart = new Cart();
        $items = $cart->items();

        if ($items === []) {
            $this->flash('error', 'Your cart is empty.');
            $this->redirect('/cart');
        }

        $customer = auth_customer();
        $userId = (int) $customer['id'];
        $address = $this->resolveAddress($userId);

        if ($address === null) {
            $this->flash('error', 'Please provide a valid delivery address.');
            $this->redirect('/checkout');
        }

        $paymentMethod = sanitize_string($_POST['payment_method'] ?? 'cod');
        $allowedMethods = ['cod', 'dummy', 'upi'];
        if (!in_array($paymentMethod, $allowedMethods, true)) {
            $paymentMethod = 'cod';
        }

        $couponCode = $_SESSION['checkout_coupon'] ?? null;
        $summary = $this->buildSummary($items, $couponCode);

        $status = 'pending';
        $paymentStatus = 'pending';

        if ($paymentMethod === 'cod') {
            $status = 'confirmed';
        }

        try {
            $result = (new Order())->createFromCheckout($userId, $items, [
                'subtotal' => $summary['subtotal'],
                'discount_amount' => $summary['discount'],
                'shipping_amount' => $summary['shipping'],
                'tax_amount' => $summary['tax'],
                'total_amount' => $summary['total'],
                'coupon_code' => $couponCode,
                'payment_method' => $paymentMethod,
                'status' => $status,
                'payment_status' => $paymentStatus,
                'shipping_name' => $address['full_name'],
                'shipping_phone' => $address['phone'],
                'shipping_address' => trim($address['address_line1'] . ' ' . ($address['address_line2'] ?? '')),
                'shipping_city' => $address['city'],
                'shipping_state' => $address['state'],
                'shipping_postal_code' => $address['postal_code'],
                'notes' => sanitize_string($_POST['notes'] ?? ''),
            ]);

            $cart->clear();
            unset($_SESSION['checkout_coupon']);

            if ($paymentMethod === 'cod') {
                $this->redirect('/checkout/success/' . $result['order_number']);
            }

            $this->redirect('/payment/' . $result['order_number']);
        } catch (\Throwable $exception) {
            $this->flash('error', 'Unable to place order. Please try again.');
            $this->redirect('/checkout');
        }
    }

    public function success(string $orderNumber): void
    {
        $customer = auth_customer();
        $order = (new Order())->findByNumberForUser($orderNumber, (int) $customer['id']);

        if ($order === null) {
            http_response_code(404);
            $this->view('errors/404', [
                'title' => 'Order Not Found',
                'message' => 'We could not find this order.',
            ]);
            return;
        }

        $this->view('checkout/success', [
            'title' => 'Order Placed',
            'order' => $order,
        ]);
    }

    /** @return array<string, float|int|string|null> */
    private function buildSummary(array $items, ?string $couponCode): array
    {
        $subtotal = 0.0;
        foreach ($items as $item) {
            $subtotal += (float) $item['line_total'];
        }

        $discount = 0.0;
        if ($couponCode) {
            $coupon = (new Coupon())->findValid($couponCode);
            if ($coupon) {
                $discount = (new Coupon())->calculateDiscount($coupon, $subtotal);
            }
        }

        $freeShippingMin = (float) setting('free_shipping_min', 999);
        $shippingFlat = (float) setting('shipping_flat_rate', 49);
        $shipping = ($subtotal - $discount) >= $freeShippingMin ? 0.0 : $shippingFlat;

        $taxPercent = (float) setting('tax_percent', 18);
        $taxable = max(0, $subtotal - $discount + $shipping);
        $tax = round($taxable * ($taxPercent / 100), 2);
        $total = round($taxable + $tax, 2);

        return [
            'subtotal' => round($subtotal, 2),
            'discount' => $discount,
            'shipping' => $shipping,
            'tax' => $tax,
            'total' => $total,
        ];
    }

    private function resolveAddress(int $userId): ?array
    {
        $addressModel = new Address();
        $addressId = sanitize_int($_POST['address_id'] ?? 0);

        if ($addressId > 0) {
            return $addressModel->findForUser($addressId, $userId);
        }

        $required = validate_required($_POST, [
            'full_name' => 'Full name',
            'phone' => 'Phone',
            'address_line1' => 'Address',
            'city' => 'City',
            'state' => 'State',
            'postal_code' => 'Postal code',
        ]);

        if ($required !== []) {
            return null;
        }

        $addressId = $addressModel->create($userId, [
            'label' => sanitize_string($_POST['label'] ?? 'Home'),
            'full_name' => sanitize_string($_POST['full_name']),
            'phone' => sanitize_string($_POST['phone']),
            'address_line1' => sanitize_string($_POST['address_line1']),
            'address_line2' => sanitize_string($_POST['address_line2'] ?? ''),
            'city' => sanitize_string($_POST['city']),
            'state' => sanitize_string($_POST['state']),
            'postal_code' => sanitize_string($_POST['postal_code']),
            'country' => sanitize_string($_POST['country'] ?? 'India'),
            'is_default' => isset($_POST['save_address']),
        ]);

        return $addressModel->findForUser($addressId, $userId);
    }
}
