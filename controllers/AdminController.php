<?php

declare(strict_types=1);

namespace Controllers;

use Core\Controller;

abstract class AdminController extends Controller
{
    protected function adminView(string $view, array $data = []): void
    {
        $this->view($view, $data, 'layouts/admin');
    }
}
