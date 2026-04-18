<?php
// Get latest news from controller data
$latestNews = $data['latestNews'] ?? [];
// Get upcoming events from controller data
$upcomingEvents = $data['upcomingEvents'] ?? [];
// Get latest gallery images from controller data
$latestGalleryImages = $data['latestGalleryImages'] ?? [];
$landingTeamCards = $data['landingTeamCards'] ?? [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <title>University of Colombo Football</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/landingPage/header/style.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/landingPage/hero/style.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/landingPage/main-feature/style.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/landingPage/news/style.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/landingPage/values/style.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/landingPage/events/style.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/landingPage/team/style.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/landingPage/gallery/style.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/landingPage/footer/style.css">
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
                <a href="http://localhost/UOC_Football/public/landingPage"><img class="header-logo" src="<?php echo ROOT; ?>/assets/images/landingPage/header/uoclogo.png" alt="Football Player"></a>
            </div>
            <ul class="nav-menu">
                <li><a href="#news">News</a></li>
                <li><a href="#events">Events</a></li>
                <li><a href="#team">Team</a></li>
                <li><a href="http://localhost/UOC_Football/public/gallery">Gallery</a></li>
            </ul>
            <a href="http://localhost/UOC_Football/public/login" class="team-portal" target="_blank" rel="noopener noreferrer">Team Portal</a>
        </nav>
    </div>
    <div class="hero">
        <div class="hero-content">
            <img class="hero-img" src="<?php echo ROOT; ?>/assets/images/landingPage/hero/image.png" alt="Football Team">
            <div class="centered">
                <h1>UNIVERSITY OF<br>COLOMBO<br>FOOTBALL</h1>
            </div>
        </div>
    </div>
    <div class="main-feature">
        <div class="feature-card">
            <?php if (!empty($latestNews) && isset($latestNews[0])): ?>
                <div class="feature-image">
                    <?php if (!empty($latestNews[0]->image)): ?>
                        <img src="<?php echo ROOT; ?>/uploads/news_images/<?php echo htmlspecialchars($latestNews[0]->image); ?>" alt="<?php echo htmlspecialchars($latestNews[0]->title); ?>">
                    <?php else: ?>
                        <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; font-size: 96px;">📰</div>
                    <?php endif; ?>
                </div>
                <div class="feature-text">
                    <h2><?php echo htmlspecialchars($latestNews[0]->title); ?></h2>
                    <p class="feature-date"><?php echo date('F j, Y', strtotime($latestNews[0]->publish_date)); ?></p>
                </div>
            <?php else: ?>
                <div class="feature-text">
                    <h2>No Featured News Available</h2>
                    <p>Check back soon for updates!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="news" id="news">
        <div class="news-container">
            <div class="news-header">
                <h2 class="section-title">Latest News</h2>
                <a href="<?php echo ROOT; ?>/moreNews" class="view-all-news-btn">View All</a>
            </div>
            <div class="news-grid">
                <?php
                if (!empty($latestNews)) {
                    // Skip the first item since it's used in main-feature
                    $remainingNews = array_slice($latestNews, 1);

                    foreach ($remainingNews as $news): ?>
                        <div class="news-card">
                            <div class="news-image">
                                <?php if (!empty($news->image)): ?>
                                    <img src="<?php echo ROOT . '/uploads/news_images/' . htmlspecialchars($news->image); ?>" alt="<?php echo htmlspecialchars($news->title); ?>">
                                <?php else: ?>
                                    <div style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; font-size: 48px;">📰</div>
                                <?php endif; ?>
                            </div>
                            <div class="news-content">
                                <h3><?php echo htmlspecialchars($news->title); ?></h3>
                                <p class="news-date"><?php echo date('F j, Y', strtotime($news->publish_date)); ?></p>
                            </div>
                        </div>
                    <?php endforeach;
                } else { ?>
                    <div class="empty-state">
                        <p>No news available at the moment.</p>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <div class="values">
        <div class="values-container">
            <div class="value-item">
                <span>FOCUS</span>
            </div>
            <div class="divider">|</div>
            <div class="value-item">
                <span>DISCIPLINE</span>
            </div>
            <div class="divider">|</div>
            <div class="value-item">
                <span>CONSISTENCY</span>
            </div>
        </div>
        <div class="decorative-shape"></div>
    </div>
    <div class="events" id="events">
        <div class="events-container">
        <div class="events-header">
            <h2 class="section-title">Next Events</h2>
            <a href="http://localhost/UOC_Football/public/moreEvent" class="view-all-btn">View All</a>
        </div>
        <div class="events-scroll">
            <?php
            if (!empty($upcomingEvents)) {
                // Define emoji mapping for different event types
                $eventEmojis = [
                    'match' => '⚽',
                    'tournament' => '🏆',
                    'training' => '🏃',
                    'meeting' => '🎯',
                    'social' => '🎉',
                    'other' => '📅'
                ];

                foreach ($upcomingEvents as $event):
                    // Get emoji based on event type, default to 📅
                    $emoji = $eventEmojis[strtolower($event->event_type ?? 'match')] ?? '📅';

                    $eventImageUrl = '';
                    if (!empty($event->image)) {
                        $rawImagePath = str_replace('\\', '/', ltrim((string)$event->image, '/'));
                        if (strpos($rawImagePath, 'uploads/') === 0) {
                            $eventImageUrl = ROOT . '/' . $rawImagePath;
                        } else {
                            $eventImageUrl = ROOT . '/uploads/event_images/' . $rawImagePath;
                        }
                    }
                    
                    // Format date and time
                    $eventDateTime = strtotime($event->date . ' ' . ($event->event_time ?? '15:00:00'));
                    $formattedDate = date('l, F j g:i A', $eventDateTime);
            ?>
                <div class="event-card">
                    <div class="event-image">
                        <?php if (!empty($eventImageUrl)): ?>
                            <img src="<?php echo htmlspecialchars($eventImageUrl); ?>" alt="<?php echo htmlspecialchars($event->title ?? 'Event'); ?>">
                        <?php else: ?>
                            <?php echo $emoji; ?>
                        <?php endif; ?>
                    </div>
                    <div class="event-details">
                        <h4><?php echo htmlspecialchars($event->title ?? 'UOC Football Event'); ?></h4>
                        <div class="event-date"><?php echo $formattedDate; ?></div>
                        <div class="event-type"><?php echo htmlspecialchars(ucfirst($event->event_type ?? 'match')); ?></div>
                        <?php if (!empty($event->location)): ?>
                            <div class="event-location">📍 <?php echo htmlspecialchars($event->location); ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php 
                endforeach;
            } else {
            ?>
                <div class="empty-state">
                    <p>No upcoming events at the moment. Check back soon!</p>
                </div>
            <?php } ?>
        </div>
    </div>
    <div class="team"  id="team">
        <div class="team-container">
            <h2 class="section-title">Team</h2>
            <div class="team-grid">
                <?php foreach ($landingTeamCards as $card): ?>
                    <div class="team-member">
                        <div class="team-avatar">
                            <img src="<?php echo htmlspecialchars($card['image']); ?>" alt="<?php echo htmlspecialchars($card['name']); ?>">
                            <span class="team-avatar-fallback"><?php echo htmlspecialchars($card['initials']); ?></span>
                        </div>
                        <div class="team-name"><?php echo htmlspecialchars($card['name']); ?></div>
                        <div class="team-role"><?php echo htmlspecialchars($card['role']); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
            <a href="http://localhost/UOC_Football/public/team" class="view-all-btn" style="margin-top: 2rem;">Explore full team</a>
        </div>
    </div>
    <div class="gallery" id="gallery">
        <div class="gallery-container">
            <div class="gallery-header">
                <h2 class="section-title">Gallery</h2>
                <a href="<?php echo ROOT; ?>/gallery" class="view-all-btn">View All</a>
            </div>
            <div class="gallery-grid">
                <?php
                if (!empty($latestGalleryImages)) {
                    foreach ($latestGalleryImages as $image):
                        // Get category emoji
                        $categoryEmojis = [
                            'matches' => '⚽',
                            'training' => '🏃',
                            'team' => '👥',
                            'events' => '🎉',
                            'other' => '📸',
                            'practice' => '🏋️'
                        ];
                        $emoji = $categoryEmojis[strtolower($image->category ?? 'other')] ?? '📸';
                ?>
                <div class="gallery-item">
                    <div class="gallery-image-wrapper">
                        <?php if (!empty($image->filepath)): ?>
                            <img src="<?php echo ROOT; ?>/<?php echo htmlspecialchars($image->filepath); ?>" alt="<?php echo htmlspecialchars($image->description ?? 'Gallery Image'); ?>" class="gallery-image">
                        <?php else: ?>
                            <div class="gallery-placeholder">
                                <?php echo $emoji; ?>
                            </div>
                        <?php endif; ?>
                        <div class="gallery-overlay">
                            <span class="gallery-category"><?php echo ucfirst($image->category ?? 'Other'); ?></span>
                        </div>
                    </div>
                </div>
                <?php 
                    endforeach;
                } else { ?>
                <div class="empty-state" style="grid-column: 1 / -1; text-align: center; padding: 3rem;">
                    <p>No photos available yet. Check back soon!</p>
                </div>
                <?php } ?>
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
    <script src="<?php echo ROOT; ?>/assets/js/landingPage/header/script.js"></script>
    <script src="<?php echo ROOT; ?>/assets/js/landingPage/hero/script.js"></script>
    <script src="<?php echo ROOT; ?>/assets/js/landingPage/main-feature/script.js"></script>
    <script src="<?php echo ROOT; ?>/assets/js/landingPage/news/script.js"></script>
    <script src="<?php echo ROOT; ?>/assets/js/landingPage/events/script.js"></script>
    <script src="<?php echo ROOT; ?>/assets/js/landingPage/team/script.js"></script>
    <script src="<?php echo ROOT; ?>/assets/js/landingPage/footer/script.js"></script>
</body>
</html>
