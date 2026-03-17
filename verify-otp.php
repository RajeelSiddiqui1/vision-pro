<?php
session_start();
require_once 'config/db.php';

if (!isset($_SESSION['reset_email'])) {
    header("Location: forgot-password.php");
    exit;
}

// Check if reset_otp column exists, if not add it
try {
    $pdo->query("SELECT reset_otp FROM users LIMIT 1");
} catch (Exception $e) {
    $pdo->exec("ALTER TABLE users ADD COLUMN reset_otp VARCHAR(10) AFTER order_otp");
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = $_POST['otp'];
    $email = $_SESSION['reset_email'];
    
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND reset_otp = ? AND otp_expiry > NOW()");
    $stmt->execute([$email, $otp]);
    $user = $stmt->fetch();
    
    if ($user) {
        $_SESSION['otp_verified'] = true;
        header("Location: reset-password.php");
        exit;
    } else {
        $error = "Invalid or expired OTP.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify OTP - VisionPro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { primary: { 50: '#f0f9ff', 100: '#e0f2fe', 200: '#bae6fd', 300: '#7dd3fc', 400: '#38bdf8', 500: '#0ea5e9', 600: '#0284c7', 700: '#0369a1', 800: '#075985', 900: '#0c4a6e', 950: '#082f49' } } } }
        }
    </script>
    <link href="assets/css/style.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="assets/images/visionpro-logo.png">
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen">
    <div class="max-w-md w-full p-8 bg-white rounded-3xl shadow-2xl border border-gray-100">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-900">Verify OTP</h1>
            <p class="text-gray-500 mt-2">Enter the 6-digit code sent to <?= htmlspecialchars($_SESSION['reset_email']) ?></p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 text-sm font-medium border border-red-100"><?= $error ?></div>
        <?php endif; ?>

        <form action="" method="POST" class="space-y-6">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">6-Digit Code</label>
                <input type="text" name="otp" required maxlength="6" placeholder="000000" 
                       class="w-full px-4 py-3 border rounded-xl outline-none focus:ring-2 focus:ring-primary-500 text-center text-2xl tracking-[1em] font-bold">
            </div>
            <button type="submit" class="w-full text-white font-bold py-4 rounded-xl hover:bg-primary-700 transition-all shadow-lg" style="background-color: #0284c7;">
                Verify OTP
            </button>
        </form>

        <p class="text-center mt-8 text-sm text-gray-500 font-medium">
            Didn't receive the code? <a href="forgot-password.php" class="text-primary-600 hover:underline font-bold">Resend</a>
        </p>
    </div>
</body>
</html>


