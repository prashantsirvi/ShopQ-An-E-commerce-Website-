<?php

declare(strict_types=1);

namespace Controllers;

use Core\Controller;
use Models\User;

/**
 * Protected customer profile area — validates AuthMiddleware in Phase 4.
 */
final class ProfileController extends Controller
{
    public function index(): void
    {
        $customer = auth_customer();
        $user = (new User())->findById((int) $customer['id']);

        if ($user === null) {
            logout_customer();
            $this->redirect('/login');
        }

        $this->view('profile/index', [
            'title' => 'My Profile',
            'user' => $user,
        ]);
    }
}
