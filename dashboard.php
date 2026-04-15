<?php
include 'includes/db_config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_name = $_SESSION['user_name'];
$user_id = $_SESSION['user_id'];

// Get user order statistics
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
    <title>Dashboard - Delicious Dispatchers</title>
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
                <a href="dashboard.php" class="nav-item active" data-tooltip="Dashboard">
                    <i class="fas fa-home"></i>
                </a>
                <?php if ($_SESSION['user_email'] === 'doshidivy2607@gmail.com'): ?>
                <a href="admin.php" class="nav-item" data-tooltip="Admin Panel">
                    <i class="fas fa-user-shield"></i>
                </a>
                <?php endif; ?>
                <a href="order.php" class="nav-item" data-tooltip="Cart">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="nav-badge">12</span>
                </a>
                <a href="profile.php" class="nav-item" data-tooltip="My Profile">
                    <i class="fas fa-users"></i>
                </a>
                <a href="order.php" class="nav-item" data-tooltip="Order Page">
                    <i class="fas fa-utensils"></i>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="dashboard-main">
            <header class="dashboard-header glass-effect">
                <div class="header-left">
                    <h1>Welcome back, <?php echo htmlspecialchars($user_name); ?>! 👋</h1>
                    <p>Here's what's happening with your tiffin service today.</p>
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
                <!-- Stats Cards -->
                <div class="stats-grid">
                    <div class="stat-card glass-effect animate-fadeInUp">
                        <div class="stat-icon orange">
                            <i class="fas fa-history"></i>
                        </div>
                        <div class="stat-info">
                            <span class="stat-label">Past Orders</span>
                            <span class="stat-value"><?php echo number_format($order_count); ?></span>
                            <span class="stat-change positive"><i class="fas fa-clock"></i> Your order history</span>
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

                <!-- Quick Actions -->
                <div class="quick-actions glass-effect animate-fadeInUp">
                    <h3>Quick Actions</h3>
                    <div class="actions-grid">
                        <a href="order.php" class="action-btn">
                            <i class="fas fa-plus"></i>
                            <span>New Order</span>
                        </a>
                    </div>
                </div>

                <!-- Order History -->
                <div class="recent-orders glass-effect animate-fadeInUp">
                    <div class="section-header">
                        <h3>Order History</h3>
                        <a href="order.php" class="view-all">View All <i class="fas fa-arrow-right"></i></a>
                    </div>
                    <table class="orders-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Payment</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
                            $stmt->execute([$user_id]);
                            $past_orders = $stmt->fetchAll();

                            if (count($past_orders) > 0) {
                                foreach ($past_orders as $order) {
                                    $status_class = strtolower(str_replace(' ', '-', $order['status']));
                                    $display_order_id = $order['order_id'] ? htmlspecialchars($order['order_id']) : '#' . str_pad($order['id'], 3, '0', STR_PAD_LEFT);
                                    echo '<tr>';
                                    echo '<td>' . $display_order_id . '</td>';
                                    echo '<td>₹' . number_format($order['total_amount'] ?? 0, 2) . '</td>';
                                    echo '<td><span class="status ' . $status_class . '">' . htmlspecialchars($order['status']) . '</span></td>';
                                    echo '<td>' . htmlspecialchars(date('M d, Y', strtotime($order['created_at']))) . '</td>';
                                    echo '<td>' . htmlspecialchars(ucfirst($order['payment_method'] ?? 'N/A')) . '</td>';
                                    echo '<td><button class="btn-icon"><i class="fas fa-eye"></i></button></td>';
                                    echo '</tr>';
                                }
                            } else {
                                echo '<tr><td colspan="6" style="text-align: center; color: rgba(255,255,255,0.6);">You have no past orders yet.</td></tr>';
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script src="js/script.js"></script>
</body>
</html>