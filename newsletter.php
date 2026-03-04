<?php
// newsletter.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Simulate sending email to Admin
        $admin_email = "admin@visionprolcd.com";
        $subject = "New Newsletter Subscription";
        $message = "A new user wants to subscribe to updates: $email";
        
        // Log the "email"
        $log_entry = "[" . date('Y-m-d H:i:s') . "] Admin Notification: $message\n";
        file_put_contents('email_log.txt', $log_entry, FILE_APPEND);
        
        // Redirect back with success param
        header("Location: " . $_SERVER['HTTP_REFERER'] . "?newsletter=success");
        exit;
    }
}
header("Location: index.php");
?>

