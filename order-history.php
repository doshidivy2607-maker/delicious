<?php
include 'includes/db_config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Pagination setup
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Get total orders count
$count_stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?");
$count_stmt->execute([$user_id]);
$total_orders = $count_stmt->fetchColumn();
$total_pages = ceil($total_orders / $per_page);

// Get orders for current page
$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT $per_page OFFSET $offset");
$stmt->execute([$user_id]);
$orders = $stmt->fetchAll();

// Get user stats for summary
$order_count_stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?");
$order_count_stmt->execute([$user_id]);
$order_count = $order_count_stmt->fetchColumn();

$spent_stmt = $pdo->prepare("SELECT SUM(total_amount) FROM orders WHERE user_id = ?");
$spent_stmt->execute([$user_id]);
$total_spent = $spent_stmt->fetchColumn() ?: 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order History - Delicious Dispatchers</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        .orders-container {
            width: 100%;
            max-width: 100%;
            margin-top: 10px;
        }
        .orders-section {
            margin-top: 20px;
        }
        .stats-section {
            margin-bottom: 20px;
        }
        .orders-table-wrapper {
            overflow-x: auto;
            width: 100%;
        }
        .orders-table {
            width: 100%;
            min-width: 800px;
            border-collapse: collapse;
        }
        .orders-table th,
        .orders-table td {
            padding: 12px 8px;
            text-align: left;
            white-space: nowrap;
        }
        @media (max-width: 1200px) {
            .orders-table {
                min-width: 700px;
            }
        }
        @media (max-width: 768px) {
            .orders-table {
                min-width: 600px;
            }
            .orders-table th,
            .orders-table td {
                padding: 8px 6px;
                font-size: 14px;
            }
        }
        
        /* Pagination Styles */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            margin-top: 20px;
            padding: 20px 0;
            flex-wrap: wrap;
        }
        
        .pagination-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            color: white;
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        
        .pagination-btn:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.3);
            transform: translateY(-1px);
        }
        
        .pagination-numbers {
            display: flex;
            gap: 6px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .pagination-number {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            color: white;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        
        .pagination-number:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.3);
            transform: translateY(-1px);
        }
        
        .pagination-number.active {
            background: linear-gradient(135deg, #ff6b35, #f7931e);
            border-color: #ff6b35;
            color: white;
            font-weight: 600;
            box-shadow: 0 4px 15px rgba(255, 107, 53, 0.3);
        }
        
        .pagination-dots {
            color: rgba(255, 255, 255, 0.6);
            padding: 0 4px;
            font-size: 16px;
        }
        
        @media (max-width: 768px) {
            .pagination {
                gap: 6px;
                padding: 15px 0;
            }
            
            .pagination-btn {
                padding: 6px 12px;
                font-size: 13px;
            }
            
            .pagination-number {
                width: 36px;
                height: 36px;
                font-size: 13px;
            }
        }
        
        section {
            padding-top: 10px;
            padding-bottom: 10px;
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
                <a href="order-food.php" class="nav-item" data-tooltip="Order Page">
                    <i class="fas fa-utensils"></i>
                </a>
                <a href="#" class="nav-item active" data-tooltip="Order History">
                    <i class="fas fa-history"></i>
                </a>
            </nav>
        </aside>

        <!-- Main Content -->
        <main class="dashboard-main">
            <!-- Header -->
            <section class="page-header">
                <div class="container">
                    <div class="header-content">
                        <h1><i class="fas fa-history"></i> Order History</h1>
                        <p>View and track all your past orders</p>
                    </div>
                </div>
            </section>

            <!-- Stats Summary -->
            <section class="stats-section">
                <div class="container">
                    <div class="stats-grid">
                        <div class="stat-card glass-effect animate-fadeInUp">
                            <div class="stat-icon blue">
                                <i class="fas fa-receipt"></i>
                            </div>
                            <div class="stat-info">
                                <span class="stat-label">Total Orders</span>
                                <span class="stat-value"><?php echo number_format($order_count); ?></span>
                                <span class="stat-change">All time orders</span>
                            </div>
                        </div>
                        <div class="stat-card glass-effect animate-fadeInUp" style="animation-delay: 0.1s;">
                            <div class="stat-icon green">
                                <i class="fas fa-rupee-sign"></i>
                            </div>
                            <div class="stat-info">
                                <span class="stat-label">Total Spent</span>
                                <span class="stat-value">Rs <?php echo number_format($total_spent, 2); ?></span>
                                <span class="stat-change">All time total</span>
                            </div>
                        </div>
                        <div class="stat-card glass-effect animate-fadeInUp" style="animation-delay: 0.2s;">
                            <div class="stat-icon orange">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="stat-info">
                                <span class="stat-label">Page <?php echo $page; ?></span>
                                <span class="stat-value"><?php echo min($per_page, $total_orders - ($page - 1) * $per_page); ?></span>
                                <span class="stat-change">Orders on this page</span>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Orders Table -->
            <section class="orders-section">
                <div class="container">
                    <div class="orders-container glass-effect animate-fadeInUp">
                        <div class="section-header">
                            <h3>All Orders</h3>
                            <div class="header-actions">
                                <span class="results-info">Showing <?php echo min($per_page, $total_orders - ($page - 1) * $per_page); ?> of <?php echo $total_orders; ?> orders</span>
                            </div>
                        </div>
                        
                        <?php if ($total_orders > 0): ?>
                            <div class="orders-table-wrapper">
                                <table class="orders-table">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Amount</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                            <th>Payment</th>
                                            <th>Items</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($orders as $order): ?>
                                            <?php 
                                            $status_class = strtolower(str_replace(' ', '-', $order['status']));
                                            $display_order_id = $order['order_id'] ? htmlspecialchars($order['order_id']) : '#' . str_pad($order['id'], 3, '0', STR_PAD_LEFT);
                                            $is_recent = (strtotime($order['created_at']) > strtotime('-7 days'));
                                            ?>
                                            <tr class="<?php echo $is_recent ? 'recent-order' : ''; ?>">
                                                <td>
                                                    <div class="order-id-cell">
                                                        <span class="order-id"><?php echo $display_order_id; ?></span>
                                                        <?php if ($is_recent): ?>
                                                            <span class="recent-badge"><i class="fas fa-sparkles"></i> Recent</span>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                                <td><strong>Rs <?php echo number_format($order['total_amount'] ?? 0, 2); ?></strong></td>
                                                <td>
                                                    <span class="status <?php echo $status_class; ?>">
                                                        <?php echo htmlspecialchars($order['status']); ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="date-cell">
                                                        <span class="date"><?php echo htmlspecialchars(date('M d, Y', strtotime($order['created_at']))); ?></span>
                                                        <span class="time"><?php echo htmlspecialchars(date('h:i A', strtotime($order['created_at']))); ?></span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="payment-cell">
                                                        <i class="fas fa-<?php echo $order['payment_method'] === 'cod' ? 'money-bill-wave' : 'credit-card'; ?>"></i>
                                                        <span><?php echo htmlspecialchars(ucfirst($order['payment_method'] ?? 'N/A')); ?></span>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="items-count">
                                                        <?php 
                                                        // Get order items count
                                                        $items_stmt = $pdo->prepare("SELECT COUNT(*) FROM order_items WHERE order_id = ?");
                                                        $items_stmt->execute([$order['id']]);
                                                        $items_count = $items_stmt->fetchColumn();
                                                        echo $items_count . ' items';
                                                        ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <div class="action-buttons">
                                                        <?php if ($order['status'] === 'Pending'): ?>
                                                            <button class="btn-icon cancel-order" onclick="cancelOrder(<?php echo $order['id']; ?>)" title="Cancel Order">
                                                                <i class="fas fa-times"></i>
                                                            </button>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <?php if ($total_pages > 1): ?>
                                <div class="pagination">
                                    <?php if ($page > 1): ?>
                                        <a href="?page=<?php echo $page - 1; ?>" class="pagination-btn">
                                            <i class="fas fa-chevron-left"></i> Previous
                                        </a>
                                    <?php endif; ?>
                                    
                                    <div class="pagination-numbers">
                                        <?php
                                        $start_page = max(1, $page - 2);
                                        $end_page = min($total_pages, $page + 2);
                                        
                                        if ($start_page > 1) {
                                            echo '<a href="?page=1" class="pagination-number">1</a>';
                                            if ($start_page > 2) echo '<span class="pagination-dots">...</span>';
                                        }
                                        
                                        for ($i = $start_page; $i <= $end_page; $i++) {
                                            $active_class = $i === $page ? 'active' : '';
                                            echo '<a href="?page=' . $i . '" class="pagination-number ' . $active_class . '">' . $i . '</a>';
                                        }
                                        
                                        if ($end_page < $total_pages) {
                                            if ($end_page < $total_pages - 1) echo '<span class="pagination-dots">...</span>';
                                            echo '<a href="?page=' . $total_pages . '" class="pagination-number">' . $total_pages . '</a>';
                                        }
                                        ?>
                                    </div>
                                    
                                    <?php if ($page < $total_pages): ?>
                                        <a href="?page=<?php echo $page + 1; ?>" class="pagination-btn">
                                            Next <i class="fas fa-chevron-right"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>

                        <?php else: ?>
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-receipt"></i>
                                </div>
                                <h3>No Orders Yet</h3>
                                <p>You haven't placed any orders yet. Start ordering delicious food now!</p>
                                <a href="order-food.php" class="btn btn-primary">
                                    <i class="fas fa-utensils"></i> Place Your First Order
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </section>
        </main>
    </div>

    <script src="js/script.js"></script>
    <script>
        function cancelOrder(orderId) {
            if (confirm('Are you sure you want to cancel this order?')) {
                fetch('cancel_order.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ order_id: orderId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification('Order cancelled successfully', 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showNotification('Failed to cancel order', 'error');
                    }
                })
                .catch(error => {
                    showNotification('Error cancelling order', 'error');
                });
            }
        }
    </script>
</body>
</html>
