<?php

class StoreEcommerceModel
{
    use Model;

    protected $table = 'store_products';
    private $tableEnsured = false;

    private function ensureTables()
    {
        if ($this->tableEnsured) {
            return;
        }

        $this->query("CREATE TABLE IF NOT EXISTS store_products (
            product_id INT AUTO_INCREMENT PRIMARY KEY,
            product_name VARCHAR(255) NOT NULL,
            description TEXT,
            category VARCHAR(100) DEFAULT 'Merchandise',
            product_image VARCHAR(255),
            is_active TINYINT(1) NOT NULL DEFAULT 1,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $this->query("CREATE TABLE IF NOT EXISTS store_product_variants (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $this->query("CREATE TABLE IF NOT EXISTS store_orders (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $this->query("CREATE TABLE IF NOT EXISTS store_order_items (
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
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $this->query("CREATE TABLE IF NOT EXISTS store_ecommerce_meta (
            meta_key VARCHAR(120) PRIMARY KEY,
            meta_value VARCHAR(255) NOT NULL,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

        $this->migrateLegacyStoreItems();
        $this->tableEnsured = true;
    }

    private function isLegacyMigrationCompleted()
    {
        $rows = $this->query(
            "SELECT meta_value
             FROM store_ecommerce_meta
             WHERE meta_key = :meta_key
             LIMIT 1",
            ['meta_key' => 'legacy_migrated']
        );

        if (empty($rows)) {
            return false;
        }

        return (string) ($rows[0]->meta_value ?? '') === '1';
    }

    private function markLegacyMigrationCompleted()
    {
        $this->query(
            "INSERT INTO store_ecommerce_meta (meta_key, meta_value)
             VALUES (:meta_key, :meta_value)
             ON DUPLICATE KEY UPDATE meta_value = VALUES(meta_value)",
            [
                'meta_key' => 'legacy_migrated',
                'meta_value' => '1',
            ]
        );
    }

    private function migrateLegacyStoreItems()
    {
        if ($this->isLegacyMigrationCompleted()) {
            return;
        }

        $productCountRows = $this->query("SELECT COUNT(*) AS total FROM store_products");
        $productCount = (int) ($productCountRows[0]->total ?? 0);
        if ($productCount > 0) {
            $this->markLegacyMigrationCompleted();
            return;
        }

        try {
            $legacyRows = $this->query("SELECT item_id, item_name, description, category, price, quantity, item_image, status FROM store_management ORDER BY item_id ASC");
        } catch (Throwable $e) {
            $legacyRows = [];
        }

        foreach ($legacyRows as $legacy) {
            $isActive = strcasecmp((string) ($legacy->status ?? 'Available'), 'Sold Out') !== 0 ? 1 : 0;
            $this->query(
                "INSERT INTO store_products (product_name, description, category, product_image, is_active)
                 VALUES (:product_name, :description, :category, :product_image, :is_active)",
                [
                    'product_name' => (string) ($legacy->item_name ?? 'Item'),
                    'description' => (string) ($legacy->description ?? ''),
                    'category' => (string) ($legacy->category ?? 'Merchandise'),
                    'product_image' => (string) ($legacy->item_image ?? ''),
                    'is_active' => $isActive,
                ]
            );

            $productId = (int) $this->lastInsertId();
            if ($productId <= 0) {
                continue;
            }

            $this->query(
                "INSERT INTO store_product_variants (product_id, size, price, stock_qty, is_active)
                 VALUES (:product_id, :size, :price, :stock_qty, :is_active)",
                [
                    'product_id' => $productId,
                    'size' => 'One Size',
                    'price' => (float) ($legacy->price ?? 0),
                    'stock_qty' => (int) ($legacy->quantity ?? 0),
                    'is_active' => $isActive,
                ]
            );
        }

        $this->markLegacyMigrationCompleted();
    }

    public function getPublicProductsWithVariants()
    {
        $this->ensureTables();

        $products = $this->query(
            "SELECT
                p.product_id,
                p.product_name,
                p.description,
                p.category,
                p.product_image,
                p.is_active,
                MIN(v.price) AS min_price,
                SUM(v.stock_qty) AS total_stock
             FROM store_products p
             JOIN store_product_variants v ON v.product_id = p.product_id AND v.is_active = 1
             WHERE p.is_active = 1
             GROUP BY p.product_id, p.product_name, p.description, p.category, p.product_image, p.is_active
             ORDER BY p.created_at DESC"
        );

        foreach ($products as $product) {
            $product->variants = $this->query(
                "SELECT variant_id, size, price, stock_qty
                 FROM store_product_variants
                 WHERE product_id = :product_id AND is_active = 1
                 ORDER BY FIELD(size, 'XS', 'S', 'M', 'L', 'XL', 'XXL'), size ASC",
                ['product_id' => (int) $product->product_id]
            );
        }

        return $products;
    }

    public function getFeaturedProducts($limit = 4)
    {
        $this->ensureTables();
        $safeLimit = max(1, (int) $limit);

        return $this->query(
            "SELECT
                p.product_id,
                p.product_name,
                p.category,
                p.product_image,
                MIN(v.price) AS min_price,
                SUM(v.stock_qty) AS total_stock
             FROM store_products p
             JOIN store_product_variants v ON v.product_id = p.product_id AND v.is_active = 1
             WHERE p.is_active = 1
             GROUP BY p.product_id, p.product_name, p.category, p.product_image
             ORDER BY p.created_at DESC
             LIMIT {$safeLimit}"
        );
    }

    public function getAdminProductsWithVariants()
    {
        $this->ensureTables();
        $products = $this->query(
            "SELECT product_id, product_name, description, category, product_image, is_active
             FROM store_products
             ORDER BY created_at DESC"
        );

        foreach ($products as $product) {
            $variants = $this->query(
                "SELECT variant_id, size, price, stock_qty, is_active
                 FROM store_product_variants
                 WHERE product_id = :product_id
                 ORDER BY FIELD(size, 'XS', 'S', 'M', 'L', 'XL', 'XXL'), size ASC",
                ['product_id' => (int) $product->product_id]
            );
            $product->variants = $variants;
            $product->min_price = !empty($variants) ? min(array_map(fn($v) => (float) $v->price, $variants)) : 0;
            $product->total_stock = !empty($variants) ? array_sum(array_map(fn($v) => (int) $v->stock_qty, $variants)) : 0;
            $product->status = ((int) $product->is_active === 1) ? 'Available' : 'Sold Out';
        }

        return $products;
    }

    public function getProductByIdWithVariants($productId)
    {
        $this->ensureTables();
        $rows = $this->query(
            "SELECT product_id, product_name, description, category, product_image, is_active
             FROM store_products
             WHERE product_id = :product_id
             LIMIT 1",
            ['product_id' => (int) $productId]
        );

        if (empty($rows)) {
            return null;
        }

        $product = $rows[0];
        $product->variants = $this->query(
            "SELECT variant_id, size, price, stock_qty, is_active
             FROM store_product_variants
             WHERE product_id = :product_id
             ORDER BY FIELD(size, 'XS', 'S', 'M', 'L', 'XL', 'XXL'), size ASC",
            ['product_id' => (int) $productId]
        );

        return $product;
    }

    private function normalizeVariants(array $variants)
    {
        $normalized = [];
        foreach ($variants as $variant) {
            $size = strtoupper(trim((string) ($variant['size'] ?? '')));
            $price = (float) ($variant['price'] ?? 0);
            $stock = (int) ($variant['stock_qty'] ?? 0);
            if ($size === '' || $price <= 0 || $stock < 0) {
                continue;
            }
            $normalized[$size] = [
                'size' => $size,
                'price' => $price,
                'stock_qty' => $stock,
                'is_active' => $stock > 0 ? 1 : 0,
            ];
        }

        return array_values($normalized);
    }

    public function createProductWithVariants(array $product, array $variants)
    {
        $this->ensureTables();
        $cleanVariants = $this->normalizeVariants($variants);
        if (empty($cleanVariants)) {
            throw new Exception('At least one valid size variant is required');
        }

        $this->query('START TRANSACTION');
        try {
            $this->query(
                "INSERT INTO store_products (product_name, description, category, product_image, is_active)
                 VALUES (:product_name, :description, :category, :product_image, :is_active)",
                [
                    'product_name' => trim((string) ($product['product_name'] ?? '')),
                    'description' => trim((string) ($product['description'] ?? '')),
                    'category' => trim((string) ($product['category'] ?? 'Merchandise')),
                    'product_image' => trim((string) ($product['product_image'] ?? '')),
                    'is_active' => (int) ($product['is_active'] ?? 1),
                ]
            );

            $productId = (int) $this->lastInsertId();
            foreach ($cleanVariants as $variant) {
                $this->query(
                    "INSERT INTO store_product_variants (product_id, size, price, stock_qty, is_active)
                     VALUES (:product_id, :size, :price, :stock_qty, :is_active)",
                    [
                        'product_id' => $productId,
                        'size' => $variant['size'],
                        'price' => $variant['price'],
                        'stock_qty' => $variant['stock_qty'],
                        'is_active' => $variant['is_active'],
                    ]
                );
            }

            $this->query('COMMIT');
            return $productId;
        } catch (Throwable $e) {
            $this->query('ROLLBACK');
            throw $e;
        }
    }

    public function updateProductWithVariants($productId, array $product, array $variants)
    {
        $this->ensureTables();
        $productId = (int) $productId;
        $cleanVariants = $this->normalizeVariants($variants);
        if ($productId <= 0 || empty($cleanVariants)) {
            throw new Exception('Invalid product update payload');
        }

        $this->query('START TRANSACTION');
        try {
            $this->query(
                "UPDATE store_products
                 SET product_name = :product_name,
                     description = :description,
                     category = :category,
                     product_image = COALESCE(NULLIF(:product_image, ''), product_image),
                     is_active = :is_active
                 WHERE product_id = :product_id",
                [
                    'product_name' => trim((string) ($product['product_name'] ?? '')),
                    'description' => trim((string) ($product['description'] ?? '')),
                    'category' => trim((string) ($product['category'] ?? 'Merchandise')),
                    'product_image' => trim((string) ($product['product_image'] ?? '')),
                    'is_active' => (int) ($product['is_active'] ?? 1),
                    'product_id' => $productId,
                ]
            );

            $this->query("DELETE FROM store_product_variants WHERE product_id = :product_id", ['product_id' => $productId]);
            foreach ($cleanVariants as $variant) {
                $this->query(
                    "INSERT INTO store_product_variants (product_id, size, price, stock_qty, is_active)
                     VALUES (:product_id, :size, :price, :stock_qty, :is_active)",
                    [
                        'product_id' => $productId,
                        'size' => $variant['size'],
                        'price' => $variant['price'],
                        'stock_qty' => $variant['stock_qty'],
                        'is_active' => $variant['is_active'],
                    ]
                );
            }

            $this->query('COMMIT');
            return true;
        } catch (Throwable $e) {
            $this->query('ROLLBACK');
            throw $e;
        }
    }

    public function deleteProduct($productId)
    {
        $this->ensureTables();
        $productId = (int) $productId;

        $this->query('START TRANSACTION');
        try {
            $usedRows = $this->query(
                "SELECT COUNT(*) AS total
                 FROM store_order_items
                 WHERE product_id = :product_id",
                ['product_id' => $productId]
            );
            $usedCount = (int) ($usedRows[0]->total ?? 0);

            if ($usedCount > 0) {
                $this->query(
                    "UPDATE store_products
                     SET is_active = 0
                     WHERE product_id = :product_id",
                    ['product_id' => $productId]
                );
                $this->query(
                    "UPDATE store_product_variants
                     SET is_active = 0
                     WHERE product_id = :product_id",
                    ['product_id' => $productId]
                );

                $this->query('COMMIT');
                return [
                    'success' => true,
                    'mode' => 'archived',
                ];
            }

            $this->query("DELETE FROM store_products WHERE product_id = :product_id", ['product_id' => $productId]);
            $this->query('COMMIT');

            return [
                'success' => true,
                'mode' => 'deleted',
            ];
        } catch (Throwable $e) {
            $this->query('ROLLBACK');
            throw $e;
        }
    }

    private function generateOrderNumber()
    {
        return 'ORD-' . date('Ymd-His') . '-' . strtoupper(substr(uniqid('', true), -5));
    }

    public function placeGuestOrder(array $buyer, array $items)
    {
        $this->ensureTables();

        if (empty($items)) {
            throw new Exception('Cart is empty');
        }

        $buyerName = trim((string) ($buyer['buyer_name'] ?? ''));
        $buyerPhone = trim((string) ($buyer['buyer_phone'] ?? ''));
        $buyerEmail = trim((string) ($buyer['buyer_email'] ?? ''));
        $buyerAddress = trim((string) ($buyer['buyer_address'] ?? ''));

        if ($buyerName === '' || $buyerPhone === '' || $buyerEmail === '' || $buyerAddress === '') {
            throw new Exception('All buyer details are required');
        }

        $this->query('START TRANSACTION');
        try {
            $validated = [];
            $subtotal = 0.0;

            foreach ($items as $item) {
                $variantId = (int) ($item['variant_id'] ?? 0);
                $quantity = (int) ($item['quantity'] ?? 0);
                if ($variantId <= 0 || $quantity <= 0) {
                    continue;
                }

                $variantRows = $this->query(
                    "SELECT
                        v.variant_id,
                        v.product_id,
                        v.size,
                        v.price,
                        v.stock_qty,
                        p.product_name,
                        p.is_active
                     FROM store_product_variants v
                     JOIN store_products p ON p.product_id = v.product_id
                     WHERE v.variant_id = :variant_id
                     FOR UPDATE",
                    ['variant_id' => $variantId]
                );

                if (empty($variantRows)) {
                    throw new Exception('A selected item is no longer available');
                }

                $variant = $variantRows[0];
                if ((int) ($variant->is_active ?? 0) !== 1 || (int) ($variant->stock_qty ?? 0) < $quantity) {
                    throw new Exception('Insufficient stock for ' . (string) ($variant->product_name ?? 'item') . ' (' . (string) ($variant->size ?? '-') . ')');
                }

                $lineTotal = ((float) $variant->price) * $quantity;
                $subtotal += $lineTotal;

                $validated[] = [
                    'variant_id' => (int) $variant->variant_id,
                    'product_id' => (int) $variant->product_id,
                    'product_name' => (string) $variant->product_name,
                    'size' => (string) $variant->size,
                    'unit_price' => (float) $variant->price,
                    'quantity' => $quantity,
                    'line_total' => $lineTotal,
                ];
            }

            if (empty($validated)) {
                throw new Exception('No valid cart items were submitted');
            }

            $orderNumber = $this->generateOrderNumber();
            $this->query(
                "INSERT INTO store_orders (
                    order_number,
                    buyer_name,
                    buyer_phone,
                    buyer_email,
                    buyer_address,
                    payment_method,
                    status,
                    subtotal,
                    total
                 ) VALUES (
                    :order_number,
                    :buyer_name,
                    :buyer_phone,
                    :buyer_email,
                    :buyer_address,
                    'COD',
                    'Pending',
                    :subtotal,
                    :total
                 )",
                [
                    'order_number' => $orderNumber,
                    'buyer_name' => $buyerName,
                    'buyer_phone' => $buyerPhone,
                    'buyer_email' => $buyerEmail,
                    'buyer_address' => $buyerAddress,
                    'subtotal' => $subtotal,
                    'total' => $subtotal,
                ]
            );

            $orderId = (int) $this->lastInsertId();

            foreach ($validated as $row) {
                $this->query(
                    "INSERT INTO store_order_items (
                        order_id,
                        product_id,
                        variant_id,
                        product_name,
                        size,
                        unit_price,
                        quantity,
                        line_total
                    ) VALUES (
                        :order_id,
                        :product_id,
                        :variant_id,
                        :product_name,
                        :size,
                        :unit_price,
                        :quantity,
                        :line_total
                    )",
                    [
                        'order_id' => $orderId,
                        'product_id' => $row['product_id'],
                        'variant_id' => $row['variant_id'],
                        'product_name' => $row['product_name'],
                        'size' => $row['size'],
                        'unit_price' => $row['unit_price'],
                        'quantity' => $row['quantity'],
                        'line_total' => $row['line_total'],
                    ]
                );

                $this->query(
                    "UPDATE store_product_variants
                     SET stock_qty = stock_qty - :stock_decrement,
                         is_active = CASE WHEN stock_qty - :status_stock_decrement <= 0 THEN 0 ELSE 1 END
                     WHERE variant_id = :variant_id",
                    [
                        'stock_decrement' => $row['quantity'],
                        'status_stock_decrement' => $row['quantity'],
                        'variant_id' => $row['variant_id'],
                    ]
                );
            }

            $this->query('COMMIT');

            return [
                'order_id' => $orderId,
                'order_number' => $orderNumber,
                'total' => round($subtotal, 2),
            ];
        } catch (Throwable $e) {
            $this->query('ROLLBACK');
            throw $e;
        }
    }

    public function getAdminOrders($status = '', $limit = 200)
    {
        $this->ensureTables();
        $safeLimit = max(1, (int) $limit);

        if ($status !== '' && in_array($status, ['Pending', 'Completed', 'Cancelled'], true)) {
            return $this->query(
                "SELECT * FROM store_orders WHERE status = :status ORDER BY created_at DESC LIMIT {$safeLimit}",
                ['status' => $status]
            );
        }

        return $this->query("SELECT * FROM store_orders ORDER BY created_at DESC LIMIT {$safeLimit}");
    }

    public function getOrderItemsByOrderId($orderId)
    {
        $this->ensureTables();

        return $this->query(
            "SELECT
                order_item_id,
                order_id,
                product_id,
                variant_id,
                product_name,
                size,
                unit_price,
                quantity,
                line_total,
                created_at
             FROM store_order_items
             WHERE order_id = :order_id
             ORDER BY order_item_id ASC",
            ['order_id' => (int) $orderId]
        );
    }

    public function getAdminOrdersWithItems($status = '', $limit = 200)
    {
        $orders = $this->getAdminOrders($status, $limit);

        foreach ($orders as $order) {
            $order->items = $this->getOrderItemsByOrderId((int) ($order->order_id ?? 0));
        }

        return $orders;
    }
}
