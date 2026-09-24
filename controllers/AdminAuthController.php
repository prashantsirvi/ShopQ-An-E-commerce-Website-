<?php

declare(strict_types=1);

namespace Controllers;

use Core\Controller;
use Models\User;

/**
 * Admin authentication — separate session namespace from customers.
 */
final class AdminAuthController extends Controller
{
    private User $users;

    public function __construct()
    {
        $this->users = new User();
    }

    public function showLoginForm(): void
    {
        $this->view('admin/auth/login', [
            'title' => 'Admin Login',
            'errors' => pull_validation_errors(),
            'old' => pull_old_input(),
        ], 'layouts/auth');
    }

    public function login(): void
    {
        $this->validateCsrf();

        $email = sanitize_email($_POST['email'] ?? '');
        $password = (string) ($_POST['password'] ?? '');

        flash_old_input(['email' => $email]);

        if (is_login_locked('admin', $email)) {
            $minutes = (int) ceil(login_lockout_remaining('admin', $email) / 60);
            $this->flash('error', "Too many failed attempts. Try again in {$minutes} minute(s).");
            $this->redirect('/admin/login');
        }

        $errors = validate_required(['email' => $email, 'password' => $password], [
            'email' => 'Email',
            'password' => 'Password',
        ]);

        if ($emailError = validate_email_format($email)) {
            $errors['email'] = $emailError;
        }

        if ($errors !== []) {
            flash_validation_errors($errors);
            $this->redirect('/admin/login');
        }

        $user = $this->users->findByEmail($email);

        if ($user === null || !$this->users->verifyPassword($user, $password)) {
            record_login_attempt('admin', $email);
            flash_validation_errors(['email' => 'Invalid admin credentials.']);
            $this->redirect('/admin/login');
        }

        if (!(int) $user['is_active']) {
            $this->flash('error', 'This admin account is inactive.');
            $this->redirect('/admin/login');
        }

        if (!in_array($user['role_slug'], ['admin', 'super_admin'], true)) {
            $this->flash('error', 'You do not have admin access.');
            $this->redirect('/admin/login');
        }

        clear_login_attempts('admin', $email);
        $this->users->updateLastLogin((int) $user['id']);
        login_admin($user);

        $this->flash('success', 'Admin login successful.');
        $this->redirect('/admin/dashboard');
    }

    public function logout(): void
    {
        $this->validateCsrf();
        logout_admin();
        $this->flash('success', 'Admin session ended.');
        $this->redirect('/admin/login');
    }
}
