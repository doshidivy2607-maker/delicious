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

// Handle user deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_user'])) {
    $user_id_to_delete = $_POST['user_id'];
    // Don't allow admin to delete themselves
    if ($user_id_to_delete != $_SESSION['user_id']) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$user_id_to_delete]);
        $success_message = "User deleted successfully.";
    } else {
        $error_message = "You cannot delete your own account.";
    }
}

// Handle user creation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_user'])) {
    $new_fullname = trim($_POST['fullname']);
    $new_email = trim($_POST['email']);
    $new_phone = trim($_POST['phone']);
    $new_password = $_POST['password'];

    // Validation
    if (empty($new_fullname) || empty($new_email) || empty($new_password)) {
        $error_message = 'Please fill in all required fields';
    } elseif (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Please enter a valid email address';
    } elseif (strlen($new_password) < 6) {
        $error_message = 'Password must be at least 6 characters long';
    } else {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$new_email]);

        if ($stmt->fetch()) {
            $error_message = 'Email already registered';
        } else {
            // Create user
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (fullname, email, phone, password) VALUES (?, ?, ?, ?)");

            if ($stmt->execute([$new_fullname, $new_email, $new_phone, $hashed_password])) {
                $success_message = "User created successfully.";
            } else {
                $error_message = 'Failed to create user. Please try again.';
            }
        }
    }
}

// Fetch all users
$stmt = $pdo->prepare("SELECT id, fullname, email, phone, created_at FROM users ORDER BY created_at DESC");
$stmt->execute();
$users = $stmt->fetchAll();

// Fetch total orders count
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM orders");
$stmt->execute();
$orders_result = $stmt->fetch();
$total_orders = $orders_result['total'] ?? 0;

// Calculate total revenue based on users (₹5,000 per user)
$total_revenue = count($users) * 5000;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Delicious Dispatchers</title>
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
                <a href="admin.php" class="nav-item active" data-tooltip="Admin Dashboard">
                    <i class="fas fa-home"></i>
                </a>
                <a href="dashboard.php" class="nav-item" data-tooltip="User Dashboard">
                    <i class="fas fa-tachometer-alt"></i>
                </a>
                <a href="#" class="nav-item" data-tooltip="User Management">
                    <i class="fas fa-users-cog"></i>
                </a>
                <a href="#" class="nav-item" data-tooltip="System Settings">
                    <i class="fas fa-cogs"></i>
                </a>
                <a href="#" class="nav-item" data-tooltip="Reports">
                    <i class="fas fa-chart-bar"></i>
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
                    <h1>Welcome, Admin <?php echo htmlspecialchars($user_name); ?>! 👋</h1>
                    <p>Admin panel for managing Delicious Dispatchers system.</p>
                </div>
                <div class="header-right">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Search...">
                    </div>
                    <div class="notification-btn">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge">5</span>
                    </div>
                    <div class="user-profile">
                        <div class="user-avatar"><?php echo strtoupper(substr($user_name, 0, 2)); ?></div>
                        <span><?php echo htmlspecialchars($user_name); ?></span>
                    </div>
                </div>
            </header>

            <div class="dashboard-content">
                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card glass-effect animate-fadeInUp">
                        <div class="stat-icon blue">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-info">
                            <span class="stat-label">Total Users</span>
                            <span class="stat-value"><?php echo count($users); ?></span>
                            <span class="stat-change positive"><i class="fas fa-arrow-up"></i> 15% this month</span>
                        </div>
                    </div>
                    <div class="stat-card glass-effect animate-fadeInUp" style="animation-delay: 0.1s;">
                        <div class="stat-icon green">
                            <i class="fas fa-shopping-cart"></i>
                        </div>
                        <div class="stat-info">
                            <span class="stat-label">Total Orders</span>
                            <span class="stat-value"><?php echo $total_orders; ?></span>
                            <span class="stat-change positive"><i class="fas fa-arrow-up"></i> 22% this month</span>
                        </div>
                    </div>
                    <div class="stat-card glass-effect animate-fadeInUp" style="animation-delay: 0.2s;">
                        <div class="stat-icon orange">
                            <i class="fas fa-rupee-sign"></i>
                        </div>
                        <div class="stat-info">
                            <span class="stat-label">Total Revenue</span>
                            <span class="stat-value">₹<?php echo number_format($total_revenue); ?></span>
                            <span class="stat-change positive"><i class="fas fa-arrow-up"></i> 18% this month</span>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="quick-actions glass-effect animate-fadeInUp">
                    <h3>Admin Actions</h3>
                    <div class="actions-grid">
                        <button type="button" class="action-btn" onclick="showAddUserForm()">
                            <i class="fas fa-user-plus"></i>
                            <span>Add User</span>
                        </button>
                        <button class="action-btn">
                            <i class="fas fa-chart-bar"></i>
                            <span>Generate Report</span>
                        </button>
                        <button class="action-btn">
                            <i class="fas fa-utensils"></i>
                            <span>Update Menu</span>
                        </button>
                        <button class="action-btn">
                            <i class="fas fa-file-export"></i>
                            <span>Export Data</span>
                        </button>
                    </div>
                </div>

                <?php if (isset($_GET['show_form'])): ?>
                <!-- Add New User -->
                <div class="add-user glass-effect animate-fadeInUp">
                    <div class="section-header">
                        <h3>Add New User</h3>
                    </div>
                    <form id="addUserForm" method="POST" class="add-user-form">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="fullname">Full Name *</label>
                                <input type="text" id="fullname" name="fullname" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email Address *</label>
                                <input type="email" id="email" name="email" required>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="phone">Phone Number</label>
                                <input type="tel" id="phone" name="phone">
                            </div>
                            <div class="form-group">
                                <label for="password">Password *</label>
                                <input type="password" id="password" name="password" required minlength="6">
                            </div>
                        </div>
                        <div class="form-actions">
                            <button type="submit" name="add_user" class="btn-create">
                                <i class="fas fa-user-plus"></i>
                                <span>Create User</span>
                            </button>
                            <a href="admin.php" class="btn-cancel">
                                <i class="fas fa-times"></i>
                                <span>Cancel</span>
                            </a>
                        </div>
                    </form>
                </div>
                <?php endif; ?>

                <!-- User Management -->
                <div class="user-management glass-effect animate-fadeInUp">
                    <div class="section-header">
                        <h3>User Management</h3>
                        <span class="user-count">Total Users: <?php echo count($users); ?></span>
                    </div>
                    <?php if (isset($success_message)): ?>
                        <div class="alert success"><?php echo $success_message; ?></div>
                    <?php endif; ?>
                    <?php if (isset($error_message)): ?>
                        <div class="alert error"><?php echo $error_message; ?></div>
                    <?php endif; ?>
                    <table class="orders-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Joined Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td>
                                    <div class="customer-info">
                                        <span class="avatar"><?php echo strtoupper(substr($user['fullname'], 0, 2)); ?></span>
                                        <span><?php echo htmlspecialchars($user['fullname']); ?></span>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo htmlspecialchars($user['phone'] ?? 'N/A'); ?></td>
                                <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                <td>
                                    <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <button type="submit" name="delete_user" class="btn-delete" title="Delete User">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                    <?php else: ?>
                                    <span class="current-user">Current User</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script>
        function showAddUserForm() {
            // Redirect with parameter to show form
            window.location.href = 'admin.php?show_form=1';
        }
    </script>
    <script src="js/script.js"></script>
</body>
</html>