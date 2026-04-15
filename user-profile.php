<?php
include 'includes/db_config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['user_name'];
$user_email = $_SESSION['user_email'];

// Get user details from database
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Get user's order statistics
$order_stats_stmt = $pdo->prepare("SELECT COUNT(*) AS order_count, COALESCE(SUM(total_amount), 0) AS total_spent FROM orders WHERE user_id = ?");
$order_stats_stmt->execute([$user_id]);
$order_stats = $order_stats_stmt->fetch();
$order_count = $order_stats['order_count'];
$total_spent = $order_stats['total_spent'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Delicious Dispatchers</title>
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
                <i class="fas fa-utensils"></i>
                <span>DD</span>
            </div>
            <nav class="sidebar-nav">
                <a href="user-dashboard.php" class="nav-item" data-tooltip="Dashboard">
                    <i class="fas fa-home"></i>
                </a>
                <a href="#" class="nav-item active" data-tooltip="My Profile">
                    <i class="fas fa-users"></i>
                </a>
                <a href="order-food.php" class="nav-item" data-tooltip="Order Page">
                    <i class="fas fa-utensils"></i>
                </a>
                <a href="order-history.php" class="nav-item" data-tooltip="Order History">
                    <i class="fas fa-history"></i>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="dashboard-main">
            <header class="dashboard-header glass-effect">
                <div class="header-left">
                    <h1>My Profile 👤</h1>
                    <p>Manage your account information and preferences.</p>
                </div>
                <div class="header-right">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Search...">
                    </div>
                    <a href="logout.php" class="logout-btn" title="Logout">
                        <i class="fas fa-sign-out-alt"></i>
                    </a>
                    <div class="user-profile">
                        <div class="user-avatar"><?php echo strtoupper(substr($user_name, 0, 2)); ?></div>
                        <span><?php echo htmlspecialchars($user_name); ?></span>
                    </div>
                </div>
            </header>

            <div class="dashboard-content">
                <!-- Profile Stats -->
                <div class="stats-grid">
                    <div class="stat-card glass-effect animate-fadeInUp">
                        <div class="stat-icon blue">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                        <div class="stat-info">
                            <span class="stat-label">Total Orders</span>
                            <span class="stat-value"><?php echo number_format($order_count); ?></span>
                            <span class="stat-change positive"><i class="fas fa-clock"></i> All time</span>
                        </div>
                    </div>
                    <div class="stat-card glass-effect animate-fadeInUp" style="animation-delay: 0.1s;">
                        <div class="stat-icon green">
                            <i class="fas fa-rupee-sign"></i>
                        </div>
                        <div class="stat-info">
                            <span class="stat-label">Total Spent</span>
                            <span class="stat-value">₹<?php echo number_format($total_spent, 2); ?></span>
                            <span class="stat-change positive"><i class="fas fa-arrow-up"></i> Lifetime total</span>
                        </div>
                    </div>
                </div>

                <!-- Profile Information -->
                <div class="profile-section glass-effect animate-fadeInUp">
                    <div class="section-header">
                        <h3>Profile Information</h3>
                        <button class="btn btn-outline btn-sm" onclick="editProfile()">
                            <i class="fas fa-edit"></i> Edit Profile
                        </button>
                    </div>
                    <div class="profile-grid">
                        <div class="profile-item">
                            <label>Full Name</label>
                            <p><?php echo htmlspecialchars($user['fullname'] ?? $user_name); ?></p>
                        </div>
                        <div class="profile-item">
                            <label>Email Address</label>
                            <p><?php echo htmlspecialchars($user['email'] ?? $user_email); ?></p>
                        </div>
                        <div class="profile-item">
                            <label>Phone Number</label>
                            <p><?php echo htmlspecialchars($user['phone'] ?? 'Not provided'); ?></p>
                        </div>
                        <div class="profile-item">
                            <label>Member Since</label>
                            <p><?php echo htmlspecialchars(date('F Y', strtotime($user['created_at'] ?? 'now'))); ?></p>
                        </div>
                    </div>
                </div>

                <!-- Account Settings -->
                <div class="profile-section glass-effect animate-fadeInUp" style="animation-delay: 0.1s;">
                    <div class="section-header">
                        <h3>Account Settings</h3>
                    </div>
                    <div class="settings-grid">
                        <div class="setting-item">
                            <div class="setting-info">
                                <i class="fas fa-lock"></i>
                                <div>
                                    <strong>Change Password</strong>
                                    <p>Update your account password</p>
                                </div>
                            </div>
                            <button class="btn btn-outline btn-sm">Change</button>
                        </div>
                        <div class="setting-item">
                            <div class="setting-info">
                                <i class="fas fa-bell"></i>
                                <div>
                                    <strong>Notifications</strong>
                                    <p>Manage your notification preferences</p>
                                </div>
                            </div>
                            <button class="btn btn-outline btn-sm">Manage</button>
                        </div>
                        <div class="setting-item">
                            <div class="setting-info">
                                <i class="fas fa-shield-alt"></i>
                                <div>
                                    <strong>Privacy Settings</strong>
                                    <p>Control your privacy and data sharing</p>
                                </div>
                            </div>
                            <button class="btn btn-outline btn-sm">Settings</button>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        function editProfile() {
            alert('Profile editing feature coming soon!');
        }
    </script>
    <script src="js/script.js"></script>
</body>
</html>
