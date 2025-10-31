document.addEventListener("DOMContentLoaded", function() {
    const loginForm = document.getElementById("loginForm");
    const signupLink = document.getElementById("signupLink");
    const forgotPasswordLink = document.getElementById("forgotPasswordLink");
    const togglePassword = document.getElementById("togglePassword");
    const passwordInput = document.getElementById("password");
    const eyeIcon = document.getElementById("eyeIcon");

    passwordInput.addEventListener("input", function() {
      if (passwordInput.value.length > 0) {
        togglePassword.classList.remove("d-none");
      } else {
        togglePassword.classList.add("d-none");
      }
    });

    togglePassword.addEventListener("click", function() {
      const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
      passwordInput.setAttribute("type", type);
      
      if (type === "password") {
        eyeIcon.classList.remove("bi-eye");
        eyeIcon.classList.add("bi-eye-slash");
      } else {
        eyeIcon.classList.remove("bi-eye-slash");
        eyeIcon.classList.add("bi-eye");
      }
    });

    loginForm.addEventListener("submit", function(e) {
      const email = document.getElementById("email").value.trim();
      const password = document.getElementById("password").value.trim();

      if (!email || !password) {
        e.preventDefault();
        alert("Please fill in all fields.");
        return;
      }
    });

    signupLink.addEventListener("click", function(e) {
      e.preventDefault();
      window.location.href = "./register.php";
    });

    forgotPasswordLink.addEventListener("click", function(e) {
      e.preventDefault();
      alert("Password reset feature coming soon.");
    });
  });