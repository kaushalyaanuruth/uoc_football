<?php
// Get all events from controller data
$allEvents = $data['allEvents'] ?? [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University of Colombo Football</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/moreEvent/header/style.css">
    <script src="<?php echo ROOT; ?>/assets/js/moreEvent/header/script.js"></script>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/moreEvent/footer/style.css">
    <script src="<?php echo ROOT; ?>/assets/js/moreEvent/footer/script.js"></script>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/moreEvent/containers/style.css">
    <script src="<?php echo ROOT; ?>/assets/js/moreEvent/containers/script.js"></script>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/moreEvent/default/style.css">
    <script src="<?php echo ROOT; ?>/assets/js/moreEvent/default/script.js"></script>
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
                <li><a href="<?php echo ROOT; ?>/moreNews">News</a></li>
                <li><a href="http://localhost/UOC_Football/public/event">Events</a></li>
                <li><a href="http://localhost/UOC_Football/public/team">Team</a></li>
                <li><a href="http://localhost/UOC_Football/public/gallery">Gallery</a></li>
            </ul>
            <a href="http://localhost/UOC_Football/public/login" class="team-portal" target="_blank" rel="noopener noreferrer">Team Portal</a>
        </nav>
    </div>
    <section class="containers-section">
        <div class="main-container">
            <a href="http://localhost/UOC_Football/public/landingPage#events" class="back-button">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    Back
            </a>

        
            <div class="sessions-grid">
                <?php
                if (!empty($allEvents)) {
                    foreach ($allEvents as $event):
                        // Format date
                        $formattedDate = date('F j, Y', strtotime($event->event_date));
                        
                        // Format time if available
                        $timeDisplay = '';
                        if (!empty($event->event_time)) {
                            $timeDisplay = ' at ' . date('g:i A', strtotime($event->event_time));
                        }
                ?>
                <div class="session-card">
                    <!-- Event Image -->
                    <?php if (!empty($event->image)): ?>
                        <img src="<?php echo ROOT; ?>/uploads/event_images/<?php echo htmlspecialchars($event->image); ?>" alt="<?php echo htmlspecialchars($event->title); ?>" class="session-image">
                    <?php else: ?>
                        <div class="session-image" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 64px;">
                            <?php
                            // Display emoji based on event type
                            $eventEmojis = [
                                'match' => '⚽',
                                'tournament' => '🏆',
                                'training' => '🏃',
                                'meeting' => '🎯',
                                'social' => '🎉',
                                'other' => '📅'
                            ];
                            echo $eventEmojis[strtolower($event->event_type)] ?? '📅';
                            ?>
                        </div>
                    <?php endif; ?>

                    <!-- Event Content -->
                    <div class="session-content">
                        <h3 class="session-title"><?php echo htmlspecialchars($event->title); ?></h3>
                        <p class="session-description">
                            <?php echo htmlspecialchars($event->description ?? 'Event details coming soon...'); ?>
                        </p>
                        
                        <!-- Event Details -->
                        <div class="session-details">
                            <div class="detail-item">
                                <!-- Calendar icon -->
                                <svg class="detail-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 
                                        2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <?php echo $formattedDate . $timeDisplay; ?>
                            </div>
                            <?php if (!empty($event->location)): ?>
                            <div class="detail-item">
                                <!-- Location icon -->
                                <svg class="detail-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M12 11c1.657 0 3-1.343 3-3S13.657 5 
                                        12 5s-3 1.343-3 3 1.343 3 
                                        3 3z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                        d="M12 22s8-4.5 8-10a8 8 0 
                                        10-16 0c0 5.5 8 10 8 10z"/>
                                </svg>
                                <?php echo htmlspecialchars($event->location); ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php 
                    endforeach;
                } else {
                ?>
                <div class="empty-state" style="grid-column: 1 / -1; text-align: center; padding: 3rem; color: #666; font-size: 1.2rem;">
                    <p>No upcoming events at the moment. Check back soon!</p>
                </div>
                <?php } ?>
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
</body>
</html>