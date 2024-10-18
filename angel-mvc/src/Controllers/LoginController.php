<?php

namespace App\Controllers;

use App\Models\User;

class LoginController extends BaseController {

    public function showLoginForm() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (!isset($_SESSION['login_attempts'])) {
            $_SESSION['login_attempts'] = 0;
        }

        $data = [
            'remaining_attempts' => 3 - $_SESSION['login_attempts'],
            'form_disabled' => ($_SESSION['login_attempts'] >= 3),  // Disable form if attempts exceed 3
            'timer' => ($_SESSION['login_attempts'] >= 3) ? 30 : null  // Example: 30 seconds timer if disabled
        ];

        return $this->render('login-form', $data);
    }

    public function login() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = $_POST['username'] ?? null;
            $password = $_POST['password'] ?? null;

            if (empty($username) || empty($password)) {
                $errors[] = "Username and password are required.";
                return $this->showLoginFormWithErrors($errors);
            }

            $user = new User();
            $saved_password_hash = $user->getPassword($username);

            if ($saved_password_hash && password_verify($password, $saved_password_hash)) {
                $_SESSION['login_attempts'] = 0;
                $_SESSION['is_logged_in'] = true;
                $_SESSION['username'] = $username;
                header("Location: /welcome");
                exit;
            } else {
                $_SESSION['login_attempts']++;
                $remaining_attempts = 3 - $_SESSION['login_attempts'];
                $errors[] = "Invalid username or password. Attempts remaining: $remaining_attempts.";
                return $this->showLoginFormWithErrors($errors, $remaining_attempts);
            }
        } else {
            return $this->showLoginForm();
        }
    }

    private function showLoginFormWithErrors($errors, $remaining_attempts = null) {
        $form_disabled = ($remaining_attempts !== null && $remaining_attempts <= 0);  // Disable form if attempts exhausted
        return $this->render('login-form', [
            'errors' => $errors,
            'remaining_attempts' => $remaining_attempts,
            'form_disabled' => $form_disabled,  // Pass form_disabled flag
            'timer' => ($form_disabled) ? 30 : null // 30 seconds timer if form is disabled
        ]);
    }

    public function logout() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        // Destroy session
        session_destroy();

        // Redirect to login form
        header("Location: /login-form");
        exit;
    }
}
