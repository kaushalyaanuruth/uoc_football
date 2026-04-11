<?php
$news = $data['news'] ?? null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($news->title ?? 'News'); ?> - UOC Football</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/landingPage/header/style.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/landingPage/footer/style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html {
            scroll-behavior: smooth;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: #333;
            background: #f9f9f9;
        }
        
        .news-detail-container {
            max-width: 800px;
            margin: 40px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .news-detail-header {
            margin-bottom: 30px;
        }
        
        .news-detail-title {
            font-size: 2.5rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 15px;
            line-height: 1.3;
        }
        
        .news-detail-meta {
            display: flex;
            gap: 20px;
            color: #666;
            font-size: 0.95rem;
            margin-bottom: 20px;
        }
        
        .news-detail-image {
            width: 100%;
            max-height: 500px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        .news-detail-content {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #444;
            word-wrap: break-word;
            white-space: pre-wrap;
        }
        
        .back-button {
            display: inline-block;
            margin-top: 30px;
            padding: 12px 24px;
            background: #663399;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: all 0.3s ease;
            font-weight: 600;
        }
        
        .back-button:hover {
            background: #552288;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 51, 153, 0.3);
        }
        
        @media (max-width: 768px) {
            .news-detail-container {
                margin: 20px 10px;
                padding: 15px;
            }
            
            .news-detail-title {
                font-size: 1.75rem;
            }
            
            .news-detail-content {
                font-size: 1rem;
            }
        }
    </style>
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
            </ul>
            <a href="<?php echo ROOT; ?>/login" class="team-portal">Team Portal</a>
        </nav>
    </div>

    <div class="news-detail-container">
        <?php if ($news): ?>
            <div class="news-detail-header">
                <h1 class="news-detail-title"><?php echo htmlspecialchars($news->title); ?></h1>
                <div class="news-detail-meta">
                    <span>📅 <?php echo date('F j, Y', strtotime($news->publish_date)); ?></span>
                    <span>✍️ By <?php echo htmlspecialchars($news->author_id ?? 'Admin'); ?></span>
                </div>
            </div>

            <?php if (!empty($news->image)): ?>
                <img src="<?php echo ROOT; ?>/uploads/news_images/<?php echo htmlspecialchars($news->image); ?>" alt="<?php echo htmlspecialchars($news->title); ?>" class="news-detail-image">
            <?php endif; ?>

            <div class="news-detail-content">
                <?php echo htmlspecialchars($news->content); ?>
            </div>

            <a href="<?php echo ROOT; ?>/landingPage#news" class="back-button">← Back to News</a>
        <?php else: ?>
            <h1>News Not Found</h1>
            <p>The news article you're looking for doesn't exist.</p>
            <a href="<?php echo ROOT; ?>/landingPage" class="back-button">← Back to Home</a>
        <?php endif; ?>
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
</body>
</html>
