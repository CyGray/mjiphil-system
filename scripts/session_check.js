// /scripts/session-check.js
document.addEventListener("DOMContentLoaded", () => {
  const loggedInUser = localStorage.getItem("loggedInUser");
  if (!loggedInUser) {
    window.location.href = "/mjiphil-system/login.php";
  }
});
