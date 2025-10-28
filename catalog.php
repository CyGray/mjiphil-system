<?php
    require_once 'auth_check.php';
?>

<?php
    $title = "MjiPhil Catalog";
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $title; ?></title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
        <link href="https://fonts.googleapis.com/css2?family=Bruno+Ace+SC&family=Cutive+Mono&family=Monomaniac+One&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Lilita+One&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
        <link rel="stylesheet" href="./styles/components.css">
    </head>
    
    <body>
        <?php include './menu.php'; ?>
        
        <div class="main-content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">
                        <div class="col-12">
                        <!-- Your catalog content goes here -->
                        <h1>Catalog Page</h1>
                       <!-- Catalog: search, category chips, product grid -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                          <div class="d-flex" style="gap:.5rem;">
                            <input id="catalogSearch" class="form-control form-control-sm" style="min-width:220px;" placeholder="Search products here..." />
                            <button class="btn btn-sm btn-outline-secondary" id="openFilters" title="Filters">
                              <i class="bi bi-filter"></i>
                            </button>
                          </div>
                        </div>

                        <style>
                          /* small local styles for the catalog look */
                          .chip { border-radius: .75rem; padding: .4rem .75rem; border:1px solid #e9e9e9; background:#fff; font-size:.9rem; }
                          .chip.active { background:#6b0f0f; color:#fff; border-color: #6b0f0f; }
                          .product-placeholder { height:120px; border-radius:8px; background:#f6f6f6; display:flex; align-items:center; justify-content:center; color:#bbb; }
                        </style>

                        <div class="mb-4">
                          <div class="d-flex gap-2 flex-wrap">
                            <button class="chip active" data-cat="all">All items</button>
                            <button class="chip" data-cat="tools">Tools and Equipment</button>
                            <button class="chip" data-cat="materials">Building Materials</button>
                            <button class="chip" data-cat="plumbing">Plumbing Supplies</button>
                            <button class="chip" data-cat="safety">Safety Gear</button>
                          </div>
                        </div>

                        <div class="row" id="productGrid">
                          <?php
                          // sample static products — backend will replace this later
                          $products = [
                            ['name'=>'ProBuild Premium Cement (50kg)','price'=>'₱280.00','desc'=>'High-grade cement for durable construction.','cat'=>'materials'],
                            ['name'=>'VoltMax Cordless Drill 20V','price'=>'₱3,450.00','desc'=>'Lightweight cordless drill with 2-speed settings.','cat'=>'tools'],
                            ['name'=>'Steel Rebar 16mm (Grade 60)','price'=>'₱750.00','desc'=>'Rust-resistant rebar for reinforced concrete.','cat'=>'materials'],
                            ['name'=>'BuildTuff Hollow Blocks (4x8x16 in)','price'=>'₱18.00','desc'=>'Pre-molded concrete for easy laying.','cat'=>'materials'],
                            ['name'=>'Duracore Safety Helmet — Yellow','price'=>'₱295.00','desc'=>'Impact-resistant hard hat with vents.','cat'=>'safety'],
                            ['name'=>'QuickSeal Waterproofing Paint','price'=>'₱295.00','desc'=>'Long-lasting waterproof finish for roofs and walls.','cat'=>'materials'],
                            ['name'=>'TorquePlus Angle Grinder 4” 750W','price'=>'₱295.00','desc'=>'Compact grinder for cutting and polishing.','cat'=>'tools'],
                            ['name'=>'CemX Ready-Mix Concrete (1m³)','price'=>'₱4,200.00','desc'=>'Pre-mixed concrete for ready pours.','cat'=>'materials'],
                          ];

                          foreach ($products as $p) {
                            ?>
                            <div class="col-6 col-md-4 col-lg-3 mb-4 product-card" data-name="<?php echo strtolower($p['name']); ?>" data-cat="<?php echo $p['cat']; ?>">
                              <div class="card h-100 shadow-sm">
                                <div class="card-body d-flex flex-column">
                                  <div class="product-placeholder mb-3">
                                    <!-- image placeholder (ignore actual images for now) -->
                                    <i class="bi bi-box-seam" style="font-size:28px;"></i>
                                  </div>
                                  <h6 class="card-title mb-1" style="font-size:.98rem;"><?php echo $p['name']; ?></h6>
                                  <p class="text-muted small mb-3"><?php echo $p['desc']; ?></p>
                                  <div class="mt-auto d-flex justify-content-between align-items-center">
                                    <strong class="text-dark"><?php echo $p['price']; ?></strong>
                                    <a class="btn btn-sm btn-dark" href="#">View</a>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <?php
                          }
                          ?>
                        </div>

                        <script>
                          // simple client-side search + category filter
                          (function(){
                            const search = document.getElementById('catalogSearch');
                            const chips = Array.from(document.querySelectorAll('.chip'));
                            const cards = Array.from(document.querySelectorAll('.product-card'));

                            function applyFilter() {
                              const q = (search.value||'').trim().toLowerCase();
                              const activeCat = chips.find(c=>c.classList.contains('active'))?.dataset.cat || 'all';
                              cards.forEach(card => {
                                const name = card.dataset.name || '';
                                const cat = card.dataset.cat || '';
                                const matchName = !q || name.indexOf(q) !== -1;
                                const matchCat = activeCat === 'all' || activeCat === cat;
                                card.style.display = (matchName && matchCat) ? '' : 'none';
                              });
                            }

                            search.addEventListener('input', applyFilter);
                            chips.forEach(c => {
                              c.addEventListener('click', () => {
                                chips.forEach(x=>x.classList.remove('active'));
                                c.classList.add('active');
                                applyFilter();
                              });
                            });
                          })();
                        </script>
                      </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>