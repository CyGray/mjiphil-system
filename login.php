<?php
require_once 'config.php';
if (isset($_SESSION['user_id'])) {
    header("Location: ./dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $errors = [];
    if (empty($email)) $errors[] = "Email is required";
    if (empty($password)) $errors[] = "Password is required";

    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT user_id, first_name, last_name, email, password, role FROM user WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && base64_decode($user['password']) === $password) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['first_name'] = $user['first_name'];
                $_SESSION['last_name'] = $user['last_name'];
                $_SESSION['role'] = $user['role'];

                header("Location: ./dashboard.php");
                exit;
            } else {
                $errors[] = "Invalid email or password";
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
    <title>Login | MJIPhil Construction</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="./styles/login.css">
  </head>

  <body>
    <!-- Include Alert Modal -->
    <?php include 'utils/alert.php'; ?>

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
          <h2 class="fw-bold mb-2 welcome-text">Welcome back!</h2>
          <p class="text-muted mb-4 sub-text">Please enter your email and password.</p>

          <?php if (!empty($errors)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
              <?php foreach ($errors as $error): ?>
                <div><?php echo htmlspecialchars($error); ?></div>
              <?php endforeach; ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          <?php endif; ?>

          <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
              <?php echo htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?>
              <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
          <?php endif; ?>

          <form method="POST" id="loginForm">
            <div class="mb-3">
              <input type="email" name="email" id="email" class="form-control custom-input" placeholder="Your email" value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" required>
            </div>

            <div class="mb-2 position-relative">
              <input type="password" name="password" id="password" class="form-control custom-input" placeholder="Password" required>
              <button type="button" class="btn btn-link position-absolute password-toggle d-none" id="togglePassword">
                <i class="bi bi-eye-slash" id="eyeIcon"></i>
              </button>
            </div>

            <div class="text-end mb-3">
              <a href="#" id="forgotPasswordLink" class="small text-muted">Forgot password?</a>
            </div>

            <button type="submit" class="btn btn-login w-100 custom-btn">Sign in</button>

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
              Don't have an account? <a href="./register.php" id="signupLink">Sign up</a>
            </p>
          </form>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="./scripts/login.js"></script>
  </body>
</html>