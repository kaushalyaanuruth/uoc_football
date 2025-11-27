<?php
// Get gallery data from controller
$images = $data['images'] ?? [];
$currentPage = $data['currentPage'] ?? 1;
$totalPages = $data['totalPages'] ?? 1;
$totalImages = $data['totalImages'] ?? 0;
$category = $data['category'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gallery - UOC Football</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/gallery/styles.css">
    <script src="<?php echo ROOT; ?>/assets/js/gallery/script.js"></script>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/gallery/header/style.css">
    <script src="<?php echo ROOT; ?>/assets/js/gallery/header/script.js"></script>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/gallery/footer/style.css">
    <script src="<?php echo ROOT; ?>/assets/js/gallery/footer/script.js"></script>
</head>
<body>
    <div class="header">
        <nav class="nav-container">
            <div class="left-section">
                <a href="http://localhost/UOC_Football/public/landingPage"><img class="header-logo" src="<?php echo ROOT; ?>/assets/images/gallery/images/uoclogo.png" alt="Football Player"></a>
            </div>
            <ul class="nav-menu">
                <li><a href="<?php echo ROOT; ?>/moreNews">News</a></li>
                <li><a href="http://localhost/UOC_Football/public/moreEvent">Events</a></li>
                <li><a href="http://localhost/UOC_Football/public/team">Team</a></li>
                <li><a href="http://localhost/UOC_Football/public/gallery">Gallery</a></li>
            </ul>
            <a href="http://localhost/UOC_Football/public/login" class="team-portal" target="_blank" rel="noopener noreferrer">Team Portal</a>
        </nav>
    </div>
    <!-- Hero Section -->
    <section class="hero">
        <div class="hero-overlay"></div>
        <div class="hero-content">
            <h1>GALLERY</h1>
            <p>Capturing moments of excellence and team spirit</p>
        </div>
    </section>

    <!-- Gallery Grid -->
    <section class="gallery-section">
        <div class="container">
            <div class="gallery-grid" id="galleryGrid">
                
                <?php
                if (!empty($images)) {
                    foreach ($images as $image):
                        // Get category emoji
                        $categoryEmojis = [
                            'matches' => '⚽',
                            'training' => '🏃',
                            'team' => '👥',
                            'events' => '🎉',
                            'other' => '📸'
                        ];
                        $emoji = $categoryEmojis[$image->category] ?? '📸';
                ?>
                <!-- Gallery Item -->
                <div class="gallery-item" data-category="<?php echo htmlspecialchars($image->category); ?>">
                    <div class="gallery-image">
                        <?php 
                        // Check if we have filepath or filename
                        if (!empty($image->filepath)):
                            // filepath already contains the full path like "uploads/gallery/image.jpg"
                            $imagePath = $image->filepath;
                        elseif (!empty($image->filename)):
                            // filename is just the file, so add the path
                            $imagePath = 'uploads/gallery/' . $image->filename;
                        else:
                            $imagePath = '';
                        endif;
                        
                        if (!empty($imagePath)):
                        ?>
                            <img src="<?php echo ROOT; ?>/<?php echo htmlspecialchars($imagePath); ?>" 
                                 alt="<?php echo htmlspecialchars($image->description ?? 'Gallery Image'); ?>"
                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                            <div style="width:100%; height:200px; display:none; align-items:center; justify-content:center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; font-size: 48px;">
                                <?php echo $emoji; ?>
                            </div>
                        <?php else: ?>
                            <div style="width:100%; height:200px; display:flex; align-items:center; justify-content:center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; font-size: 48px;">
                                <?php echo $emoji; ?>
                            </div>
                        <?php endif; ?>
                        <div class="gallery-overlay">
                            <div class="overlay-content">
                                <h3><?php echo htmlspecialchars($image->description ?? 'Gallery Image'); ?></h3>
                                <p><?php echo date('F Y', strtotime($image->created_at)); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <?php 
                    endforeach;
                } else {
                ?>
                <div class="empty-state" style="grid-column: 1 / -1; text-align: center; padding: 3rem; color: #666; font-size: 1.2rem;">
                    <p>No images available in the gallery yet. Check back soon!</p>
                </div>
                <?php } ?>

            </div>

            <?php if ($totalPages > 1): ?>
            <!-- Pagination -->
            <div class="pagination">
                <?php if ($currentPage > 1): ?>
                    <a href="<?php echo ROOT; ?>/gallery?page=<?php echo $currentPage - 1; ?><?php echo $category ? '&category=' . $category : ''; ?>" class="page-btn">&laquo; Previous</a>
                <?php endif; ?>
                
                <?php
                // Show page numbers
                $startPage = max(1, $currentPage - 2);
                $endPage = min($totalPages, $currentPage + 2);
                
                if ($startPage > 1): ?>
                    <a href="<?php echo ROOT; ?>/gallery?page=1<?php echo $category ? '&category=' . $category : ''; ?>" class="page-number">1</a>
                    <?php if ($startPage > 2): ?>
                        <span class="page-dots">...</span>
                    <?php endif; ?>
                <?php endif; ?>
                
                <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                    <a href="<?php echo ROOT; ?>/gallery?page=<?php echo $i; ?><?php echo $category ? '&category=' . $category : ''; ?>" 
                       class="page-number <?php echo $i == $currentPage ? 'active' : ''; ?>">
                        <?php echo $i; ?>
                    </a>
                <?php endfor; ?>
                
                <?php if ($endPage < $totalPages): ?>
                    <?php if ($endPage < $totalPages - 1): ?>
                        <span class="page-dots">...</span>
                    <?php endif; ?>
                    <a href="<?php echo ROOT; ?>/gallery?page=<?php echo $totalPages; ?><?php echo $category ? '&category=' . $category : ''; ?>" class="page-number"><?php echo $totalPages; ?></a>
                <?php endif; ?>
                
                <?php if ($currentPage < $totalPages): ?>
                    <a href="<?php echo ROOT; ?>/gallery?page=<?php echo $currentPage + 1; ?><?php echo $category ? '&category=' . $category : ''; ?>" class="page-btn">Next &raquo;</a>
                <?php endif; ?>
            </div>
            
            <p class="pagination-info">Showing page <?php echo $currentPage; ?> of <?php echo $totalPages; ?> (<?php echo $totalImages; ?> total images)</p>
            <?php endif; ?>
        </div>
    </section>

    <div class="lightbox" id="lightbox">
        <span class="lightbox-close" id="lightboxClose">&times;</span>
        <button class="lightbox-nav lightbox-prev" id="lightboxPrev">&#10094;</button>
        <button class="lightbox-nav lightbox-next" id="lightboxNext">&#10095;</button>
        <div class="lightbox-content">
            <img src="" alt="" id="lightboxImage">
            <div class="lightbox-caption">
                <h3 id="lightboxTitle"></h3>
                <p id="lightboxDesc"></p>
            </div>
        </div>
    </div>

   <footer class="footer">
        <div class="footer-row">
            <!-- Left: UOC Logos -->
            <div class="footer-left">
                <img src="<?php echo ROOT; ?>/assets/images/gallery/images/uoc-football-logo.png" alt="UOC Football Logo" class="footer-logo">
                <img src="<?php echo ROOT; ?>/assets/images/gallery/images/uoc-logo.png" alt="UOC Logo" class="footer-logo">
            </div>

            <!-- Center: Sponsors -->
            <div class="footer-center">
                <h4 class="sponsor-title">Sponsors</h4>
                <div class="sponsor-logos">
                    <img src="<?php echo ROOT; ?>/assets/images/gallery/images/lanka-lands-logo.png" alt="Lanka Lands" class="footer-sponsor">
                    <img src="<?php echo ROOT; ?>/assets/images/gallery/images/appeton-logo.png" alt="Appeton" class="footer-sponsor">
                </div>
            </div>

            <!-- Right: Social Links -->
            <div class="footer-right">
                <p class="follow-text">Follow us</p>
                <div class="social-links">
                    <a href="#" class="social-link">
                        <img src="<?php echo ROOT; ?>/assets/images/gallery/images/instagram.png" alt="Instagram" class="social-icon">
                    </a>
                    <a href="#" class="social-link">
                        <img src="<?php echo ROOT; ?>/assets/images/gallery/images/facebook.png" alt="Facebook" class="social-icon">
                    </a>
                </div>
            </div>
        </div>

        <p class="footer-copy">UOC FOOTBALL © 2025 All rights reserved</p>
    </footer>

    <script src="script.js"></script>
</body>
</html>