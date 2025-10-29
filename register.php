<?php
require_once 'config.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: ./catalog.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Split full name into first and last name
    $name_parts = explode(' ', $name, 2);
    $first_name = $name_parts[0];
    $last_name = isset($name_parts[1]) ? $name_parts[1] : '';
    
    $phone = ''; // Optional field
    $role = 'regular'; // Default role

    $errors = [];

    // Validation
    if (empty($name)) $errors[] = "Full name is required";
    if (empty($email)) $errors[] = "Email is required";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Invalid email format";
    if (empty($password)) $errors[] = "Password is required";
    if (strlen($password) < 6) $errors[] = "Password must be at least 6 characters";
    if ($password !== $confirm_password) $errors[] = "Passwords do not match";

    if (empty($errors)) {
        try {
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT user_id FROM user WHERE email = ?");
            $stmt->execute([$email]);
            
            if ($stmt->fetch()) {
                $errors[] = "Email already registered";
            } else {
                // Insert new user
                $encoded_password = base64_encode($password);
                $stmt = $pdo->prepare("INSERT INTO user (first_name, last_name, email, password, phone, role) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$first_name, $last_name, $email, $encoded_password, $phone, $role]);
                
                $_SESSION['success'] = "Registration successful! Please login.";
                header("Location: ./login.php");
                exit;
            }
        } catch (PDOException $e) {
            $errors[] = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register | MJIPhil Construction</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./styles/register.css">
  </head>

  <body>
    <div class="login-wrapper d-flex">

      <div class="left-area flex-grow-1">
        <img
          src="https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcSq0QCnioq1exA5sUOxXPD1XH0jNUdLrxCEug&s"
          alt="MJIPhil Construction Logo"
          class="company-logo"
        />
        <p class="tagline">Committed to Quality.<br>Driven by Integrity.</p>
      </div>

      <div class="login-panel d-flex align-items-center justify-content-center">
        <div class="login-form text-center w-75">
          <h2 class="fw-bold mb-2 welcome-text">Create an Account</h2>
          <p class="text-muted mb-4 sub-text">Please fill in the details to register.</p>

          <?php if (!empty($errors)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <?php foreach ($errors as $error): ?>
                <div><?php echo htmlspecialchars($error); ?></div>
              <?php endforeach; ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          <?php endif; ?>

          <form method="POST" id="registerForm">
            <div class="mb-3">
              <input type="text" name="name" id="name" class="form-control custom-input" placeholder="Full name" value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" required>
            </div>
            <div class="mb-3">
              <input type="email" name="email" id="email" class="form-control custom-input" placeholder="Email address" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
            </div>
            <div class="mb-3">
              <input type="password" name="password" id="password" class="form-control custom-input" placeholder="Password" required>
            </div>
            <div class="mb-3">
              <input type="password" name="confirm_password" id="confirmPassword" class="form-control custom-input" placeholder="Confirm password" required>
            </div>

            <button type="submit" class="btn btn-login w-100 custom-btn">Register</button>

            <div class="d-flex align-items-center my-3">
              <hr class="flex-grow-1">
              <span class="px-2 text-muted small">or continue with</span>
              <hr class="flex-grow-1">
            </div>

            <button type="button" class="btn btn-outline-secondary w-100 custom-google">
              <img src="https://www.gstatic.com/firebasejs/ui/2.0.0/images/auth/google.svg" alt="Google" width="20" class="me-2">
              Continue with Google
            </button>

            <p class="small text-muted mt-3">
              Already have an account? <a href="./login.php" id="loginLink">Sign in</a>
            </p>
          </form>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      document.addEventListener("DOMContentLoaded", function() {
        const registerForm = document.getElementById("registerForm");
        const loginLink = document.getElementById("loginLink");

        // Client-side validation
        registerForm.addEventListener("submit", function(e) {
          const password = document.getElementById("password").value.trim();
          const confirmPassword = document.getElementById("confirmPassword").value.trim();

          if (password !== confirmPassword) {
            e.preventDefault();
            alert("Passwords do not match!");
            return;
          }

          if (password.length < 6) {
            e.preventDefault();
            alert("Password must be at least 6 characters long!");
            return;
          }
        });

        loginLink.addEventListener("click", function(e) {
          e.preventDefault();
          window.location.href = "./login.php";
        });
      });
    </script>
  </body>
</html>