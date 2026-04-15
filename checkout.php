<?php
include 'includes/db_config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get cart data from POST or session
$cart_items = [];
$total_amount = 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cart_data'])) {
    $cart_data = json_decode($_POST['cart_data'], true);
    if ($cart_data && is_array($cart_data)) {
        $cart_items = $cart_data;
        foreach ($cart_items as $item) {
            $total_amount += $item['price'] * $item['quantity'];
        }
    }
} elseif (isset($_SESSION['checkout_cart'])) {
    $cart_items = $_SESSION['checkout_cart'];
    $total_amount = $_SESSION['checkout_total'] ?? 0;
} else {
    // Redirect back to order page if no cart data
    header('Location: order-food.php');
    exit();
}

// Calculate totals
$subtotal = $total_amount;
$delivery_fee = $subtotal > 300 ? 0 : 40; // Free delivery above ₹300
$tax = $subtotal * 0.05; // 5% GST
$grand_total = $subtotal + $delivery_fee + $tax;

// Get user details
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Handle order placement
$order_success = false;
$order_id = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['place_order'])) {
    // Generate order ID
    $order_id = 'DD' . date('Ymd') . rand(1000, 9999);

    // Get form data
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    $city = trim($_POST['city']);
    $pincode = trim($_POST['pincode']);
    $delivery_time = $_POST['delivery_time'];
    $scheduled_time = $_POST['scheduled_time'] ?? null;
    $payment_method = $_POST['payment_method'];
    $order_notes = trim($_POST['order_notes'] ?? '');

    // Payment details (from Razorpay)
    $payment_id = $_POST['razorpay_payment_id'] ?? null;
    $razorpay_order_id = $_POST['razorpay_order_id'] ?? null;
    $razorpay_signature = $_POST['razorpay_signature'] ?? null;
    $payment_status = $_POST['payment_status'] ?? 'completed';

    $full_address = $address . ', ' . $city . ' - ' . $pincode;
    $delivery_schedule = $delivery_time === 'schedule' ? $scheduled_time : 'ASAP';

    // Determine order status based on payment
    $order_status = 'completed';
    if ($payment_method === 'cod') {
        $order_status = 'pending'; // COD orders start as pending until paid
    } elseif ($payment_status === 'failed') {
        $order_status = 'failed'; // Failed Razorpay payments
    }

    // Insert order
    $stmt = $pdo->prepare("INSERT INTO orders (order_id, user_id, items, subtotal, delivery_fee, tax, total_amount, payment_method, payment_id, status, delivery_address, delivery_time, order_notes, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $order_success = $stmt->execute([
        $order_id,
        $user_id,
        json_encode($cart_items),
        $subtotal,
        $delivery_fee,
        $tax,
        $grand_total,
        $payment_method,
        $payment_id,
        $order_status,
        $full_address,
        $delivery_schedule,
        $order_notes
    ]);

    if ($order_success) {
        // Clear cart from session
        unset($_SESSION['checkout_cart']);
        unset($_SESSION['checkout_total']);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Delicious Dispatchers</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/order.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Razorpay SDK -->
    <script src="env.js"></script>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
</head>
<body class="order-page">

    <!-- Checkout Section -->
    <section class="checkout-section">
        <div class="container">
            <div class="checkout-layout">
                <!-- Checkout Form -->
                <div class="checkout-form-container">
                    <div class="checkout-header">
                        <h1><i class="fas fa-shopping-cart"></i> Checkout</h1>
                        <p>Complete your order</p>
                    </div>

                    <?php if ($order_success): ?>
                    <!-- Order Success -->
                    <div class="order-success glass-effect animate-fadeInUp">
                        <div class="success-icon">
                            <?php if ($order_status === 'completed'): ?>
                                <i class="fas fa-check-circle"></i>
                            <?php elseif ($order_status === 'pending'): ?>
                                <i class="fas fa-clock"></i>
                            <?php else: ?>
                                <i class="fas fa-times-circle"></i>
                            <?php endif; ?>
                        </div>
                        <?php if ($order_status === 'completed'): ?>
                            <h2>Order Placed Successfully!</h2>
                            <p>Thank you for your order. Your food will be delivered soon.</p>
                        <?php elseif ($order_status === 'pending'): ?>
                            <h2>Order Placed Successfully!</h2>
                            <p>Your order has been placed. Please pay cash on delivery when your food arrives.</p>
                        <?php else: ?>
                            <h2>Payment Failed</h2>
                            <p>Your order has been saved but payment was not completed. You can try again or contact support.</p>
                        <?php endif; ?>
                        <div class="order-details">
                            <p><strong>Order ID:</strong> <?php echo $order_id; ?></p>
                            <p><strong>Total Amount:</strong> ₹<?php echo number_format($grand_total, 2); ?></p>
                            <p><strong>Status:</strong>
                                <span class="status-badge <?php echo strtolower($order_status); ?>">
                                    <?php echo ucfirst($order_status); ?>
                                </span>
                            </p>
                        </div>
                        <div class="success-actions">
                            <a href="user-dashboard.php" class="btn btn-primary">Track Order</a>
                            <a href="order-food.php" class="btn btn-outline">Order More</a>
                        </div>
                    </div>
                    <?php else: ?>

                    <form id="checkoutForm" method="POST" class="checkout-form glass-effect animate-fadeInUp">
                        <!-- Delivery Information -->
                        <div class="form-section">
                            <h3><i class="fas fa-map-marker-alt"></i> Delivery Information</h3>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="name">Full Name *</label>
                                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['fullname']); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="phone">Phone Number *</label>
                                    <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="email">Email Address *</label>
                                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="address">Delivery Address *</label>
                                <textarea id="address" name="address" rows="3" placeholder="Enter your complete delivery address" required></textarea>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="city">City *</label>
                                    <input type="text" id="city" name="city" required>
                                </div>
                                <div class="form-group">
                                    <label for="pincode">Pincode *</label>
                                    <input type="text" id="pincode" name="pincode" required>
                                </div>
                            </div>
                        </div>

                        <!-- Delivery Time -->
                        <div class="form-section">
                            <h3><i class="fas fa-clock"></i> Delivery Time</h3>
                            <div class="delivery-options">
                                <label class="delivery-option">
                                    <input type="radio" name="delivery_time" value="asap" checked>
                                    <span class="option-content">
                                        <strong>As soon as possible</strong>
                                        <small>Usually within 30-45 minutes</small>
                                    </span>
                                </label>
                                <label class="delivery-option">
                                    <input type="radio" name="delivery_time" value="schedule">
                                    <span class="option-content">
                                        <strong>Schedule for later</strong>
                                        <input type="datetime-local" name="scheduled_time" min="<?php echo date('Y-m-d\TH:i'); ?>">
                                    </span>
                                </label>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="form-section">
                            <h3><i class="fas fa-credit-card"></i> Payment Method</h3>
                            <div class="payment-options">
                                <label class="payment-option">
                                    <input type="radio" name="payment_method" value="razorpay" checked>
                                    <span class="option-content">
                                        <i class="fab fa-google-pay"></i>
                                        <span>UPI / Cards / Wallets</span>
                                        <small>Powered by Razorpay</small>
                                    </span>
                                </label>
                                <label class="payment-option">
                                    <input type="radio" name="payment_method" value="cod">
                                    <span class="option-content">
                                        <i class="fas fa-money-bill-wave"></i>
                                        <span>Cash on Delivery</span>
                                        <small>Pay when you receive</small>
                                    </span>
                                </label>
                            </div>
                        </div>

                        <!-- Order Notes -->
                        <div class="form-section">
                            <h3><i class="fas fa-sticky-note"></i> Order Notes (Optional)</h3>
                            <textarea name="order_notes" rows="3" placeholder="Any special instructions for your order..."></textarea>
                        </div>

                        <!-- Hidden cart data -->
                        <input type="hidden" name="cart_data" value='<?php echo json_encode($cart_items); ?>'>
                        <input type="hidden" name="subtotal" value="<?php echo $subtotal; ?>">
                        <input type="hidden" name="delivery_fee" value="<?php echo $delivery_fee; ?>">
                        <input type="hidden" name="tax" value="<?php echo $tax; ?>">
                        <input type="hidden" name="total" value="<?php echo $grand_total; ?>">

                        <!-- Place Order Button -->
                        <div class="checkout-actions">
                            <button type="button" class="btn btn-primary btn-lg" onclick="initiatePayment()">
                                <i class="fas fa-lock"></i> Pay ₹<?php echo number_format($grand_total, 2); ?>
                            </button>
                            <a href="order-food.php" class="btn btn-outline">Back to Menu</a>
                        </div>
                    </form>
                    <?php endif; ?>
                </div>

                <!-- Order Summary -->
                <div class="order-summary glass-effect animate-fadeInRight">
                    <div class="summary-header">
                        <h3><i class="fas fa-receipt"></i> Order Summary</h3>
                    </div>

                    <div class="order-items">
                        <?php foreach ($cart_items as $item): ?>
                        <div class="summary-item">
                            <div class="item-info">
                                <h4><?php echo htmlspecialchars($item['name']); ?></h4>
                                <span class="item-quantity">Qty: <?php echo $item['quantity']; ?></span>
                            </div>
                            <div class="item-price">
                                ₹<?php echo number_format($item['price'] * $item['quantity'], 2); ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="summary-breakdown">
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span>₹<?php echo number_format($subtotal, 2); ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Delivery Fee</span>
                            <span>₹<?php echo number_format($delivery_fee, 2); ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Tax (GST 5%)</span>
                            <span>₹<?php echo number_format($tax, 2); ?></span>
                        </div>
                        <div class="summary-row total">
                            <strong>Total</strong>
                            <strong>₹<?php echo number_format($grand_total, 2); ?></strong>
                        </div>
                    </div>

                    <div class="delivery-info">
                        <i class="fas fa-truck"></i>
                        <span>Free delivery on orders above ₹300</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Razorpay Configuration
        const razorpayConfig = {
            key: ENV.RAZORPAY.KEY_ID, 
            amount: <?php echo $grand_total * 100; ?>, // Amount in paisa
            currency: 'INR',
            name: 'Delicious Dispatchers',
            description: 'Food Order Payment',
            image: '', // Remove logo for now to avoid connection issues
            order_id: '', // Will be generated by backend
            handler: function (response) {
                // Handle successful payment
                console.log('Payment successful:', response);

                // Clear localStorage cart after successful payment
                localStorage.removeItem('cart');
                localStorage.removeItem('cartTotal');

                // Submit the form with payment details
                const form = document.getElementById('checkoutForm');
                const paymentIdInput = document.createElement('input');
                paymentIdInput.type = 'hidden';
                paymentIdInput.name = 'razorpay_payment_id';
                paymentIdInput.value = response.razorpay_payment_id;
                form.appendChild(paymentIdInput);

                const orderIdInput = document.createElement('input');
                orderIdInput.type = 'hidden';
                orderIdInput.name = 'razorpay_order_id';
                orderIdInput.value = response.razorpay_order_id;
                form.appendChild(orderIdInput);

                const signatureInput = document.createElement('input');
                signatureInput.type = 'hidden';
                signatureInput.name = 'razorpay_signature';
                signatureInput.value = response.razorpay_signature;
                form.appendChild(signatureInput);

                // Add place_order flag
                const placeOrderInput = document.createElement('input');
                placeOrderInput.type = 'hidden';
                placeOrderInput.name = 'place_order';
                placeOrderInput.value = '1';
                form.appendChild(placeOrderInput);

                form.submit();
            },
            prefill: {
                name: '<?php echo addslashes($user['fullname']); ?>',
                email: '<?php echo addslashes($user['email']); ?>',
                contact: '<?php echo addslashes($user['phone'] ?? ''); ?>'
            },
            modal: {
                ondismiss: function() {
                    // Handle when Razorpay modal is closed without payment
                    console.log('Payment cancelled by user');

                    // Save order with failed status
                    saveFailedOrder();
                }
            },
            theme: {
                color: '#ff6b35'
            }
        };

        function saveFailedOrder() {
            const form = document.getElementById('checkoutForm');

            // Add payment method and status for failed order
            const paymentMethodInput = document.createElement('input');
            paymentMethodInput.type = 'hidden';
            paymentMethodInput.name = 'payment_method';
            paymentMethodInput.value = 'razorpay';
            form.appendChild(paymentMethodInput);

            const statusInput = document.createElement('input');
            statusInput.type = 'hidden';
            statusInput.name = 'payment_status';
            statusInput.value = 'failed';
            form.appendChild(statusInput);

            // Add place_order flag
            const placeOrderInput = document.createElement('input');
            placeOrderInput.type = 'hidden';
            placeOrderInput.name = 'place_order';
            placeOrderInput.value = '1';
            form.appendChild(placeOrderInput);

            form.submit();
        }

        function initiatePayment() {
            const form = document.getElementById('checkoutForm');

            // Validate form
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            // Check payment method
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;

            if (paymentMethod === 'cod') {
                // Cash on Delivery - clear cart and submit form directly
                localStorage.removeItem('cart');
                localStorage.removeItem('cartTotal');
                
                const placeOrderInput = document.createElement('input');
                placeOrderInput.type = 'hidden';
                placeOrderInput.name = 'place_order';
                placeOrderInput.value = '1';
                form.appendChild(placeOrderInput);
                form.submit();
            } else {
                // Razorpay payment
                const rzp = new Razorpay(razorpayConfig);
                rzp.open();
            }
        }

        // Store cart data in session for page refresh
        <?php if (!isset($_SESSION['checkout_cart'])): ?>
        fetch('store_cart_session.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                cart: <?php echo json_encode($cart_items); ?>,
                total: <?php echo $grand_total; ?>
            })
        });
        <?php endif; ?>

        // Handle delivery time options
        document.querySelectorAll('input[name="delivery_time"]').forEach(radio => {
            radio.addEventListener('change', function() {
                const scheduledTime = document.querySelector('input[name="scheduled_time"]');
                if (this.value === 'schedule') {
                    scheduledTime.required = true;
                    scheduledTime.disabled = false;
                } else {
                    scheduledTime.required = false;
                    scheduledTime.disabled = true;
                }
            });
        });
    </script>

    <script src="js/script.js"></script>
</body>
</html>