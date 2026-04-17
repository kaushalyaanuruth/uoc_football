<?php
// Get all events from controller data
$allEvents = $data['allEvents'] ?? [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Events - UOC Football</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/moreEvent/header/style.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/moreEvent/footer/style.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/moreEvent/containers/style.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/moreEvent/default/style.css">
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

    <section class="events-section">
        <div class="main-container">
            <a href="<?php echo ROOT; ?>/landingPage#events" class="back-button">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Back
            </a>

            <div class="events-grid">
                <?php
                if (!empty($allEvents)) {
                    foreach ($allEvents as $event):
                        // Format date
                        $formattedDate = date('F j, Y', strtotime($event->date ?? $event->event_date));
                        
                        // Format time if available
                        $timeDisplay = '';
                        if (!empty($event->event_time)) {
                            $timeDisplay = ' at ' . date('g:i A', strtotime($event->event_time));
                        }
                ?>
                <div class="event-card" data-event-id="<?php echo $event->event_id; ?>">
                    <!-- Event Image -->
                    <div class="event-image">
                        <?php if (!empty($event->image)): ?>
                            <img src="<?php echo ROOT; ?>/uploads/event_images/<?php echo htmlspecialchars($event->image); ?>" alt="<?php echo htmlspecialchars($event->title); ?>">
                        <?php else: ?>
                            <div class="event-image-placeholder">
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
                                echo $eventEmojis[strtolower($event->event_type ?? 'match')] ?? '📅';
                                ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Event Content -->
                    <div class="event-content">
                        <h3 class="event-title"><?php echo htmlspecialchars($event->title); ?></h3>
                        <p class="event-date"><?php echo $formattedDate . $timeDisplay; ?></p>
                        <?php if (!empty($event->location)): ?>
                            <p class="event-location">📍 <?php echo htmlspecialchars($event->location); ?></p>
                        <?php endif; ?>
                        <p class="event-excerpt">
                            <?php 
                            $excerpt = strip_tags($event->description ?? '');
                            echo htmlspecialchars(substr($excerpt, 0, 120) . (strlen($excerpt) > 120 ? '...' : '')); 
                            ?>
                        </p>
                        <button class="read-more-btn" 
                                data-id="<?php echo $event->event_id; ?>"
                                data-title="<?php echo htmlspecialchars($event->title); ?>"
                                data-date="<?php echo $formattedDate; ?>"
                                data-time="<?php echo htmlspecialchars($event->event_time ?? ''); ?>"
                                data-location="<?php echo htmlspecialchars($event->location ?? ''); ?>"
                                data-image="<?php echo htmlspecialchars($event->image ?? ''); ?>"
                                data-description="<?php echo htmlspecialchars($event->description ?? ''); ?>">
                            Details
                        </button>
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
    </section>

    <!-- Event Modal/Popup -->
    <div class="event-modal" id="eventModal">
        <div class="modal-overlay" id="modalOverlay"></div>
        <div class="modal-content">
            <button class="modal-close" id="modalClose">&times;</button>
            <div class="modal-image" id="modalImage"></div>
            <div class="modal-body">
                <h2 class="modal-title" id="modalTitle"></h2>
                <p class="modal-date" id="modalDate"></p>
                <p class="modal-location" id="modalLocation"></p>
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
    <script src="<?php echo ROOT; ?>/assets/js/moreEvent/header/script.js"></script>
    <script src="<?php echo ROOT; ?>/assets/js/moreEvent/footer/script.js"></script>
    <script src="<?php echo ROOT; ?>/assets/js/moreEvent/default/script.js"></script>
</body>
</html>