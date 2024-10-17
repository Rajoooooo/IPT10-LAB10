<?php
namespace App\Controllers;

use App\Models\User;

use Exception;

class LoginController extends BaseController
{

    public function index() {

        $template = 'login-form';


        $output = $this->render($template);

        return $output;
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
