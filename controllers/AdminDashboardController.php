<?php

declare(strict_types=1);

namespace Controllers;

use Core\Controller;
use Models\Analytics;

final class AdminDashboardController extends Controller
{
    public function index(): void
    {
        $analytics = new Analytics();

        $this->view('admin/dashboard/index', [
            'title' => 'Admin Dashboard',
            'admin' => auth_admin(),
            'summary' => $analytics->dashboardSummary(),
            'topProducts' => $analytics->topProducts(5),
            'trending' => $analytics->trendingProducts(5),
            'popularSearches' => $analytics->popularSearches(6),
        ], 'layouts/admin');
    }

    public function analyticsApi(): void
    {
        $analytics = new Analytics();

        $this->json([
            'revenue' => $analytics->revenueLastDays(7),
            'orders_by_status' => $analytics->ordersByStatus(),
            'top_products' => $analytics->topProducts(5),
        ]);
    }
}
