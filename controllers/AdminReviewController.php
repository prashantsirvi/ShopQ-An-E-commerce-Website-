<?php

declare(strict_types=1);

namespace Controllers;

use Models\Review;

final class AdminReviewController extends AdminController
{
    public function index(): void
    {
        $page = max(1, sanitize_int($_GET['page'] ?? 1));
        $pendingOnly = isset($_GET['pending']);
        $result = (new Review())->adminList($pendingOnly, $page);

        $this->adminView('admin/reviews/index', [
            'title' => 'Reviews',
            'reviews' => $result['items'],
            'pagination' => $result['pagination'],
            'pendingOnly' => $pendingOnly,
        ]);
    }

    public function approve(int $id): void
    {
        $this->validateCsrf();
        (new Review())->setApproved($id, true);
        $this->flash('success', 'Review approved.');
        $this->redirect('/admin/reviews');
    }

    public function reject(int $id): void
    {
        $this->validateCsrf();
        (new Review())->setApproved($id, false);
        $this->flash('success', 'Review rejected.');
        $this->redirect('/admin/reviews');
    }
}
