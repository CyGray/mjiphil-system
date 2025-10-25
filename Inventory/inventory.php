<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory - MJI PHIL Construction</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link href="inventory.css" rel="stylesheet">
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

    <script src="inventory.js"></script>
</body>
</html>