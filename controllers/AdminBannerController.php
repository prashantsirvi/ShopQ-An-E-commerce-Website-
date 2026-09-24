<?php

declare(strict_types=1);

namespace Controllers;

use Models\Banner;

final class AdminBannerController extends AdminController
{
    public function index(): void
    {
        $this->adminView('admin/banners/index', [
            'title' => 'Banners',
            'banners' => (new Banner())->adminAll(),
        ]);
    }

    public function create(): void
    {
        $this->adminView('admin/banners/form', ['title' => 'Add Banner', 'banner' => null]);
    }

    public function store(): void
    {
        $this->validateCsrf();
        (new Banner())->adminCreate($this->validated());
        $this->flash('success', 'Banner created.');
        $this->redirect('/admin/banners');
    }

    public function edit(int $id): void
    {
        $banner = (new Banner())->adminFind($id);

        if ($banner === null) {
            $this->flash('error', 'Banner not found.');
            $this->redirect('/admin/banners');
        }

        $this->adminView('admin/banners/form', ['title' => 'Edit Banner', 'banner' => $banner]);
    }

    public function update(int $id): void
    {
        $this->validateCsrf();
        (new Banner())->adminUpdate($id, $this->validated());
        $this->flash('success', 'Banner updated.');
        $this->redirect('/admin/banners');
    }

    public function delete(int $id): void
    {
        $this->validateCsrf();
        (new Banner())->adminDelete($id);
        $this->flash('success', 'Banner deleted.');
        $this->redirect('/admin/banners');
    }

    private function validated(): array
    {
        return [
            'title' => sanitize_string($_POST['title'] ?? ''),
            'subtitle' => sanitize_string($_POST['subtitle'] ?? ''),
            'image_path' => sanitize_string($_POST['image_path'] ?? ''),
            'link_url' => sanitize_string($_POST['link_url'] ?? ''),
            'placement' => in_array($_POST['placement'] ?? '', ['hero', 'sidebar', 'footer', 'category'], true) ? $_POST['placement'] : 'hero',
            'sort_order' => sanitize_int($_POST['sort_order'] ?? 0),
            'is_active' => isset($_POST['is_active']) ? 1 : 0,
        ];
    }
}
