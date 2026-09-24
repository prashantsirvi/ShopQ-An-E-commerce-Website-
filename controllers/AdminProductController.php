<?php

declare(strict_types=1);

namespace Controllers;

use Core\Database;
use Models\Brand;
use Models\Category;
use Models\Product;

final class AdminProductController extends AdminController
{
    private Product $products;

    public function __construct()
    {
        $this->products = new Product();
    }

    public function index(): void
    {
        $page = max(1, sanitize_int($_GET['page'] ?? 1));
        $q = sanitize_string($_GET['q'] ?? '');
        $result = $this->products->adminList($q, $page);

        $this->adminView('admin/products/index', [
            'title' => 'Products',
            'products' => $result['items'],
            'pagination' => $result['pagination'],
            'q' => $q,
        ]);
    }

    public function create(): void
    {
        $this->adminView('admin/products/form', [
            'title' => 'Add Product',
            'product' => null,
            'categories' => (new Category())->adminAll(),
            'brands' => (new Brand())->getActive(),
        ]);
    }

    public function store(): void
    {
        $this->validateCsrf();
        $data = $this->validatedProductData();
        $data['slug'] = unique_slug($data['name'], fn ($slug) => $this->slugExists($slug));
        $id = $this->products->adminCreate($data);
        log_activity('product.create', 'product', $id);
        $this->flash('success', 'Product created.');
        $this->redirect('/admin/products');
    }

    public function edit(int $id): void
    {
        $product = $this->products->adminFind($id);

        if ($product === null) {
            $this->flash('error', 'Product not found.');
            $this->redirect('/admin/products');
        }

        $this->adminView('admin/products/form', [
            'title' => 'Edit Product',
            'product' => $product,
            'categories' => (new Category())->adminAll(),
            'brands' => (new Brand())->getActive(),
        ]);
    }

    public function update(int $id): void
    {
        $this->validateCsrf();

        if ($this->products->adminFind($id) === null) {
            $this->flash('error', 'Product not found.');
            $this->redirect('/admin/products');
        }

        $data = $this->validatedProductData();
        $data['slug'] = unique_slug($data['name'], fn ($slug) => $this->slugExists($slug, $id));
        $this->products->adminUpdate($id, $data);
        log_activity('product.update', 'product', $id);
        $this->flash('success', 'Product updated.');
        $this->redirect('/admin/products');
    }

    public function delete(int $id): void
    {
        $this->validateCsrf();
        $this->products->adminDelete($id);
        log_activity('product.delete', 'product', $id);
        $this->flash('success', 'Product removed.');
        $this->redirect('/admin/products');
    }

    private function validatedProductData(): array
    {
        return [
            'category_id' => sanitize_int($_POST['category_id'] ?? 0),
            'brand_id' => sanitize_int($_POST['brand_id'] ?? 0) ?: null,
            'name' => sanitize_string($_POST['name'] ?? ''),
            'sku' => strtoupper(sanitize_string($_POST['sku'] ?? '')),
            'short_description' => sanitize_string($_POST['short_description'] ?? ''),
            'description' => sanitize_string($_POST['description'] ?? ''),
            'base_price' => (float) ($_POST['base_price'] ?? 0),
            'sale_price' => $_POST['sale_price'] !== '' ? (float) $_POST['sale_price'] : null,
            'discount_percent' => sanitize_int($_POST['discount_percent'] ?? 0),
            'stock' => sanitize_int($_POST['stock'] ?? 0),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'is_featured' => isset($_POST['is_featured']) ? 1 : 0,
        ];
    }

    private function slugExists(string $slug, ?int $ignoreId = null): bool
    {
        $params = ['slug' => $slug];
        $sql = 'SELECT id FROM products WHERE slug = :slug AND deleted_at IS NULL';

        if ($ignoreId !== null) {
            $sql .= ' AND id != :id';
            $params['id'] = $ignoreId;
        }

        return Database::getInstance()->fetch($sql . ' LIMIT 1', $params) !== null;
    }
}
