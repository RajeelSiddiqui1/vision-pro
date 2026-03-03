<?php
/**
 * Email Helper Functions
 * Uses PHPMailer for sending emails
 */

require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

/**
 * Send an email using PHPMailer
 * 
 * @param string $to Recipient email
 * @param string $to_name Recipient name
 * @param string $subject Email subject
 * @param string $body Email body (HTML)
 * @param string|null $alt_body Plain text alternative body
 * @return bool True if sent successfully, false otherwise
 */
function send_email($to, $to_name, $subject, $body, $alt_body = null) {
    $config = require __DIR__ . '/../config/email.php';
    
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->SMTPDebug = $config['debug'];                      // Enable verbose debug output
        $mail->isSMTP();                                            // Send using SMTP
        $mail->Host       = $config['host'];                       // Set the SMTP server
        $mail->SMTPAuth   = true;                                 // Enable SMTP authentication
        $mail->Username   = $config['username'];                   // SMTP username
        $mail->Password   = $config['password'];                   // SMTP password
        $mail->SMTPSecure = $config['encryption'];                 // Enable TLS encryption
        $mail->Port       = $config['port'];                       // TCP port to connect to
        
        // Recipients
        $mail->setFrom($config['from_email'], $config['from_name']);
        $mail->addAddress($to, $to_name);                          // Add a recipient
        $mail->addReplyTo($config['reply_to'], $config['reply_name']);
        
        // Content
        $mail->isHTML(true);                                       // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->AltBody = $alt_body ?? strip_tags($body);
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        // Log error for debugging
        $error_msg = "[" . date('Y-m-d H:i:s') . "] Email failed to: $to | Error: {$mail->ErrorInfo}\n";
        file_put_contents(__DIR__ . '/../email_errors.log', $error_msg, FILE_APPEND);
        return false;
    }
}

/**
 * Send order confirmation email
 */
function send_order_confirmation($order_id, $user_email, $user_name, $order_details) {
    global $pdo;
    
    $subject = "Order Confirmation - Order #$order_id";
    
    // Get order items
    $items_stmt = $pdo->prepare("SELECT oi.*, p.name as product_name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
    $items_stmt->execute([$order_id]);
    $order_items = $items_stmt->fetchAll();
    
    // Get order info
    $order_stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
    $order_stmt->execute([$order_id]);
    $order = $order_stmt->fetch();
    
    // Build items HTML
    $items_html = '';
    foreach ($order_items as $item) {
        $items_html .= "<tr>";
        $items_html .= "<td style='padding: 12px; border-bottom: 1px solid #e5e7eb;'>{$item['product_name']}</td>";
        $items_html .= "<td style='padding: 12px; border-bottom: 1px solid #e5e7eb; text-align: center;'>{$item['quantity']}</td>";
        $items_html .= "<td style='padding: 12px; border-bottom: 1px solid #e5e7eb; text-align: right;'>$" . number_format($item['price'], 2) . "</td>";
        $items_html .= "<td style='padding: 12px; border-bottom: 1px solid #e5e7eb; text-align: right; font-weight: bold;'>$" . number_format($item['quantity'] * $item['price'], 2) . "</td>";
        $items_html .= "</tr>";
    }
    
    // Calculate totals
    $subtotal = $order['total_amount'] / 1.13;
    $tax = $order['total_amount'] - $subtotal;
    
    $body = "
    <div style='font-family: Arial, sans-serif; max-width: 700px; margin: 0 auto;'>
        <div style='background: #0284c7; padding: 20px; text-align: center;'>
            <h1 style='color: white; margin: 0;'>VisionPro LCD</h1>
        </div>
        <div style='padding: 20px; background: #f9fafb;'>
            <h2 style='color: #111827;'>Thank you for your order!</h2>
            <p>Hi $user_name,</p>
            <p>We've received your order <strong>#$order_id</strong> and are preparing it for shipment.</p>
            
            <h3 style='margin-top: 24px; color: #111827;'>Order Details</h3>
            <table style='width: 100%; border-collapse: collapse; margin-top: 12px; background: white; border-radius: 8px; overflow: hidden;'>
                <thead>
                    <tr style='background: #f3f4f6;'>
                        <th style='padding: 12px; text-align: left;'>Product</th>
                        <th style='padding: 12px; text-align: center;'>Qty</th>
                        <th style='padding: 12px; text-align: right;'>Price</th>
                        <th style='padding: 12px; text-align: right;'>Total</th>
                    </tr>
                </thead>
                <tbody>
                    $items_html
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan='3' style='padding: 12px; text-align: right; font-weight: bold;'>Subtotal:</td>
                        <td style='padding: 12px; text-align: right;'>$" . number_format($subtotal, 2) . "</td>
                    </tr>
                    <tr>
                        <td colspan='3' style='padding: 12px; text-align: right; font-weight: bold;'>Tax (13%):</td>
                        <td style='padding: 12px; text-align: right;'>$" . number_format($tax, 2) . "</td>
                    </tr>
                    <tr style='background: #f3f4f6;'>
                        <td colspan='3' style='padding: 12px; text-align: right; font-weight: bold; font-size: 18px;'>Total:</td>
                        <td style='padding: 12px; text-align: right; font-weight: bold; font-size: 18px;'>$" . number_format($order['total_amount'], 2) . "</td>
                    </tr>
                </tfoot>
            </table>
            
            <p style='margin-top: 20px;'><strong>Status:</strong> {$order_details['status']}</p>
            <p>We'll notify you when your order ships.</p>
        </div>
        <div style='padding: 20px; text-align: center; color: #6b7280; font-size: 12px;'>
            <p>&copy; " . date('Y') . " VisionPro LCD. All rights reserved.</p>
        </div>
    </div>
    ";
    
    return send_email($user_email, $user_name, $subject, $body);
}

/**
 * Send order status update email
 */
function send_order_status_update($order_id, $user_email, $user_name, $status, $tracking = null) {
    $subject = "Order Update #$order_id - " . ucfirst($status);
    
    $tracking_html = $tracking ? "<p><strong>Tracking Number:</strong> $tracking</p>" : "";
    
    $body = "
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
        <div style='background: #0284c7; padding: 20px; text-align: center;'>
            <h1 style='color: white; margin: 0;'>VisionPro LCD</h1>
        </div>
        <div style='padding: 20px; background: #f9fafb;'>
            <h2 style='color: #111827;'>Order Update</h2>
            <p>Hi $user_name,</p>
            <p>Your order <strong>#$order_id</strong> status has been updated to: <strong>" . ucfirst($status) . "</strong></p>
            $tracking_html
            <p>If you have any questions, please contact us.</p>
        </div>
        <div style='padding: 20px; text-align: center; color: #6b7280; font-size: 12px;'>
            <p>&copy; " . date('Y') . " VisionPro LCD. All rights reserved.</p>
        </div>
    </div>
    ";
    
    return send_email($user_email, $user_name, $subject, $body);
}

/**
 * Send password reset OTP email
 */
function send_password_reset_otp($user_email, $user_name, $otp) {
    $subject = "Password Reset OTP - VisionPro LCD";
    
    $body = "
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
        <div style='background: #0284c7; padding: 20px; text-align: center;'>
            <h1 style='color: white; margin: 0;'>VisionPro LCD</h1>
        </div>
        <div style='padding: 20px; background: #f9fafb;'>
            <h2 style='color: #111827;'>Password Reset</h2>
            <p>Hi $user_name,</p>
            <p>Your OTP for password reset is:</p>
            <div style='background: white; padding: 20px; text-align: center; font-size: 32px; letter-spacing: 8px; margin: 20px 0;'>
                <strong>$otp</strong>
            </div>
            <p>This OTP expires in 15 minutes.</p>
            <p>If you didn't request this, please ignore this email.</p>
        </div>
        <div style='padding: 20px; text-align: center; color: #6b7280; font-size: 12px;'>
            <p>&copy; " . date('Y') . " VisionPro LCD. All rights reserved.</p>
        </div>
    </div>
    ";
    
    return send_email($user_email, $user_name, $subject, $body);
}

/**
 * Send contact form submission email to admin
 */
function send_contact_form($name, $email, $subject, $message) {
    $admin_email = 'info@visionprolcd.com';
    $full_subject = "Contact Form: $subject";
    
    $body = "
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
        <div style='background: #0284c7; padding: 20px; text-align: center;'>
            <h1 style='color: white; margin: 0;'>VisionPro LCD</h1>
        </div>
        <div style='padding: 20px; background: #f9fafb;'>
            <h2 style='color: #111827;'>New Contact Form Submission</h2>
            <p><strong>Name:</strong> $name</p>
            <p><strong>Email:</strong> $email</p>
            <p><strong>Subject:</strong> $subject</p>
            <div style='background: white; padding: 15px; border-radius: 8px; margin-top: 15px;'>
                <p style='margin: 0;'>$message</p>
            </div>
        </div>
    </div>
    ";
    
    return send_email($admin_email, 'VisionPro Admin', $full_subject, $body);
}

/**
 * Send appointment confirmation email to customer
 */
function send_appointment_confirmation($appointment_id, $customer_email, $customer_name, $appointment_details) {
    $subject = "Appointment Confirmed - #$appointment_id";
    
    $body = "
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
        <div style='background: #0284c7; padding: 20px; text-align: center;'>
            <h1 style='color: white; margin: 0;'>VisionPro LCD</h1>
        </div>
        <div style='padding: 20px; background: #f9fafb;'>
            <h2 style='color: #111827;'>Your Appointment is Confirmed!</h2>
            <p>Hi $customer_name,</p>
            <p>Great news! Your repair appointment has been <strong>confirmed</strong>.</p>
            
            <div style='background: white; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                <p style='margin: 0;'><strong>Appointment ID:</strong> #$appointment_id</p>
                <p style='margin: 10px 0 0;'><strong>Service:</strong> {$appointment_details['service_name']}</p>
                <p style='margin: 10px 0 0;'><strong>Price:</strong> $" . number_format($appointment_details['service_price'], 2) . "</p>
                <p style='margin: 10px 0 0;'><strong>Date:</strong> " . date('F d, Y', strtotime($appointment_details['appointment_date'])) . "</p>
                <p style='margin: 10px 0 0;'><strong>Time:</strong> " . date('h:i A', strtotime($appointment_details['appointment_time'])) . "</p>
                <p style='margin: 10px 0 0;'><strong>Device:</strong> {$appointment_details['device_model']}</p>
            </div>
            
            <p>Please arrive 10 minutes before your scheduled time. Bring your device along with any relevant accessories.</p>
            <p>If you need to reschedule or cancel, please contact us at least 24 hours in advance.</p>
            
            <p style='margin-top: 20px;'>Thank you for choosing VisionPro LCD!</p>
        </div>
        <div style='padding: 20px; text-align: center; color: #6b7280; font-size: 12px;'>
            <p>&copy; " . date('Y') . " VisionPro LCD. All rights reserved.</p>
        </div>
    </div>
    ";
    
    return send_email($customer_email, $customer_name, $subject, $body);
}

/**
 * Send appointment rejection/cancellation email to customer
 */
function send_appointment_cancelled($appointment_id, $customer_email, $customer_name, $reason = '') {
    $subject = "Appointment Cancelled - #$appointment_id";
    
    $reason_html = $reason ? "<p><strong>Reason:</strong> $reason</p>" : "";
    
    $body = "
    <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
        <div style='background: #0284c7; padding: 20px; text-align: center;'>
            <h1 style='color: white; margin: 0;'>VisionPro LCD</h1>
        </div>
        <div style='padding: 20px; background: #f9fafb;'>
            <h2 style='color: #111827;'>Appointment Cancelled</h2>
            <p>Hi $customer_name,</p>
            <p>Your appointment <strong>#$appointment_id</strong> has been cancelled.</p>
            $reason_html
            <p>If you would like to reschedule, please visit our website or contact us.</p>
            
            <p style='margin-top: 20px;'>Thank you for your understanding.</p>
        </div>
        <div style='padding: 20px; text-align: center; color: #6b7280; font-size: 12px;'>
            <p>&copy; " . date('Y') . " VisionPro LCD. All rights reserved.</p>
        </div>
    </div>
    ";
    
    return send_email($customer_email, $customer_name, $subject, $body);
}
