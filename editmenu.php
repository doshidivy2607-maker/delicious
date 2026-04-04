<?php
include 'includes/db_config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Check if user is admin
$user_email = $_SESSION['user_email'];
if ($user_email !== 'doshidivy2607@gmail.com') {
    header('Location: dashboard.php');
    exit();
}

$user_name = $_SESSION['user_name'];
$admin_name = 'Divya Doshi';
$admin_email = 'doshidivy2607@gmail.com';
$admin_phone = '6359785901';

// Handle menu item addition
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_menu_item'])) {
    $name = trim($_POST['name']);
    $category = $_POST['category'];
    $price = (float)$_POST['price'];
    $original_price = !empty($_POST['original_price']) ? (float)$_POST['original_price'] : null;
    $description = trim($_POST['description']);
    $image = trim($_POST['image']);

    // Validate image URL - reject base64 data URLs
    if (!empty($image)) {
        if (strpos($image, 'data:image/') === 0) {
            $menu_error = 'Please enter a valid image URL, not upload a file. Use URLs from image hosting services like Unsplash.';
        } elseif (!filter_var($image, FILTER_VALIDATE_URL)) {
            $menu_error = 'Please enter a valid image URL.';
        }
    }

    $rating = (float)$_POST['rating'];
    $reviews = (int)$_POST['reviews'];
    $calories = (int)$_POST['calories'];
    $is_bestseller = isset($_POST['is_bestseller']) ? 1 : 0;
    $is_new = isset($_POST['is_new']) ? 1 : 0;
    $spice_level = (int)$_POST['spice_level'];

    if (empty($name) || empty($category) || $price <= 0) {
        $menu_error = 'Please fill in all required fields';
    } else {
        $stmt = $pdo->prepare("INSERT INTO menu (name, category, price, original_price, description, image, rating, reviews, calories, is_bestseller, is_new, spice_level) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if ($stmt->execute([$name, $category, $price, $original_price, $description, $image, $rating, $reviews, $calories, $is_bestseller, $is_new, $spice_level])) {
            $menu_success = "Menu item added successfully.";
        } else {
            $menu_error = 'Failed to add menu item. Please try again.';
        }
    }
}

// Handle menu item deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_menu_item'])) {
    $menu_id = $_POST['menu_id'];
    $stmt = $pdo->prepare("DELETE FROM menu WHERE id = ?");
    if ($stmt->execute([$menu_id])) {
        $menu_success = "Menu item deleted successfully.";
    } else {
        $menu_error = 'Failed to delete menu item. Please try again.';
    }
}

// Handle menu item update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_menu_item'])) {
    $menu_id = $_POST['menu_id'];
    $name = trim($_POST['name']);
    $category = $_POST['category'];
    $price = (float)$_POST['price'];
    $original_price = !empty($_POST['original_price']) ? (float)$_POST['original_price'] : null;
    $description = trim($_POST['description']);
    $image = trim($_POST['image']);
    $rating = (float)$_POST['rating'];
    $reviews = (int)$_POST['reviews'];
    $calories = (int)$_POST['calories'];
    $is_bestseller = isset($_POST['is_bestseller']) ? 1 : 0;
    $is_new = isset($_POST['is_new']) ? 1 : 0;
    $spice_level = (int)$_POST['spice_level'];

    // Validate image URL - reject base64 data URLs
    if (!empty($image)) {
        if (strpos($image, 'data:image/') === 0) {
            $menu_error = 'Please enter a valid image URL, not upload a file. Use URLs from image hosting services like Unsplash.';
        } elseif (!filter_var($image, FILTER_VALIDATE_URL)) {
            $menu_error = 'Please enter a valid image URL.';
        }
    }

    if (empty($name) || empty($category) || $price <= 0) {
        $menu_error = 'Please fill in all required fields';
    } else {
        $stmt = $pdo->prepare("UPDATE menu SET name = ?, category = ?, price = ?, original_price = ?, description = ?, image = ?, rating = ?, reviews = ?, calories = ?, is_bestseller = ?, is_new = ?, spice_level = ? WHERE id = ?");
        if ($stmt->execute([$name, $category, $price, $original_price, $description, $image, $rating, $reviews, $calories, $is_bestseller, $is_new, $spice_level, $menu_id])) {
            $menu_success = "Menu item updated successfully.";
        } else {
            $menu_error = 'Failed to update menu item. Please try again.';
        }
    }
}

// Fetch all menu items
$stmt = $pdo->prepare("SELECT * FROM menu ORDER BY category, name");
$stmt->execute();
$menu_items = $stmt->fetchAll();

// Count items by category
$category_counts = [];
foreach ($menu_items as $item) {
    $category = $item['category'];
    if (!isset($category_counts[$category])) {
        $category_counts[$category] = 0;
    }
    $category_counts[$category]++;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Management - Delicious Dispatchers</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
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
                <i class="fas fa-user-shield"></i>
                <span>Admin</span>
            </div>
            <nav class="sidebar-nav">
                <a href="dashboard.php" class="nav-item" data-tooltip="User Dashboard">
                    <i class="fas fa-tachometer-alt"></i>
                </a>
            </nav>
            <div class="sidebar-footer">
                <a href="logout.php" class="nav-item logout" data-tooltip="Logout">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="dashboard-main">
            <header class="dashboard-header glass-effect">
                <div class="header-left">
                    <h1>Menu Management 🍽️</h1>
                    <p>Add, edit, and manage menu items.</p>
                </div>
                <div class="header-right">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Search menu items...">
                    </div>
                    <button class="btn-create" onclick="showAddMenuForm()">
                        <i class="fas fa-plus"></i>
                        <span>Add Item</span>
                    </button>
                </div>
            </header>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card glass-effect animate-fadeInUp">
                    <div class="stat-icon green">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <div class="stat-info">
                        <span class="stat-label">Total Items</span>
                        <span class="stat-value"><?php echo count($menu_items); ?></span>
                        <span class="stat-change positive"><i class="fas fa-arrow-up"></i> Available</span>
                    </div>
                </div>
                <?php foreach ($category_counts as $category => $count): ?>
                <div class="stat-card glass-effect animate-fadeInUp">
                    <div class="stat-icon blue">
                        <i class="fas fa-tag"></i>
                    </div>
                    <div class="stat-info">
                        <span class="stat-label"><?php echo ucfirst($category); ?> Items</span>
                        <span class="stat-value"><?php echo $count; ?></span>
                        <span class="stat-change neutral"><i class="fas fa-circle"></i> Category</span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions glass-effect animate-fadeInUp">
                <h3>Quick Actions</h3>
                <div class="actions-grid">
                    <button type="button" class="action-btn" onclick="showAddMenuForm()">
                        <i class="fas fa-plus"></i>
                        <span>Add Menu Item</span>
                    </button>
                    <a href="users.php" class="action-btn">
                        <i class="fas fa-users-cog"></i>
                        <span>Manage Users</span>
                    </a>
                    <a href="savedorder.php" class="action-btn">
                        <i class="fas fa-shopping-cart"></i>
                        <span>View Orders</span>
                    </a>
                </div>
            </div>

            <?php if (isset($_GET['edit_menu'])): ?>
            <!-- Edit Menu Item Form -->
            <?php
            $edit_id = $_GET['edit_menu'];
            $stmt = $pdo->prepare("SELECT * FROM menu WHERE id = ?");
            $stmt->execute([$edit_id]);
            $edit_item = $stmt->fetch();
            if ($edit_item):
            ?>
            <div class="add-user glass-effect animate-fadeInUp">
                <div class="section-header">
                    <h3>Edit Menu Item</h3>
                </div>
                <form method="POST" class="add-user-form">
                    <input type="hidden" name="menu_id" value="<?php echo $edit_item['id']; ?>">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="name">Item Name *</label>
                            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($edit_item['name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="category">Category *</label>
                            <select id="category" name="category" required>
                                <option value="">Select Category</option>
                                <option value="veg" <?php echo $edit_item['category'] === 'veg' ? 'selected' : ''; ?>>Vegetarian</option>
                                <option value="nonveg" <?php echo $edit_item['category'] === 'nonveg' ? 'selected' : ''; ?>>Non-Vegetarian</option>
                                <option value="beverages" <?php echo $edit_item['category'] === 'beverages' ? 'selected' : ''; ?>>Beverages</option>
                                <option value="desserts" <?php echo $edit_item['category'] === 'desserts' ? 'selected' : ''; ?>>Desserts</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="price">Price (₹) *</label>
                            <input type="number" id="price" name="price" step="0.01" value="<?php echo $edit_item['price']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="original_price">Original Price (₹)</label>
                            <input type="number" id="original_price" name="original_price" step="0.01" value="<?php echo $edit_item['original_price'] ?? ''; ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="3"><?php echo htmlspecialchars($edit_item['description']); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="image">Image URL</label>
                        <input type="url" id="image" name="image" value="<?php echo htmlspecialchars($edit_item['image']); ?>" placeholder="https://example.com/image.jpg">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="rating">Rating (0-5)</label>
                            <input type="number" id="rating" name="rating" step="0.1" min="0" max="5" value="<?php echo $edit_item['rating']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="reviews">Reviews Count</label>
                            <input type="number" id="reviews" name="reviews" value="<?php echo $edit_item['reviews']; ?>">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label for="calories">Calories</label>
                            <input type="number" id="calories" name="calories" value="<?php echo $edit_item['calories']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="spice_level">Spice Level (0-3)</label>
                            <input type="number" id="spice_level" name="spice_level" min="0" max="3" value="<?php echo $edit_item['spice_level']; ?>">
                        </div>
                    </div>
                    <div class="form-row">
                        <div class="form-group checkbox-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="is_bestseller" <?php echo $edit_item['is_bestseller'] ? 'checked' : ''; ?>>
                                <span class="checkmark"></span>
                                Bestseller
                            </label>
                        </div>
                        <div class="form-group checkbox-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="is_new" <?php echo $edit_item['is_new'] ? 'checked' : ''; ?>>
                                <span class="checkmark"></span>
                                New Item
                            </label>
                        </div>
                    </div>
                    <div class="form-actions">
                        <button type="submit" name="update_menu_item" class="btn-create">
                            <i class="fas fa-save"></i>
                            <span>Update Item</span>
                        </button>
                        <a href="editmenu.php" class="btn-cancel">
                            <i class="fas fa-times"></i>
                            <span>Cancel</span>
                        </a>
                    </div>
                </form>
            </div>
            <?php endif; ?>
            <?php endif; ?>

            <!-- Menu Management -->
            <div class="menu-management glass-effect animate-fadeInUp">
                <div class="section-header">
                    <h3>Menu Items</h3>
                    <span class="user-count">Total Items: <?php echo count($menu_items); ?></span>
                    <button class="btn-create" onclick="showAddMenuForm()">
                        <i class="fas fa-plus"></i>
                        <span>Add Item</span>
                    </button>
                </div>
                <?php if (isset($menu_success)): ?>
                    <div class="alert success"><?php echo $menu_success; ?></div>
                <?php endif; ?>
                <?php if (isset($menu_error)): ?>
                    <div class="alert error"><?php echo $menu_error; ?></div>
                <?php endif; ?>

                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Rating</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($menu_items as $item): ?>
                        <tr>
                            <td><?php echo $item['id']; ?></td>
                            <td>
                                <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                            </td>
                            <td><?php echo htmlspecialchars($item['name']); ?></td>
                            <td>
                                <span class="category-badge <?php echo $item['category']; ?>">
                                    <?php echo ucfirst($item['category']); ?>
                                </span>
                            </td>
                            <td>₹<?php echo number_format($item['price'], 2); ?></td>
                            <td><?php echo $item['rating']; ?> ⭐</td>
                            <td>
                                <a href="editmenu.php?edit_menu=<?php echo $item['id']; ?>" class="btn-edit" title="Edit Item">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this menu item?');">
                                    <input type="hidden" name="menu_id" value="<?php echo $item['id']; ?>">
                                    <button type="submit" name="delete_menu_item" class="btn-delete" title="Delete Item">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script>
        function showAddMenuForm() {
            // Create a simple add form overlay or redirect to add form
            const addForm = `
                <div id="addMenuOverlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; display: flex; align-items: center; justify-content: center;">
                    <div style="background: white; padding: 30px; border-radius: 15px; max-width: 600px; width: 90%; max-height: 90%; overflow-y: auto;">
                        <h3 style="margin-bottom: 20px; color: #333;">Add New Menu Item</h3>
                        <form method="POST" style="display: grid; gap: 15px;">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                <input type="text" name="name" placeholder="Item Name *" required style="padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                                <select name="category" required style="padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                                    <option value="">Select Category *</option>
                                    <option value="veg">Vegetarian</option>
                                    <option value="nonveg">Non-Vegetarian</option>
                                    <option value="beverages">Beverages</option>
                                    <option value="desserts">Desserts</option>
                                </select>
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                <input type="number" name="price" step="0.01" placeholder="Price (₹) *" required style="padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                                <input type="number" name="original_price" step="0.01" placeholder="Original Price (₹)" style="padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                            </div>
                            <textarea name="description" placeholder="Description" rows="3" style="padding: 10px; border: 1px solid #ddd; border-radius: 5px;"></textarea>
                            <input type="url" name="image" placeholder="Image URL" style="padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                            <div style="display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 15px;">
                                <input type="number" name="rating" step="0.1" min="0" max="5" placeholder="Rating" style="padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                                <input type="number" name="reviews" placeholder="Reviews" style="padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                                <input type="number" name="calories" placeholder="Calories" style="padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                                <input type="number" name="spice_level" min="0" max="3" placeholder="Spice Level" style="padding: 10px; border: 1px solid #ddd; border-radius: 5px;">
                            </div>
                            <div style="display: flex; gap: 15px; align-items: center;">
                                <label style="display: flex; align-items: center; gap: 5px;">
                                    <input type="checkbox" name="is_bestseller">
                                    Bestseller
                                </label>
                                <label style="display: flex; align-items: center; gap: 5px;">
                                    <input type="checkbox" name="is_new">
                                    New Item
                                </label>
                            </div>
                            <div style="display: flex; gap: 10px; justify-content: flex-end;">
                                <button type="button" onclick="closeAddForm()" style="padding: 10px 20px; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer;">Cancel</button>
                                <button type="submit" name="add_menu_item" style="padding: 10px 20px; background: #ff6b35; color: white; border: none; border-radius: 5px; cursor: pointer;">Add Item</button>
                            </div>
                        </form>
                    </div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', addForm);
        }

        function closeAddForm() {
            const overlay = document.getElementById('addMenuOverlay');
            if (overlay) {
                overlay.remove();
            }
        }
    </script>
</body>
</html>