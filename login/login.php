<!doctype html>
<html lang="en">

  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Page Skeleton</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="login-style.css">
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
          <h2 class="fw-bold mb-2 welcome-text">Welcome back!</h2>
          <p class="text-muted mb-4 sub-text">Please enter your email and password.</p>

          <form id="loginForm">
            <div class="mb-3">
              <input type="email" id="email" class="form-control custom-input" placeholder="Your email" required>
            </div>

            <div class="mb-2">
              <input type="password" id="password" class="form-control custom-input" placeholder="Password" required>
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
              Don’t have an account? <a href="#" id="signupLink">Sign up</a>
            </p>
          </form>
        </div>
      </div>
    </div>

    <script src="login-script.js"></script>
  </body>

</html>