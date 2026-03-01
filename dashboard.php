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
                    <div class="stat-card glass-effect animate-fadeInUp" style="animation-delay: 0.2s;">
                        <div class="stat-icon blue">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-info">
                            <span class="stat-label">Active Subscribers</span>
                            <span class="stat-value">1,234</span>
                            <span class="stat-change positive"><i class="fas fa-arrow-up"></i> 23 new today</span>
                        </div>
                    </div>
                    <div class="stat-card glass-effect animate-fadeInUp" style="animation-delay: 0.3s;">
                        <div class="stat-icon purple">
                            <i class="fas fa-truck"></i>
                        </div>
                        <div class="stat-info">
                            <span class="stat-label">Deliveries</span>
                            <span class="stat-value">142</span>
                            <span class="stat-change">14 pending</span>
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
                        <button class="action-btn">
                            <i class="fas fa-user-plus"></i>
                            <span>Add Customer</span>
                        </button>
                        <button class="action-btn">
                            <i class="fas fa-utensils"></i>
                            <span>Update Menu</span>
                        </button>
                        <button class="action-btn">
                            <i class="fas fa-file-invoice"></i>
                            <span>Generate Report</span>
                        </button>
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="recent-orders glass-effect animate-fadeInUp">
                    <div class="section-header">
                        <h3>Recent Orders</h3>
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
                            <tr>
                                <td>#DD001</td>
                                <td>
                                    <div class="customer-info">
                                        <span class="avatar">RS</span>
                                        <span>Rahul Sharma</span>
                                    </div>
                                </td>
                                <td>Monthly Veg</td>
                                <td>₹3,500</td>
                                <td><span class="status delivered">Delivered</span></td>
                                <td><button class="btn-icon"><i class="fas fa-eye"></i></button></td>
                            </tr>
                            <tr>
                                <td>#DD002</td>
                                <td>
                                    <div class="customer-info">
                                        <span class="avatar">PK</span>
                                        <span>Priya Kumar</span>
                                    </div>
                                </td>
                                <td>Weekly Non-Veg</td>
                                <td>₹1,200</td>
                                <td><span class="status in-transit">In Transit</span></td>
                                <td><button class="btn-icon"><i class="fas fa-eye"></i></button></td>
                            </tr>
                            <tr>
                                <td>#DD003</td>
                                <td>
                                    <div class="customer-info">
                                        <span class="avatar">AV</span>
                                        <span>Amit Verma</span>
                                    </div>
                                </td>
                                <td>Daily Lunch</td>
                                <td>₹150</td>
                                <td><span class="status pending">Pending</span></td>
                                <td><button class="btn-icon"><i class="fas fa-eye"></i></button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script src="js/script.js"></script>
</body>
</html>