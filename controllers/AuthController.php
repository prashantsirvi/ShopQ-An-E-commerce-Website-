<?php

declare(strict_types=1);

namespace Controllers;

use Core\Controller;
use Models\User;

/**
 * Customer authentication — register, login, logout.
 */
final class AuthController extends Controller
{
    private User $users;

    public function __construct()
    {
        $this->users = new User();
    }

    public function showLoginForm(): void
    {
        $this->view('auth/login', [
            'title' => 'Login',
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

        if (is_login_locked('customer', $email)) {
            $minutes = (int) ceil(login_lockout_remaining('customer', $email) / 60);
            $this->flash('error', "Too many failed attempts. Try again in {$minutes} minute(s).");
            $this->redirect('/login');
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
            $this->redirect('/login');
        }

        $user = $this->users->findByEmail($email);

        if ($user === null || !$this->users->verifyPassword($user, $password)) {
            record_login_attempt('customer', $email);
            flash_validation_errors(['email' => 'Invalid email or password.']);
            $this->flash('error', 'Login failed. Please check your credentials.');
            $this->redirect('/login');
        }

        if (!(int) $user['is_active']) {
            $this->flash('error', 'Your account is inactive. Contact support.');
            $this->redirect('/login');
        }

        if (!in_array($user['role_slug'], ['customer'], true)) {
            $this->flash('error', 'Please use the admin login page for staff accounts.');
            $this->redirect('/login');
        }

        clear_login_attempts('customer', $email);
        $this->users->updateLastLogin((int) $user['id']);
        merge_guest_commerce((int) $user['id']);
        login_customer($user);

        $this->flash('success', 'Welcome back, ' . $user['first_name'] . '!');
        $this->redirect(intended_url('/profile'));
    }

    public function showRegisterForm(): void
    {
        $this->view('auth/register', [
            'title' => 'Create Account',
            'errors' => pull_validation_errors(),
            'old' => pull_old_input(),
        ], 'layouts/auth');
    }

    public function register(): void
    {
        $this->validateCsrf();

        $data = [
            'first_name' => sanitize_string($_POST['first_name'] ?? ''),
            'last_name' => sanitize_string($_POST['last_name'] ?? ''),
            'email' => sanitize_email($_POST['email'] ?? ''),
            'phone' => sanitize_string($_POST['phone'] ?? ''),
            'password' => (string) ($_POST['password'] ?? ''),
            'password_confirmation' => (string) ($_POST['password_confirmation'] ?? ''),
        ];

        flash_old_input([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone' => $data['phone'],
        ]);

        $errors = validate_required($data, [
            'first_name' => 'First name',
            'last_name' => 'Last name',
            'email' => 'Email',
            'password' => 'Password',
            'password_confirmation' => 'Password confirmation',
        ]);

        if ($emailError = validate_email_format($data['email'])) {
            $errors['email'] = $emailError;
        }

        if ($phoneError = validate_phone($data['phone'])) {
            $errors['phone'] = $phoneError;
        }

        if ($passwordError = validate_password_strength($data['password'])) {
            $errors['password'] = $passwordError;
        }

        if ($confirmError = validate_password_confirmation($data['password'], $data['password_confirmation'])) {
            $errors['password_confirmation'] = $confirmError;
        }

        if ($this->users->emailExists($data['email'])) {
            $errors['email'] = 'An account with this email already exists.';
        }

        if ($errors !== []) {
            flash_validation_errors($errors);
            $this->redirect('/register');
        }

        $userId = $this->users->createCustomer($data);
        $this->users->createWalletForUser($userId);

        $user = $this->users->findById($userId);

        if ($user !== null) {
            merge_guest_commerce($userId);
            login_customer($user);
        }

        clear_old_input();
        $this->flash('success', 'Account created successfully. Welcome to ShopQ!');
        $this->redirect('/profile');
    }

    public function logout(): void
    {
        $this->validateCsrf();
        logout_customer();
        $this->flash('success', 'You have been logged out.');
        $this->redirect('/');
    }
}
