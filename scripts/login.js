// login.js

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

    loginForm.addEventListener("submit", async function(e) {
        e.preventDefault();

        const email = document.getElementById("email").value.trim();
        const password = document.getElementById("password").value.trim();

        if (!email || !password) {
            showAlert("warning", "Missing Information", "Please fill in all fields.");
            return;
        }

        try {
            const formData = new FormData();
            formData.append("email", email);
            formData.append("password", password);

            const response = await fetch("./api/login.php", {
                method: "POST",
                body: formData
            });

            const data = await response.json();
            console.log("[Login] Response:", data);

            if (data.success) {
                console.log(`[Login] User ID: ${data.user_id}, Role: ${data.role}`);
                showAlert("success", "Success", "Login successful! Redirecting...");
                
                // Redirect after a short delay to show the success message
                setTimeout(() => {
                    window.location.href = "./dashboard.php";
                }, 1500);
            } else {
                showAlert("danger", "Login Failed", data.message);
            }

        } catch (err) {
            console.error("Login failed:", err);
            showAlert("danger", "Login Error", "Login error: " + err.message);
        }
    });

    signupLink.addEventListener("click", function(e) {
        e.preventDefault();
        window.location.href = "./register.php";
    });

    forgotPasswordLink.addEventListener("click", function(e) {
        e.preventDefault();
        showAlert("info", "Coming Soon", "Password reset feature coming soon.");
    });
});