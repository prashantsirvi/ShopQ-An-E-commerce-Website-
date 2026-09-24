<?php

declare(strict_types=1);

namespace Controllers;

use Core\Controller;
use Models\Product;

/**
 * Marketing and utility pages.
 */
final class PageController extends Controller
{
    public function deals(): void
    {
        $page = sanitize_int($_GET['page'] ?? 1, 1);
        $result = (new Product())->getDeals($page);

        $this->view('products/deals', [
            'title' => 'Deals & Offers',
            'heading' => 'Deals & Offers',
            'subtitle' => 'Save more on discounted products across categories.',
            'products' => $result['items'],
            'pagination' => $result['pagination'],
            'breadcrumb' => [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Deals', 'url' => null],
            ],
        ]);
    }

    public function newsletter(): void
    {
        $this->validateCsrf();
        $this->flash('success', 'Thanks for subscribing! Newsletter feature expands in later phases.');
        $this->redirect('/');
    }
}
