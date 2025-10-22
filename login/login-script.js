document.addEventListener("DOMContentLoaded", function() {
  const loginForm = document.getElementById("loginForm");
  const signupLink = document.getElementById("signupLink");
  const forgotPasswordLink = document.getElementById("forgotPasswordLink");

  loginForm.addEventListener("submit", function(e) {
    e.preventDefault();
    window.location.href = "dashboard.html";
  });

  signupLink.addEventListener("click", function(e) {
    e.preventDefault();
    window.location.href = "register.php";
  });

  forgotPasswordLink.addEventListener("click", function(e) {
    e.preventDefault();
    window.location.href = "forgotpassword.html";
  });
});