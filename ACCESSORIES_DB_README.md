# VisionPro Accessories Database Documentation

Yeh document accessories implementation ke liye database mein kiye gaye changes ki details provide karta hai.

## 1. Overview
Accessories ko separate tables mein rakhne ke bajaye humne existing `products` table ko hi extend kiya hai. Isse architecture clean rehti hai aur search/cart logic handle karna asaan hota hai.

## 2. Table: `products` (Modified)
Is table mein aik naya column add kiya gaya hai jo differentiate karta hai ke item aik regular product hai ya accessory.

| Column | Type | Default | Description |
| --- | --- | --- | --- |
| `type` | `ENUM('product', 'accessory')` | `'product'` | Item ki category define karta hai. |
| `brand_id` | `INT` | `NULL` | Brand table se linkage ke liye. |
| `quality_tier` | `VARCHAR(100)` | `'Standard'` | E.g., 'Original', 'Premium High Copy', etc. |
| `warranty` | `VARCHAR(100)` | `NULL` | Warranty details (e.g., '6 Months'). |
| `compatibility` | `TEXT` | `NULL` | Devices jin ke saath yeh accessory support karti hai. |
| `bulk_pricing` | `JSON` | `NULL` | Wholesale rates support ke liye. |

### SQL Command used:
```sql
ALTER TABLE products ADD COLUMN type ENUM('product', 'accessory') DEFAULT 'product';
ALTER TABLE products ADD COLUMN brand_id INT DEFAULT NULL;
ALTER TABLE products ADD COLUMN quality_tier VARCHAR(100) DEFAULT 'Standard';
ALTER TABLE products ADD COLUMN warranty VARCHAR(100) DEFAULT 'Lifetime Warranty';
ALTER TABLE products ADD COLUMN compatibility TEXT DEFAULT NULL;
ALTER TABLE products ADD COLUMN bulk_pricing JSON DEFAULT NULL;
```

## 3. New Tables
Accessories aur products ki categorization behtar karne ke liye yeh tables add kiye gaye hain:

### `brands` Table
Stores brand info (Apple, Samsung, etc.)
```sql
CREATE TABLE IF NOT EXISTS brands (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    logo VARCHAR(255),
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### `device_categories` & `device_subcategories`
Repairs aur accessories ki compatibility filter karne ke liye use hote hain.

## 4. Implementation Logic
Ab code mein accessories ko fetch karne ke liye hum check karte hain:

*   **Accessories Page:** `SELECT * FROM products WHERE type = 'accessory'`
*   **Products Page:** `SELECT * FROM products WHERE type = 'product'`

Admin panel mein bhi dono ke separate sections (Gallerires) bana diye gaye hain jo isi logic par base karte hain.

---
*Note: Agar aap manually changes restore karna chahte hain toh `tmp_add_type.php` aur `fix-database.php` files refer kar sakte hain.*
