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

// Fetch total orders count
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM orders");
$stmt->execute();
$orders_result = $stmt->fetch();
$total_orders = $orders_result['total'] ?? 0;

// Fetch all orders for display
$stmt = $pdo->prepare("SELECT o.*, u.fullname, u.email, u.phone FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC");
$stmt->execute();
$orders = $stmt->fetchAll();

// Calculate total revenue from orders
$stmt = $pdo->prepare("SELECT SUM(total_amount) as revenue FROM orders WHERE status = 'completed'");
$stmt->execute();
$revenue_result = $stmt->fetch();
$total_revenue = $revenue_result['revenue'] ?? 0;

// Calculate pending orders
$stmt = $pdo->prepare("SELECT COUNT(*) as pending FROM orders WHERE status != 'completed'");
$stmt->execute();
$pending_result = $stmt->fetch();
$pending_orders = $pending_result['pending'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orders Management - Delicious Dispatchers</title>
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
                <a href="users.php" class="nav-item" data-tooltip="User Management">
                    <i class="fas fa-users-cog"></i>
                </a>
                <a href="savedorder.php" class="nav-item active" data-tooltip="Orders Management">
                    <i class="fas fa-shopping-cart"></i>
                </a>
                <a href="editmenu.php" class="nav-item" data-tooltip="Menu Management">
                    <i class="fas fa-utensils"></i>
                </a>
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
                    <h1>Orders Management 🛒</h1>
                    <p>View and manage all customer orders.</p>
                </div>
                <div class="header-right">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Search orders...">
                    </div>
                </div>
            </header>

            <!-- Stats Cards -->
            <div class="stats-grid">
                <div class="stat-card glass-effect animate-fadeInUp">
                    <div class="stat-icon green">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                    <div class="stat-info">
                        <span class="stat-label">Total Orders</span>
                        <span class="stat-value"><?php echo $total_orders; ?></span>
                        <span class="stat-change positive"><i class="fas fa-arrow-up"></i> This month</span>
                    </div>
                </div>
                <div class="stat-card glass-effect animate-fadeInUp" style="animation-delay: 0.1s;">
                    <div class="stat-icon orange">
                        <i class="fas fa-rupee-sign"></i>
                    </div>
                    <div class="stat-info">
                        <span class="stat-label">Total Revenue</span>
                        <span class="stat-value">₹<?php echo number_format($total_revenue); ?></span>
                        <span class="stat-change positive"><i class="fas fa-arrow-up"></i> 18% this month</span>
                    </div>
                </div>
                <div class="stat-card glass-effect animate-fadeInUp" style="animation-delay: 0.2s;">
                    <div class="stat-icon blue">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-info">
                        <span class="stat-label">Pending Orders</span>
                        <span class="stat-value"><?php echo $pending_orders; ?></span>
                        <span class="stat-change neutral"><i class="fas fa-minus"></i> Processing</span>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions glass-effect animate-fadeInUp">
                <h3>Quick Actions</h3>
                <div class="actions-grid">
                    <a href="users.php" class="action-btn">
                        <i class="fas fa-users-cog"></i>
                        <span>Manage Users</span>
                    </a>
                    <a href="editmenu.php" class="action-btn">
                        <i class="fas fa-utensils"></i>
                        <span>Manage Menu</span>
                    </a>
                    <button class="action-btn">
                        <i class="fas fa-file-export"></i>
                        <span>Export Orders</span>
                    </button>
                </div>
            </div>

            <!-- Orders Management -->
            <div class="orders-management glass-effect animate-fadeInUp">
                <div class="section-header">
                    <h3>All Orders</h3>
                    <span class="user-count">Total Orders: <?php echo count($orders); ?></span>
                </div>
                <table class="orders-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Payment</th>
                            <th>Order Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                        <tr>
                            <td>#<?php echo $order['id']; ?></td>
                            <td>
                                <div class="customer-info">
                                    <span class="avatar"><?php echo strtoupper(substr($order['fullname'] ?? 'G', 0, 2)); ?></span>
                                    <span><?php echo htmlspecialchars($order['fullname'] ?? 'Guest'); ?></span>
                                    <br><small><?php echo htmlspecialchars($order['email'] ?? 'N/A'); ?></small>
                                </div>
                            </td>
                            <td><?php echo htmlspecialchars($order['items']); ?></td>
                            <td>₹<?php echo number_format($order['total_amount'], 2); ?></td>
                            <td>
                                <span class="status-badge <?php echo strtolower($order['status']); ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($order['payment_method']); ?></td>
                            <td><?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</body>
</html>