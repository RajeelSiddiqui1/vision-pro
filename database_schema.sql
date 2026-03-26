-- =====================================================
-- VisionPro Refurbishing - Database Schema
-- Hostinger Deployment Ready
-- =====================================================

-- Create Database
CREATE DATABASE IF NOT EXISTS visionpro CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE visionpro;

-- =====================================================
-- USERS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    role ENUM('admin', 'user') DEFAULT 'user',
    business_name VARCHAR(255),
    is_verified BOOLEAN DEFAULT FALSE,
    remember_token VARCHAR(255),
    order_otp VARCHAR(10),
    otp_expiry DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_phone (phone)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- BRANDS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS brands (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    logo VARCHAR(255),
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_slug (slug),
    INDEX idx_active (is_active)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- CATEGORIES TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    parent_id INT DEFAULT NULL,
    brand_id INT DEFAULT NULL,
    image_url VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (brand_id) REFERENCES brands(id) ON DELETE SET NULL,
    INDEX idx_slug (slug),
    INDEX idx_parent (parent_id),
    INDEX idx_brand (brand_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- PRODUCTS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT,
    brand_id INT,
    type ENUM('product', 'accessory') DEFAULT 'product',
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    discount_price DECIMAL(10, 2),
    sku VARCHAR(100) UNIQUE,
    part_number VARCHAR(100),
    stock_quantity INT DEFAULT 0,
    stock_deducted INT DEFAULT 0,
    main_image VARCHAR(255),
    is_featured BOOLEAN DEFAULT FALSE,
    is_active BOOLEAN DEFAULT TRUE,
    quality_tier VARCHAR(50),
    warranty VARCHAR(100),
    compatibility VARCHAR(255),
    bulk_pricing JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (brand_id) REFERENCES brands(id) ON DELETE SET NULL,
    INDEX idx_slug (slug),
    INDEX idx_category (category_id),
    INDEX idx_brand (brand_id),
    INDEX idx_type (type),
    INDEX idx_featured (is_featured),
    INDEX idx_part_number (part_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- PRODUCT IMAGES TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    sort_order INT DEFAULT 0,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- ADDRESSES TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS addresses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    address_line1 VARCHAR(255) NOT NULL,
    address_line2 VARCHAR(255),
    city VARCHAR(100) NOT NULL,
    state VARCHAR(100) NOT NULL,
    postal_code VARCHAR(20) NOT NULL,
    country VARCHAR(100) DEFAULT 'Canada',
    is_default BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- ORDERS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(50) NOT NULL UNIQUE,
    user_id INT NOT NULL,
    total_amount DECIMAL(10, 2) NOT NULL,
    tax_amount DECIMAL(10, 2) DEFAULT 0,
    shipping_amount DECIMAL(10, 2) DEFAULT 0,
    discount_amount DECIMAL(10, 2) DEFAULT 0,
    status ENUM('pending', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded') DEFAULT 'pending',
    payment_status ENUM('unpaid', 'paid', 'refunded', 'failed') DEFAULT 'unpaid',
    payment_method VARCHAR(50),
    payment_transaction_id VARCHAR(100),
    shipping_address TEXT,
    shipping_address_id INT,
    billing_address_id INT,
    tracking_number VARCHAR(100),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (shipping_address_id) REFERENCES addresses(id),
    FOREIGN KEY (billing_address_id) REFERENCES addresses(id),
    INDEX idx_order_number (order_number),
    INDEX idx_user (user_id),
    INDEX idx_status (status),
    INDEX idx_payment_status (payment_status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- ORDER ITEMS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    product_image VARCHAR(255),
    quantity INT NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    subtotal DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id),
    INDEX idx_order (order_id),
    INDEX idx_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- CART TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    session_id VARCHAR(255),
    product_id INT NOT NULL,
    quantity INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_session (session_id),
    INDEX idx_product (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- DEVICE CATEGORIES TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS device_categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    icon VARCHAR(100),
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_slug (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- DEVICE SUBCATEGORIES TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS device_subcategories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES device_categories(id) ON DELETE CASCADE,
    INDEX idx_slug (slug),
    INDEX idx_category (category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- REPAIR SERVICES TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS repair_services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    device_category_id INT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT,
    price DECIMAL(10, 2),
    category VARCHAR(100),
    icon VARCHAR(50),
    duration_minutes INT DEFAULT 60,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (device_category_id) REFERENCES device_categories(id) ON DELETE SET NULL,
    INDEX idx_slug (slug),
    INDEX idx_device (device_category_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- BLOG POSTS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS blog_posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    content TEXT,
    excerpt TEXT,
    featured_image VARCHAR(255),
    author VARCHAR(100),
    status ENUM('draft', 'published') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_slug (slug),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- APPOINTMENTS TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    service_type VARCHAR(100),
    device_model VARCHAR(100),
    issue_description TEXT,
    preferred_date DATE,
    preferred_time TIME,
    status ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_status (status),
    INDEX idx_date (preferred_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- NEWSLETTER TABLE
-- =====================================================
CREATE TABLE IF NOT EXISTS newsletter (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    is_active BOOLEAN DEFAULT TRUE,
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- DEFAULT ADMIN USER (Password: admin123)
-- =====================================================
INSERT INTO users (full_name, email, password, phone, role, is_verified) 
VALUES ('Admin', 'admin@visionpro.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+1234567890', 'admin', TRUE)
ON DUPLICATE KEY UPDATE email = email;

-- =====================================================
-- DEFAULT CATEGORIES
-- =====================================================
INSERT INTO categories (name, slug, image_url) VALUES
('iPhone Screens', 'iphone-screens', 'assets/images/categories/iphone-screens.png'),
('iPhone Batteries', 'iphone-batteries', 'assets/images/categories/iphone-batteries.png'),
('iPhone Parts', 'iphone-parts', 'assets/images/categories/iphone-parts.png'),
('iPad Screens', 'ipad-screens', 'assets/images/categories/ipad-screens.png'),
('Samsung Screens', 'samsung-screens', 'assets/images/categories/samsung-screens.png')
ON DUPLICATE KEY UPDATE name = name;

-- =====================================================
-- SAMPLE PRODUCTS
-- =====================================================
INSERT INTO products (category_id, name, slug, description, price, discount_price, part_number, stock_quantity, main_image, is_featured) VALUES
(1, 'iPhone 11 Pro Max Screen', 'iphone-11-pro-max-screen', 'Original iPhone 11 Pro Max LCD Screen with Frame', 89.99, 79.99, 'IP11PM-LCD-001', 50, 'assets/images/products/iphone-screen.jpg', TRUE),
(1, 'iPhone 12 Screen', 'iphone-12-screen', 'Original iPhone 12 OLED Screen', 119.99, 99.99, 'IP12-OLED-001', 30, 'assets/images/products/iphone-screen.jpg', TRUE),
(1, 'iPhone 13 Screen', 'iphone-13-screen', 'Original iPhone 13 OLED Screen', 149.99, 129.99, 'IP13-OLED-001', 25, 'assets/images/products/iphone-screen.jpg', FALSE)
ON DUPLICATE KEY UPDATE name = name;

-- =====================================================
-- SAMPLE BLOG POSTS
-- =====================================================
INSERT INTO blog_posts (title, slug, content, excerpt, author, status) VALUES
('Welcome to VisionPro Refurbishing', 'welcome-to-visionpro', 'Welcome to VisionPro - your trusted source for quality phone parts and refurbishing services.', 'Welcome to VisionPro Refurbishing', 'Admin', 'published'),
('How to Choose the Right Phone Screen', 'choose-right-phone-screen', 'Learn how to select the perfect replacement screen for your phone.', 'Guide to choosing phone screens', 'Admin', 'published')
ON DUPLICATE KEY UPDATE title = title;

-- =====================================================
-- END OF SCHEMA
-- =====================================================
