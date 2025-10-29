// products.js
async function loadProducts() {
  const grid = document.getElementById('productsGrid');
  grid.innerHTML = ''; // clear

  try {
    const res = await fetch('get_products.php');
    const data = await res.json();

    if (!Array.isArray(data)) {
      grid.innerHTML = '<div class="col-12">No products or error loading products.</div>';
      return;
    }

    if (data.length === 0) {
      grid.innerHTML = '<div class="col-12">No products yet. Add one!</div>';
      return;
    }

    // create cards
    data.forEach(prod => {
      const col = document.createElement('div');
      col.className = 'col-sm-6 col-md-4 col-lg-3';

      const card = document.createElement('div');
      card.className = 'card h-100 product-card';

      const cardBody = document.createElement('div');
      cardBody.className = 'card-body d-flex flex-column';

      const title = document.createElement('h5');
      title.className = 'card-title';
      title.textContent = prod.name;

      const cat = document.createElement('p');
      cat.className = 'mb-1 text-muted';
      cat.textContent = `Category: ${prod.category}`;

      const price = document.createElement('p');
      price.className = 'mb-1';
      price.innerHTML = `<strong>Price:</strong> ₱${parseFloat(prod.price).toFixed(2)}`;

      const qty = document.createElement('p');
      qty.className = 'mb-3';
      qty.innerHTML = `<strong>Qty:</strong> ${prod.qty}`;

      const idSmall = document.createElement('small');
      idSmall.className = 'text-muted mt-auto';
      idSmall.textContent = `ID: ${prod.id}`;

      cardBody.append(title, cat, price, qty, idSmall);
      card.appendChild(cardBody);
      col.appendChild(card);
      grid.appendChild(col);
    });
  } catch (err) {
    console.error(err);
    grid.innerHTML = '<div class="col-12">Error loading products.</div>';
  }
}

// run on load
document.addEventListener('DOMContentLoaded', () => {
  if (typeof loadProducts === 'function') loadProducts();
});
