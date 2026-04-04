<?php
include 'includes/db_config.php';

try {
    // Check orders count and revenue
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM orders");
    $stmt->execute();
    $orders_result = $stmt->fetch();
    $total_orders = $orders_result['total'] ?? 0;

    $stmt = $pdo->prepare("SELECT SUM(total_amount) as revenue FROM orders WHERE status = 'completed'");
    $stmt->execute();
    $revenue_result = $stmt->fetch();
    $total_revenue = $revenue_result['revenue'] ?? 0;

    echo "Total Orders: $total_orders\n";
    echo "Total Revenue: ₹" . number_format($total_revenue, 2) . "\n";

    // Show recent orders
    $stmt = $pdo->prepare("SELECT o.*, u.fullname FROM orders o LEFT JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC LIMIT 5");
    $stmt->execute();
    $recent_orders = $stmt->fetchAll();

    echo "\nRecent Orders:\n";
    foreach ($recent_orders as $order) {
        echo "#{$order['id']} - {$order['fullname']} - ₹{$order['total_amount']} - {$order['status']} - {$order['created_at']}\n";
    }

} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>