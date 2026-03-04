<?php
session_start();
require_once 'config/db.php';
require_once 'includes/email_helper.php';

// Check if there's a pending signup
if (!isset($_SESSION['pending_signup_user_id'])) {
    header("Location: signup.php");
    exit;
}

$user_id = $_SESSION['pending_signup_user_id'];
$email = $_SESSION['pending_signup_email'];

// Get user info
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

if (!$user) {
    unset($_SESSION['pending_signup_user_id']);
    unset($_SESSION['pending_signup_email']);
    header("Location: signup.php");
    exit;
}

// Check if already verified
if ($user['is_verified']) {
    // Already verified, redirect to login
    unset($_SESSION['pending_signup_user_id']);
    unset($_SESSION['pending_signup_email']);
    header("Location: login.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['resend'])) {
        // Resend OTP
        $new_otp = sprintf("%06d", mt_rand(1, 999999));
        $new_expiry = date("Y-m-d H:i:s", strtotime("+15 minutes"));
        $otp_stmt = $pdo->prepare("UPDATE users SET order_otp = ?, otp_expiry = ? WHERE id = ?");
        $otp_stmt->execute([$new_otp, $new_expiry, $user_id]);
        
        // Get full name
        $name_stmt = $pdo->prepare("SELECT full_name FROM users WHERE id = ?");
        $name_stmt->execute([$user_id]);
        $full_name = $name_stmt->fetchColumn();
        
        // Send new OTP email
        $otp_body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
            <div style='background: #0284c7; padding: 20px; text-align: center;'>
                <h1 style='color: white; margin: 0;'>VisionPro LCD</h1>
            </div>
            <div style='padding: 20px; background: #f9fafb;'>
                <h2 style='color: #111827;'>Verify Your Email</h2>
                <p>Hi $full_name,</p>
                <p>Your new verification code:</p>
                <div style='background: white; padding: 20px; text-align: center; font-size: 32px; letter-spacing: 8px; margin: 20px 0;'>
                    <strong>$new_otp</strong>
                </div>
                <p>This code expires in 15 minutes.</p>
            </div>
        </div>
        ";
        
        send_email($email, $full_name, "Verify Your Email - VisionPro LCD", $otp_body);
        $success = "New OTP sent to your email!";
    } else {
        $otp = $_POST['otp'];
        
        // Verify OTP
        if ($otp == $user['order_otp'] && strtotime($user['otp_expiry']) > time()) {
            // OTP valid - mark user as verified
            $update_stmt = $pdo->prepare("UPDATE users SET is_verified = 1, order_otp = NULL, otp_expiry = NULL WHERE id = ?");
            $update_stmt->execute([$user_id]);
            
            // Clear pending signup session
            unset($_SESSION['pending_signup_user_id']);
            unset($_SESSION['pending_signup_email']);
            
            // Set success message in session for login page
            $_SESSION['signup_success'] = "Email verified successfully! You can now login.";
            
            // Redirect to login
            header("Location: login.php");
            exit;
        } else {
            $error = "Invalid or expired OTP. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Email - VisionPro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { primary: { 50: '#f0f9ff', 100: '#e0f2fe', 200: '#bae6fd', 300: '#7dd3fc', 400: '#38bdf8', 500: '#0ea5e9', 600: '#0284c7', 700: '#0369a1', 800: '#075985', 900: '#0c4a6e', 950: '#082f49' } } } }
        }
    </script>
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body class="bg-gray-50 flex flex-col min-h-screen">
    <?php include 'includes/header.php'; ?>

    <main class="flex-grow flex items-center justify-center py-20 px-4">
        <div class="max-w-md w-full bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-primary-100 text-primary-600 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-gray-900">Verify Your Email</h1>
                <p class="text-gray-500 mt-2">Enter the OTP sent to your email</p>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 font-medium"><?= $error ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="bg-green-50 text-green-600 p-4 rounded-xl mb-6 font-medium"><?= $success ?></div>
            <?php endif; ?>

            <div class="bg-gray-50 p-4 rounded-xl mb-6 text-center">
                <p class="text-sm text-gray-500">Sent to: <?= htmlspecialchars($email) ?></p>
            </div>

            <form action="signup-verify-otp.php" method="POST" class="space-y-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2 text-center">Enter 6-digit OTP</label>
                    <input type="text" name="otp" maxlength="6" minlength="6" pattern="[0-9]{6}" required 
                           class="w-full text-center text-3xl tracking-[0.5em] font-bold py-4 border-2 border-gray-200 rounded-xl outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-200"
                           placeholder="000000" autocomplete="off">
                </div>

                <button type="submit" class="w-full bg-primary-600 text-white font-bold py-4 rounded-xl hover:bg-primary-700 transition-all shadow-lg shadow-primary-200">
                    Verify & Create Account
                </button>
            </form>

            <form action="signup-verify-otp.php" method="POST" class="mt-4">
                <button type="submit" name="resend" class="w-full text-primary-600 font-medium hover:underline">
                    Didn't receive OTP? Resend
                </button>
            </form>

            <div class="mt-6 pt-6 border-t border-gray-100 text-center">
                <a href="signup.php" class="text-gray-500 hover:text-gray-700 text-sm">
                    ← Back to Sign Up
                </a>
            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>


