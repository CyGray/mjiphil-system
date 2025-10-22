<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory - MJI PHIL Construction</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4a2c2a;
            --secondary-color: #6b4542;
            --accent-color: #8b5e5a;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f8f8;
        }

        .navbar {
            background-color: var(--primary-color);
            padding: 1rem 2rem;
        }

        .navbar-brand {
            color: white !important;
            font-weight: bold;
            font-size: 1.2rem;
        }

        .navbar-nav .nav-link {
            color: white !important;
            margin: 0 1rem;
        }

        .header-title {
            font-size: 2rem;
            font-weight: bold;
            letter-spacing: 0.1em;
            margin: 2rem 0 1rem 0;
        }

        .tabs {
            border-bottom: 2px solid #ddd;
            margin-bottom: 1.5rem;
        }

        .tab-btn {
            background: none;
            border: none;
            padding: 0.75rem 1.5rem;
            cursor: pointer;
            font-weight: 500;
            color: #666;
            border-bottom: 3px solid transparent;
            transition: all 0.3s;
        }

        .tab-btn.active {
            color: var(--primary-color);
            border-bottom-color: var(--primary-color);
        }

        .filter-section {
            background: white;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .btn-primary-custom {
            background-color: var(--primary-color);
            border: none;
            color: white;
            padding: 0.5rem 1.2rem;
            border-radius: 20px;
            font-weight: 500;
            transition: all 0.3s;
            font-size: 0.9rem;
        }

        .btn-primary-custom:hover {
            background-color: var(--secondary-color);
        }

        .btn-outline-custom {
            border: 1px solid #ddd;
            background: white;
            color: #333;
            padding: 0.5rem 1.2rem;
            border-radius: 20px;
            transition: all 0.3s;
            font-size: 0.9rem;
        }

        .btn-outline-custom:hover {
            background-color: #f8f9fa;
        }

        .dropdown-custom {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 0.5rem 1.2rem;
            border-radius: 20px;
            cursor: pointer;
            font-size: 0.9rem;
        }

        table {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-collapse: separate;
            border-spacing: 0;
        }

        thead {
            background-color: #d9d9d9;
        }

        thead th {
            font-weight: 600;
            color: #333;
            padding: 0.85rem 1rem;
            cursor: pointer;
            user-select: none;
            font-size: 0.9rem;
            border-bottom: none;
        }

        thead th:first-child {
            border-top-left-radius: 8px;
        }

        thead th:last-child {
            border-top-right-radius: 8px;
        }

        tbody td {
            padding: 0.85rem 1rem;
            vertical-align: middle;
            font-size: 0.9rem;
            border-bottom: 1px solid #e8e8e8;
        }

        tbody tr:nth-child(even) {
            background-color: #fafafa;
        }

        tbody tr:hover {
            background-color: #f0f0f0;
        }

        tbody tr:last-child td:first-child {
            border-bottom-left-radius: 8px;
        }

        tbody tr:last-child td:last-child {
            border-bottom-right-radius: 8px;
        }

        tbody tr:last-child td {
            border-bottom: none;
        }

        .item-image {
            width: 45px;
            height: 45px;
            background-color: #d8d8d8;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .grid-view {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 1.5rem;
        }

        .grid-item {
            background: white;
            border-radius: 8px;
            padding: 1rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            text-align: center;
            transition: all 0.3s;
        }

        .grid-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .grid-item-image {
            width: 100%;
            height: 150px;
            background-color: #e8e8e8;
            border-radius: 8px;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .grid-item-image img {
            max-width: 100%;
            max-height: 100%;
            object-fit: cover;
        }

        .grid-item h5 {
            font-size: 1rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .grid-item p {
            margin: 0.25rem 0;
            font-size: 0.9rem;
            color: #666;
        }

        .footer {
            background-color: var(--primary-color);
            color: white;
            padding: 2.5rem 0 1.5rem 0;
            margin-top: 4rem;
        }

        .footer h5 {
            font-weight: bold;
            margin-bottom: 1rem;
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .footer p {
            font-size: 0.85rem;
            line-height: 1.6;
        }

        .footer a {
            color: white;
            text-decoration: none;
            display: block;
            margin: 0.4rem 0;
            font-size: 0.85rem;
        }

        .footer a:hover {
            text-decoration: underline;
        }

        .social-icons a {
            color: white;
            font-size: 1.5rem;
            margin: 0 0.5rem;
        }

        .modal-content {
            border-radius: 12px;
        }

        .modal-header {
            border-bottom: none;
            padding: 1.5rem;
        }

        .modal-body {
            padding: 2rem;
        }

        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 0.5rem;
        }

        .form-control {
            border-radius: 6px;
            border: 1px solid #ddd;
            padding: 0.75rem;
        }

        .image-upload-area {
            width: 120px;
            height: 120px;
            background-color: #666;
            border-radius: 8px;
            margin: 0 auto 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s;
        }

        .image-upload-area:hover {
            background-color: #555;
        }

        .pagination {
            margin-top: 2rem;
            gap: 0.3rem;
        }

        .page-link {
            color: #333;
            border: 1px solid #ddd;
            padding: 0.45rem 0.7rem;
            font-size: 0.9rem;
            border-radius: 4px;
            margin: 0 0.15rem;
        }

        .page-item.active .page-link {
            background-color: white;
            border-color: #333;
            color: #333;
            font-weight: 600;
        }

        .page-item {
            margin: 0;
        }

        .view-toggle {
            display: flex;
            gap: 0;
            border: 1px solid #ddd;
            border-radius: 4px;
            overflow: hidden;
        }

        .view-toggle button {
            padding: 0.5rem 0.75rem;
            border: none;
            background: white;
            cursor: pointer;
            transition: all 0.3s;
            color: #666;
            border-right: 1px solid #ddd;
        }

        .view-toggle button:last-child {
            border-right: none;
        }

        .view-toggle button.active {
            background-color: #f0f0f0;
            color: #333;
        }

        .search-box {
            position: relative;
        }

        .search-box input {
            padding-right: 3rem;
        }

        .search-box i {
            position: absolute;
            right: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
        }

        .action-icons {
            display: flex;
            gap: 0.75rem;
            justify-content: flex-end;
        }

        .action-icons i {
            cursor: pointer;
            color: #999;
            font-size: 1rem;
        }

        .action-icons i:hover {
            color: #666;
        }

        .pagination-info {
            font-size: 0.9rem;
            color: #666;
        }

        .showing-dropdown {
            border: 1px solid #ddd;
            padding: 0.3rem 0.5rem;
            border-radius: 4px;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <i class="fas fa-building"></i> MJI PHIL
            </a>
            <ul class="navbar-nav ms-auto d-flex flex-row">
                <li class="nav-item"><a class="nav-link" href="#">About Us</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Products & Services</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Inventory</a></li>
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="fas fa-user-circle"></i> Log In</a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-4">
        <h1 class="header-title">INVENTORY</h1>

        <!-- Tabs -->
        <div class="tabs">
            <button class="tab-btn active" onclick="switchTab('all')">All</button>
            <button class="tab-btn" onclick="switchTab('brgy')">Brgy. Bata</button>
        </div>

        <!-- Search and Controls -->
        <div class="row mb-3">
            <div class="col-md-6">
                <div class="search-box">
                    <input type="text" class="form-control" placeholder="Search by name or ID..." id="searchInput">
                    <i class="fas fa-search"></i>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex gap-3 flex-wrap">
                        <select class="dropdown-custom" id="typeFilter">
                            <option>Type: All</option>
                            <option>Tools</option>
                            <option>Materials</option>
                            <option>Safety Gear</option>
                            <option>Essentials</option>
                        </select>
                        <select class="btn-outline-custom" id="stockFilter">
                            <option>Low Stock</option>
                            <option>In Stock</option>
                            <option>Out of Stock</option>
                        </select>
                        <select class="btn-outline-custom" id="demandFilter">
                            <option>High Demand</option>
                            <option>Medium Demand</option>
                            <option>Low Demand</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <div class="d-flex gap-2 justify-content-end align-items-center flex-wrap">
                        <button class="btn-primary-custom" onclick="openAddModal()">
                            <i class="fas fa-plus"></i> Add Item
                        </button>
                        <button class="btn-outline-custom" onclick="bulkUpload()">
                            <i class="fas fa-upload"></i> Bulk Upload
                        </button>
                        <button class="btn-outline-custom" onclick="filterItems()">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        <div class="view-toggle">
                            <button class="active" id="listViewBtn" onclick="switchView('list')">
                                <i class="fas fa-list"></i>
                            </button>
                            <button id="gridViewBtn" onclick="switchView('grid')">
                                <i class="fas fa-th"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table View -->
        <div id="listView">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Item Name <i class="fas fa-sort"></i></th>
                            <th>Image <i class="fas fa-sort"></i></th>
                            <th>Price <i class="fas fa-sort"></i></th>
                            <th>Quantity <i class="fas fa-sort"></i></th>
                            <th>Monthly Sales <i class="fas fa-sort"></i></th>
                            <th>Type <i class="fas fa-sort"></i></th>
                            <th>Description <i class="fas fa-sort"></i></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="inventoryTableBody">
                        <!-- Items will be loaded here -->
                    </tbody>
                </table>
            </div>

            <!-- Pagination with info -->
            <div class="d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <span class="pagination-info">Showing</span>
                    <select class="showing-dropdown">
                        <option>10</option>
                        <option>25</option>
                        <option>50</option>
                    </select>
                </div>
                <div class="pagination-info">Showing 1 to 8 out of 8 records</div>
                <nav>
                    <ul class="pagination mb-0">
                        <li class="page-item"><a class="page-link" href="#"><i class="fas fa-chevron-left"></i></a></li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item"><a class="page-link" href="#">4</a></li>
                        <li class="page-item"><a class="page-link" href="#"><i class="fas fa-chevron-right"></i></a></li>
                    </ul>
                </nav>
            </div>
        </div>

        <!-- Grid View -->
        <div id="gridView" style="display: none;">
            <div class="grid-view" id="inventoryGridView">
                <!-- Grid items will be loaded here -->
            </div>
        </div>
    </div>

    <!-- Add Item Modal -->
    <div class="modal fade" id="addItemModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add an Item</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="addItemForm">
                        <div class="mb-3 text-center">
                            <label class="form-label">Item Image</label>
                            <div class="image-upload-area" onclick="document.getElementById('imageUpload').click()">
                                <i class="fas fa-camera fa-2x text-white"></i>
                            </div>
                            <input type="file" id="imageUpload" style="display: none;" accept="image/*">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Item Name</label>
                            <input type="text" class="form-control" placeholder="Product name">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Price</label>
                            <input type="text" class="form-control" placeholder="Input price here">
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Quantity</label>
                                <input type="number" class="form-control" placeholder="Quantity">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Item Type</label>
                                <select class="form-control">
                                    <option>Select Type</option>
                                    <option>Tools</option>
                                    <option>Materials</option>
                                    <option>Safety Gear</option>
                                    <option>Essentials</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Image URL (optional)</label>
                            <input type="text" class="form-control" placeholder="Image URL">
                        </div>

                        <button type="submit" class="btn btn-primary-custom w-100">Confirm</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <h5>LOGO</h5>
                    <p>Your trusted partner in construction supply and services.</p>
                </div>
                <div class="col-md-2">
                    <h5>PRIVACY</h5>
                    <a href="#">Terms of use</a>
                    <a href="#">Privacy policy</a>
                    <a href="#">Contact</a>
                </div>
                <div class="col-md-2">
                    <h5>SERVICES</h5>
                    <a href="#">Shop</a>
                    <a href="#">Order Status</a>
                    <a href="#">News</a>
                </div>
                <div class="col-md-2">
                    <h5>ABOUT US</h5>
                    <a href="#">Find a location</a>
                    <a href="#">About us</a>
                    <a href="#">Our story</a>
                </div>
                <div class="col-md-3">
                    <h5>INFORMATION</h5>
                    <a href="#">Press & events</a>
                    <a href="#">Sell your products</a>
                    <a href="#">Jobs</a>
                    <div class="social-icons mt-3">
                        <h5>SOCIAL MEDIA</h5>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sample data
        const items = [
            { id: 1, name: 'Item #1', price: 'PHP 1,000.00', quantity: 23, sales: 18, type: 'Tools', description: 'Lorem ipsum dolor sit amet', image: null },
            { id: 3, name: 'Item #3', price: 'PHP 1,000.00', quantity: 23, sales: 18, type: 'Materials', description: 'Lorem ipsum dolor sit amet', image: null },
            { id: 4, name: 'Item #4', price: 'PHP 1,000.00', quantity: 23, sales: 18, type: 'Safety Gear', description: 'Lorem ipsum dolor sit amet', image: null },
            { id: 19, name: 'Item #19', price: 'PHP 1,000.00', quantity: 23, sales: 18, type: 'Essentials', description: 'Lorem ipsum dolor sit amet', image: null },
            { id: 20, name: 'Item #20', price: 'PHP 1,000.00', quantity: 23, sales: 18, type: 'Tools', description: 'Lorem ipsum dolor sit amet', image: null },
            { id: 24, name: 'Item #24', price: 'PHP 1,000.00', quantity: 23, sales: 18, type: 'Materials', description: 'Lorem ipsum dolor sit amet', image: null },
            { id: 35, name: 'Item #35', price: 'PHP 1,000.00', quantity: 23, sales: 18, type: 'Safety Gear', description: 'Lorem ipsum dolor sit amet', image: null },
            { id: 41, name: 'Item #41', price: 'PHP 1,000.00', quantity: 23, sales: 18, type: 'Essentials', description: 'Lorem ipsum dolor sit amet', image: null }
        ];

        const gridItems = [
            { name: 'Hollowblocks', price: 'PHP 1,000.00', stock: 23, image: null },
            { name: 'Hammer', price: 'Php 150.00', stock: 23, image: null },
            { name: 'Iron Nails', price: 'Php 1,000.00', stock: 23, image: null },
            { name: 'Cement', price: 'Php 1,000.00', stock: 23, image: null },
            { name: 'Rebar', price: 'Php 1,000.00', stock: 23, image: null },
            { name: 'PVC Pipesh', price: 'Php 1,000.00', stock: 23, image: null },
            { name: 'Chokdee Floor Tile', price: 'Php 1,000.00', stock: 23, image: null },
            { name: 'Boysen Enamel', price: 'Php 1,000.00', stock: 23, image: null },
            { name: 'Bosny Spray Paint', price: 'Php 1,000.00', stock: 23, image: null },
            { name: 'Rockwool Insulation', price: 'Php 1,000.00', stock: 23, image: null },
            { name: 'Energy-saving LED', price: 'Php 1,000.00', stock: 23, image: null },
            { name: 'Electrical Wire', price: 'Php 1,000.00', stock: 23, image: null }
        ];

        // Load table data
        function loadTableData() {
            const tbody = document.getElementById('inventoryTableBody');
            tbody.innerHTML = '';
            items.forEach(item => {
                tbody.innerHTML += `
                    <tr>
                        <td>${item.id}</td>
                        <td>${item.name}</td>
                        <td><div class="item-image"><i class="fas fa-image text-secondary"></i></div></td>
                        <td>${item.price}</td>
                        <td>${item.quantity}</td>
                        <td>${item.sales}</td>
                        <td>${item.type}</td>
                        <td>${item.description}</td>
                        <td>
                            <div class="action-icons">
                                <i class="fas fa-pen"></i>
                                <i class="fas fa-trash-alt"></i>
                            </div>
                        </td>
                    </tr>
                `;
            });
        }

        // Load grid data
        function loadGridData() {
            const grid = document.getElementById('inventoryGridView');
            grid.innerHTML = '';
            gridItems.forEach(item => {
                grid.innerHTML += `
                    <div class="grid-item">
                        <div class="grid-item-image">
                            <i class="fas fa-image fa-3x text-secondary"></i>
                        </div>
                        <h5>${item.name}</h5>
                        <p><strong>${item.price}</strong></p>
                        <p>In stock: ${item.stock}</p>
                    </div>
                `;
            });
        }

        // Switch tabs
        function switchTab(tab) {
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
        }

        // Switch view
        function switchView(view) {
            const listView = document.getElementById('listView');
            const gridView = document.getElementById('gridView');
            const listBtn = document.getElementById('listViewBtn');
            const gridBtn = document.getElementById('gridViewBtn');

            if (view === 'list') {
                listView.style.display = 'block';
                gridView.style.display = 'none';
                listBtn.classList.add('active');
                gridBtn.classList.remove('active');
            } else {
                listView.style.display = 'none';
                gridView.style.display = 'block';
                listBtn.classList.remove('active');
                gridBtn.classList.add('active');
            }
        }

        // Open add modal
        function openAddModal() {
            const modal = new bootstrap.Modal(document.getElementById('addItemModal'));
            modal.show();
        }

        // Bulk upload
        function bulkUpload() {
            alert('Bulk upload functionality');
        }

        // Filter items
        function filterItems() {
            alert('Filter functionality');
        }

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            // Implement search logic here
        });

        // Initialize
        loadTableData();
        loadGridData();
    </script>
</body>
</html>