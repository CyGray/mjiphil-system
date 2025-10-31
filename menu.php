<link rel="stylesheet" href="../components/components-style.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

<?php
    $current_page = basename($_SERVER['PHP_SELF']);
?>

<div class="col-sm-2 sidebar d-flex flex-column">
    <div class="sidebar-header">
        <img
            src="./assets/nobg-logo.png"
            alt="MJIPhil Construction Logo"
            class="company-logo"
        />
        <span class="company-name">MJIPHIL CONSTRUCTION</span>
    </div>

    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>" href="dashboard.php">
                <i class="bi bi-speedometer2 me-2"></i>DASHBOARD
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?php echo ($current_page == 'catalog.php') ? 'active' : ''; ?>" href="catalog.php">
                <i class="bi bi-grid-3x3-gap-fill me-2"></i>CATALOG
            </a>
        </li>
    </ul>
    
    <div class="logout-container">
        <a class="nav-link logout-btn" href="./scripts/logout.php">
            <i class="bi bi-box-arrow-right me-2"></i>LOGOUT
        </a>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const logoutBtn = document.querySelector('.logout-btn');
        if (logoutBtn) {
            logoutBtn.addEventListener('click', function(e) {
                e.preventDefault();
                if (confirm('Are you sure you want to logout?')) {
                    window.location.href = this.getAttribute('href');
                }
            });
        }
    });
</script>