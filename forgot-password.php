<?php
session_start();
require_once 'config/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = htmlspecialchars($_POST['email']);
    
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user) {
        $otp = sprintf("%06d", mt_rand(1, 999999));
        $expiry = date("Y-m-d H:i:s", strtotime("+15 minutes"));
        
        $update = $pdo->prepare("UPDATE users SET reset_otp = ?, otp_expiry = ? WHERE id = ?");
        $update->execute([$otp, $expiry, $user['id']]);
        
        $_SESSION['reset_email'] = $email;
        
        // Get user name for email
        $name_stmt = $pdo->prepare("SELECT full_name FROM users WHERE id = ?");
        $name_stmt->execute([$user['id']]);
        $user_name = $name_stmt->fetch()['full_name'];
        
        // Send real OTP email
        require_once 'includes/email_helper.php';
        send_password_reset_otp($email, $user_name, $otp);
        
        $success = "A 6-digit OTP has been sent to your email.";
        header("refresh:3;url=verify-otp.php");
    } else {
        $error = "No account found with that email address.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password - VisionPro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">
    <div class="max-w-md w-full p-8 bg-white rounded-3xl shadow-2xl border border-gray-100">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Forgot Password?</h1>
            <p class="text-gray-500 mt-2">Enter your email and we'll send you an OTP to reset your password.</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 text-sm font-medium border border-red-100"><?= $error ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="bg-green-50 text-green-600 p-4 rounded-xl mb-6 text-sm font-medium border border-green-100"><?= $success ?></div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Email Address</label>
                <input type="email" name="email" required placeholder="name@company.com" 
                       class="w-full px-4 py-3 border rounded-xl outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            <button type="submit" class="w-full bg-primary-600 text-white font-bold py-4 rounded-xl hover:bg-primary-700 transition-all shadow-lg shadow-primary-100">
                Send OTP
            </button>
        </form>

        <p class="text-center mt-8 text-sm text-gray-500 font-medium">
            Remembered your password? <a href="login.php" class="text-primary-600 hover:underline">Login</a>
        </p>
    </div>
</body>
</html>
