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

// Calculate total revenue from orders (only successful online payments)
$stmt = $pdo->prepare("SELECT SUM(total_amount) as revenue FROM orders WHERE status = 'done' or (status = 'confirmed' and payment_method = 'razorpay')");
$stmt->execute();
$revenue_result = $stmt->fetch();
$total_revenue = $revenue_result['revenue'] ?? 0;

// Debug: Check what orders match the revenue criteria
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM orders WHERE status IN ('completed', 'done') AND payment_method = 'razorpay'");
$stmt->execute();
$debug_result = $stmt->fetch();
$revenue_order_count = $debug_result['count'] ?? 0;


// Calculate pending orders
$stmt = $pdo->prepare("SELECT COUNT(*) as pending FROM orders WHERE status NOT IN ('completed', 'done')");
$stmt->execute();
$pending_result = $stmt->fetch();
$pending_orders = $pending_result['pending'] ?? 0;

// Handle order status updates
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['mark_paid'])) {
        $order_id = $_POST['order_id'];
        $stmt = $pdo->prepare("UPDATE orders SET status = 'completed' WHERE id = ? AND payment_method = 'cod' AND status = 'pending'");
        $stmt->execute([$order_id]);
    } elseif (isset($_POST['mark_delivered'])) {
        $order_id = $_POST['order_id'];
        $stmt = $pdo->prepare("UPDATE orders SET status = 'done' WHERE id = ? AND status IN ('completed', 'confirmed', 'pending')");
        $stmt->execute([$order_id]);
    }

    // Refresh the page after status update
    header('Location: savedorder.php');
    exit();
}
// Function to format items for display
function formatOrderItems($items_json) {
    $items = json_decode($items_json, true);
    if (!$items || !is_array($items)) {
        return 'N/A';
    }

    $formatted_items = [];
    foreach ($items as $item) {
        $name = htmlspecialchars($item['name'] ?? 'Unknown');
        $quantity = $item['quantity'] ?? 1;
        $formatted_items[] = $name . '(' . $quantity . ')';
    }

    return implode(', ', $formatted_items);
}
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
                    <span class="user-count">Total Orders: <?php echo count($orders); ?> | Revenue Orders: <?php echo $revenue_order_count; ?> | Revenue: ₹<?php echo number_format($total_revenue); ?></span>
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
                            <th>Actions</th>
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
                            <td><?php echo formatOrderItems($order['items']); ?></td>
                            <td>₹<?php echo number_format($order['total_amount'], 2); ?></td>
                            <td>
                                <span class="status-badge <?php echo strtolower($order['status']); ?>">
                                    <?php echo ucfirst($order['status']); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($order['payment_method']); ?></td>
                            <td><?php echo date('M d, Y H:i', strtotime($order['created_at'])); ?></td>
                            <td>
                                <?php if ($order['status'] !== 'failed' && $order['status'] !== 'done'): ?>
                                <div class="order-actions" data-order-id="<?php echo $order['id']; ?>" data-status="<?php echo $order['status']; ?>" data-payment="<?php echo $order['payment_method']; ?>">
                                    <i class="fas fa-ellipsis-v action-trigger"></i>
                                </div>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <!-- Context Menu -->
    <div id="contextMenu" class="context-menu">
        <div class="context-menu-item" data-action="mark_paid">
            <i class="fas fa-check"></i> Mark as Paid
        </div>
        <div class="context-menu-item" data-action="mark_delivered">
            <i class="fas fa-truck"></i> Mark as Done
        </div>
    </div>

    <style>
        .order-actions {
            position: relative;
            cursor: pointer;
            padding: 8px;
            border-radius: 4px;
            transition: background-color 0.2s;
        }

        .order-actions:hover {
            background-color: rgba(255, 107, 53, 0.1);
        }

        .action-trigger {
            color: #666;
            font-size: 14px;
        }

        .context-menu {
            position: absolute;
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            padding: 8px 0;
            min-width: 160px;
            z-index: 1000;
            display: none;
        }

        .context-menu-item {
            padding: 10px 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #333;
            transition: background-color 0.2s;
        }

        .context-menu-item:hover {
            background-color: #f8f9fa;
        }

        .context-menu-item i {
            width: 16px;
            color: #666;
        }

        .status-badge.done {
            background: #28a745;
            color: white;
        }
    </style>

    <script>
        // Context menu functionality
        let currentOrderId = null;

        document.addEventListener('DOMContentLoaded', function() {
            const contextMenu = document.getElementById('contextMenu');
            const orderActions = document.querySelectorAll('.order-actions');

            // Handle action trigger clicks
            orderActions.forEach(action => {
                action.addEventListener('click', function(e) {
                    e.stopPropagation();
                    currentOrderId = this.dataset.orderId;
                    const status = this.dataset.status;
                    const payment = this.dataset.payment;

                    // Position context menu
                    const rect = this.getBoundingClientRect();
                    contextMenu.style.left = rect.left + 'px';
                    contextMenu.style.top = (rect.bottom + 5) + 'px';
                    contextMenu.style.display = 'block';

                    // Update menu items based on order status
                    const markPaidItem = contextMenu.querySelector('[data-action="mark_paid"]');
                    const markDeliveredItem = contextMenu.querySelector('[data-action="mark_delivered"]');

                    // Hide all items first
                    markPaidItem.style.display = 'none';
                    markDeliveredItem.style.display = 'none';

                    if (payment === 'cod' && status === 'pending') {
                        // COD pending orders: show "Mark as Paid"
                        markPaidItem.style.display = 'flex';
                    } else if (payment === 'razorpay' && (status === 'completed' || status === 'confirmed')) {
                        // Razorpay confirmed/completed orders: show "Mark as Done"
                        markDeliveredItem.style.display = 'flex';
                    } else if (payment === 'cod' && status === 'completed') {
                        // COD completed orders: show "Mark as Done"
                        markDeliveredItem.style.display = 'flex';
                    }

                    // Hide the context menu entirely if no actions are available
                    if (markPaidItem.style.display === 'none' && markDeliveredItem.style.display === 'none') {
                        contextMenu.style.display = 'none';
                    }
                    // For all other cases: show no options
                });
            });

            // Handle context menu item clicks
            contextMenu.addEventListener('click', function(e) {
                const action = e.target.closest('.context-menu-item');
                if (!action) return;

                const actionType = action.dataset.action;

                if (actionType === 'mark_paid') {
                    //if (confirm('Mark this COD order as paid?')) {
                        submitOrderAction('mark_paid', currentOrderId);
                    // }
                } else if (actionType === 'mark_delivered') {
                    // if (confirm('Mark this order as done?')) {
                        submitOrderAction('mark_delivered', currentOrderId);
                    // }
                }

                contextMenu.style.display = 'none';
            });

            // Hide context menu when clicking elsewhere
            document.addEventListener('click', function() {
                contextMenu.style.display = 'none';
            });
        });

        function submitOrderAction(action, orderId) {
            const form = document.createElement('form');
            form.method = 'POST';
            form.style.display = 'none';

            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = action;
            actionInput.value = '1';
            form.appendChild(actionInput);

            const orderInput = document.createElement('input');
            orderInput.type = 'hidden';
            orderInput.name = 'order_id';
            orderInput.value = orderId;
            form.appendChild(orderInput);

            document.body.appendChild(form);
            form.submit();
        }
    </script>
</body>
</html>