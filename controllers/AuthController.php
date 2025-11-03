<?php
/**
 * AuthController
 *
 * Handles user authentication-related actions:
 *  - Login
 *  - Register
 *  - Logout
 *  - Session check
 *
 * Depends on a User model and a PDO instance.
 */

// Include the User model file dynamically
$userFile = __DIR__ . '/../models/User.php';
include_once $userFile;

// Ensure the User class exists after including the file
if (!class_exists('User')) {
    die("Error: Class 'User' not found after including $userFile. Check for syntax errors in User.php.");
}

class AuthController
{
    /** @var PDO $pdo Database connection */
    private $pdo;

    /** @var User $userModel Instance of the User model */
    private $userModel;

    /**
     * AuthController constructor.
     *
     * @param PDO $pdo Database connection
     */
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
        $this->userModel = new User($pdo);
    }

    /**
     * Handle login logic (form submission + validation + session creation)
     *
     * @return void
     */
    public function login()
    {
        // Load configuration (includes reCAPTCHA keys, etc.)
        $config = include __DIR__ . '/../config.php';

        // Initialize variables for view rendering
        $errors = [];
        $generalError = '';

        // Only process the form on POST requests
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Collect and sanitize form input
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            // Basic server-side validation
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors['email'] = 'Please enter a valid email address.';
            }

            if (empty($password)) {
                $errors['password'] = 'Password is required.';
            }

            // Proceed only if there are no validation errors
            if (empty($errors)) {
                // Fetch the user by email
                $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
                $stmt->execute(['email' => $email]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                // Verify password and user existence
                if ($user && password_verify($password, $user['password'])) {
                    // Secure session regeneration (prevents session fixation)
                    session_regenerate_id(true);

                    // Store session data
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_email'] = $user['email'];

                    // Inside the login() method, after successful authentication:
                    $_SESSION['user_id'] = $user['id'];  // Or whatever your user ID field is
                    session_regenerate_id(true);  // Security best practice
                    header('Location: /dashboard');  // Redirect to new dashboard
                    exit();
                } else {
                    // Avoid revealing whether the email exists
                    $generalError = 'Invalid email or password.';
                }
            }
        }

        // Render the login view with available variables
        include __DIR__ . '/../views/login.php';
    }

    /**
     * Handle user registration (form submission + validation + creation)
     *
     * @return void
     */
    public function register()
    {
        $error = '';
        $generalError = '';
        $config = include __DIR__ . '/../config.php';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Collect form data
            $firstname = $_POST['firstname'] ?? '';
            $lastname = $_POST['lastname'] ?? '';
            $school = $_POST['school'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            // Validate required fields
            if (empty($firstname) || empty($lastname) || empty($email) || empty($password)) {
                $error = 'First name, last name, email, and password are required.';
            } elseif ($this->userModel->findByEmail($email)) {
                $error = 'Email already registered.';
            } else {
                    // Create the new user record
                    try {
                        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                        $this->userModel->create($firstname, $lastname, $school, $email, $hashedPassword);

                        // Redirect to login page after successful registration
                        header('Location: /login');
                        exit;
                    } catch (Exception $e) {
                        $error = 'Registration failed: ' . $e->getMessage();
                    }
            }
        }

        // Render the register view with error variables
        include __DIR__ . '/../views/register.php';
    }

    /**
     * Destroy the user session and log out
     *
     * @return void
     */
    public function logout()
    {
        session_destroy();
        header('Location: /login');
        exit;
    }

    /**
     * Check if a user is currently authenticated (for AJAX, etc.)
     *
     * @return void
     */
    public function check()
    {
        echo json_encode([
            'authenticated' => isset($_SESSION['user_id'])
        ]);
    }
}
