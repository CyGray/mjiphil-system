// Search functionality
document.addEventListener('DOMContentLoaded', function() {
    // Auto-search functionality
    const searchInput = document.getElementById('headerSearch');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.form.submit();
            }, 500);
        });
    }

    // Add form validation
    const addItemForm = document.getElementById('addItemForm');
    if (addItemForm) {
        addItemForm.addEventListener('submit', function(e) {
            const price = this.querySelector('input[name="price"]');
            const quantity = this.querySelector('input[name="stock_quantity"]');
            
            if (parseFloat(price.value) < 0) {
                e.preventDefault();
                alert('Price cannot be negative');
                price.focus();
                return;
            }
            
            if (parseInt(quantity.value) < 0) {
                e.preventDefault();
                alert('Quantity cannot be negative');
                quantity.focus();
                return;
            }
        });
    }
});

// Sort table function
function sortTable(column) {
    const url = new URL(window.location.href);
    const currentSort = url.searchParams.get('sort');
    const currentOrder = url.searchParams.get('order');
    
    let newOrder = 'asc';
    if (currentSort === column && currentOrder === 'asc') {
        newOrder = 'desc';
    }
    
    url.searchParams.set('sort', column);
    url.searchParams.set('order', newOrder);
    window.location.href = url.toString();
}

// Modal functions
function openAddModal() {
    document.getElementById('addItemModal').classList.add('show');
}

function closeModal() {
    document.getElementById('addItemModal').classList.remove('show');
}

function editItem(productId) {
    if (confirm('Edit item ' + productId + '?')) {
        // You can implement edit functionality here
        // For now, just show a message
        alert('Edit functionality for item ' + productId + ' will be implemented soon.');
    }
}

function deleteItem(productId) {
    if (confirm('Are you sure you want to delete this item?')) {
        // You can implement delete functionality here
        fetch('delete_item.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'product_id=' + productId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Item deleted successfully');
                location.reload();
            } else {
                alert('Error deleting item: ' + data.message);
            }
        })
        .catch(error => {
            alert('Error deleting item: ' + error);
        });
    }
}

function logout() {
    if (confirm('Are you sure you want to logout?')) {
        window.location.href = 'logout.php';
    }
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('addItemModal');
    if (event.target === modal) {
        closeModal();
    }
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl + N to open add modal
    if (e.ctrlKey && e.key === 'n') {
        e.preventDefault();
        openAddModal();
    }
    
    // Escape to close modal
    if (e.key === 'Escape') {
        closeModal();
    }
});