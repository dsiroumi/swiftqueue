<?php
/**
 * views/register.php
 * 
 * This view renders the user registration form with client-side validation using Validate.js,
 * password strength feedback, and invisible reCAPTCHA integration.
 * 
 * Assumes variables like $errors (array), $generalError (string), and $config (array)
 * are passed from the controller. Includes a layout file for shared structure.
 */

// Include shared layout (header, footer, etc.)
include 'layout.php';
?>

<main class="flex-grow container mx-auto p-4 flex items-center justify-center">
    <div class="bg-white p-6 rounded shadow-md w-full max-w-md">
        <h2 class="text-xl font-bold mb-4">Register</h2>
        
        <!-- Display global (non-field-specific) errors, e.g., CAPTCHA or server issues -->
        <?php if (isset($generalError)): ?>
            <p class="text-red-500 mb-4"><?= htmlspecialchars($generalError) ?></p>
        <?php endif; ?>
        
        <!-- Registration form with POST method, client-side validation, and novalidate to disable browser defaults -->
        <form method="POST" class="space-y-4" id="registerForm" novalidate>
            <!-- First Name Field -->
            <div>
                <input type="text" name="firstname" id="firstname" placeholder="First Name" value="<?= htmlspecialchars($_POST['firstname'] ?? '') ?>" required class="w-full p-2 border rounded">
                <span id="firstnameError" class="text-red-500 text-sm"><?= $errors['firstname'] ?? '' ?></span>
            </div>
            
            <!-- Last Name Field -->
            <div>
                <input type="text" name="lastname" id="lastname" placeholder="Last Name" value="<?= htmlspecialchars($_POST['lastname'] ?? '') ?>" required class="w-full p-2 border rounded">
                <span id="lastnameError" class="text-red-500 text-sm"><?= $errors['lastname'] ?? '' ?></span>
            </div>
            
            <!-- School Field -->
            <div>
                <input type="text" name="school" id="school" placeholder="School" value="<?= htmlspecialchars($_POST['school'] ?? '') ?>" required class="w-full p-2 border rounded">
                <span id="schoolError" class="text-red-500 text-sm"><?= $errors['school'] ?? '' ?></span>
            </div>
            
            <!-- Email Field -->
            <div>
                <input type="email" name="email" id="email" placeholder="Email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required class="w-full p-2 border rounded">
                <span id="emailError" class="text-red-500 text-sm"><?= $errors['email'] ?? '' ?></span>
            </div>
            
            <!-- Password Field with Strength Indicator -->
            <div>
                <input type="password" name="password" id="password" placeholder="Password" required class="w-full p-2 border rounded">
                <span id="passwordError" class="text-red-500 text-sm"><?= $errors['password'] ?? '' ?></span>
                <div id="passwordStrength" class="text-sm text-gray-600"></div>
            </div>
            
            <!-- Hidden input for reCAPTCHA token (populated by JS) -->
            <input type="hidden" name="recaptcha_token" id="recaptcha_token">
            
            <!-- Submit Button -->
            <button type="submit" class="w-full bg-green-600 text-white p-2 rounded hover:bg-green-700">Register</button>
        </form>
        
        <!-- Login Link -->
        <p class="mt-4 text-center">Have an account? <a href="/login" class="text-blue-600 hover:underline">Login</a></p>
    </div>
</main>
<?php include 'footer.php'; ?>
<!-- Load Google reCAPTCHA v3 script (invisible; must be loaded asynchronously) -->
<script src="https://www.google.com/recaptcha/api.js?render=<?= htmlspecialchars($config['recaptcha']['site_key']) ?>"></script>

<!-- Load Validate.js via CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/validate.js/0.13.1/validate.min.js"></script>

<!-- Custom JavaScript: Validation with Validate.js, password strength, and reCAPTCHA -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('registerForm');
    const strengthDiv = document.getElementById('passwordStrength');
    
    // Define validation constraints using Validate.js
    const constraints = {
        firstname: {
            presence: { allowEmpty: false, message: '^This field cannot be empty.' }
        },
        lastname: {
            presence: { allowEmpty: false, message: '^This field cannot be empty.' }
        },
        school: {
            presence: { allowEmpty: false, message: '^This field cannot be empty.' }
        },
        email: {
            presence: { allowEmpty: false, message: '^This field cannot be empty.' },
            email: { message: '^Please enter a valid email address.' }
        },
        password: {
            presence: { allowEmpty: false, message: '^This field cannot be empty.' },
            // Custom validator for password strength
            format: {
                pattern: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z\d]).{8,}$/,
                message: '^Password must be at least 8 characters with uppercase, lowercase, number, and special character.'
            }
        }
    };

    // Function to validate a single field and update UI
    const validateField = (fieldName) => {
        const field = document.getElementById(fieldName);
        const errorSpan = document.getElementById(`${fieldName}Error`);
        const values = { [fieldName]: field.value.trim() };
        const errors = validate(values, { [fieldName]: constraints[fieldName] }) || {};

        const errorMsg = errors[fieldName] ? errors[fieldName][0] : '';
        errorSpan.textContent = errorMsg;
        if (errorMsg) {
            field.classList.add('border-red-500');
            field.classList.remove('border-gray-300');  // Adjust default class if needed
        } else {
            field.classList.remove('border-red-500');
            field.classList.add('border-gray-300');
        }

        return !errorMsg;
    };

    // Real-time validation: Validate on blur
    ['firstname', 'lastname', 'school', 'email', 'password'].forEach(fieldName => {
        document.getElementById(fieldName).addEventListener('blur', () => validateField(fieldName));
    });

    // Password strength checker (updates on input, independent of validation)
    document.getElementById('password').addEventListener('input', () => {
        const password = document.getElementById('password').value;
        let strength = 'Weak';
        let colorClass = 'text-red-600';

        if (password.length >= 8 && /[A-Z]/.test(password) && /[a-z]/.test(password) && /[0-9]/.test(password) && /[^A-Za-z0-9]/.test(password)) {
            strength = 'Strong';
            colorClass = 'text-green-600';
        } else if (password.length >= 6) {
            strength = 'Medium';
            colorClass = 'text-yellow-600';
        }

        strengthDiv.textContent = `Password strength: ${strength}`;
        strengthDiv.className = `text-sm ${colorClass}`;

        // Re-validate to clear error if fixed
        validateField('password');
    });

    // Form submission handler: Validate all fields first
    form.addEventListener('submit', (e) => {
        e.preventDefault();  // Prevent default until validated

        // Collect all form values
        const formValues = {
            firstname: document.getElementById('firstname').value.trim(),
            lastname: document.getElementById('lastname').value.trim(),
            school: document.getElementById('school').value.trim(),
            email: document.getElementById('email').value.trim(),
            password: document.getElementById('password').value
        };

        const errors = validate(formValues, constraints) || {};
        let valid = true;

        // Update UI for all fields
        Object.keys(constraints).forEach(fieldName => {
            const errorMsg = errors[fieldName] ? errors[fieldName][0] : '';
            document.getElementById(`${fieldName}Error`).textContent = errorMsg;
            const field = document.getElementById(fieldName);
            if (errorMsg) {
                field.classList.add('border-red-500');
                field.classList.remove('border-gray-300');
                valid = false;
            } else {
                field.classList.remove('border-red-500');
                field.classList.add('border-gray-300');
            }
        });

        if (!valid) return;  // Stop if errors

        // Generate reCAPTCHA token and submit
        grecaptcha.ready(() => {
            grecaptcha.execute('<?= htmlspecialchars($config['recaptcha']['site_key']) ?>', { action: 'register' })
                .then((token) => {
                    document.getElementById('recaptcha_token').value = token;
                    form.submit();
                })
                .catch((error) => {
                    console.error('reCAPTCHA error:', error);
                });
        });
    });
});
</script>
