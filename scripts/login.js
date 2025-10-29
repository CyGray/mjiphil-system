// /scripts/login-script.js
document.addEventListener("DOMContentLoaded", function() {
  const loginForm = document.getElementById("loginForm");
  const signupLink = document.getElementById("signupLink");
  const forgotPasswordLink = document.getElementById("forgotPasswordLink");

  // If user already logged in before
  const loggedInUser = localStorage.getItem("loggedInUser");
  if (loggedInUser) {
    window.location.href = "/mjiphil-system/catalog.php";
  }

  loginForm.addEventListener("submit", function(e) {
    e.preventDefault();

    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();

    const users = JSON.parse(localStorage.getItem("registeredUsers")) || [];

    const foundUser = users.find(
      user => user.email === email && user.password === btoa(password)
    );

    if (foundUser) {
      localStorage.setItem("loggedInUser", JSON.stringify(foundUser));
      alert("Login successful!");
      window.location.href = "/mjiphil-system/catalog.php";
    } else {
      alert("Invalid email or password.");
    }
  });

  signupLink.addEventListener("click", function(e) {
    e.preventDefault();
    window.location.href = "/mjiphil-system/register/register.php";
  });

  forgotPasswordLink.addEventListener("click", function(e) {
    e.preventDefault();
    window.location.href = "/mjiphil-system/forgotpassword.php";
  });
});
