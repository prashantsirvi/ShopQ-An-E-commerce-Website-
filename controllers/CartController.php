<?php

declare(strict_types=1);

namespace Controllers;

use Core\Controller;
use Models\Cart;

final class CartController extends Controller
{
    private Cart $cart;

    public function __construct()
    {
        $this->cart = new Cart();
    }

    public function index(): void
    {
        $items = $this->cart->items();

        $this->view('cart/index', [
            'title' => 'Shopping Cart',
            'items' => $items,
            'subtotal' => $this->cart->subtotal(),
        ]);
    }

    public function add(): void
    {
        $this->validateCsrf();

        $productId = sanitize_int($_POST['product_id'] ?? 0);
        $variantId = sanitize_int($_POST['variant_id'] ?? 0) ?: null;
        $quantity = max(1, sanitize_int($_POST['quantity'] ?? 1));

        try {
            $this->cart->add($productId, $variantId, $quantity);
            $this->respondCartAction('Product added to cart.');
        } catch (\Throwable $exception) {
            $this->respondCartAction($exception->getMessage(), false);
        }
    }

    public function update(): void
    {
        $this->validateCsrf();

        $productId = sanitize_int($_POST['product_id'] ?? 0);
        $variantId = sanitize_int($_POST['variant_id'] ?? 0) ?: null;
        $quantity = sanitize_int($_POST['quantity'] ?? 1);

        $this->cart->update($productId, $variantId, $quantity);
        $this->respondCartAction('Cart updated.');
    }

    public function remove(): void
    {
        $this->validateCsrf();

        $productId = sanitize_int($_POST['product_id'] ?? 0);
        $variantId = sanitize_int($_POST['variant_id'] ?? 0) ?: null;

        $this->cart->remove($productId, $variantId);
        $this->respondCartAction('Item removed from cart.');
    }

    private function respondCartAction(string $message, bool $success = true): void
    {
        if ($this->wantsJson()) {
            $this->json([
                'success' => $success,
                'message' => $message,
                'cart_count' => cart_count(),
                'subtotal' => format_money(cart_service()->subtotal()),
            ], $success ? 200 : 422);
        }

        $this->flash($success ? 'success' : 'error', $message);
        $this->redirect($_POST['redirect'] ?? '/cart');
    }

    private function wantsJson(): bool
    {
        return strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest'
            || str_contains($_SERVER['HTTP_ACCEPT'] ?? '', 'application/json');
    }
}
