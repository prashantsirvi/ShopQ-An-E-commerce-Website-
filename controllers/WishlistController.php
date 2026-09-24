<?php

declare(strict_types=1);

namespace Controllers;

use Core\Controller;
use Models\Wishlist;

final class WishlistController extends Controller
{
    private Wishlist $wishlist;

    public function __construct()
    {
        $this->wishlist = new Wishlist();
    }

    public function index(): void
    {
        $this->view('wishlist/index', [
            'title' => 'My Wishlist',
            'items' => $this->wishlist->items(),
        ]);
    }

    public function add(): void
    {
        $this->validateCsrf();
        $productId = sanitize_int($_POST['product_id'] ?? 0);
        $this->wishlist->add($productId);
        $this->respond('Added to wishlist.');
    }

    public function remove(): void
    {
        $this->validateCsrf();
        $productId = sanitize_int($_POST['product_id'] ?? 0);
        $this->wishlist->remove($productId);
        $this->respond('Removed from wishlist.');
    }

    private function respond(string $message): void
    {
        if ($this->wantsJson()) {
            $this->json([
                'success' => true,
                'message' => $message,
                'wishlist_count' => wishlist_count(),
            ]);
        }

        $this->flash('success', $message);
        $this->redirect($_POST['redirect'] ?? '/wishlist');
    }

    private function wantsJson(): bool
    {
        return strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';
    }
}
