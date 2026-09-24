<?php

declare(strict_types=1);

namespace Controllers;

use Core\Controller;
use Models\Brand;
use Models\Category;
use Models\Product;

/**
 * Product catalog — listing, detail, category, search, and AJAX suggestions.
 */
final class ProductController extends Controller
{
    private Product $products;
    private Category $categories;
    private Brand $brands;

    public function __construct()
    {
        $this->products = new Product();
        $this->categories = new Category();
        $this->brands = new Brand();
    }

    public function index(): void
    {
        $filters = $this->extractFilters();
        $sort = $this->extractSort();
        $page = sanitize_int($_GET['page'] ?? 1, 1);
        $result = $this->products->list($filters, $sort, $page);

        $this->renderCatalog([
            'title' => 'All Products',
            'heading' => 'All Products',
            'subtitle' => 'Browse our complete catalog with filters and sorting.',
            'breadcrumb' => [['label' => 'Home', 'url' => url('/')], ['label' => 'Products', 'url' => null]],
            'products' => $result['items'],
            'pagination' => $result['pagination'],
            'filters' => $filters,
            'sort' => $sort,
            'basePath' => url('/products'),
        ]);
    }

    public function category(string $slug): void
    {
        $category = $this->categories->findBySlug($slug);

        if ($category === null) {
            $this->notFound();
        }

        $filters = $this->extractFilters();
        $filters['category_id'] = (int) $category['id'];
        $sort = $this->extractSort();
        $page = sanitize_int($_GET['page'] ?? 1, 1);
        $result = $this->products->list($filters, $sort, $page);

        $this->renderCatalog([
            'title' => $category['name'],
            'heading' => $category['name'],
            'subtitle' => $category['description'] ?? 'Explore products in this category.',
            'breadcrumb' => [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Products', 'url' => url('/products')],
                ['label' => $category['name'], 'url' => null],
            ],
            'products' => $result['items'],
            'pagination' => $result['pagination'],
            'filters' => $filters,
            'sort' => $sort,
            'basePath' => url('/category/' . $category['slug']),
            'activeCategory' => $category,
        ]);
    }

    public function show(string $slug): void
    {
        $product = $this->products->findBySlug($slug);

        if ($product === null) {
            $this->notFound();
        }

        $this->products->recordView((int) $product['id']);
        track_recently_viewed((int) $product['id']);

        $recentIds = array_filter(recently_viewed_ids(), static fn ($id) => $id !== (int) $product['id']);

        $this->view('products/show', [
            'title' => $product['name'],
            'metaDescription' => $product['short_description'] ?? $product['name'],
            'product' => $product,
            'relatedProducts' => $this->products->getRelated((int) $product['id'], (int) $product['category_id']),
            'recentlyViewed' => $this->products->getRecentlyViewed($recentIds, 4),
            'breadcrumb' => [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Products', 'url' => url('/products')],
                ['label' => $product['category_name'], 'url' => url('/category/' . $product['category_slug'])],
                ['label' => $product['name'], 'url' => null],
            ],
        ]);
    }

    public function search(): void
    {
        $query = sanitize_string($_GET['q'] ?? '');
        $sort = $this->extractSort();
        $page = sanitize_int($_GET['page'] ?? 1, 1);
        $result = $this->products->search($query, $page, 12, $sort);

        $this->renderCatalog([
            'title' => $query !== '' ? 'Search: ' . $query : 'Search',
            'heading' => $query !== '' ? 'Results for "' . $query . '"' : 'Search Products',
            'subtitle' => $result['pagination']['total'] . ' product(s) found',
            'breadcrumb' => [
                ['label' => 'Home', 'url' => url('/')],
                ['label' => 'Search', 'url' => null],
            ],
            'products' => $result['items'],
            'pagination' => $result['pagination'],
            'filters' => $this->extractFilters(),
            'sort' => $sort,
            'basePath' => url('/search'),
            'searchQuery' => $query,
        ]);
    }

    public function suggestions(): void
    {
        $query = sanitize_string($_GET['q'] ?? '');
        $items = $this->products->suggestions($query);

        $payload = array_map(static function (array $item): array {
            return [
                'id' => (int) $item['id'],
                'name' => $item['name'],
                'slug' => $item['slug'],
                'price' => format_money((float) ($item['display_price'] ?? effective_price($item))),
                'url' => url('/products/' . $item['slug']),
                'image' => $item['image_url'] ?? product_image_url(null),
            ];
        }, $items);

        $this->json(['success' => true, 'items' => $payload]);
    }

    private function renderCatalog(array $data): void
    {
        $data['categories'] = $this->categories->getActive(20);
        $data['brands'] = $this->brands->getActive();
        $data['priceRange'] = $this->products->priceRange();
        $data['popularSearches'] = $this->products->popularSearches();

        $this->view('products/index', $data);
    }

    /** @return array<string, mixed> */
    private function extractFilters(): array
    {
        $filters = [];

        if ($brandSlug = sanitize_string($_GET['brand'] ?? '')) {
            $brand = (new Brand())->findBySlug($brandSlug);
            if ($brand !== null) {
                $filters['brand_id'] = (int) $brand['id'];
            }
        }

        if (isset($_GET['min_price']) && $_GET['min_price'] !== '') {
            $filters['min_price'] = sanitize_float($_GET['min_price']);
        }

        if (isset($_GET['max_price']) && $_GET['max_price'] !== '') {
            $filters['max_price'] = sanitize_float($_GET['max_price']);
        }

        if (isset($_GET['rating']) && $_GET['rating'] !== '') {
            $filters['min_rating'] = sanitize_float($_GET['rating']);
        }

        if (isset($_GET['in_stock'])) {
            $filters['in_stock'] = true;
        }

        return $filters;
    }

    private function extractSort(): string
    {
        $sort = sanitize_string($_GET['sort'] ?? 'newest');
        $allowed = ['newest', 'price_asc', 'price_desc', 'popular', 'rating'];

        return in_array($sort, $allowed, true) ? $sort : 'newest';
    }

    private function notFound(): never
    {
        http_response_code(404);
        $this->view('errors/404', [
            'title' => 'Product Not Found',
            'message' => 'The product you are looking for does not exist or is unavailable.',
        ]);
        exit;
    }
}
