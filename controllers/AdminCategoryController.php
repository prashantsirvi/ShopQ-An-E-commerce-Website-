<?php

declare(strict_types=1);

namespace Controllers;

use Core\Database;
use Models\Category;

final class AdminCategoryController extends AdminController
{
    public function index(): void
    {
        $this->adminView('admin/categories/index', [
            'title' => 'Categories',
            'categories' => (new Category())->adminAll(),
        ]);
    }

    public function create(): void
    {
        $this->adminView('admin/categories/form', ['title' => 'Add Category', 'category' => null]);
    }

    public function store(): void
    {
        $this->validateCsrf();
        $name = sanitize_string($_POST['name'] ?? '');
        $slug = unique_slug($name, fn ($s) => $this->slugExists($s));

        (new Category())->adminCreate([
            'name' => $name,
            'slug' => $slug,
            'description' => sanitize_string($_POST['description'] ?? ''),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'sort_order' => sanitize_int($_POST['sort_order'] ?? 0),
        ]);

        $this->flash('success', 'Category created.');
        $this->redirect('/admin/categories');
    }

    public function edit(int $id): void
    {
        $category = (new Category())->adminFind($id);

        if ($category === null) {
            $this->flash('error', 'Category not found.');
            $this->redirect('/admin/categories');
        }

        $this->adminView('admin/categories/form', ['title' => 'Edit Category', 'category' => $category]);
    }

    public function update(int $id): void
    {
        $this->validateCsrf();
        $name = sanitize_string($_POST['name'] ?? '');

        (new Category())->adminUpdate($id, [
            'name' => $name,
            'slug' => unique_slug($name, fn ($s) => $this->slugExists($s, $id)),
            'description' => sanitize_string($_POST['description'] ?? ''),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
            'sort_order' => sanitize_int($_POST['sort_order'] ?? 0),
        ]);

        $this->flash('success', 'Category updated.');
        $this->redirect('/admin/categories');
    }

    private function slugExists(string $slug, ?int $ignoreId = null): bool
    {
        $params = ['slug' => $slug];
        $sql = 'SELECT id FROM categories WHERE slug = :slug';

        if ($ignoreId !== null) {
            $sql .= ' AND id != :id';
            $params['id'] = $ignoreId;
        }

        return Database::getInstance()->fetch($sql . ' LIMIT 1', $params) !== null;
    }
}
