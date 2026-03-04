<?php
// Migration: Create repair_services and appointments tables

$pdo = new PDO("mysql:host=localhost;dbname=visionpro;charset=utf8mb4", "root", "");

echo "Creating repair_services table...\n";
$pdo->exec("CREATE TABLE IF NOT EXISTS repair_services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    duration_minutes INT DEFAULT 60,
    icon VARCHAR(50),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

echo "Creating appointments table...\n";
$pdo->exec("CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    service_id INT NOT NULL,
    user_id INT,
    customer_name VARCHAR(255) NOT NULL,
    customer_email VARCHAR(255) NOT NULL,
    customer_phone VARCHAR(20) NOT NULL,
    device_model VARCHAR(255),
    device_issue TEXT,
    appointment_date DATE NOT NULL,
    appointment_time TIME NOT NULL,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (service_id) REFERENCES repair_services(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
)");

// Insert default repair services
echo "Inserting default repair services...\n";

$services = [
    ['Screen Repair', 'screen-repair', 'Professional screen replacement for all major smartphone brands. We use high-quality OEM parts.', 99.00, 60, '📱'],
    ['Battery Replacement', 'battery-replacement', 'Replace your old battery with a new one. Same-day service available.', 69.00, 30, '🔋'],
    ['Water Damage Repair', 'water-damage-repair', 'Expert water damage treatment and component cleaning.', 89.00, 120, '💧'],
    ['Charging Port Repair', 'charging-port-repair', 'Fix charging issues, slow charging, or no charging problems.', 79.00, 45, '🔌'],
    ['Camera Repair', 'camera-repair', 'Rear or front camera replacement and repair services.', 99.00, 60, '📷'],
    ['Speaker & Microphone Repair', 'speaker-microphone-repair', 'Fix audio issues, speaker not working, or microphone problems.', 69.00, 45, '🔊'],
    ['Software Issues', 'software-issues', 'Software diagnostics, OS reinstall, jailbreak removal, and more.', 59.00, 60, '💻'],
    ['Data Recovery', 'data-recovery', 'Recover lost data from damaged devices. Photos, contacts, messages.', 149.00, 180, '💾'],
    ['Unlock Services', 'unlock-services', 'Unlock your device from any carrier. Fast and reliable.', 49.00, 24, '🔓'],
    ['Accessories', 'accessories', 'Browse our selection of cases, chargers, cables, and more.', 29.00, 15, '🎧']
];

$stmt = $pdo->prepare("INSERT IGNORE INTO repair_services (name, slug, description, price, duration_minutes, icon) VALUES (?, ?, ?, ?, ?, ?)");
foreach ($services as $service) {
    $stmt->execute($service);
}

echo "Migration completed successfully!\n";

