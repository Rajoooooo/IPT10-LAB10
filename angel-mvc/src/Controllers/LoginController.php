<?php
namespace Controllers;

use Models\User;
use Exception;

class LoginController
{
    private $userModel;

    public function __construct(User $userModel)
    {
        $this->userModel = $userModel;
    }

    // GET /login-form
    public function showLoginForm()
    {
        include __DIR__ . '/../views/login_form.php';
    }

    // POST /login
    public function login()
    {
        try {
            // Get the submitted form data
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';

            // Validate input
            if (empty($username) || empty($password)) {
                throw new Exception("Username and password are required.");
            }

            // Authenticate the user
            $user = $this->userModel->login($username, $password);

            if ($user) {
                echo "Login successful! Welcome, " . htmlspecialchars($user['first_name']);
            } else {
                throw new Exception("Invalid credentials.");
            }

        } catch (Exception $e) {
            // Display error messages
            echo $e->getMessage();
        }
    }
}
?>
