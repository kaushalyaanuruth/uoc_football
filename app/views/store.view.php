<?php
$items = $data['items'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <title>UOC Football Store</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/landingPage/header/style.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/landingPage/footer/style.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/landingPage/store/style.css">
</head>
<body>
    <div class="header">
        <nav class="nav-container">
            <div class="left-section">
                <a href="<?php echo ROOT; ?>/landingPage"><img class="header-logo" src="<?php echo ROOT; ?>/assets/images/landingPage/header/uoclogo.png" alt="UOC Football Logo"></a>
            </div>
            <ul class="nav-menu">
                <li><a href="<?php echo ROOT; ?>/landingPage#news">News</a></li>
                <li><a href="<?php echo ROOT; ?>/landingPage#events">Events</a></li>
                <li><a href="<?php echo ROOT; ?>/landingPage#team">Team</a></li>
                <li><a href="<?php echo ROOT; ?>/gallery">Gallery</a></li>
                <li><a href="<?php echo ROOT; ?>/store" class="active">Store</a></li>
            </ul>
            <a href="<?php echo ROOT; ?>/login" class="team-portal" target="_blank" rel="noopener noreferrer">Team Portal</a>
            <button class="hamburger-menu" id="hamburgerMenu">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </nav>
    </div>

    <div class="mobile-drawer" id="mobileDrawer">
        <div class="mobile-drawer-content">
            <a href="<?php echo ROOT; ?>/landingPage#news">News</a>
            <a href="<?php echo ROOT; ?>/landingPage#events">Events</a>
            <a href="<?php echo ROOT; ?>/landingPage#team">Team</a>
            <a href="<?php echo ROOT; ?>/gallery">Gallery</a>
            <a href="<?php echo ROOT; ?>/store">Store</a>
            <div class="mobile-drawer-buttons">
                <a href="<?php echo ROOT; ?>/login" class="team-portal store-mobile-portal" target="_blank" rel="noopener noreferrer">Team Portal</a>
            </div>
        </div>
    </div>

    <main class="store-page">
        <section class="store-hero">
            <h1>Official Store</h1>
            <p>Shop official UOC Football jerseys and merchandise. Support the team and wear the colors with pride.</p>
        </section>

        <section class="store-container">
            <div class="store-toolbar">
                <button type="button" class="store-cart-btn" id="openCartBtn">Cart (<span id="cartCount">0</span>)</button>
            </div>
            <?php if (empty($items)): ?>
                <div class="store-empty">
                    <h3>Store items are coming soon</h3>
                    <p>Please check back later for the latest merchandise.</p>
                </div>
            <?php else: ?>
                <div class="store-grid" id="storeGrid">
                    <?php foreach ($items as $item): ?>
                        <?php
                        $isSoldOut = ((int) ($item->is_active ?? 0) !== 1) || ((int) ($item->total_stock ?? 0) <= 0);
                        $image = trim((string) ($item->product_image ?? ''));
                        $imageUrl = $image !== '' ? (ROOT . '/' . ltrim(str_replace('\\', '/', $image), '/')) : '';
                        $variants = $item->variants ?? [];
                        ?>
                        <article class="store-card" data-product-id="<?php echo (int) ($item->product_id ?? 0); ?>" data-product-name="<?php echo htmlspecialchars((string) ($item->product_name ?? 'Item')); ?>">
                            <div class="store-image">
                                <?php if ($imageUrl !== ''): ?>
                                    <img src="<?php echo htmlspecialchars($imageUrl); ?>" alt="<?php echo htmlspecialchars((string) ($item->product_name ?? 'Store item')); ?>">
                                <?php else: ?>
                                    <span class="store-image-fallback">Store</span>
                                <?php endif; ?>
                            </div>
                            <div class="store-body">
                                <div class="store-head">
                                    <h3 class="store-name"><?php echo htmlspecialchars((string) ($item->product_name ?? 'Item')); ?></h3>
                                    <span class="store-category"><?php echo htmlspecialchars((string) ($item->category ?? 'Merchandise')); ?></span>
                                </div>
                                <div class="store-price">From Rs. <?php echo number_format((float) ($item->min_price ?? 0), 2); ?></div>
                                <div class="store-stock <?php echo $isSoldOut ? 'sold' : ''; ?>">
                                    <?php echo $isSoldOut ? 'Sold Out' : ('In stock: ' . (int) ($item->total_stock ?? 0)); ?>
                                </div>
                                <p class="store-description"><?php echo htmlspecialchars((string) ($item->description ?? 'Official UOC Football merchandise.')); ?></p>
                                <div class="store-buy-box">
                                    <label class="store-field-label">Size</label>
                                    <select class="store-variant-select" <?php echo $isSoldOut ? 'disabled' : ''; ?>>
                                        <?php foreach ($variants as $variant): ?>
                                            <?php if ((int) ($variant->stock_qty ?? 0) <= 0) { continue; } ?>
                                            <option value="<?php echo (int) ($variant->variant_id ?? 0); ?>" data-price="<?php echo (float) ($variant->price ?? 0); ?>" data-size="<?php echo htmlspecialchars((string) ($variant->size ?? '')); ?>">
                                                <?php echo htmlspecialchars((string) ($variant->size ?? 'One Size')); ?> - Rs. <?php echo number_format((float) ($variant->price ?? 0), 2); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>

                                    <label class="store-field-label">Quantity</label>
                                    <input type="number" class="store-qty-input" min="1" value="1" <?php echo $isSoldOut ? 'disabled' : ''; ?>>

                                    <button type="button" class="store-add-btn" <?php echo $isSoldOut ? 'disabled' : ''; ?>>Add to Cart</button>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <aside class="store-cart-drawer" id="storeCartDrawer" aria-hidden="true">
        <div class="store-cart-head">
            <h3>Your Cart</h3>
            <button type="button" id="closeCartBtn">&times;</button>
        </div>
        <div class="store-cart-items" id="cartItems"></div>
        <div class="store-cart-footer">
            <div class="store-cart-total">Total: Rs. <span id="cartTotal">0.00</span></div>
            <button type="button" class="store-checkout-btn" id="openCheckoutBtn">Checkout</button>
        </div>
    </aside>
    <div class="store-overlay" id="storeOverlay" hidden></div>

    <div class="store-checkout-modal" id="checkoutModal" hidden>
        <form class="store-checkout-form" id="checkoutForm">
            <h3>Checkout Details</h3>
            <label>Name</label>
            <input type="text" name="buyer_name" required>
            <label>Phone</label>
            <input type="text" name="buyer_phone" required>
            <label>Email</label>
            <input type="email" name="buyer_email" required>
            <label>Address</label>
            <textarea name="buyer_address" rows="3" required></textarea>
            <div class="store-checkout-actions">
                <button type="button" id="cancelCheckoutBtn">Cancel</button>
                <button type="submit">Place Order</button>
            </div>
        </form>
    </div>

    <footer class="footer">
        <div class="footer-row">
            <div class="footer-left">
                <img src="<?php echo ROOT; ?>/assets/images/landingPage/footer/uoc-football-logo.png" alt="UOC Football Logo" class="footer-logo">
                <img src="<?php echo ROOT; ?>/assets/images/landingPage/footer/uoc-logo.png" alt="UOC Logo" class="footer-logo">
            </div>
            <div class="footer-center">
                <h4 class="sponsor-title">Sponsors</h4>
                <div class="sponsor-logos">
                    <img src="<?php echo ROOT; ?>/assets/images/landingPage/footer/lanka-lands-logo.png" alt="Lanka Lands" class="footer-sponsor">
                    <img src="<?php echo ROOT; ?>/assets/images/landingPage/footer/appeton-logo.png" alt="Appeton" class="footer-sponsor">
                </div>
            </div>
            <div class="footer-right">
                <p class="follow-text">Follow us</p>
                <div class="social-links">
                    <a href="#" class="social-link">
                        <img src="<?php echo ROOT; ?>/assets/images/landingPage/footer/instagram.png" alt="Instagram" class="social-icon">
                    </a>
                    <a href="#" class="social-link">
                        <img src="<?php echo ROOT; ?>/assets/images/landingPage/footer/facebook.png" alt="Facebook" class="social-icon">
                    </a>
                </div>
            </div>
        </div>
        <p class="footer-copy">UOC FOOTBALL © 2025 All rights reserved</p>
    </footer>

    <script src="<?php echo ROOT; ?>/assets/js/landingPage/header/script.js"></script>
    <script>
        window.STORE_CONFIG = {
            checkoutUrl: '<?php echo ROOT; ?>/store/checkout'
        };
    </script>
    <script src="<?php echo ROOT; ?>/assets/js/landingPage/store/script.js"></script>
</body>
</html>
