document.addEventListener("DOMContentLoaded", function() {
  const loginForm = document.getElementById("loginForm");
  const registerForm = document.getElementById("registerForm");
  const loginLink = document.getElementById("loginLink");
  const forgotPasswordLink = document.getElementById("forgotPasswordLink");

  if (loginForm) {
    loginForm.addEventListener("submit", function(e) {
      e.preventDefault();
      window.location.href = "/mjiphil-system2/dashboard.php";
    });
  }

  if (registerForm) {
    registerForm.addEventListener("submit", function(e) {
      e.preventDefault();
      window.location.href = "/mjiphil-system2/login/login.php";
    });
  }

  if (loginLink) {
    loginLink.addEventListener("click", function(e) {
      e.preventDefault();
      window.location.href = "/mjiphil-system2/login/login.php";
    });
  }

  if (forgotPasswordLink) {
    forgotPasswordLink.addEventListener("click", function(e) {
      e.preventDefault();
      window.location.href = "/mjiphil-system2/forgotpassword.php";
    });
  }
});