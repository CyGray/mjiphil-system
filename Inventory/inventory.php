<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory - MJI PHIL Construction</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: #f8f8f8;
        }

        .header {
            background-color: white;
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .logo {
            width: 40px;
            height: 40px;
            background-color: #8b3a3a;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 0.8rem;
        }

        .company-name {
            font-weight: 600;
            color: #333;
            font-size: 0.95rem;
        }

        .header-right {
            display: flex;
            align-items: center;
            gap: 2rem;
        }

        .menu-icon {
            font-size: 1.2rem;
            cursor: pointer;
            color: #666;
        }

        .search-header {
            position: relative;
            width: 300px;
        }

        .search-header input {
            width: 100%;
            padding: 0.5rem 1rem;
            padding-left: 2.5rem;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            font-size: 0.85rem;
        }

        .search-header i {
            position: absolute;
            left: 0.8rem;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 0.9rem;
        }

        .filter-icon {
            width: 36px;
            height: 36px;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            background: white;
        }

        .main-layout {
            display: flex;
            min-height: calc(100vh - 70px);
        }

        .sidebar {
            width: 240px;
            background-color: #8b3a3a;
            color: white;
            padding: 2rem 0;
        }

        .sidebar-item {
            padding: 0.9rem 2rem;
            display: flex;
            align-items: center;
            gap: 0.8rem;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 0.9rem;
            color: rgba(255,255,255,0.8);
        }

        .sidebar-item:hover {
            background-color: rgba(255,255,255,0.1);
            color: white;
        }

        .sidebar-item.active {
            background-color: rgba(255,255,255,0.15);
            color: white;
            border-left: 4px solid white;
            padding-left: calc(2rem - 4px);
        }

        .sidebar-item i {
            width: 20px;
            text-align: center;
        }

        .logout-section {
            position: absolute;
            bottom: 2rem;
            width: 240px;
            padding: 0 2rem;
        }

        .logout-item {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            padding: 0.9rem 0;
            cursor: pointer;
            color: rgba(255,255,255,0.8);
            font-size: 0.9rem;
        }

        .logout-item:hover {
            color: white;
        }

        .content-area {
            flex: 1;
            padding: 2rem 2.5rem;
            overflow-y: auto;
        }

        .page-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 1.5rem;
        }

        .tabs {
            display: flex;
            gap: 0;
            margin-bottom: 2rem;
            border-bottom: 2px solid #e0e0e0;
        }

        .tab-btn {
            padding: 0.8rem 2rem;
            border: none;
            background: none;
            cursor: pointer;
            font-size: 0.9rem;
            color: #666;
            border-bottom: 3px solid transparent;
            margin-bottom: -2px;
            transition: all 0.3s;
        }

        .tab-btn.active {
            color: #8b3a3a;
            border-bottom-color: #8b3a3a;
            font-weight: 600;
        }

        .controls-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .controls-left {
            display: flex;
            gap: 0.8rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .controls-right {
            display: flex;
            gap: 0.8rem;
            align-items: center;
            flex-wrap: wrap;
        }

        .btn {
            padding: 0.6rem 1.2rem;
            border-radius: 6px;
            cursor: pointer;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            border: none;
            transition: all 0.3s;
            white-space: nowrap;
            font-weight: 500;
        }

        .btn-primary {
            background-color: #8b3a3a;
            color: white;
        }

        .btn-primary:hover {
            background-color: #6b2a2a;
        }

        .btn-outline {
            background: white;
            border: 1px solid #e0e0e0;
            color: #333;
        }

        .btn-outline:hover {
            background: #f8f8f8;
        }

        .view-toggle {
            display: flex;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            overflow: hidden;
            background: white;
        }

        .view-toggle button {
            padding: 0.5rem 0.9rem;
            border: none;
            background: white;
            cursor: pointer;
            color: #666;
            border-right: 1px solid #e0e0e0;
        }

        .view-toggle button:last-child {
            border-right: none;
        }

        .view-toggle button.active {
            background-color: #8b3a3a;
            color: white;
        }

        .table-container {
            background: white;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        thead {
            background-color: #f5f5f5;
        }

        thead th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            color: #333;
            font-size: 0.85rem;
            white-space: nowrap;
            cursor: pointer;
            user-select: none;
            border-bottom: 1px solid #e0e0e0;
        }

        thead th:hover {
            background-color: #ebebeb;
        }

        thead th i {
            margin-left: 0.3rem;
            font-size: 0.7rem;
            color: #999;
        }

        tbody td {
            padding: 1rem;
            border-bottom: 1px solid #f0f0f0;
            font-size: 0.85rem;
            color: #555;
        }

        tbody tr:hover {
            background-color: #fafafa;
        }

        .item-image {
            width: 40px;
            height: 40px;
            background-color: #e8e8e8;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #999;
        }

        .action-icons {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
        }

        .action-icons i {
            cursor: pointer;
            color: #999;
            font-size: 0.9rem;
            transition: color 0.3s;
        }

        .action-icons i:hover {
            color: #8b3a3a;
        }

        .pagination-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 2rem;
            padding: 0 0.5rem;
        }

        .pagination-info {
            font-size: 0.85rem;
            color: #666;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .showing-dropdown {
            border: 1px solid #e0e0e0;
            padding: 0.4rem 0.7rem;
            border-radius: 4px;
            font-size: 0.85rem;
            background: white;
        }

        .pagination {
            display: flex;
            gap: 0.3rem;
            list-style: none;
        }

        .page-link {
            padding: 0.5rem 0.8rem;
            border: 1px solid #e0e0e0;
            background: white;
            color: #333;
            text-decoration: none;
            border-radius: 4px;
            font-size: 0.85rem;
            cursor: pointer;
            transition: all 0.3s;
        }

        .page-link:hover {
            background-color: #f8f8f8;
        }

        .page-link.active {
            background-color: #8b3a3a;
            color: white;
            border-color: #8b3a3a;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal.show {
            display: flex;
        }

        .modal-content {
            background: white;
            border-radius: 12px;
            width: 90%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            padding: 1.5rem;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #999;
            padding: 0;
            width: 30px;
            height: 30px;
        }

        .modal-body {
            padding: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-label {
            display: block;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #333;
            font-size: 0.9rem;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            font-size: 0.9rem;
        }

        .form-control:focus {
            outline: none;
            border-color: #8b3a3a;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
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

        .image-upload-area i {
            font-size: 2rem;
            color: white;
        }

        .error-text {
            color: #d32f2f;
            font-size: 0.8rem;
            margin-top: 0.3rem;
        }

        @media (max-width: 1024px) {
            .sidebar {
                width: 200px;
            }
            
            .logout-section {
                width: 200px;
            }
        }

        @media (max-width: 768px) {
            .sidebar {
                display: none;
            }
            
            .controls-bar {
                flex-direction: column;
                align-items: stretch;
            }
            
            .controls-left, .controls-right {
                width: 100%;
                justify-content: space-between;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo-section">
            <div class="logo">MJI</div>
            <span class="company-name">MJIPHIL CONSTRUCTION</span>
        </div>
        <div class="header-right">
            <i class="fas fa-bars menu-icon"></i>
            <div class="search-header">
                <i class="fas fa-search"></i>
                <input type="text" placeholder="Search Products here" id="headerSearch">
            </div>
            <div class="filter-icon">
                <i class="fas fa-filter"></i>
            </div>
        </div>
    </div>

    <div class="main-layout">
        <div class="sidebar">
            <div class="sidebar-item">
                <i class="fas fa-th-large"></i>
                <span>CATALOG</span>
            </div>
            <div class="sidebar-item active">
                <i class="fas fa-box"></i>
                <span>INVENTORY</span>
            </div>
            <div class="sidebar-item">
                <i class="fas fa-lock"></i>
                <span>PRIVACY</span>
            </div>
            <div class="sidebar-item">
                <i class="fas fa-cog"></i>
                <span>SERVICES</span>
            </div>
            <div class="sidebar-item">
                <i class="fas fa-info-circle"></i>
                <span>ABOUT US</span>
            </div>
            <div class="sidebar-item">
                <i class="fas fa-file-alt"></i>
                <span>INFORMATION</span>
            </div>
            <div class="sidebar-item">
                <i class="fas fa-share-alt"></i>
                <span>SOCIAL MEDIA</span>
            </div>
            <div class="logout-section">
                <div class="logout-item">
                    <i class="fas fa-user-circle"></i>
                    <span>Logout</span>
                </div>
            </div>
        </div>

        <div class="content-area">
            <h1 class="page-title">INVENTORY</h1>

            <div class="tabs">
                <button class="tab-btn active" onclick="switchTab('all')">All</button>
                <button class="tab-btn" onclick="switchTab('brgy')">Brgy. Bata</button>
            </div>

            <div class="controls-bar">
                <div class="controls-left">
                    <button class="btn btn-outline" onclick="toggleDropdown('type')">
                        Type: All <i class="fas fa-chevron-down"></i>
                    </button>
                    <button class="btn btn-outline">
                        Monthly Sales: > 10 <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
                <div class="controls-right">
                    <button class="btn btn-primary" onclick="openAddModal()">
                        <i class="fas fa-plus"></i> Add Item
                    </button>
                    <button class="btn btn-outline" onclick="openBulkUpload()">
                        <i class="fas fa-upload"></i> Bulk Upload
                    </button>
                    <button class="btn btn-outline">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <div class="view-toggle">
                        <button class="active" onclick="switchView('list')">
                            <i class="fas fa-list"></i>
                        </button>
                        <button onclick="switchView('grid')">
                            <i class="fas fa-th"></i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th onclick="sortTable('id')">ID <i class="fas fa-sort"></i></th>
                            <th onclick="sortTable('name')">Item Name <i class="fas fa-sort"></i></th>
                            <th>Image</th>
                            <th onclick="sortTable('price')">Price <i class="fas fa-sort"></i></th>
                            <th onclick="sortTable('quantity')">Quantity <i class="fas fa-sort"></i></th>
                            <th onclick="sortTable('sales')">Monthly Sales <i class="fas fa-sort"></i></th>
                            <th onclick="sortTable('type')">Type <i class="fas fa-sort"></i></th>
                            <th>Description</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody id="inventoryTableBody">
                    </tbody>
                </table>
            </div>

            <div class="pagination-container">
                <div class="pagination-info">
                    <span>Showing</span>
                    <select class="showing-dropdown" onchange="changePageSize(this.value)">
                        <option value="10">10</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>
                <div class="pagination-info" id="paginationInfo">Showing 1 to 10 out of 10 records</div>
                <div class="pagination" id="paginationControls"></div>
            </div>
        </div>
    </div>

    <div class="modal" id="addItemModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title">Add an Item</h2>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body">
                <form id="addItemForm" onsubmit="addItem(event)">
                    <div class="form-group" style="text-align: center;">
                        <label class="form-label">Item Image</label>
                        <div class="image-upload-area" onclick="document.getElementById('imageUpload').click()">
                            <i class="fas fa-camera"></i>
                        </div>
                        <input type="file" id="imageUpload" style="display: none;" accept="image/*">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Item Name</label>
                        <input type="text" class="form-control" id="itemName" placeholder="Product name" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Price</label>
                        <input type="text" class="form-control" id="itemPrice" placeholder="Input price here" required>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">Quantity</label>
                            <input type="number" class="form-control" id="itemQuantity" placeholder="Quantity" min="0" required>
                            <div class="error-text" id="quantityError" style="display: none;">Quantity must be greater than 0</div>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Item Type</label>
                            <select class="form-control" id="itemType" required>
                                <option value="">Select Type</option>
                                <option value="Tools">Tools</option>
                                <option value="Materials">Materials</option>
                                <option value="Safety Gear">Safety Gear</option>
                                <option value="Essentials">Essentials</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Image URL (optional)</label>
                        <input type="text" class="form-control" id="itemImageUrl" placeholder="Image URL">
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; justify-content: center;">
                        Confirm
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script>
        let items = [
            { id: 1, name: 'Item #1', price: 1000, quantity: 23, sales: 18, type: 'Tools', description: 'Lorem ipsum dolor sit amet', image: null },
            { id: 2, name: 'Item #2', price: 1000, quantity: 23, sales: 18, type: 'Materials', description: 'Lorem ipsum dolor sit amet', image: null },
            { id: 3, name: 'Item #3', price: 1000, quantity: 23, sales: 18, type: 'Materials', description: 'Lorem ipsum dolor sit amet', image: null },
            { id: 4, name: 'Item #4', price: 1000, quantity: 23, sales: 18, type: 'Safety Gear', description: 'Lorem ipsum dolor sit amet', image: null },
            { id: 5, name: 'Item #5', price: 1000, quantity: 23, sales: 18, type: 'Tools', description: 'Lorem ipsum dolor sit amet', image: null },
            { id: 6, name: 'Item #6', price: 1000, quantity: 23, sales: 18, type: 'Materials', description: 'Lorem ipsum dolor sit amet', image: null },
            { id: 7, name: 'Item #7', price: 1000, quantity: 23, sales: 18, type: 'Safety Gear', description: 'Lorem ipsum dolor sit amet', image: null },
            { id: 8, name: 'Item #8', price: 1000, quantity: 23, sales: 18, type: 'Essentials', description: 'Lorem ipsum dolor sit amet', image: null },
            { id: 9, name: 'Item #9', price: 1000, quantity: 23, sales: 18, type: 'Safety Gear', description: 'Lorem ipsum dolor sit amet', image: null },
            { id: 10, name: 'Item #10', price: 1000, quantity: 23, sales: 18, type: 'Essentials', description: 'Lorem ipsum dolor sit amet', image: null }
        ];

        let filteredItems = [...items];
        let currentPage = 1;
        let itemsPerPage = 10;
        let sortColumn = null;
        let sortDirection = 'asc';

        function renderTable() {
            const tbody = document.getElementById('inventoryTableBody');
            const startIndex = (currentPage - 1) * itemsPerPage;
            const endIndex = startIndex + itemsPerPage;
            const pageItems = filteredItems.slice(startIndex, endIndex);

            tbody.innerHTML = pageItems.map(item => `
                <tr>
                    <td>${item.id}</td>
                    <td>${item.name}</td>
                    <td>
                        <div class="item-image">
                            <i class="fas fa-image"></i>
                        </div>
                    </td>
                    <td>PHP ${item.price.toFixed(2)}</td>
                    <td>${item.quantity}</td>
                    <td>${item.sales}</td>
                    <td>${item.type}</td>
                    <td>${item.description}</td>
                    <td>
                        <div class="action-icons">
                            <i class="fas fa-pen" onclick="editItem(${item.id})"></i>
                            <i class="fas fa-trash-alt" onclick="deleteItem(${item.id})"></i>
                        </div>
                    </td>
                </tr>
            `).join('');

            updatePaginationInfo();
            renderPagination();
        }

        function updatePaginationInfo() {
            const startIndex = (currentPage - 1) * itemsPerPage + 1;
            const endIndex = Math.min(startIndex + itemsPerPage - 1, filteredItems.length);
            document.getElementById('paginationInfo').textContent = 
                `Showing ${startIndex} to ${endIndex} out of ${filteredItems.length} records`;
        }

        function renderPagination() {
            const totalPages = Math.ceil(filteredItems.length / itemsPerPage);
            const paginationControls = document.getElementById('paginationControls');
            
            let html = `<a class="page-link" onclick="changePage(${currentPage - 1})" ${currentPage === 1 ? 'style="pointer-events: none; opacity: 0.5;"' : ''}>
                <i class="fas fa-chevron-left"></i>
            </a>`;

            for (let i = 1; i <= totalPages; i++) {
                if (i === 1 || i === totalPages || (i >= currentPage - 1 && i <= currentPage + 1)) {
                    html += `<a class="page-link ${i === currentPage ? 'active' : ''}" onclick="changePage(${i})">${i}</a>`;
                } else if (i === currentPage - 2 || i === currentPage + 2) {
                    html += `<span class="page-link" style="pointer-events: none;">...</span>`;
                }
            }

            html += `<a class="page-link" onclick="changePage(${currentPage + 1})" ${currentPage === totalPages ? 'style="pointer-events: none; opacity: 0.5;"' : ''}>
                <i class="fas fa-chevron-right"></i>
            </a>`;

            paginationControls.innerHTML = html;
        }

        function changePage(page) {
            const totalPages = Math.ceil(filteredItems.length / itemsPerPage);
            if (page >= 1 && page <= totalPages) {
                currentPage = page;
                renderTable();
            }
        }

        function changePageSize(size) {
            itemsPerPage = parseInt(size);
            currentPage = 1;
            renderTable();
        }

        function sortTable(column) {
            if (sortColumn === column) {
                sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
            } else {
                sortColumn = column;
                sortDirection = 'asc';
            }

            filteredItems.sort((a, b) => {
                let valA = a[column];
                let valB = b[column];

                if (typeof valA === 'string') {
                    valA = valA.toLowerCase();
                    valB = valB.toLowerCase();
                }

                if (valA < valB) return sortDirection === 'asc' ? -1 : 1;
                if (valA > valB) return sortDirection === 'asc' ? 1 : -1;
                return 0;
            });

            renderTable();
        }

        function searchItems() {
            const searchTerm = document.getElementById('headerSearch').value.toLowerCase();
            filteredItems = items.filter(item => 
                item.name.toLowerCase().includes(searchTerm) || 
                item.id.toString().includes(searchTerm)
            );
            currentPage = 1;
            renderTable();
        }

        document.getElementById('headerSearch').addEventListener('input', searchItems);

        function switchTab(tab) {
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
        }

        function switchView(view) {
            const buttons = document.querySelectorAll('.view-toggle button');
            buttons.forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
        }

        function openAddModal() {
            document.getElementById('addItemModal').classList.add('show');
            document.getElementById('addItemForm').reset();
        }

        function closeModal() {
            document.getElementById('addItemModal').classList.remove('show');
        }

        function addItem(event) {
            event.preventDefault();
            
            const name = document.getElementById('itemName').value;
            const price = parseFloat(document.getElementById('itemPrice').value);
            const quantity = parseInt(document.getElementById('itemQuantity').value);
            const type = document.getElementById('itemType').value;

            if (quantity <= 0) {
                document.getElementById('quantityError').style.display = 'block';
                return;
            }

            const newId = Math.max(...items.map(i => i.id)) + 1;
            const newItem = {
                id: newId,
                name: name,
                price: price,
                quantity: quantity,
                sales: 0,
                type: type,
                description: 'Lorem ipsum dolor sit amet',
                image: null
            };

            items.push(newItem);
            filteredItems = [...items];
            renderTable();
            closeModal();
        }

        function editItem(id) {
            const item = items.find(i => i.id === id);
            if (item) {
                document.getElementById('itemName').value = item.name;
                document.getElementById('itemPrice').value = item.price;
                document.getElementById('itemQuantity').value = item.quantity;
                document.getElementById('itemType').value = item.type;
                openAddModal();
            }
        }

        function deleteItem(id) {
            if (confirm('Are you sure you want to delete this item?')) {
                items = items.filter(item => item.id !== id);
                filteredItems = [...items];
                renderTable();
            }
        }

        function openBulkUpload() {
            alert('Bulk upload feature - Coming soon');
        }

        function toggleDropdown(type) {
            alert(`Filter by ${type} - Feature coming soon`);
        }

        window.onclick = function(event) {
            const modal = document.getElementById('addItemModal');
            if (event.target === modal) {
                closeModal();
            }
        }

        renderTable();
    </script>
</body>
</html>