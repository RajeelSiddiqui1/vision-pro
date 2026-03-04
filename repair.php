<?php
session_start();
require_once 'config/db.php';
require_once 'includes/email_helper.php';

$error = '';
$success = '';

// Get all categories for repair services filter
$service_categories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();

// Get selected category
$selected_category = $_GET['category'] ?? '';

// Get services based on category filter
if ($selected_category) {
    $stmt = $pdo->prepare("SELECT * FROM repair_services WHERE is_active = 1 AND category_id = ? ORDER BY id ASC");
    $stmt->execute([$selected_category]);
    $services = $stmt->fetchAll();
} else {
    $services = $pdo->query("SELECT * FROM repair_services WHERE is_active = 1 ORDER BY id ASC")->fetchAll();
}

// Get all active device categories (for device selection)
$device_categories = $pdo->query("SELECT * FROM device_categories WHERE is_active = 1 ORDER BY name ASC")->fetchAll();

// Get all active subcategories
$subcategories = $pdo->query("SELECT ds.*, dc.name as category_name 
    FROM device_subcategories ds 
    JOIN device_categories dc ON ds.category_id = dc.id 
    WHERE ds.is_active = 1 AND dc.is_active = 1 
    ORDER BY dc.name ASC, ds.name ASC")->fetchAll();

// Group subcategories by category_id for JavaScript
$subcategories_by_category = [];
foreach ($subcategories as $sub) {
    $subcategories_by_category[$sub['category_id']][] = $sub;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $service_id = intval($_POST['service_id'] ?? 0);
    $category_id = intval($_POST['category_id'] ?? 0);
    $subcategory_id = intval($_POST['subcategory_id'] ?? 0);
    $customer_name = trim($_POST['customer_name'] ?? '');
    $customer_email = trim($_POST['customer_email'] ?? '');
    $customer_phone = trim($_POST['customer_phone'] ?? '');
    $device_model = trim($_POST['device_model'] ?? '');
    $device_issue = trim($_POST['device_issue'] ?? '');
    $appointment_date = $_POST['appointment_date'] ?? '';
    $appointment_time = $_POST['appointment_time'] ?? '';

    // Validation
    if (empty($service_id) || empty($customer_name) || empty($customer_email) || empty($customer_phone) || empty($appointment_date) || empty($appointment_time)) {
        $error = 'Please fill in all required fields.';
    } else {
        // Get logged in user ID if available
        $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;

        // Get device info
        $device_info = '';
        if ($category_id > 0 && $subcategory_id > 0) {
            $cat_name = '';
            $sub_name = '';
            foreach ($device_categories as $c) { if ($c['id'] == $category_id) { $cat_name = $c['name']; break; } }
            foreach ($subcategories as $s) { if ($s['id'] == $subcategory_id) { $sub_name = $s['name']; break; } }
            $device_info = $cat_name . ' ' . $sub_name;
            if (!empty($device_model)) $device_info .= ' - ' . $device_model;
        } elseif (!empty($device_model)) {
            $device_info = $device_model;
        }

        // Insert appointment
        $stmt = $pdo->prepare("INSERT INTO appointments (service_id, user_id, customer_name, customer_email, customer_phone, device_model, device_issue, appointment_date, appointment_time, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
        
        try {
            $stmt->execute([$service_id, $user_id, $customer_name, $customer_email, $customer_phone, $device_info, $device_issue, $appointment_date, $appointment_time]);
            $appointment_id = $pdo->lastInsertId();
            $success = 'Your appointment has been booked successfully! We will contact you shortly to confirm.';
            
            // Get service details for email
            $service_stmt = $pdo->prepare("SELECT * FROM repair_services WHERE id = ?");
            $service_stmt->execute([$service_id]);
            $service_details = $service_stmt->fetch();
            
            // Send email to admin
            $admin_subject = "New Repair Appointment - #" . $appointment_id;
            $admin_body = "
            <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;'>
                <div style='background: #0284c7; padding: 20px; text-align: center;'>
                    <h1 style='color: white; margin: 0;'>VisionPro LCD</h1>
                </div>
                <div style='padding: 20px; background: #f9fafb;'>
                    <h2 style='color: #111827;'>New Repair Appointment</h2>
                    <p><strong>Appointment ID:</strong> #$appointment_id</p>
                    <p><strong>Service:</strong> {$service_details['name']}</p>
                    <p><strong>Price:</strong> $" . number_format($service_details['price'], 2) . "</p>
                    <p><strong>Duration:</strong> {$service_details['duration_minutes']} minutes</p>
                    <hr style='border: none; border-top: 1px solid #e5e7eb; margin: 20px 0;'>
                    <p><strong>Customer Name:</strong> $customer_name</p>
                    <p><strong>Email:</strong> $customer_email</p>
                    <p><strong>Phone:</strong> $customer_phone</p>
                    <p><strong>Device:</strong> $device_info</p>
                    <p><strong>Issue:</strong> $device_issue</p>
                    <p><strong>Appointment Date:</strong> " . date('F d, Y', strtotime($appointment_date)) . "</p>
                    <p><strong>Appointment Time:</strong> " . date('h:i A', strtotime($appointment_time)) . "</p>
                </div>
            </div>
            ";
            send_email('Visionpro.lcd@gmail.com', 'VisionPro Admin', $admin_subject, $admin_body);
            
            // Clear form data
            $_POST = [];
        } catch (Exception $e) {
            $error = 'Failed to book appointment. Please try again.';
        }
    }
}

// Get minimum date (tomorrow)
$min_date = date('Y-m-d', strtotime('+1 day'));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Repair Appointment - VisionPro</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9',
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                            950: '#082f49',
                        },
                    },
                },
            },
        }
    </script>
    <link href="assets/css/style.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="assets/images/visionpro-logo.png">
    <link rel="apple-touch-icon" href="assets/images/visionpro-logo.png">
</head>
<body class="bg-gray-50">
    <?php include 'includes/header.php'; ?>

    <!-- Hero -->
    <section class="bg-primary-900 text-white py-20 relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-br from-primary-800 to-primary-900"></div>
        <div class="container mx-auto px-4 relative z-10 text-center">
            <h1 class="text-5xl font-bold mb-6">Professional Repair Services</h1>
            <p class="text-xl text-primary-100 max-w-2xl mx-auto">Fast, reliable repairs for all your devices. Book an appointment today and get your device back in perfect working condition.</p>
        </div>
    </section>

    <!-- Main Content -->
    <section class="py-16">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
                
                <!-- Services List -->
                <div class="lg:col-span-1">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Our Services</h2>
                    
                    <!-- Category Filter -->
                    <div class="mb-4">
                        <select id="service_category_filter" onchange="filterServicesByCategory(this.value)" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">All Services</option>
                            <?php foreach($service_categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= $selected_category == $cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="space-y-4" id="services-list">
                        <?php foreach($services as $service): ?>
                        <div class="bg-white p-5 rounded-xl shadow-sm border border-gray-100 hover:border-primary-200 transition-colors service-card cursor-pointer" data-service-id="<?= $service['id'] ?>" data-service-name="<?= htmlspecialchars($service['name']) ?>" data-service-price="<?= $service['price'] ?>" data-service-duration="<?= $service['duration_minutes'] ?>" data-category-id="<?= $service['category_id'] ?? '' ?>">
                            <div class="flex items-center gap-4">
                                <span class="text-3xl"><?= htmlspecialchars($service['icon']) ?></span>
                                <div>
                                    <h3 class="font-bold text-gray-900"><?= htmlspecialchars($service['name']) ?></h3>
                                    <p class="text-sm text-gray-500"><?= $service['duration_minutes'] ?> minutes</p>
                                </div>
                                <div class="ml-auto font-bold text-primary-600">$<?= number_format($service['price'], 2) ?></div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Booking Form -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Book Your Appointment</h2>
                        
                        <?php if ($error): ?>
                            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                                <?= htmlspecialchars($error) ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($success): ?>
                            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                                <?= htmlspecialchars($success) ?>
                            </div>
                        <?php else: ?>
                        
                        <form method="POST" class="space-y-6">
                            <!-- Device Selection -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Select Brand *</label>
                                    <select name="category_id" id="category_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                        <option value="">-- Select Brand --</option>
                                        <?php foreach($device_categories as $cat): ?>
                                        <option value="<?= $cat['id'] ?>" <?= (isset($_POST['category_id']) && $_POST['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cat['icon'] . ' ' . $cat['name']) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Device Type *</label>
                                    <select name="subcategory_id" id="subcategory_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                        <option value="">-- Select Type --</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Model -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Specific Model (Optional)</label>
                                <input type="text" name="device_model" value="<?= htmlspecialchars($_POST['device_model'] ?? '') ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="e.g., iPhone 14 Pro, Samsung S23 Ultra">
                            </div>

                            <!-- Select Service -->
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Select Repair Service *</label>
                                <select name="service_id" id="service_id" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    <option value="">-- Select Service --</option>
                                    <?php foreach($services as $service): ?>
                                    <option value="<?= $service['id'] ?>" <?= (isset($_POST['service_id']) && $_POST['service_id'] == $service['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($service['name']) ?> - $<?= number_format($service['price'], 2) ?> (<?= $service['duration_minutes'] ?> min)
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>

                            <!-- Customer Info -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Full Name *</label>
                                    <input type="text" name="customer_name" value="<?= htmlspecialchars($_POST['customer_name'] ?? '') ?>" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="John Doe">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Email *</label>
                                    <input type="email" name="customer_email" value="<?= htmlspecialchars($_POST['customer_email'] ?? '') ?>" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="john@example.com">
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Phone Number *</label>
                                    <input type="tel" name="customer_phone" value="<?= htmlspecialchars($_POST['customer_phone'] ?? '') ?>" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="+1 (555) 123-4567">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Describe the Issue</label>
                                <textarea name="device_issue" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Please describe the problem with your device..."><?= htmlspecialchars($_POST['device_issue'] ?? '') ?></textarea>
                            </div>

                            <!-- Appointment Date & Time -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Preferred Date *</label>
                                    <input type="date" name="appointment_date" value="<?= htmlspecialchars($_POST['appointment_date'] ?? '') ?>" required min="<?= $min_date ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Preferred Time *</label>
                                    <select name="appointment_time" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                        <option value="">-- Select Time --</option>
                                        <option value="09:00:00" <?= (isset($_POST['appointment_time']) && $_POST['appointment_time'] === '09:00:00') ? 'selected' : '' ?>>9:00 AM</option>
                                        <option value="10:00:00" <?= (isset($_POST['appointment_time']) && $_POST['appointment_time'] === '10:00:00') ? 'selected' : '' ?>>10:00 AM</option>
                                        <option value="11:00:00" <?= (isset($_POST['appointment_time']) && $_POST['appointment_time'] === '11:00:00') ? 'selected' : '' ?>>11:00 AM</option>
                                        <option value="12:00:00" <?= (isset($_POST['appointment_time']) && $_POST['appointment_time'] === '12:00:00') ? 'selected' : '' ?>>12:00 PM</option>
                                        <option value="13:00:00" <?= (isset($_POST['appointment_time']) && $_POST['appointment_time'] === '13:00:00') ? 'selected' : '' ?>>1:00 PM</option>
                                        <option value="14:00:00" <?= (isset($_POST['appointment_time']) && $_POST['appointment_time'] === '14:00:00') ? 'selected' : '' ?>>2:00 PM</option>
                                        <option value="15:00:00" <?= (isset($_POST['appointment_time']) && $_POST['appointment_time'] === '15:00:00') ? 'selected' : '' ?>>3:00 PM</option>
                                        <option value="16:00:00" <?= (isset($_POST['appointment_time']) && $_POST['appointment_time'] === '16:00:00') ? 'selected' : '' ?>>4:00 PM</option>
                                        <option value="17:00:00" <?= (isset($_POST['appointment_time']) && $_POST['appointment_time'] === '17:00:00') ? 'selected' : '' ?>>5:00 PM</option>
                                    </select>
                                </div>
                            </div>

                            <button type="submit" class="w-full bg-primary-600 text-white px-8 py-4 rounded-lg font-bold hover:bg-primary-700 transition-colors text-lg">
                                Book Appointment
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Why Choose Us -->
    <section class="py-16 bg-gray-100">
        <div class="container mx-auto px-4">
            <h2 class="text-3xl font-bold text-gray-900 text-center mb-12">Why Choose Us?</h2>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 bg-primary-100 text-primary-600 rounded-full flex items-center justify-center text-3xl mx-auto mb-4">⚡</div>
                    <h3 class="font-bold text-gray-900 mb-2">Fast Service</h3>
                    <p class="text-gray-500 text-sm">Most repairs completed within the same day</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-primary-100 text-primary-600 rounded-full flex items-center justify-center text-3xl mx-auto mb-4">🛡️</div>
                    <h3 class="font-bold text-gray-900 mb-2">Warranty</h3>
                    <p class="text-gray-500 text-sm">90-day warranty on all repairs</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-primary-100 text-primary-600 rounded-full flex items-center justify-center text-3xl mx-auto mb-4">💯</div>
                    <h3 class="font-bold text-gray-900 mb-2">Quality Parts</h3>
                    <p class="text-gray-500 text-sm">Only OEM quality parts used</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 bg-primary-100 text-primary-600 rounded-full flex items-center justify-center text-3xl mx-auto mb-4">👨‍🔧</div>
                    <h3 class="font-bold text-gray-900 mb-2">Expert Technicians</h3>
                    <p class="text-gray-500 text-sm">Certified repair professionals</p>
                </div>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script>
        // Subcategories data from PHP
        const subcategoriesByCategory = <?= json_encode($subcategories_by_category) ?>;
        
        // Handle category change for device selection
        document.getElementById('category_id').addEventListener('change', function() {
            const categoryId = this.value;
            const subcategorySelect = document.getElementById('subcategory_id');
            
            // Clear current options
            subcategorySelect.innerHTML = '<option value="">-- Select Type --';
            
            if (categoryId && subcategoriesByCategory[categoryId]) {
                subcategoriesByCategory[categoryId].forEach(function(sub) {
                    const option = document.createElement('option');
                    option.value = sub.id;
                    option.textContent = sub.name;
                    subcategorySelect.appendChild(option);
                });
            }
        });

        // Filter services by category
        function filterServicesByCategory(categoryId) {
            if (categoryId) {
                window.location.href = 'repair.php?category=' + categoryId;
            } else {
                window.location.href = 'repair.php';
            }
        }

        // Handle service card click
        document.querySelectorAll('.service-card').forEach(card => {
            card.addEventListener('click', function() {
                const serviceId = this.dataset.serviceId;
                document.getElementById('service_id').value = serviceId;
                // Scroll to form
                document.querySelector('form').scrollIntoView({ behavior: 'smooth' });
            });
        });
    </script>
</body>
</html>


