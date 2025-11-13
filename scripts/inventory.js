// Search functionality
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM Content Loaded - Starting initialization');
    
    // Auto-search functionality
    const searchInput = document.getElementById('headerSearch');
    if (searchInput) {
        console.log('Search input found, setting up event listener');
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.form.submit();
            }, 500);
        });
    } else {
        console.log('Search input NOT found');
    }

    // Initialize modal events
    console.log('Initializing modal events');
    initializeModalEvents();
    
    // Pre-initialize file upload for hidden modal
    console.log('Pre-initializing file upload');
    initializeFileUpload();
});

// Sort table function
function sortTable(column) {
    console.log('Sorting table by:', column);
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
// Update the openAddModal function to ensure proper initialization
function openAddModal() {
    console.log('Opening add modal');
    const modal = document.getElementById('addItemModal');
    if (modal) {
        modal.classList.add('show');
        console.log('Modal show class added');
        
        // Force a reflow to ensure the modal is visible before initializing file upload
        void modal.offsetWidth;
        
        // Re-initialize file upload with the modal now visible
        console.log('Re-initializing file upload for visible modal');
        initializeFileUpload();
    } else {
        console.error('Add item modal not found!');
    }
}

function closeModal() {
    console.log('Closing modal');
    const modal = document.getElementById('addItemModal');
    if (modal) {
        modal.classList.remove('show');
    }
    // Reset form when closing modal
    resetAddForm();
}

function resetAddForm() {
    console.log('Resetting add form');
    const form = document.getElementById('addItemForm');
    if (form) {
        form.reset();
        console.log('Form reset completed');
    }
    removeImage(); // Reset file upload area
}

function initializeModalEvents() {
    console.log('initializeModalEvents called');
    const addItemForm = document.getElementById('addItemForm');
    if (addItemForm) {
        console.log('Add item form found, setting up submit handler');
        addItemForm.addEventListener('submit', function(e) {
            console.log('Form submit triggered');
            const price = this.querySelector('input[name="price"]');
            const quantity = this.querySelector('input[name="stock_quantity"]');
            const fileInput = document.getElementById('product_image');
            const imageUrlInput = this.querySelector('input[name="image_url"]');
            
            console.log('Price element:', price);
            console.log('Quantity element:', quantity);
            console.log('File input element:', fileInput);
            console.log('Image URL input element:', imageUrlInput);
            
            // Validate price and quantity
            if (parseFloat(price.value) < 0) {
                console.log('Price validation failed');
                e.preventDefault();
                alert('Price cannot be negative');
                price.focus();
                return;
            }
            
            if (parseInt(quantity.value) < 0) {
                console.log('Quantity validation failed');
                e.preventDefault();
                alert('Quantity cannot be negative');
                quantity.focus();
                return;
            }

            // Validate that either file or URL is provided, not both
            const hasFile = fileInput && fileInput.files && fileInput.files.length > 0;
            const hasUrl = imageUrlInput && imageUrlInput.value.trim() !== '';
            
            console.log('File validation - hasFile:', hasFile, 'hasUrl:', hasUrl);
            
            if (hasFile && hasUrl) {
                console.log('Both file and URL provided - validation failed');
                e.preventDefault();
                alert('Please provide either an image file or an image URL, not both.');
                return;
            }
            
            console.log('Form validation passed, submitting...');
        });
    } else {
        console.error('Add item form NOT found in initializeModalEvents!');
    }
}

function initializeFileUpload() {
    console.log('initializeFileUpload called');
    const fileUploadArea = document.getElementById('fileUploadArea');
    const fileInput = document.getElementById('product_image');
    const filePreview = document.getElementById('filePreview');
    const previewImage = document.getElementById('previewImage');
    const imageUrlInput = document.querySelector('input[name="image_url"]');

    console.log('File upload elements:', {
        fileUploadArea: fileUploadArea,
        fileInput: fileInput,
        filePreview: filePreview,
        previewImage: previewImage,
        imageUrlInput: imageUrlInput
    });

    if (!fileUploadArea) {
        console.error('File upload area not found!');
        return;
    }

    if (!fileInput) {
        console.error('File input not found!');
        return;
    }

    // Create a new file input with better positioning
    const newFileInput = document.createElement('input');
    newFileInput.type = 'file';
    newFileInput.id = 'product_image';
    newFileInput.name = 'product_image';
    newFileInput.accept = '.png,.jpg,.jpeg,.webp,.svg';
    newFileInput.style.cssText = `
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
        z-index: 10;
    `;

    // Replace the old file input
    const oldFileInput = document.getElementById('product_image');
    if (oldFileInput) {
        oldFileInput.parentNode.removeChild(oldFileInput);
    }

    // Add the new file input to the upload area
    fileUploadArea.style.position = 'relative';
    fileUploadArea.appendChild(newFileInput);

    // Get the updated reference
    const updatedFileInput = document.getElementById('product_image');

    // Ensure proper visibility
    if (fileUploadArea) {
        fileUploadArea.style.display = 'block';
    }
    if (filePreview) {
        filePreview.style.display = 'none';
    }

    // File input change event
    updatedFileInput.addEventListener('change', (e) => {
        console.log('File input change event fired');
        if (e.target.files && e.target.files.length) {
            console.log('File selected:', e.target.files[0].name);
            handleFileSelect(e.target.files[0], updatedFileInput, imageUrlInput);
        } else {
            console.log('No files selected');
        }
    });

    // Drag and drop functionality
    fileUploadArea.addEventListener('dragover', (e) => {
        console.log('File dragged over upload area');
        e.preventDefault();
        e.stopPropagation();
        fileUploadArea.classList.add('dragover');
    });

    fileUploadArea.addEventListener('dragleave', (e) => {
        console.log('File dragged out of upload area');
        e.preventDefault();
        e.stopPropagation();
        fileUploadArea.classList.remove('dragover');
    });

    fileUploadArea.addEventListener('drop', (e) => {
        console.log('File dropped on upload area');
        e.preventDefault();
        e.stopPropagation();
        fileUploadArea.classList.remove('dragover');
        
        if (e.dataTransfer.files.length) {
            console.log('Files detected in drop:', e.dataTransfer.files.length);
            updatedFileInput.files = e.dataTransfer.files;
            
            // Trigger change event manually for drop
            const event = new Event('change', { bubbles: true });
            updatedFileInput.dispatchEvent(event);
        }
    });

    console.log('File upload initialization completed - using overlay method');
    console.log('File input position and style:', {
        position: updatedFileInput.style.position,
        opacity: updatedFileInput.style.opacity,
        width: updatedFileInput.style.width,
        height: updatedFileInput.style.height,
        zIndex: updatedFileInput.style.zIndex
    });
}

function handleFileSelect(file, fileInput, imageUrlInput) {
    console.log('handleFileSelect called with file:', file ? file.name : 'null');
    
    const filePreview = document.getElementById('filePreview');
    const previewImage = document.getElementById('previewImage');
    const fileUploadArea = document.getElementById('fileUploadArea');

    console.log('Preview elements:', {
        filePreview: filePreview,
        previewImage: previewImage,
        fileUploadArea: fileUploadArea
    });

    if (!file) {
        console.error('No file provided to handleFileSelect');
        return;
    }

    // Validate file type
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/svg+xml'];
    console.log('File type:', file.type, 'Size:', file.size);
    
    if (!allowedTypes.includes(file.type)) {
        console.log('Invalid file type detected:', file.type);
        alert('Invalid file type. Please select a PNG, JPG, JPEG, WebP, or SVG file.');
        return;
    }
    console.log('File type validation passed');

    // Validate file size (5MB)
    if (file.size > 5 * 1024 * 1024) {
        console.log('File size too large:', file.size);
        alert('File size too large. Maximum size is 5MB.');
        return;
    }
    console.log('File size validation passed');

    // Clear URL input when file is selected
    if (imageUrlInput) {
        imageUrlInput.value = '';
        console.log('Cleared image URL input');
    }

    // Show preview
    console.log('Creating file reader for preview');
    const reader = new FileReader();
    reader.onload = (e) => {
        console.log('File reader loaded, setting preview image');
        previewImage.src = e.target.result;
        filePreview.style.display = 'block';
        fileUploadArea.style.display = 'none';
        console.log('Preview shown, upload area hidden');
    };
    reader.onerror = (e) => {
        console.error('File reader error:', e);
    };
    reader.readAsDataURL(file);
    console.log('File reader started reading file');
}

function removeImage() {
    console.log('removeImage called');
    const fileInput = document.getElementById('product_image');
    const filePreview = document.getElementById('filePreview');
    const fileUploadArea = document.getElementById('fileUploadArea');
    const imageUrlInput = document.querySelector('input[name="image_url"]');

    console.log('Elements for removal:', {
        fileInput: fileInput,
        filePreview: filePreview,
        fileUploadArea: fileUploadArea,
        imageUrlInput: imageUrlInput
    });

    if (fileInput) {
        console.log('Removing file input');
        fileInput.remove();
    }

    if (filePreview) {
        filePreview.style.display = 'none';
        console.log('File preview hidden');
    }

    if (fileUploadArea) {
        fileUploadArea.style.display = 'block';
        console.log('File upload area shown');
    }

    if (imageUrlInput) {
        imageUrlInput.value = '';
        console.log('Image URL input cleared');
    }
    
    // Re-initialize file upload after removal
    setTimeout(() => {
        console.log('Re-initializing file upload after removal');
        initializeFileUpload();
    }, 50);
}

function editItem(productId) {
    console.log('Edit item clicked:', productId);
    
    // Fetch item data
    fetch('scripts/get_item.php?product_id=' + productId)
        .then(response => {
            return response.text().then(text => {
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error('Invalid JSON response:', text);
                    throw new Error('Server returned invalid JSON');
                }
            });
        })
        .then(data => {
            if (data.success) {
                populateEditForm(data.item);
                openEditModal();
            } else {
                alert('Error loading item data: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading item data: ' + error.message);
        });
}

function deleteItem(productId) {
    console.log('Delete item clicked:', productId);
    if (confirm('Are you sure you want to delete this item?')) {
        fetch('scripts/delete_item.php', {
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
    console.log('Logout clicked');
    if (confirm('Are you sure you want to logout?')) {
        window.location.href = 'logout.php';
    }
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('addItemModal');
    if (event.target === modal) {
        console.log('Clicked outside modal, closing');
        closeModal();
    }
}

function editItem(productId) {
    console.log('Edit item clicked:', productId);
    
    // Fetch item data
    fetch('scripts/get_item.php?product_id=' + productId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateEditForm(data.item);
                openEditModal();
            } else {
                alert('Error loading item data: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading item data');
        });
}

function populateEditForm(item) {
    document.getElementById('edit_product_id').value = item.product_id;
    document.getElementById('edit_product_name').value = item.product_name;
    document.getElementById('edit_description').value = item.description;
    document.getElementById('edit_price').value = item.price;
    document.getElementById('edit_stock_quantity').value = item.stock_quantity;
    document.getElementById('edit_category_id').value = item.category_id;
    document.getElementById('edit_image_url').value = item.image_url || '';

    // Handle image preview
    const filePreview = document.getElementById('editFilePreview');
    const previewImage = document.getElementById('editPreviewImage');
    const fileUploadArea = document.getElementById('editFileUploadArea');
    
    if (item.image_url) {
        previewImage.src = item.image_url;
        filePreview.style.display = 'block';
        fileUploadArea.style.display = 'none';
    } else {
        filePreview.style.display = 'none';
        fileUploadArea.style.display = 'block';
    }
}

function openEditModal() {
    console.log('Opening edit modal');
    const modal = document.getElementById('editItemModal');
    if (modal) {
        modal.classList.add('show');
        // Initialize file upload for edit modal
        setTimeout(() => {
            initializeEditFileUpload();
        }, 50);
    }
}

function closeEditModal() {
    console.log('Closing edit modal');
    const modal = document.getElementById('editItemModal');
    if (modal) {
        modal.classList.remove('show');
    }
}

function initializeEditFileUpload() {
    console.log('initializeEditFileUpload called');
    const fileUploadArea = document.getElementById('editFileUploadArea');
    const fileInput = document.getElementById('edit_product_image');
    const filePreview = document.getElementById('editFilePreview');
    const previewImage = document.getElementById('editPreviewImage');
    const imageUrlInput = document.getElementById('edit_image_url');

    if (!fileUploadArea || !fileInput) {
        console.error('Edit file upload elements not found!');
        return;
    }

    // Create a new file input
    const newFileInput = document.createElement('input');
    newFileInput.type = 'file';
    newFileInput.id = 'edit_product_image';
    newFileInput.name = 'product_image';
    newFileInput.accept = '.png,.jpg,.jpeg,.webp,.svg';
    newFileInput.style.cssText = `
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
        z-index: 10;
    `;

    // Replace the old file input
    const oldFileInput = document.getElementById('edit_product_image');
    if (oldFileInput) {
        oldFileInput.parentNode.removeChild(oldFileInput);
    }

    fileUploadArea.style.position = 'relative';
    fileUploadArea.appendChild(newFileInput);

    const updatedFileInput = document.getElementById('edit_product_image');

    // File input change event
    updatedFileInput.addEventListener('change', (e) => {
        console.log('Edit file input change event fired');
        if (e.target.files && e.target.files.length) {
            handleEditFileSelect(e.target.files[0], updatedFileInput, imageUrlInput);
        }
    });

    // Drag and drop functionality
    fileUploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        e.stopPropagation();
        fileUploadArea.classList.add('dragover');
    });

    fileUploadArea.addEventListener('dragleave', (e) => {
        e.preventDefault();
        e.stopPropagation();
        fileUploadArea.classList.remove('dragover');
    });

    fileUploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        e.stopPropagation();
        fileUploadArea.classList.remove('dragover');
        
        if (e.dataTransfer.files.length) {
            updatedFileInput.files = e.dataTransfer.files;
            const event = new Event('change', { bubbles: true });
            updatedFileInput.dispatchEvent(event);
        }
    });
}

function handleEditFileSelect(file, fileInput, imageUrlInput) {
    const filePreview = document.getElementById('editFilePreview');
    const previewImage = document.getElementById('editPreviewImage');
    const fileUploadArea = document.getElementById('editFileUploadArea');

    if (!file) return;

    // Validate file type
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp', 'image/svg+xml'];
    if (!allowedTypes.includes(file.type)) {
        alert('Invalid file type. Please select a PNG, JPG, JPEG, WebP, or SVG file.');
        return;
    }

    // Validate file size (5MB)
    if (file.size > 5 * 1024 * 1024) {
        alert('File size too large. Maximum size is 5MB.');
        return;
    }

    // Clear URL input when file is selected
    if (imageUrlInput) {
        imageUrlInput.value = '';
    }

    // Show preview
    const reader = new FileReader();
    reader.onload = (e) => {
        previewImage.src = e.target.result;
        filePreview.style.display = 'block';
        fileUploadArea.style.display = 'none';
    };
    reader.readAsDataURL(file);
}

function removeEditImage() {
    const fileInput = document.getElementById('edit_product_image');
    const filePreview = document.getElementById('editFilePreview');
    const fileUploadArea = document.getElementById('editFileUploadArea');
    const imageUrlInput = document.getElementById('edit_image_url');

    if (fileInput) fileInput.remove();
    if (filePreview) filePreview.style.display = 'none';
    if (fileUploadArea) fileUploadArea.style.display = 'block';
    if (imageUrlInput) imageUrlInput.value = '';
    
    setTimeout(() => {
        initializeEditFileUpload();
    }, 50);
}

// Update the existing deleteItem function to use the correct endpoint
function deleteItem(productId) {
    console.log('Delete item clicked:', productId);
    if (confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
        fetch('scripts/delete_item.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'product_id=' + productId
        })
        .then(response => {
            // First, check if the response is valid JSON
            return response.text().then(text => {
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error('Invalid JSON response:', text);
                    throw new Error('Server returned invalid JSON');
                }
            });
        })
        .then(data => {
            if (data.success) {
                alert('Item deleted successfully');
                location.reload();
            } else {
                alert('Error deleting item: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting item: ' + error.message);
        });
    }
}

// Update window.onclick to handle both modals
window.onclick = function(event) {
    const addModal = document.getElementById('addItemModal');
    const editModal = document.getElementById('editItemModal');
    
    if (event.target === addModal) {
        console.log('Clicked outside add modal, closing');
        closeModal();
    }
    if (event.target === editModal) {
        console.log('Clicked outside edit modal, closing');
        closeEditModal();
    }
}

// Update keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl + N to open add modal
    if (e.ctrlKey && e.key === 'n') {
        e.preventDefault();
        console.log('Ctrl+N pressed, opening modal');
        openAddModal();
    }
    
    // Escape to close modals
    if (e.key === 'Escape') {
        console.log('Escape pressed, closing modals');
        closeModal();
        closeEditModal();
    }
});