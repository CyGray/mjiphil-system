// scripts.js
document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('addProductForm');
  const toastEl = document.getElementById('liveToast');
  const toastBody = document.getElementById('toastBody');
  const toast = new bootstrap.Toast(toastEl);

  form.addEventListener('submit', async (e) => {
    e.preventDefault();

    const name = document.getElementById('productName').value.trim();
    const price = document.getElementById('productPrice').value;
    const qty = document.getElementById('productQty').value;
    const category = document.getElementById('productCategory').value;

    if (!name || !price || !qty || !category) {
      showToast('Please fill out all fields.');
      return;
    }

    // Prepare payload
    const payload = { name, price, qty, category };

    try {
      const res = await fetch('add_product.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });

      const data = await res.json();

      if (res.ok && data.success) {
        showToast('Product added successfully.');
        form.reset();
        // refresh product list (function provided by products.js)
        if (typeof loadProducts === 'function') loadProducts();
      } else {
        showToast(data.message || 'Could not add product.');
      }
    } catch (err) {
      console.error(err);
      showToast('Network or server error.');
    }
  });

  function showToast(msg) {
    toastBody.textContent = msg;
    toast.show();
  }
});
