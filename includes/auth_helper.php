<?php
/**
 * Authentication Helper - Handles remember me token-based login
 * Include this file at the top of any page that requires authentication
 * 
 * Token is automatically refreshed each time user visits to extend expiry
 */

// Generate a secure remember token
function generateRememberToken() {
    return bin2hex(random_bytes(32));
}

// Check and validate remember cookie
function checkRememberCookie() {
    global $pdo;
    
    // Skip if already logged in
    if (isset($_SESSION['user_id'])) {
        // Refresh token expiry if logged in via remember cookie
        if (isset($_COOKIE['remember_token'])) {
            extendRememberToken($_SESSION['user_id']);
        }
        return true;
    }
    
    // Check for remember cookie
    if (isset($_COOKIE['remember_token'])) {
        $token = $_COOKIE['remember_token'];
        
        // Find user with this token
        $stmt = $pdo->prepare("SELECT * FROM users WHERE remember_token = ?");
        $stmt->execute([$token]);
        $user = $stmt->fetch();
        
        if ($user) {
            // Check if token is expired
            if ($user['token_expiry'] && strtotime($user['token_expiry']) > time()) {
                // Token is valid - log the user in
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_role'] = $user['role'];
                
                // Extend token expiry by 30 days from now
                extendRememberToken($user['id']);
                
                return true;
            } else {
                // Token expired - clear it
                clearRememberToken($user['id']);
            }
        }
    }
    
    return false;
}

// Extend remember token expiry
function extendRememberToken($user_id) {
    global $pdo;
    
    // Get current token
    $stmt = $pdo->prepare("SELECT remember_token FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $token = $stmt->fetchColumn();
    
    if ($token) {
        $new_expiry = date('Y-m-d H:i:s', strtotime('+30 days'));
        
        // Update database
        $stmt = $pdo->prepare("UPDATE users SET token_expiry = ? WHERE id = ?");
        $stmt->execute([$new_expiry, $user_id]);
        
        // Extend cookie (30 days from now)
        setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/', '', false, true);
    }
}

// Set remember cookie and database token (always called on login)
function setRememberToken($user_id) {
    global $pdo;
    
    $token = generateRememberToken();
    $expiry = date('Y-m-d H:i:s', strtotime('+30 days'));
    
    // Store in database
    $stmt = $pdo->prepare("UPDATE users SET remember_token = ?, token_expiry = ? WHERE id = ?");
    $stmt->execute([$token, $expiry, $user_id]);
    
    // Set cookie (30 days)
    setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/', '', false, true);
    
    return true;
}

// Clear remember cookie and database token
function clearRememberToken($user_id) {
    global $pdo;
    
    // Clear from database
    $stmt = $pdo->prepare("UPDATE users SET remember_token = NULL, token_expiry = NULL WHERE id = ?");
    $stmt->execute([$user_id]);
    
    // Clear cookie
    setcookie('remember_token', '', time() - 3600, '/');
    
    return true;
}

// Auto-login check - call this at the top of protected pages
function requireAuth() {
    // Check remember cookie first
    checkRememberCookie();
    
    // Now check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        $current_url = urlencode($_SERVER['REQUEST_URI']);
        header("Location: login.php?redirect=$current_url");
        exit;
    }
}

// Auto-login but don't require auth (for optional login on public pages)
function optionalAuth() {
    checkRememberCookie();
}


