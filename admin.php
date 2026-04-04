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

// Redirect to appropriate admin pages
if (isset($_GET['section'])) {
    $section = $_GET['section'];
    if ($section === 'users') {
        header('Location: users.php');
        exit();
    } elseif ($section === 'orders') {
        header('Location: savedorder.php');
        exit();
    } elseif ($section === 'menu') {
        header('Location: editmenu.php');
        exit();
    }
}

// Default redirect to users page
header('Location: users.php');
exit();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirecting...</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            padding: 50px;
            background: #f5f5f5;
        }
        .redirect-message {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: inline-block;
        }
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #ff6b35;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="redirect-message">
        <div class="spinner"></div>
        <h2>Redirecting to Admin Panel...</h2>
        <p>If you're not redirected automatically, <a href="users.php">click here</a>.</p>
    </div>

    <script>
        // Auto redirect after 2 seconds
        setTimeout(function() {
            window.location.href = 'users.php';
        }, 2000);
    </script>
</body>
</html>