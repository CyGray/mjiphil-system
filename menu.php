<link rel="stylesheet" href="../styles/components.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

<?php
    $current_page = basename($_SERVER['PHP_SELF']);
    $is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
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
        
        <!-- Catalog - Show if not on catalog page -->
        <?php if ($current_page != 'catalog.php'): ?>
        <li class="nav-item">
            <a class="nav-link" href="catalog.php">
                <i class="bi bi-grid-3x3-gap-fill me-2"></i>CATALOG
            </a>
        </li>
        <?php endif; ?>
        
        <!-- Inventory - Show if not on inventory page AND user is admin -->
        <?php if ($current_page != 'inventory.php' && $is_admin): ?>
        <li class="nav-item">
            <a class="nav-link" href="inventory.php">
                <i class="bi bi-box-seam me-2"></i>INVENTORY
            </a>
        </li>
        <?php endif; ?>
        
        <!-- About Us - Grayed out -->
        <li class="nav-item">
            <a class="nav-link disabled" href="#" style="color: #6c757d !important; cursor: not-allowed;">
                <i class="bi bi-info-circle me-2"></i>ABOUT US
            </a>
        </li>
        
        <!-- Settings - Grayed out -->
        <li class="nav-item">
            <a class="nav-link disabled" href="#" style="color: #6c757d !important; cursor: not-allowed;">
                <i class="bi bi-gear me-2"></i>SETTINGS
            </a>
        </li>
    </ul>
    
    <div class="logout-container mt-auto">
        <a class="nav-link logout-btn" href="./api/logout.php">
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

<style>
    /* Additional styling for disabled links */
    .nav-link.disabled {
        opacity: 0.6;
    }
    
    .nav-link.disabled:hover {
        background-color: transparent !important;
        transform: none !important;
    }
    
    /* Logout button styling - no background, smaller, left aligned */
    .logout-btn {
        background-color: transparent !important;
        color: #dc3545 !important;
        border: none;
        padding: 8px 15px !important;
        margin: 5px 0 !important;
        font-size: 13px !important;
        font-weight: 500;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        transition: all 0.2s ease;
        border-radius: 8px;
        width: auto !important;
    }
    
    .logout-btn i {
        color: #dc3545 !important;
        font-size: 14px !important;
        margin-right: 8px;
        transition: color 0.2s ease;
    }
    
    .logout-btn:hover {
        background-color: rgba(220, 53, 69, 0.1) !important;
        color: #dc3545 !important;
        transform: translateX(3px);
    }
    
    .logout-btn:hover i {
        color: #dc3545 !important;
    }
</style>