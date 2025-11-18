document.addEventListener("DOMContentLoaded", function() {
  const registerForm = document.getElementById("registerForm");
  const loginLink = document.getElementById("loginLink");

  registerForm.addEventListener("submit", function(e) {
    e.preventDefault();

    const name = document.getElementById("name").value.trim();
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value.trim();
    const confirmPassword = document.getElementById("confirmPassword").value.trim();

    if (password !== confirmPassword) {
      alert("Passwords do not match!");
      return;
    }

    const users = JSON.parse(localStorage.getItem("registeredUsers")) || [];

    const existingUser = users.find(u => u.email === email);
    if (existingUser) {
      alert("This email is already registered.");
      return;
    }

    users.push({
      name,
      email,
      password: btoa(password), 
    });

    localStorage.setItem("registeredUsers", JSON.stringify(users));
    alert("Registration successful! Please log in.");
    window.location.href = "/mjiphil-system/login.php";
  });

  loginLink.addEventListener("click", function(e) {
    e.preventDefault();
    window.location.href = "/mjiphil-system/login.php";
  });
});
