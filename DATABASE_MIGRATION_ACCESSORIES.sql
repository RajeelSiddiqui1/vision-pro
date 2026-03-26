-- =====================================================
-- DATABASE MIGRATION: ACCESSORIES & BRAND SUPPORT
-- Run this if you already have the VisionPro DB running
-- =====================================================

-- 1. Create Brands Table
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


-- 2. Update Products Table
-- Adding type to distinguish accessories
-- Adding brand_id and other details
ALTER TABLE products 
ADD COLUMN IF NOT EXISTS type ENUM('product', 'accessory') DEFAULT 'product' AFTER category_id,
ADD COLUMN IF NOT EXISTS brand_id INT DEFAULT NULL AFTER type,
ADD COLUMN IF NOT EXISTS quality_tier VARCHAR(100) DEFAULT 'Standard',
ADD COLUMN IF NOT EXISTS warranty VARCHAR(100) DEFAULT 'Lifetime Warranty',
ADD COLUMN IF NOT EXISTS compatibility TEXT DEFAULT NULL,
ADD COLUMN IF NOT EXISTS bulk_pricing JSON DEFAULT NULL;

-- Add Foreign Key and Indexes to Products
ALTER TABLE products ADD INDEX IF NOT EXISTS idx_type (type);
ALTER TABLE products ADD INDEX IF NOT EXISTS idx_brand (brand_id);
-- Check if foreign key exists is complex in raw SQL without stored procedures, 
-- but normally you'd run:
-- ALTER TABLE products ADD CONSTRAINT fk_product_brand FOREIGN KEY (brand_id) REFERENCES brands(id) ON DELETE SET NULL;


-- 3. Update Categories Table
ALTER TABLE categories 
ADD COLUMN IF NOT EXISTS brand_id INT DEFAULT NULL AFTER parent_id;

ALTER TABLE categories ADD INDEX IF NOT EXISTS idx_brand (brand_id);


-- 4. Device categorization for Repairs (If missing)
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
-- END OF MIGRATION
-- =====================================================
