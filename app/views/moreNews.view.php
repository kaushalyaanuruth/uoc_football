<?php
// Get all news from controller data
$allNews = $data['allNews'] ?? [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All News - UOC Football</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/moreNews/header/style.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/moreNews/footer/style.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/moreNews/containers/style.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/moreNews/default/styles.css">
    <style>
        *{
            font-family: 'poppins', sans-serif;
        }
        body {
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <nav class="nav-container">
            <div class="left-section">
                <a href="<?php echo ROOT; ?>/landingPage"><img class="header-logo" src="<?php echo ROOT; ?>/assets/images/landingPage/header/uoclogo.png" alt="UOC Logo"></a>
            </div>
            <ul class="nav-menu">
                <li><a href="<?php echo ROOT; ?>/moreNews">News</a></li>
                <li><a href="<?php echo ROOT; ?>/moreEvent">Events</a></li>
                <li><a href="<?php echo ROOT; ?>/team">Team</a></li>
                <li><a href="<?php echo ROOT; ?>/gallery">Gallery</a></li>
            </ul>
            <a href="<?php echo ROOT; ?>/login" class="team-portal" target="_blank" rel="noopener noreferrer">Team Portal</a>
        </nav>
    </div>

    <section class="news-section">
        <div class="main-container">
            <a href="<?php echo ROOT; ?>/landingPage#news" class="back-button">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Back
            </a>

            <div class="news-grid">
                <?php
                if (!empty($allNews)) {
                    foreach ($allNews as $news):
                        // Format date
                        $formattedDate = date('F j, Y', strtotime($news->publish_date));
                ?>
                <div class="news-card" data-news-id="<?php echo $news->id; ?>">
                    <!-- News Image -->
                    <div class="news-image">
                        <?php if (!empty($news->image)): ?>
                            <img src="<?php echo ROOT; ?>/uploads/news_images/<?php echo htmlspecialchars($news->image); ?>" alt="<?php echo htmlspecialchars($news->title); ?>">
                        <?php else: ?>
                            <div class="news-image-placeholder">
                                📰
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- News Content -->
                    <div class="news-content">
                        <h3 class="news-title"><?php echo htmlspecialchars($news->title); ?></h3>
                        <p class="news-date"><?php echo $formattedDate; ?></p>
                        <p class="news-excerpt">
                            <?php 
                            $excerpt = strip_tags($news->content);
                            echo htmlspecialchars(substr($excerpt, 0, 120) . (strlen($excerpt) > 120 ? '...' : '')); 
                            ?>
                        </p>
                        <button class="read-more-btn" 
                                data-id="<?php echo $news->id; ?>"
                                data-title="<?php echo htmlspecialchars($news->title); ?>"
                                data-date="<?php echo $formattedDate; ?>"
                                data-image="<?php echo htmlspecialchars($news->image ?? ''); ?>"
                                data-content="<?php echo htmlspecialchars($news->content); ?>">
                            Read More
                        </button>
                    </div>
                </div>
                <?php 
                    endforeach;
                } else {
                ?>
                <div class="empty-state">
                    <p>No news available at the moment. Check back soon!</p>
                </div>
                <?php } ?>
            </div>
        </div>
    </section>

    <!-- News Modal/Popup -->
    <div class="news-modal" id="newsModal">
        <div class="modal-overlay" id="modalOverlay"></div>
        <div class="modal-content">
            <button class="modal-close" id="modalClose">&times;</button>
            <div class="modal-image" id="modalImage"></div>
            <div class="modal-body">
                <h2 class="modal-title" id="modalTitle"></h2>
                <p class="modal-date" id="modalDate"></p>
                <div class="modal-text" id="modalText"></div>
            </div>
        </div>
    </div>

    <footer class="footer">
        <div class="footer-row">
            <!-- Left: UOC Logos -->
            <div class="footer-left">
                <img src="<?php echo ROOT; ?>/assets/images/landingPage/footer/uoc-football-logo.png" alt="UOC Football Logo" class="footer-logo">
                <img src="<?php echo ROOT; ?>/assets/images/landingPage/footer/uoc-logo.png" alt="UOC Logo" class="footer-logo">
            </div>

            <!-- Center: Sponsors -->
            <div class="footer-center">
                <h4 class="sponsor-title">Sponsors</h4>
                <div class="sponsor-logos">
                    <img src="<?php echo ROOT; ?>/assets/images/landingPage/footer/lanka-lands-logo.png" alt="Lanka Lands" class="footer-sponsor">
                    <img src="<?php echo ROOT; ?>/assets/images/landingPage/footer/appeton-logo.png" alt="Appeton" class="footer-sponsor">
                </div>
            </div>

            <!-- Right: Social Links -->
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
    <script src="<?php echo ROOT; ?>/assets/js/moreNews/header/script.js"></script>
    <script src="<?php echo ROOT; ?>/assets/js/moreNews/footer/script.js"></script>
    <script src="<?php echo ROOT; ?>/assets/js/moreNews/containers/script.js"></script>
</body>
</html>
