<?php
include 'includes/db_config.php';

// Check if user is logged in
$is_logged_in = isset($_SESSION['user_id']);

// Fetch menu items from database
$stmt = $pdo->prepare("SELECT * FROM menu ORDER BY category, name");
$stmt->execute();
$menu_items_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Convert to the expected format (for backward compatibility)
$menu_items = [];
foreach ($menu_items_db as $item) {
    $menu_items[] = [
        'id' => $item['id'],
        'name' => $item['name'],
        'category' => $item['category'],
        'price' => (float)$item['price'],
        'original_price' => $item['original_price'] ? (float)$item['original_price'] : null,
        'description' => $item['description'],
        'image' => $item['image'],
        'rating' => (float)$item['rating'],
        'reviews' => (int)$item['reviews'],
        'calories' => (int)$item['calories'],
        'is_bestseller' => (bool)$item['is_bestseller'],
        'is_new' => (bool)$item['is_new'],
        'spice_level' => (int)$item['spice_level']
    ];
}

// If no items in DB, use default hardcoded items (for initial setup)
if (empty($menu_items)) {
    $menu_items = [
        // Veg Tiffins
        [
            'id' => 1,
            'name' => 'Classic Veg Thali',
            'category' => 'veg',
            'price' => 120,
            'original_price' => 150,
            'description' => '2 Rotis, Rice, Dal, Sabzi, Salad, Pickle',
            'image' => 'https://images.unsplash.com/photo-1546833999-b9f581a1996d?w=400',
            'rating' => 4.8,
            'reviews' => 245,
            'calories' => 650,
            'is_bestseller' => true,
            'is_new' => false,
            'spice_level' => 2
        ],
        [
            'id' => 2,
            'name' => 'Paneer Special Thali',
            'category' => 'veg',
            'price' => 150,
            'original_price' => 180,
            'description' => 'Paneer Curry, 3 Rotis, Rice, Dal, Raita',
            'image' => 'https://images.unsplash.com/photo-1585937421612-70a008356fbe?w=400',
            'rating' => 4.9,
            'reviews' => 189,
            'calories' => 780,
            'is_bestseller' => true,
            'is_new' => false,
            'spice_level' => 2
        ],
        [
            'id' => 3,
            'name' => 'South Indian Combo',
            'category' => 'veg',
            'price' => 130,
            'original_price' => 160,
            'description' => '2 Dosa, Sambar, Coconut Chutney, Vada',
            'image' => 'https://images.unsplash.com/photo-1630383249896-424e482df921?w=400',
            'rating' => 4.7,
            'reviews' => 156,
            'calories' => 550,
            'is_bestseller' => false,
            'is_new' => true,
            'spice_level' => 1
        ],
        [
            'id' => 4,
            'name' => 'Gujarati Thali',
            'category' => 'veg',
            'price' => 140,
            'original_price' => 170,
            'description' => 'Dhokla, Thepla, Kadhi, Rice, Rotla, Sweet',
            'image' => 'https://images.unsplash.com/photo-1567337710282-00832b415979?w=400',
            'rating' => 4.6,
            'reviews' => 98,
            'calories' => 700,
            'is_bestseller' => false,
            'is_new' => false,
            'spice_level' => 1
        ],
        // Non-Veg Tiffins
        [
            'id' => 5,
            'name' => 'Chicken Thali',
            'category' => 'nonveg',
            'price' => 180,
            'original_price' => 220,
            'description' => 'Chicken Curry, 3 Rotis, Rice, Dal, Salad',
            'image' => 'https://images.unsplash.com/photo-1603894584373-5ac82b2ae398?w=400',
            'rating' => 4.9,
            'reviews' => 312,
            'calories' => 850,
            'is_bestseller' => true,
            'is_new' => false,
            'spice_level' => 3
        ],
        [
            'id' => 6,
            'name' => 'Mutton Special',
            'category' => 'nonveg',
            'price' => 220,
            'original_price' => 280,
            'description' => 'Mutton Rogan Josh, 3 Rotis, Biryani Rice',
            'image' => 'https://images.unsplash.com/photo-1574653853027-5382a3d23a15?w=400',
            'rating' => 4.8,
            'reviews' => 178,
            'calories' => 950,
            'is_bestseller' => false,
            'is_new' => false,
            'spice_level' => 3
        ],
        [
            'id' => 7,
            'name' => 'Fish Curry Meal',
            'category' => 'nonveg',
            'price' => 200,
            'original_price' => 250,
            'description' => 'Fish Curry, Rice, Fried Fish, Salad',
            'image' => 'https://images.unsplash.com/photo-1467003909585-2f8a72700288?w=400',
            'rating' => 4.7,
            'reviews' => 134,
            'calories' => 720,
            'is_bestseller' => false,
            'is_new' => true,
            'spice_level' => 2
        ],
        [
            'id' => 8,
            'name' => 'Egg Curry Thali',
            'category' => 'nonveg',
            'price' => 140,
            'original_price' => 170,
            'description' => 'Egg Curry, 3 Rotis, Rice, Dal, Boiled Egg',
            'image' => 'https://images.unsplash.com/photo-1626500155537-99dafd5eda8d?w=400',
            'rating' => 4.5,
            'reviews' => 89,
            'calories' => 680,
            'is_bestseller' => false,
            'is_new' => false,
            'spice_level' => 2
        ],
        // Diet/Healthy Options
        [
            'id' => 9,
            'name' => 'Keto Meal Box',
            'category' => 'diet',
            'price' => 200,
            'original_price' => 250,
            'description' => 'Grilled Chicken, Salad, Eggs, Avocado',
            'image' => 'https://images.unsplash.com/photo-1490645935967-10de6ba17061?w=400',
            'rating' => 4.8,
            'reviews' => 167,
            'calories' => 450,
            'is_bestseller' => true,
            'is_new' => false,
            'spice_level' => 1
        ],
        [
            'id' => 10,
            'name' => 'Protein Power Bowl',
            'category' => 'diet',
            'price' => 180,
            'original_price' => 220,
            'description' => 'Quinoa, Chickpeas, Paneer, Vegetables',
            'image' => 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=400',
            'rating' => 4.7,
            'reviews' => 145,
            'calories' => 520,
            'is_bestseller' => false,
            'is_new' => true,
            'spice_level' => 1
        ],
        [
            'id' => 11,
            'name' => 'Low Carb Delight',
            'category' => 'diet',
            'price' => 170,
            'original_price' => 200,
            'description' => 'Grilled Veggies, Soup, Salad, Sprouts',
            'image' => 'https://images.unsplash.com/photo-1540189549336-e6e99c3679fe?w=400',
            'rating' => 4.6,
            'reviews' => 112,
            'calories' => 380,
            'is_bestseller' => false,
            'is_new' => false,
            'spice_level' => 1
        ],
        [
            'id' => 12,
            'name' => 'Diabetic Friendly',
            'category' => 'diet',
            'price' => 160,
            'original_price' => 190,
            'description' => 'Multigrain Roti, Low GI Veggies, Dal',
            'image' => 'https://images.unsplash.com/photo-1547592180-85f173990554?w=400',
            'rating' => 4.5,
            'reviews' => 78,
            'calories' => 420,
            'is_bestseller' => false,
            'is_new' => false,
            'spice_level' => 1
        ],
        // Subscription Plans
        [
            'id' => 13,
            'name' => 'Weekly Veg Plan',
            'category' => 'subscription',
            'price' => 699,
            'original_price' => 900,
            'description' => '7 Days Lunch - Different menu daily',
            'image' => 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?w=400',
            'rating' => 4.9,
            'reviews' => 456,
            'calories' => 600,
            'is_bestseller' => true,
            'is_new' => false,
            'spice_level' => 2
        ],
        [
            'id' => 14,
            'name' => 'Monthly Premium',
            'category' => 'subscription',
            'price' => 2499,
            'original_price' => 3500,
            'description' => '30 Days Lunch + Dinner - Premium meals',
            'image' => 'https://images.unsplash.com/photo-1498837167922-ddd27525d352?w=400',
            'rating' => 4.8,
            'reviews' => 289,
            'calories' => 700,
            'is_bestseller' => true,
            'is_new' => false,
            'spice_level' => 2
        ],
        [
            'id' => 15,
            'name' => 'Corporate Lunch',
            'category' => 'subscription',
            'price' => 3999,
            'original_price' => 5000,
            'description' => '30 Days - 5 People - Office Lunch',
            'image' => 'https://images.unsplash.com/photo-1414235077428-338989a2e8c0?w=400',
            'rating' => 4.7,
            'reviews' => 167,
            'calories' => 650,
            'is_bestseller' => false,
            'is_new' => true,
            'spice_level' => 2
        ],
        [
            'id' => 16,
            'name' => 'Family Pack Weekly',
            'category' => 'subscription',
            'price' => 1999,
            'original_price' => 2500,
            'description' => '7 Days - 4 People - Full Family Meals',
            'image' => 'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?w=400',
            'rating' => 4.9,
            'reviews' => 234,
            'calories' => 700,
            'is_bestseller' => false,
            'is_new' => false,
            'spice_level' => 2
        ]
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
</head>
<body class="order-page">
    <!-- Header -->
    <header class="header glass-effect">
        <div class="container">
            <a href="index.php" class="logo">
                <i class="fas fa-utensils logo-icon"></i>
                <span class="logo-text">Delicious<span class="highlight">Dispatchers</span></span>
            </a>
            <nav class="nav-menu" id="navMenu">
                <a href="index.php" class="nav-link">Home</a>
                <a href="order.php" class="nav-link active">Order Now</a>
                <a href="index.php#features" class="nav-link">Features</a>
                <a href="index.php#pricing" class="nav-link">Pricing</a>
                <a href="index.php#contact" class="nav-link">Contact</a>
            </nav>
            <div class="header-actions">
                <div class="cart-btn" id="cartToggle">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="cart-count" id="cartCount">0</span>
                </div>
                <?php if($is_logged_in): ?>
                    <a href="dashboard.php" class="btn btn-outline">Dashboard</a>
                    <a href="logout.php" class="btn btn-primary">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-outline">Login</a>
                    <a href="register.php" class="btn btn-primary">Register</a>
                <?php endif; ?>
            </div>
            <div class="hamburger" id="hamburger">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </header>

    <!-- Order Hero Section -->
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
                            <input type="range" min="0" max="500" value="500" id="priceRange">
                            <div class="price-labels">
                                <span>₹0</span>
                                <span id="priceValue">₹500</span>
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
                        <?php foreach($menu_items as $item): ?>
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
                                    <?php if($item['is_bestseller']): ?>
                                    <span class="badge bestseller"><i class="fas fa-fire"></i> Bestseller</span>
                                    <?php endif; ?>
                                    <?php if($item['is_new']): ?>
                                    <span class="badge new"><i class="fas fa-sparkles"></i> New</span>
                                    <?php endif; ?>
                                    <?php 
                                    $discount = round((($item['original_price'] - $item['price']) / $item['original_price']) * 100);
                                    if($discount > 0):
                                    ?>
                                    <span class="badge discount"><?php echo $discount; ?>% OFF</span>
                                    <?php endif; ?>
                                </div>

                                <!-- Category Badge -->
                                <span class="category-badge <?php echo $item['category']; ?>">
                                    <?php 
                                    if($item['category'] == 'veg') echo '<i class="fas fa-leaf"></i> Veg';
                                    elseif($item['category'] == 'nonveg') echo '<i class="fas fa-drumstick-bite"></i> Non-Veg';
                                    elseif($item['category'] == 'diet') echo '<i class="fas fa-heartbeat"></i> Diet';
                                    else echo '<i class="fas fa-calendar-check"></i> Plan';
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
                                        <?php for($i = 0; $i < $item['spice_level']; $i++): ?>
                                        <i class="fas fa-pepper-hot"></i>
                                        <?php endfor; ?>
                                    </span>
                                </div>

                                <div class="card-footer">
                                    <div class="card-price">
                                        <span class="current-price">₹<?php echo $item['price']; ?></span>
                                        <?php if($item['original_price'] > $item['price']): ?>
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

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <!-- Menu Items Data for JavaScript -->
    <script>
        const menuItemsData = <?php echo json_encode($menu_items); ?>;
    </script>
    <script src="js/script.js"></script>
    <script src="js/order.js"></script>
</body>
</html>