<?php
/**
 * api/views/login.php
 *
 * This view renders the login form with reCAPTCHA v3 integration.
 * It displays any errors passed from the controller and handles form submission.
 * Assumes layout.php provides the overall page structure (header, footer, etc.).
 */

// Include the main layout (header, nav, etc.)
include 'layout.php';
?>

<main class="flex-grow container mx-auto p-4 flex items-center justify-center">
    <div class="bg-white p-6 rounded shadow-md w-full max-w-md">

        <!-- Page title -->
        <h2 class="text-xl font-bold mb-4">Login</h2>

        <!-- General error message (e.g., authentication or reCAPTCHA errors) -->
        <?php if (isset($generalError)): ?>
            <p class="text-red-500 mb-4"><?= htmlspecialchars($generalError) ?></p>
        <?php endif; ?>

        <!-- Login form -->
        <form method="POST" class="space-y-4" id="loginForm">
            
            <!-- Email input -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    placeholder="Email" 
                    required 
                    class="w-full p-2 border rounded mt-1"
                >
                <?php if (isset($errors['email'])): ?>
                    <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['email']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Password input -->
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    placeholder="Password" 
                    required 
                    class="w-full p-2 border rounded mt-1"
                >
                <?php if (isset($errors['password'])): ?>
                    <p class="text-red-500 text-sm mt-1"><?= htmlspecialchars($errors['password']) ?></p>
                <?php endif; ?>
            </div>

            <!-- Hidden reCAPTCHA token -->
            <input type="hidden" name="recaptcha_token" id="recaptcha_token">

            <!-- Submit button -->
            <button 
                type="submit" 
                class="w-full bg-blue-600 text-white p-2 rounded hover:bg-blue-700"
            >
                Login
            </button>
        </form>

        <!-- Registration link -->
        <p class="mt-4 text-center">
            Do not have an account? 
            <a href="/register" class="text-blue-600 hover:underline">Register here</a>
        </p>
    </div>
</main>

<!-- Include footer -->
<?php include 'footer.php'; ?>

<!-- Load reCAPTCHA v3 script asynchronously -->
<script src="https://www.google.com/recaptcha/api.js?render=<?= htmlspecialchars($config['recaptcha']['site_key']) ?>"></script>

<script>
    /**
     * Handle form submission with reCAPTCHA v3
     * Prevent default form submit, execute reCAPTCHA, then submit with token
     */
    document.getElementById('loginForm').addEventListener('submit', function(event) {
        event.preventDefault(); // Stop default submission

        grecaptcha.ready(function() {
            grecaptcha.execute('<?= htmlspecialchars($config['recaptcha']['site_key']) ?>', { action: 'login' })
                .then(function(token) {
                    // Set the reCAPTCHA token and submit form
                    document.getElementById('recaptcha_token').value = token;
                    event.target.submit();
                })
                .catch(function(error) {
                    console.error('reCAPTCHA error:', error);
                    // Optionally display a user-friendly message
                });
        });
    });
</script>
