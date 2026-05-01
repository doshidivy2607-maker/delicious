<?php
include 'includes/db_config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['fullname'];
            $_SESSION['user_email'] = $user['email'];
            // Redirect admin to admin panel
            if ($user['email'] === 'doshidivy2607@gmail.com') {
                header('Location: admin.php');
            } else {
                header('Location: user-dashboard.php');
            }
            exit();
        } else {
            $error = 'Invalid email or password';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Delicious Dispatchers</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/checkbox-fix.css">
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
                    <i class="fas fa-sign-in-alt"></i>
                </div>
                <div class="floating-element el-1"><i class="fas fa-check-circle"></i></div>
                <div class="floating-element el-2"><i class="fas fa-star"></i></div>
                <div class="floating-element el-3"><i class="fas fa-heart"></i></div>
            </div>
            <h2>Welcome Back!</h2>
            <p>Login to access your tiffin service management dashboard and manage your business efficiently.</p>
            <div class="auth-features">
                <div class="auth-feature">
                    <i class="fas fa-shield-alt"></i>
                    <span>Secure Login</span>
                </div>
                <div class="auth-feature">
                    <i class="fas fa-clock"></i>
                    <span>24/7 Access</span>
                </div>
                <div class="auth-feature">
                    <i class="fas fa-mobile-alt"></i>
                    <span>Multi-device</span>
                </div>
            </div>
        </div>
        <div class="auth-right animate-fadeInRight">
            <div class="auth-form-container glass-effect">
                <div class="auth-header">
                    <h2>Sign In</h2>
                    <p>Enter your credentials to continue</p>
                </div>
                
                <?php if ($error): ?>
                    <div class="alert alert-error animate-shake">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($_GET['registered'])): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        Registration successful! Please login.
                    </div>
                <?php endif; ?>

                <form method="POST" class="auth-form" id="loginForm">
                    <div class="form-group">
                        <label><i class="fas fa-envelope"></i> Email Address</label>
                        <input type="email" name="email" placeholder="Enter your email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>
                    <div class="form-group">
                        <label><i class="fas fa-lock"></i> Password</label>
                        <div class="password-input">
                            <input type="password" name="password" id="password" placeholder="Enter your password" required>
                            <button type="button" class="toggle-password" onclick="togglePassword('password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="form-options">
                        <label class="checkbox-container">
                            <input type="checkbox" name="remember">
                            <span class="checkmark"></span>
                            Remember me
                        </label>
                        <a href="#" class="forgot-link">Forgot Password?</a>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block btn-lg">
                        <i class="fas fa-sign-in-alt"></i> Sign In
                    </button>
                </form>

                <div class="auth-divider">
                    <span>Or continue with</span>
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
                    <p>Don't have an account? <a href="register.php">Create Account</a></p>
                </div>
            </div>
        </div>
    </div>

    <script src="js/script.js"></script>
</body>
</html>