-- Full merchandise ecommerce foundation (products, variants, orders)

CREATE TABLE IF NOT EXISTS store_products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(255) NOT NULL,
    description TEXT,
    category VARCHAR(100) DEFAULT 'Merchandise',
    product_image VARCHAR(255),
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS store_product_variants (
    variant_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    size VARCHAR(30) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    stock_qty INT NOT NULL DEFAULT 0,
    sku VARCHAR(100) DEFAULT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_product_size (product_id, size),
    KEY idx_variant_product (product_id),
    CONSTRAINT fk_variant_product FOREIGN KEY (product_id) REFERENCES store_products(product_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS store_orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(40) NOT NULL UNIQUE,
    buyer_name VARCHAR(180) NOT NULL,
    buyer_phone VARCHAR(40) NOT NULL,
    buyer_email VARCHAR(180) NOT NULL,
    buyer_address TEXT NOT NULL,
    payment_method ENUM('COD','Manual') NOT NULL DEFAULT 'COD',
    status ENUM('Pending','Completed','Cancelled') NOT NULL DEFAULT 'Pending',
    subtotal DECIMAL(10,2) NOT NULL DEFAULT 0,
    total DECIMAL(10,2) NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS store_order_items (
    order_item_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    variant_id INT NOT NULL,
    product_name VARCHAR(255) NOT NULL,
    size VARCHAR(30) NOT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    quantity INT NOT NULL,
    line_total DECIMAL(10,2) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    KEY idx_order_items_order (order_id),
    KEY idx_order_items_product (product_id),
    CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES store_orders(order_id) ON DELETE CASCADE,
    CONSTRAINT fk_order_items_product FOREIGN KEY (product_id) REFERENCES store_products(product_id),
    CONSTRAINT fk_order_items_variant FOREIGN KEY (variant_id) REFERENCES store_product_variants(variant_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Optional one-time migration from legacy store_management table:
-- INSERT INTO store_products (product_name, description, category, product_image, is_active)
-- SELECT item_name, description, category, item_image,
--        CASE WHEN status = 'Sold Out' THEN 0 ELSE 1 END
-- FROM store_management;
