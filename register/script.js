document.addEventListener("DOMContentLoaded", function() {
  const loginForm = document.getElementById("loginForm");
  const registerForm = document.getElementById("registerForm");
  const signupLink = document.getElementById("signupLink");
  const loginLink = document.getElementById("loginLink");
  const forgotPasswordLink = document.getElementById("forgotPasswordLink");

  if (loginForm) {
    loginForm.addEventListener("submit", function(e) {
      e.preventDefault();
      window.location.href = "dashboard.php";
    });
  }

  if (registerForm) {
    registerForm.addEventListener("submit", function(e) {
      e.preventDefault();
      window.location.href = "login/login.php";
    });
  }

  if (loginLink) {
    loginLink.addEventListener("click", function(e) {
      e.preventDefault();
      window.location.href = "login/login.php";
    });
  }

  // Forgot password redirect
  if (forgotPasswordLink) {
    forgotPasswordLink.addEventListener("click", function(e) {
      e.preventDefault();
      window.location.href = "forgotpassword.php";
    });
  }
});
