// Sample inventory data
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

// Render table with current items
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

// Update pagination information text
function updatePaginationInfo() {
    const startIndex = (currentPage - 1) * itemsPerPage + 1;
    const endIndex = Math.min(startIndex + itemsPerPage - 1, filteredItems.length);
    document.getElementById('paginationInfo').textContent = 
        `Showing ${startIndex} to ${endIndex} out of ${filteredItems.length} records`;
}

// Render pagination controls
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

// Change to specific page
function changePage(page) {
    const totalPages = Math.ceil(filteredItems.length / itemsPerPage);
    if (page >= 1 && page <= totalPages) {
        currentPage = page;
        renderTable();
    }
}

// Change items per page
function changePageSize(size) {
    itemsPerPage = parseInt(size);
    currentPage = 1;
    renderTable();
}

// Sort table by column
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

// Search items by name or ID
function searchItems() {
    const searchTerm = document.getElementById('headerSearch').value.toLowerCase();
    filteredItems = items.filter(item => 
        item.name.toLowerCase().includes(searchTerm) || 
        item.id.toString().includes(searchTerm)
    );
    currentPage = 1;
    renderTable();
}

// Add event listener for search input
document.getElementById('headerSearch').addEventListener('input', searchItems);

// Switch between tabs
function switchTab(tab) {
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
}

// Switch between list and grid view
function switchView(view) {
    const buttons = document.querySelectorAll('.view-toggle button');
    buttons.forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
}

// Open add item modal