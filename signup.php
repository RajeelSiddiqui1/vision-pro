<?php
require_once 'config/db.php';
require_once 'includes/security.php';
require_once 'includes/email_helper.php';

// Prevent caching and redirect if already authenticated
no_cache_headers();
redirect_if_logged_in();

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF Check
    csrf_verify();
    $full_name = htmlspecialchars($_POST['full_name']);
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $business_name = htmlspecialchars($_POST['business_name']);

    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        // Check if email exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "Email already registered.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Generate OTP
            $otp = sprintf("%06d", mt_rand(1, 999999));
            $otp_expiry = date("Y-m-d H:i:s", strtotime("+15 minutes"));
            
            $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password, business_name, order_otp, otp_expiry, is_verified) VALUES (?, ?, ?, ?, ?, ?, 0)");
            if ($stmt->execute([$full_name, $email, $hashed_password, $business_name, $otp, $otp_expiry])) {
                $user_id = $pdo->lastInsertId();
                
                // Store user_id in session for OTP verification
                $_SESSION['pending_signup_user_id'] = $user_id;
                $_SESSION['pending_signup_email'] = $email;
                
                // Send OTP email
                $otp_body = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                    <div style='background: #0284c7; padding: 20px; text-align: center;'>
                        <h1 style='color: white; margin: 0;'>VisionPro LCD</h1>
                    </div>
                    <div style='padding: 20px; background: #f9fafb;'>
                        <h2 style='color: #111827;'>Verify Your Email</h2>
                        <p>Hi $full_name,</p>
                        <p>Thank you for signing up! Your verification code:</p>
                        <div style='background: white; padding: 20px; text-align: center; font-size: 32px; letter-spacing: 8px; margin: 20px 0;'>
                            <strong>$otp</strong>
                        </div>
                        <p>This code expires in 15 minutes.</p>
                    </div>
                </div>
                ";
                
                send_email($email, $full_name, "Verify Your Email - VisionPro LCD", $otp_body);
                
                // Redirect to OTP verification page
                header("Location: signup-verify-otp.php");
                exit;
            } else {
                $error = "Something went wrong. Please try again.";
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
    <title>Sign Up - VisionPro LCD Refurbishing</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { primary: { 50: '#f0f9ff', 100: '#e0f2fe', 200: '#bae6fd', 300: '#7dd3fc', 400: '#38bdf8', 500: '#0ea5e9', 600: '#0284c7', 700: '#0369a1', 800: '#075985', 900: '#0c4a6e', 950: '#082f49' } } } }
        }
    </script>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="assets/images/visionpro-logo.png">
    <link rel="apple-touch-icon" href="assets/images/visionpro-logo.png">
</head>
<body class="bg-gray-50 flex flex-col min-h-screen">
    <?php include 'includes/header.php'; ?>

    <main class="flex-grow flex items-center justify-center py-20 px-4">
        <div class="max-w-md w-full bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900">Create Account</h1>
                <p class="text-gray-500 mt-2">Join Mississauga's leading wholesale network</p>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-50 text-red-600 p-4 rounded-lg mb-6 text-sm"><?= $error ?></div>
            <?php endif; ?>

            <form action="signup.php" method="POST" class="space-y-5">
                <!-- CSRF Protection -->
                <?= csrf_field() ?>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                    <input type="text" name="full_name" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Business Name (Optional)</label>
                    <input type="text" name="business_name" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input type="email" name="email" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input type="password" name="password" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                    <input type="password" name="confirm_password" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition-all">
                </div>
                <div class="flex items-center">
                    <input type="checkbox" required class="w-4 h-4 text-primary-600 border-gray-300 rounded focus:ring-primary-500">
                    <label class="ml-2 text-sm text-gray-600">I agree to the <a href="terms.php" class="text-primary-600 hover:underline">Terms & Conditions</a></label>
                </div>
                <button type="submit" class="w-full bg-primary-600 text-white font-bold py-3 rounded-xl hover:bg-primary-700 transition-all shadow-lg shadow-primary-200">Register</button>
            </form>

            <p class="text-center mt-8 text-sm text-gray-600">
                Already have an account? <a href="login.php" class="text-primary-600 font-semibold hover:underline">Login here</a>
            </p>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>


