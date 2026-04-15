<?php
include 'includes/db_config.php';

// Check if user is logged in
$is_logged_in = isset($_SESSION['user_id']);

// Fetch menu items from database
$stmt = $pdo->prepare('SELECT * FROM menu ORDER BY category, name');
$stmt->execute();
$menu_items_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Convert to the expected format (for backward compatibility)
$menu_items = [];
foreach ($menu_items_db as $item) {
    $menu_items[] = [
        'id' => $item['id'],
        'name' => $item['name'],
        'category' => $item['category'],
        'price' => (float) $item['price'],
        'original_price' => $item['original_price'] ? (float) $item['original_price'] : null,
        'description' => $item['description'],
        'image' => $item['image'],
        'rating' => (float) $item['rating'],
        'reviews' => (int) $item['reviews'],
        'calories' => (int) $item['calories'],
        'is_bestseller' => (bool) $item['is_bestseller'],
        'is_new' => (bool) $item['is_new'],
        'spice_level' => (int) $item['spice_level']
    ];
}

// Get category filter
$active_category = isset($_GET['category']) ? $_GET['category'] : 'all';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Now - Delicious Dispatchers</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/order.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        section {
            padding: 10px 0;
        }
    </style>
</head>
    
<body class="dashboard-page">
    <div class="animated-bg">
        <div class="floating-shapes">
            <span></span><span></span><span></span><span></span>
        </div>
    </div>

    <div class="dashboard-container">
        <!-- Sidebar -->
        <aside class="dashboard-sidebar glass-effect">
            <div class="sidebar-logo">
                <i class="fas fa-utensils"></i>
                <span>DD</span>
            </div>
            <nav class="sidebar-nav">
                <a href="user-dashboard.php" class="nav-item" data-tooltip="Dashboard">
                    <i class="fas fa-home"></i>
                </a>
                <a href="user-profile.php" class="nav-item" data-tooltip="My Profile">
                    <i class="fas fa-users"></i>
                </a>
                <a href="#" class="nav-item active" data-tooltip="Order Page">
                    <i class="fas fa-utensils"></i>
                </a>
                <a href="order-history.php" class="nav-item" data-tooltip="Order History">
                    <i class="fas fa-history"></i>
                </a>
                <a href="#" class="nav-item" data-tooltip="Cart" id="cartToggle">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="nav-badge" id="cartCount">0</span>
                </a>
            </nav>
        </aside>

    <!-- Order Hero Section -->
        <main class="dashboard-main">
            <section class="order-hero">
                <div class="container">
                    <div class="order-hero-content animate-fadeIn">
                        <h1>Order Your <span class="gradient-text">Favorite Tiffin</span></h1>
                        <p>Fresh, homemade food delivered to your doorstep. Choose from our wide variety of healthy and delicious meals.</p>
                        
                        <!-- Search Bar -->
                        <div class="search-container glass-effect">
                            <i class="fas fa-search"></i>
                            <input type="text" id="searchInput" placeholder="Search for meals, cuisines, or diet plans...">
                            <button class="search-btn btn btn-primary">Search</button>
                        </div>

                        <!-- Quick Stats -->
                        <div class="quick-stats">
                            <div class="quick-stat">
                                <i class="fas fa-utensils"></i>
                                <span>50+ Dishes</span>
                            </div>
                            <div class="quick-stat">
                                <i class="fas fa-truck"></i>
                                <span>Free Delivery</span>
                            </div>
                            <div class="quick-stat">
                                <i class="fas fa-clock"></i>
                                <span>30 Min Delivery</span>
                            </div>
                            <div class="quick-stat">
                                <i class="fas fa-star"></i>
                                <span>4.9 Rating</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

    <!-- Main Order Section -->
            <section class="order-section">
                <div class="container">
                    <div class="order-layout">
                        <!-- Sidebar Filters -->
                        <aside class="filter-sidebar glass-effect animate-fadeInLeft">
                            <div class="filter-header">
                                <h3><i class="fas fa-filter"></i> Filters</h3>
                                <button class="clear-filters" id="clearFilters">Clear All</button>
                            </div>

                            <!-- Categories -->
                            <div class="filter-group">
                                <h4>Categories</h4>
                                <div class="filter-options">
                                    <label class="filter-option <?php echo $active_category == 'all' ? 'active' : ''; ?>">
                                        <input type="radio" name="category" value="all" <?php echo $active_category == 'all' ? 'checked' : ''; ?>>
                                        <span class="filter-icon"><i class="fas fa-th-large"></i></span>
                                        <span class="filter-label">All Items</span>
                                        <span class="filter-count">16</span>
                                    </label>
                                    <label class="filter-option <?php echo $active_category == 'veg' ? 'active' : ''; ?>">
                                        <input type="radio" name="category" value="veg" <?php echo $active_category == 'veg' ? 'checked' : ''; ?>>
                                        <span class="filter-icon veg"><i class="fas fa-leaf"></i></span>
                                        <span class="filter-label">Vegetarian</span>
                                        <span class="filter-count">4</span>
                                    </label>
                                    <label class="filter-option <?php echo $active_category == 'nonveg' ? 'active' : ''; ?>">
                                        <input type="radio" name="category" value="nonveg" <?php echo $active_category == 'nonveg' ? 'checked' : ''; ?>>
                                        <span class="filter-icon nonveg"><i class="fas fa-drumstick-bite"></i></span>
                                        <span class="filter-label">Non-Vegetarian</span>
                                        <span class="filter-count">4</span>
                                    </label>
                                    <label class="filter-option <?php echo $active_category == 'diet' ? 'active' : ''; ?>">
                                        <input type="radio" name="category" value="diet" <?php echo $active_category == 'diet' ? 'checked' : ''; ?>>
                                        <span class="filter-icon diet"><i class="fas fa-heartbeat"></i></span>
                                        <span class="filter-label">Diet & Healthy</span>
                                        <span class="filter-count">4</span>
                                    </label>
                                    <label class="filter-option <?php echo $active_category == 'subscription' ? 'active' : ''; ?>">
                                        <input type="radio" name="category" value="subscription" <?php echo $active_category == 'subscription' ? 'checked' : ''; ?>>
                                        <span class="filter-icon subscription"><i class="fas fa-calendar-check"></i></span>
                                        <span class="filter-label">Subscriptions</span>
                                        <span class="filter-count">4</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Price Range -->
                            <div class="filter-group">
                                <h4>Price Range</h4>
                                <div class="price-range">
                                    <input type="range" min="0" max="50000" value="10000" id="priceRange">
                                    <div class="price-labels">
                                        <span>₹0</span>
                                        <span id="priceValue">₹10000</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Spice Level -->
                            <div class="filter-group">
                                <h4>Spice Level</h4>
                                <div class="spice-options">
                                    <label class="spice-option">
                                        <input type="checkbox" value="1">
                                        <span class="spice-level mild">
                                            <i class="fas fa-pepper-hot"></i>
                                            Mild
                                        </span>
                                    </label>
                                    <label class="spice-option">
                                        <input type="checkbox" value="2">
                                        <span class="spice-level medium">
                                            <i class="fas fa-pepper-hot"></i>
                                            <i class="fas fa-pepper-hot"></i>
                                            Medium
                                        </span>
                                    </label>
                                    <label class="spice-option">
                                        <input type="checkbox" value="3">
                                        <span class="spice-level hot">
                                            <i class="fas fa-pepper-hot"></i>
                                            <i class="fas fa-pepper-hot"></i>
                                            <i class="fas fa-pepper-hot"></i>
                                            Hot
                                        </span>
                                    </label>
                                </div>
                            </div>

                            <!-- Special Filters -->
                            <div class="filter-group">
                                <h4>Special</h4>
                                <div class="special-filters">
                                    <label class="checkbox-filter">
                                        <input type="checkbox" id="bestsellerFilter">
                                        <span class="checkmark"></span>
                                        <span><i class="fas fa-fire"></i> Bestsellers</span>
                                    </label>
                                    <label class="checkbox-filter">
                                        <input type="checkbox" id="newFilter">
                                        <span class="checkmark"></span>
                                        <span><i class="fas fa-sparkles"></i> New Arrivals</span>
                                    </label>
                                    <label class="checkbox-filter">
                                        <input type="checkbox" id="discountFilter">
                                        <span class="checkmark"></span>
                                        <span><i class="fas fa-percent"></i> On Discount</span>
                                    </label>
                                </div>
                            </div>
                        </aside>

                        <!-- Menu Grid -->
                        <main class="menu-main">
                    <!-- Category Tabs (Mobile) -->
                    <div class="category-tabs glass-effect">
                        <button class="tab-btn active" data-category="all">
                            <i class="fas fa-th-large"></i> All
                        </button>
                        <button class="tab-btn" data-category="veg">
                            <i class="fas fa-leaf"></i> Veg
                        </button>
                        <button class="tab-btn" data-category="nonveg">
                            <i class="fas fa-drumstick-bite"></i> Non-Veg
                        </button>
                        <button class="tab-btn" data-category="diet">
                            <i class="fas fa-heartbeat"></i> Diet
                        </button>
                        <button class="tab-btn" data-category="subscription">
                            <i class="fas fa-calendar-check"></i> Plans
                        </button>
                    </div>

                    <!-- Sort Options -->
                    <div class="sort-bar glass-effect">
                        <div class="results-count">
                            <span id="resultsCount">16</span> items found
                        </div>
                        <div class="sort-options">
                            <label>Sort by:</label>
                            <select id="sortSelect">
                                <option value="popular">Most Popular</option>
                                <option value="price-low">Price: Low to High</option>
                                <option value="price-high">Price: High to Low</option>
                                <option value="rating">Highest Rated</option>
                                <option value="newest">Newest First</option>
                            </select>
                        </div>
                        <div class="view-options">
                            <button class="view-btn active" data-view="grid">
                                <i class="fas fa-th"></i>
                            </button>
                            <button class="view-btn" data-view="list">
                                <i class="fas fa-list"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Menu Items Grid -->
                    <div class="menu-grid" id="menuGrid">
                        <?php foreach ($menu_items as $item): ?>
                        <div class="menu-card glass-effect animate-fadeInUp" 
                             data-category="<?php echo $item['category']; ?>"
                             data-price="<?php echo $item['price']; ?>"
                             data-rating="<?php echo $item['rating']; ?>"
                             data-spice="<?php echo $item['spice_level']; ?>"
                             data-bestseller="<?php echo $item['is_bestseller'] ? '1' : '0'; ?>"
                             data-new="<?php echo $item['is_new'] ? '1' : '0'; ?>">
                            
                            <!-- Image Container -->
                            <div class="card-image">
                                <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" loading="lazy">
                                
                                <!-- Badges -->
                                <div class="card-badges">
                                    <?php if ($item['is_bestseller']): ?>
                                    <span class="badge bestseller"><i class="fas fa-fire"></i> Bestseller</span>
                                    <?php endif; ?>
                                    <?php if ($item['is_new']): ?>
                                    <span class="badge new"><i class="fas fa-sparkles"></i> New</span>
                                    <?php endif; ?>
                                    <?php
                                    $discount = round((($item['original_price'] - $item['price']) / $item['original_price']) * 100);
                                    if ($discount > 0):
                                        ?>
                                    <span class="badge discount"><?php echo $discount; ?>% OFF</span>
                                    <?php endif; ?>
                                </div>

                                <!-- Category Badge -->
                                <span class="category-badge <?php echo $item['category']; ?>">
                                    <?php
                                    if ($item['category'] == 'veg')
                                        echo '<i class="fas fa-leaf"></i> Veg';
                                    elseif ($item['category'] == 'nonveg')
                                        echo '<i class="fas fa-drumstick-bite"></i> Non-Veg';
                                    elseif ($item['category'] == 'diet')
                                        echo '<i class="fas fa-heartbeat"></i> Diet';
                                    else
                                        echo '<i class="fas fa-calendar-check"></i> Plan';
                                    ?>
                                </span>

                                <!-- Quick Actions -->
                                <div class="quick-actions">
                                    <button class="quick-btn wishlist" title="Add to Wishlist">
                                        <i class="far fa-heart"></i>
                                    </button>
                                    <button class="quick-btn view" title="Quick View" onclick="openQuickView(<?php echo $item['id']; ?>)">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Card Content -->
                            <div class="card-content">
                                <div class="card-header">
                                    <h3 class="card-title"><?php echo $item['name']; ?></h3>
                                    <div class="card-rating">
                                        <i class="fas fa-star"></i>
                                        <span><?php echo $item['rating']; ?></span>
                                        <span class="reviews">(<?php echo $item['reviews']; ?>)</span>
                                    </div>
                                </div>

                                <p class="card-description"><?php echo $item['description']; ?></p>

                                <div class="card-meta">
                                    <span class="calories">
                                        <i class="fas fa-fire-alt"></i> <?php echo $item['calories']; ?> cal
                                    </span>
                                    <span class="spice-indicator">
                                        <?php for ($i = 0; $i < $item['spice_level']; $i++): ?>
                                        <i class="fas fa-pepper-hot"></i>
                                        <?php endfor; ?>
                                    </span>
                                </div>

                                <div class="card-footer">
                                    <div class="card-price">
                                        <span class="current-price">₹<?php echo $item['price']; ?></span>
                                        <?php if ($item['original_price'] > $item['price']): ?>
                                        <span class="original-price">₹<?php echo $item['original_price']; ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="card-actions">
                                        <div class="quantity-selector">
                                            <button class="qty-btn minus" onclick="updateQty(this, -1)">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <input type="number" value="1" min="1" max="10" class="qty-input">
                                            <button class="qty-btn plus" onclick="updateQty(this, 1)">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                        <button class="add-to-cart-btn" 
                                                onclick="addToCart(<?php echo $item['id']; ?>, '<?php echo addslashes($item['name']); ?>', <?php echo $item['price']; ?>, '<?php echo $item['image']; ?>', this)">
                                            <i class="fas fa-cart-plus"></i>
                                            <span>Add</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Load More -->
                    <div class="load-more">
                        <button class="btn btn-outline btn-lg" id="loadMoreBtn">
                            <i class="fas fa-sync-alt"></i> Load More Items
                        </button>
                    </div>
                </main>
                    </div>
                </div>
            </section>

            <!-- Cart Sidebar -->
    <div class="cart-overlay" id="cartOverlay"></div>
    <aside class="cart-sidebar glass-effect" id="cartSidebar">
        <div class="cart-header">
            <h3><i class="fas fa-shopping-cart"></i> Your Cart</h3>
            <button class="close-cart" id="closeCart">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="cart-items" id="cartItems">
            <!-- Cart items will be dynamically added here -->
            <div class="empty-cart" id="emptyCart">
                <div class="empty-cart-icon">
                    <i class="fas fa-shopping-basket"></i>
                </div>
                <h4>Your cart is empty</h4>
                <p>Add some delicious items to your cart</p>
            </div>
        </div>

        <div class="cart-summary" id="cartSummary" style="display: none;">
            <div class="summary-row">
                <span>Subtotal</span>
                <span id="subtotal">₹0</span>
            </div>
            <div class="summary-row">
                <span>Delivery Fee</span>
                <span id="deliveryFee">₹30</span>
            </div>
            <div class="summary-row">
                <span>Tax (5%)</span>
                <span id="tax">₹0</span>
            </div>
            <div class="promo-code">
                <input type="text" placeholder="Enter promo code">
                <button class="btn btn-outline">Apply</button>
            </div>
            <div class="summary-row total">
                <span>Total</span>
                <span id="total">₹0</span>
            </div>
            <button class="btn btn-primary btn-block btn-lg checkout-btn" onclick="proceedToCheckout()">
                <i class="fas fa-lock"></i> Proceed to Checkout
            </button>
            <p class="secure-text">
                <i class="fas fa-shield-alt"></i> Secure checkout powered by Razorpay
            </p>
        </div>
    </aside>

    <!-- Quick View Modal -->
    <div class="modal-overlay" id="quickViewModal">
        <div class="modal-content glass-effect">
            <button class="modal-close" onclick="closeQuickView()">
                <i class="fas fa-times"></i>
            </button>
            <div class="modal-body">
                <div class="modal-image">
                    <img src="" alt="" id="modalImage">
                </div>
                <div class="modal-details">
                    <span class="modal-category" id="modalCategory"></span>
                    <h2 id="modalTitle"></h2>
                    <div class="modal-rating" id="modalRating"></div>
                    <p class="modal-description" id="modalDescription"></p>
                    <div class="modal-meta" id="modalMeta"></div>
                    <div class="modal-price" id="modalPrice"></div>
                    <div class="modal-actions">
                        <div class="quantity-selector large">
                            <button class="qty-btn minus" onclick="updateModalQty(-1)">
                                <i class="fas fa-minus"></i>
                            </button>
                            <input type="number" value="1" min="1" max="10" class="qty-input" id="modalQty">
                            <button class="qty-btn plus" onclick="updateModalQty(1)">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                        <button class="btn btn-primary btn-lg" id="modalAddBtn">
                            <i class="fas fa-cart-plus"></i> Add to Cart
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Cart Button (Mobile) -->
    <button class="floating-cart-btn glass-effect" id="floatingCartBtn">
        <i class="fas fa-shopping-cart"></i>
        <span class="floating-cart-count" id="floatingCartCount">0</span>
        <span class="floating-cart-total" id="floatingCartTotal">₹0</span>
    </button>
        </main>
    </div>

    <!-- Menu Items Data for JavaScript -->
    <script>
        const menuItemsData = <?php echo json_encode($menu_items); ?>;
    </script>
    <script src="js/script.js"></script>
    <script src="js/order.js"></script>
</body>
</html>
