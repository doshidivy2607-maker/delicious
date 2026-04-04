<?php
include 'includes/db_config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['cart']) && isset($data['total'])) {
        $_SESSION['checkout_cart'] = $data['cart'];
        $_SESSION['checkout_total'] = $data['total'];
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
}
?>