<?php

declare(strict_types=1);

namespace Controllers;

use Core\Controller;
use Models\CompareList;

final class CompareController extends Controller
{
    private CompareList $compare;

    public function __construct()
    {
        $this->compare = new CompareList();
    }

    public function index(): void
    {
        $this->view('compare/index', [
            'title' => 'Compare Products',
            'items' => $this->compare->items(),
            'maxItems' => 4,
        ]);
    }

    public function add(): void
    {
        $this->validateCsrf();
        $productId = sanitize_int($_POST['product_id'] ?? 0);

        if (!$this->compare->add($productId)) {
            $this->respond('You can compare up to 4 products only.', false);
        }

        $this->respond('Product added to compare.');
    }

    public function remove(): void
    {
        $this->validateCsrf();
        $productId = sanitize_int($_POST['product_id'] ?? 0);
        $this->compare->remove($productId);
        $this->respond('Product removed from compare.');
    }

    public function clear(): void
    {
        $this->validateCsrf();
        $this->compare->clear();
        $this->respond('Compare list cleared.');
    }

    private function respond(string $message, bool $success = true): void
    {
        if ($this->wantsJson()) {
            $this->json([
                'success' => $success,
                'message' => $message,
                'compare_count' => compare_count(),
            ], $success ? 200 : 422);
        }

        $this->flash($success ? 'success' : 'error', $message);
        $this->redirect($_POST['redirect'] ?? '/compare');
    }

    private function wantsJson(): bool
    {
        return strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';
    }
}
