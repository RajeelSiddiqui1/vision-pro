<?php
require_once 'config/db.php';
require_once 'includes/security.php';
require_once 'includes/auth_helper.php';

// Prevent caching and redirect if already authenticated
no_cache_headers();
redirect_if_logged_in();

$error = '';
$success = $_SESSION['signup_success'] ?? '';
unset($_SESSION['signup_success']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF Check
    csrf_verify();

    $email = strtolower(trim($_POST['email']));
    $password = $_POST['password']; 

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['full_name'];
        $_SESSION['user_role'] = $user['role'];
        
        // Always set remember token - user stays logged in
        setRememberToken($user['id']);
        
        // Handle custom redirect (e.g. back to checkout)
        $default_redirect = ($user['role'] === 'admin') ? 'admin.php' : 'dashboard.php';
        $redirect = !empty($_POST['redirect']) ? sanitize_redirect($_POST['redirect'], $default_redirect) : $default_redirect;
        
        // Use JavaScript redirect to be 100% sure it works even if headers are blocked
        echo "<script>window.location.href='$redirect';</script>";
        echo "<noscript><meta http-equiv='refresh' content='0;url=$redirect'></noscript>";
        header("Location: $redirect");
        exit;
    } else {
        if (!$user) {
            $error = "Invalid email or password. (User not found)";
        } else {
            $error = "Invalid email or password. (Password mismatch)";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - VisionPro LCD Refurbishing</title>
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
                <h1 class="text-3xl font-bold text-gray-900">Welcome Back</h1>
                <p class="text-gray-500 mt-2">Log in to your wholesale account</p>
            </div>

            <?php if ($error): ?>
                <div class="bg-red-50 text-red-600 p-4 rounded-lg mb-6 text-sm"><?= $error ?></div>
            <?php endif; ?>

            <form action="login.php" method="POST" class="space-y-6">
                <!-- CSRF Protection -->
                <?= csrf_field() ?>
                
                <!-- Preserve redirect URL if present -->
                <input type="hidden" name="redirect" value="<?= htmlspecialchars($_GET['redirect'] ?? '') ?>">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                    <input type="email" name="email" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition-all">
                </div>
                <div>
                    <div class="flex justify-between mb-1">
                        <label class="text-sm font-medium text-gray-700">Password</label>
                        <a href="forgot-password.php" class="text-xs text-primary-600 hover:underline">Forgot password?</a>
                    </div>
                    <input type="password" name="password" required class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-primary-500 focus:border-transparent outline-none transition-all">
                </div>
                <button type="submit" class="w-full bg-primary-600 text-white font-bold py-3 rounded-xl hover:bg-primary-700 transition-all shadow-lg shadow-primary-200">Login</button>
            </form>

            <p class="text-center mt-8 text-sm text-gray-600">
                Don't have an account? <a href="signup.php" class="text-primary-600 font-semibold hover:underline">Sign up now</a>
            </p>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>


