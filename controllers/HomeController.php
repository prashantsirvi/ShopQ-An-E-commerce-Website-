<?php

declare(strict_types=1);

namespace Controllers;

use Core\Controller;
use Models\Category;
use Models\Product;

/**
 * Home page — premium storefront landing with live catalog data.
 */
final class HomeController extends Controller
{
    public function index(): void
    {
        $products = new Product();
        $categories = new Category();

        $this->view('home/index', [
            'title' => 'Home',
            'metaDescription' => setting('site_tagline'),
            'categories' => $categories->getActive(10),
            'featuredProducts' => $products->getFeatured(8),
            'bestSellers' => $products->getBestSellers(8),
            'trendingProducts' => $products->getTrending(8),
            'flashSaleProducts' => $products->getFlashSale(6),
        ]);
    }
}
