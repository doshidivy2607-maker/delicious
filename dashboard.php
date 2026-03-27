<?php
include 'includes/db_config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_name = $_SESSION['user_name'];
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
                <a href="order.php" class="nav-item" data-tooltip="Orders">
                    <i class="fas fa-shopping-cart"></i>
                    <span class="nav-badge">12</span>
                </a>
                <a href="#" class="nav-item" data-tooltip="Customers">
                    <i class="fas fa-users"></i>
                </a>
                <a href="#" class="nav-item" data-tooltip="Menu">
                    <i class="fas fa-utensils"></i>
                </a>
                <a href="#" class="nav-item" data-tooltip="Deliveries">
                    <i class="fas fa-truck"></i>
                </a>
                <a href="#" class="nav-item" data-tooltip="Reports">
                    <i class="fas fa-chart-bar"></i>
                </a>
                <a href="#" class="nav-item" data-tooltip="Settings">
                    <i class="fas fa-cog"></i>
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
                    <h1>Welcome back, <?php echo htmlspecialchars($user_name); ?>! 👋</h1>
                    <p>Here's what's happening with your tiffin service today.</p>
                </div>
                <div class="header-right">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Search...">
                    </div>
                    <div class="notification-btn">
                        <i class="fas fa-bell"></i>
                        <span class="notification-badge">3</span>
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
                        <div class="stat-icon orange">
                            <i class="fas fa-shopping-bag"></i>
                        </div>
                        <div class="stat-info">
                            <span class="stat-label">Today's Orders</span>
                            <span class="stat-value">156</span>
                            <span class="stat-change positive"><i class="fas fa-arrow-up"></i> 12% from yesterday</span>
                        </div>
                    </div>
                    <div class="stat-card glass-effect animate-fadeInUp" style="animation-delay: 0.1s;">
                        <div class="stat-icon green">
                            <i class="fas fa-rupee-sign"></i>
                        </div>
                        <div class="stat-info">
                            <span class="stat-label">Today's Revenue</span>
                            <span class="stat-value">₹24,500</span>
                            <span class="stat-change positive"><i class="fas fa-arrow-up"></i> 8% from yesterday</span>
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

                <!-- Live Orders -->
                <div class="recent-orders glass-effect animate-fadeInUp">
                    <div class="section-header">
                        <h3>Live Orders</h3>
                        <a href="order.php" class="view-all">View All <i class="fas fa-arrow-right"></i></a>
                    </div>
                    <table class="orders-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Plan</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Fetch live orders (pending or in-transit)
                            $stmt = $pdo->prepare("SELECT o.*, u.fullname FROM orders o JOIN users u ON o.user_id = u.id WHERE o.status IN ('Pending', 'In Transit') ORDER BY o.created_at DESC LIMIT 5");
                            $stmt->execute();
                            $live_orders = $stmt->fetchAll();

                            if (count($live_orders) > 0) {
                                foreach ($live_orders as $order) {
                                    $status_class = strtolower(str_replace(' ', '-', $order['status']));
                                    echo '<tr>';
                                    echo '<td>#' . str_pad($order['id'], 3, '0', STR_PAD_LEFT) . '</td>';
                                    echo '<td>';
                                    echo '<div class="customer-info">';
                                    echo '<span class="avatar">' . strtoupper(substr($order['fullname'], 0, 2)) . '</span>';
                                    echo '<span>' . htmlspecialchars($order['fullname']) . '</span>';
                                    echo '</div>';
                                    echo '</td>';
                                    echo '<td>' . htmlspecialchars($order['plan_name'] ?? 'N/A') . '</td>';
                                    echo '<td>₹' . number_format($order['amount'] ?? 0) . '</td>';
                                    echo '<td><span class="status ' . $status_class . '">' . htmlspecialchars($order['status']) . '</span></td>';
                                    echo '<td><button class="btn-icon"><i class="fas fa-eye"></i></button></td>';
                                    echo '</tr>';
                                }
                            } else {
                                echo '<tr><td colspan="6" style="text-align: center; color: rgba(255,255,255,0.6);">No live orders at the moment</td></tr>';
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