<?php
include 'includes/db_config.php';

// Create subscriptions table if not exists
$sql = "CREATE TABLE IF NOT EXISTS subscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    plan_name VARCHAR(50) NOT NULL,
    plan_type VARCHAR(20) NOT NULL, -- 'monthly' or 'yearly'
    amount DECIMAL(10, 2) NOT NULL,
    razorpay_subscription_id VARCHAR(100),
    razorpay_payment_id VARCHAR(100),
    status VARCHAR(50) DEFAULT 'pending', -- pending, active, cancelled, expired
    start_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    end_date TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)";
$pdo->exec($sql);

// Handle subscription creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'create_subscription') {
        // Check if user is logged in
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Please login first']);
            exit;
        }

        $user_id = $_SESSION['user_id'];
        $plan_name = $_POST['plan_name'];
        $plan_type = $_POST['plan_type']; // 'monthly' or 'yearly'

        // Define plan details
        $plans = [
            'starter' => [
                'monthly' => ['amount' => 999, 'name' => 'Starter Monthly'],
                'yearly' => ['amount' => 799, 'name' => 'Starter Yearly']
            ],
            'professional' => [
                'monthly' => ['amount' => 2499, 'name' => 'Professional Monthly'],
                'yearly' => ['amount' => 1999, 'name' => 'Professional Yearly']
            ],
            'enterprise' => [
                'monthly' => ['amount' => 4999, 'name' => 'Enterprise Monthly'],
                'yearly' => ['amount' => 3999, 'name' => 'Enterprise Yearly']
            ]
        ];

        if (!isset($plans[$plan_name][$plan_type])) {
            echo json_encode(['success' => false, 'message' => 'Invalid plan']);
            exit;
        }

        $plan_details = $plans[$plan_name][$plan_type];
        $amount = $plan_details['amount'] * 100; // Razorpay expects amount in paisa

        // Razorpay API integration
        $razorpay_key_id = 'rzp_test_SZOTNbBPCFlatb'; // Use your test key
        $razorpay_key_secret = 'your_razorpay_secret_key'; // Replace with your secret key

        // Create subscription data for Razorpay
        $subscription_data = [
            'plan_id' => '', // You'll need to create plans in Razorpay dashboard
            'total_count' => $plan_type === 'monthly' ? 12 : 1, // 12 months or 1 year
            'quantity' => 1,
            'customer_notify' => 1,
            'start_at' => time() + 86400, // Start tomorrow
            'expire_by' => time() + (365 * 24 * 60 * 60), // Expire in 1 year
            'addons' => [],
            'notes' => [
                'user_id' => $user_id,
                'plan_name' => $plan_name,
                'plan_type' => $plan_type
            ]
        ];

        // For demo purposes, we'll simulate the subscription creation
        // In production, you would use Razorpay API to create the subscription
        $subscription_id = 'sub_' . uniqid();

        // Save subscription to database
        $stmt = $pdo->prepare("INSERT INTO subscriptions (user_id, plan_name, plan_type, amount, razorpay_subscription_id, status) VALUES (?, ?, ?, ?, ?, 'pending')");
        $stmt->execute([$user_id, $plan_name, $plan_type, $plan_details['amount'], $subscription_id]);

        echo json_encode([
            'success' => true,
            'subscription_id' => $subscription_id,
            'amount' => $amount,
            'plan_name' => $plan_details['name']
        ]);

    } elseif ($action === 'verify_payment') {
        $razorpay_payment_id = $_POST['razorpay_payment_id'];
        $razorpay_subscription_id = $_POST['razorpay_subscription_id'];
        $razorpay_signature = $_POST['razorpay_signature'];

        // Verify payment signature (simplified for demo)
        // In production, use Razorpay SDK to verify signature
        $generated_signature = hash_hmac('sha256', $razorpay_payment_id . '|' . $razorpay_subscription_id, 'your_razorpay_secret_key');

        if ($generated_signature === $razorpay_signature) {
            // Update subscription status
            $stmt = $pdo->prepare("UPDATE subscriptions SET status = 'active', razorpay_payment_id = ?, end_date = DATE_ADD(start_date, INTERVAL 1 YEAR) WHERE razorpay_subscription_id = ?");
            $stmt->execute([$razorpay_payment_id, $razorpay_subscription_id]);

            echo json_encode(['success' => true, 'message' => 'Subscription activated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Payment verification failed']);
        }
    }
}
?>