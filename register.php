<?php
include 'includes/db_config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullname = trim($_POST['fullname']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validation
    if (empty($fullname) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Please fill in all required fields';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } else {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            $error = 'Email already registered';
        } else {
            // Insert user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (fullname, email, phone, password) VALUES (?, ?, ?, ?)");
            
            if ($stmt->execute([$fullname, $email, $phone, $hashed_password])) {
                header('Location: login.php?registered=1');
                exit();
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Delicious Dispatchers</title>
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="auth-page">
    <!-- Animated Background -->
    <div class="animated-bg">
        <div class="floating-shapes">
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>

    <div class="auth-container">
        <div class="auth-left animate-fadeInLeft">
            <a href="index.php" class="auth-logo">
                <i class="fas fa-utensils"></i>
                <span>Delicious<span class="highlight">Dispatchers</span></span>
            </a>
            <div class="auth-illustration">
                <div class="illustration-circle glass-effect">
                    <i class="fas fa-user-plus"></i>
                </div>
                <div class="floating-element el-1"><i class="fas fa-rocket"></i></div>
                <div class="floating-element el-2"><i class="fas fa-gift"></i></div>
                <div class="floating-element el-3"><i class="fas fa-crown"></i></div>
            </div>
            <h2>Join Us Today!</h2>
            <p>Create your account and start managing your tiffin service business with the most powerful platform.</p>
            <div class="auth-benefits">
                <div class="benefit-item">
                    <i class="fas fa-check-circle"></i>
                    <span>14-day free trial</span>
                </div>
                <div class="benefit-item">
                    <i class="fas fa-check-circle"></i>
                    <span>No credit card required</span>
                </div>
                <div class="benefit-item">
                    <i class="fas fa-check-circle"></i>
                    <span>Cancel anytime</span>
                </div>
                <div class="benefit-item">
                    <i class="fas fa-check-circle"></i>
                    <span>24/7 customer support</span>
                </div>
            </div>
        </div>
        <div class="auth-right animate-fadeInRight">
            <div class="auth-form-container glass-effect">
                <div class="auth-header">
                    <h2>Create Account</h2>
                    <p>Fill in your details to get started</p>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-error animate-shake">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="auth-form" id="registerForm">
                    <div class="form-group">
                        <label><i class="fas fa-user"></i> Full Name *</label>
                        <input type="text" name="fullname" placeholder="Enter your full name" required value="<?php echo isset($_POST['fullname']) ? htmlspecialchars($_POST['fullname']) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> Email Address *</label>
                        <input type="email" name="email" placeholder="Enter your email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-phone"></i> Phone Number</label>
                        <input type="tel" name="phone" placeholder="Enter your phone number" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label><i class="fas fa-lock"></i> Password *</label>
                            <div class="password-input">
                                <input type="password" name="password" id="password" placeholder="Min. 6 characters" required>
                                <button type="button" class="toggle-password" onclick="togglePassword('password')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label><i class="fas fa-lock"></i> Confirm Password *</label>
                            <div class="password-input">
                                <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm password" required>
                                <button type="button" class="toggle-password" onclick="togglePassword('confirm_password')">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="password-strength" id="passwordStrength">
                        <div class="strength-bar">
                            <div class="strength-fill"></div>
                        </div>
                        <span class="strength-text">Password Strength</span>
                    </div>
                    <div class="form-options">
                        <label class="checkbox-container">
                            <input type="checkbox" name="terms" required>
                            <span class="checkmark"></span>
                            I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>
                        </label>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block btn-lg">
                        <i class="fas fa-user-plus"></i> Create Account
                    </button>
                </form>

                <div class="auth-divider">
                    <span>Or sign up with</span>
                </div>

                <div class="social-auth">
                    <button class="social-btn google">
                        <i class="fab fa-google"></i> Google
                    </button>
                    <button class="social-btn facebook">
                        <i class="fab fa-facebook-f"></i> Facebook
                    </button>
                </div>

                <div class="auth-footer">
                    <p>Already have an account? <a href="login.php">Sign In</a></p>
                </div>
            </div>
        </div>
    </div>

    <script src="js/script.js"></script>
</body>
</html>