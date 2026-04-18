<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>University of Colombo Football</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/team/default/style.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/team/header/style.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/team/footer/style.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/team/players/style.css">
</head>
<body>
    <div class="header">
        <nav class="nav-container">
            <div class="left-section">
                <a href="http://localhost/UOC_Football/public/landingPage"><img class="header-logo" src="<?php echo ROOT; ?>/assets/images/team/header/uoclogo.png" alt="Football Player"></a>
            </div>
            <ul class="nav-menu">
                <li><a href="<?php echo ROOT; ?>/moreNews">News</a></li>
                <li><a href="http://localhost/UOC_Football/public/moreEvent">Events</a></li>
                <li><a href="http://localhost/UOC_Football/public/team">Team</a></li>
                <li><a href="http://localhost/UOC_Football/public/gallery">Gallery</a></li>
            </ul>
            <a href="<?php echo ROOT; ?>/login" class="team-portal" target="_blank" rel="noopener noreferrer">Team Portal</a>
        </nav>
    </div>
    <div class="main-container">
        <?php
            $playersByPosition = $data['playersByPosition'] ?? [
                'goalkeepers' => [],
                'defenders' => [],
                'midfielders' => [],
                'strikers' => []
            ];
            $coaches = $data['coaches'] ?? [];
        ?>
         <a href="<?php echo ROOT; ?>/landingPage#team" class="back-btn">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M15 18L9 12L15 6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Back
        </a>
        <div class="team-page-intro">
            <h1>Meet Our Team</h1>
            <p>Present squad grouped by playing position.</p>
        </div>
        <nav class="position-tabs">
            <a href="#goalkeepers" class="position-tab" data-position="goalkeepers">Goalkeepers</a>
            <a href="#defenders" class="position-tab" data-position="defenders">Defenders</a>
            <a href="#midfielders" class="position-tab" data-position="midfielders">Midfielders</a>
            <a href="#strikers" class="position-tab" data-position="strikers">Strikers</a>
            <a href="#coach" class="position-tab" data-position="coach">Coaches</a>
        </nav>
        <!-- Goalkeepers Section -->
        <section class="position-section" data-section="goalkeepers" id="goalkeepers">
            <h2 class="position-title" >Goalkeepers</h2>
            <div class="players-grid">
                <?php if (!empty($playersByPosition['goalkeepers'])): ?>
                    <?php foreach ($playersByPosition['goalkeepers'] as $player): ?>
                        <div class="player-card">
                            <div class="player-image">
                                <img src="<?php echo htmlspecialchars($player['image']); ?>" alt="<?php echo htmlspecialchars($player['name']); ?>" class="player-photo">
                                <div class="player-overlay">
                                    <div class="player-number"><?php echo htmlspecialchars($player['position']); ?></div>
                                    <div class="player-name"><?php echo htmlspecialchars($player['name']); ?></div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No goalkeepers assigned in present teams.</p>
                <?php endif; ?>
            </div>
        </section>
        <!-- Defenders Section -->
        <section class="position-section" data-section="defenders" id="defenders">
            <h2 class="position-title">Defenders</h2>
            <div class="players-grid">
                <?php if (!empty($playersByPosition['defenders'])): ?>
                    <?php foreach ($playersByPosition['defenders'] as $player): ?>
                        <div class="player-card">
                            <div class="player-image">
                                <img src="<?php echo htmlspecialchars($player['image']); ?>" alt="<?php echo htmlspecialchars($player['name']); ?>" class="player-photo">
                                <div class="player-overlay">
                                    <div class="player-number"><?php echo htmlspecialchars($player['position']); ?></div>
                                    <div class="player-name"><?php echo htmlspecialchars($player['name']); ?></div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No defenders assigned in present teams.</p>
                <?php endif; ?>
            </div>
        </section>
        <!-- Midfielders Section -->
        <section class="position-section" data-section="midfielders" id="midfielders">
            <h2 class="position-title" >Midfielders</h2>
            <div class="players-grid">
                <?php if (!empty($playersByPosition['midfielders'])): ?>
                    <?php foreach ($playersByPosition['midfielders'] as $player): ?>
                        <div class="player-card">
                            <div class="player-image">
                                <img src="<?php echo htmlspecialchars($player['image']); ?>" alt="<?php echo htmlspecialchars($player['name']); ?>" class="player-photo">
                                <div class="player-overlay">
                                    <div class="player-number"><?php echo htmlspecialchars($player['position']); ?></div>
                                    <div class="player-name"><?php echo htmlspecialchars($player['name']); ?></div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No midfielders assigned in present teams.</p>
                <?php endif; ?>
            </div>
        </section>
        <!-- Strikers Section -->
        <section class="position-section" data-section="strikers"  id="strikers">
            <h2 class="position-title">Strikers</h2>
            <div class="players-grid">
                <?php if (!empty($playersByPosition['strikers'])): ?>
                    <?php foreach ($playersByPosition['strikers'] as $player): ?>
                        <div class="player-card">
                            <div class="player-image">
                                <img src="<?php echo htmlspecialchars($player['image']); ?>" alt="<?php echo htmlspecialchars($player['name']); ?>" class="player-photo">
                                <div class="player-overlay">
                                    <div class="player-number"><?php echo htmlspecialchars($player['position']); ?></div>
                                    <div class="player-name"><?php echo htmlspecialchars($player['name']); ?></div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No strikers assigned in present teams.</p>
                <?php endif; ?>
            </div>
        </section>
        <!-- Coach Section -->
        <section class="position-section" data-section="coach"  id="coach">
            <h2 class="position-title">Coaches</h2>
            <div class="players-grid">
                <?php if (!empty($coaches)): ?>
                    <?php foreach ($coaches as $coach): ?>
                        <div class="player-card">
                            <div class="player-image">
                                <img src="<?php echo htmlspecialchars($coach['image']); ?>" alt="Coach" class="player-photo">
                                <div class="player-overlay">
                                    <div class="player-number"><?php echo htmlspecialchars($coach['license']); ?></div>
                                    <div class="player-name"><?php echo htmlspecialchars($coach['name']); ?></div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>No coaches assigned in present teams.</p>
                <?php endif; ?>
            </div>
        </section>
    </div>
    <footer class="footer">
        <div class="footer-row">
            <!-- Left: UOC Logos -->
            <div class="footer-left">
                <img src="<?php echo ROOT; ?>/assets/images/team/footer/uoc-football-logo.png" alt="UOC Football Logo" class="footer-logo">
                <img src="<?php echo ROOT; ?>/assets/images/team/footer/uoc-logo.png" alt="UOC Logo" class="footer-logo">
            </div>

            <!-- Center: Sponsors -->
            <div class="footer-center">
                <h4 class="sponsor-title">Sponsors</h4>
                <div class="sponsor-logos">
                    <img src="<?php echo ROOT; ?>/assets/images/team/footer/lanka-lands-logo.png" alt="Lanka Lands" class="footer-sponsor">
                    <img src="<?php echo ROOT; ?>/assets/images/team/footer/appeton-logo.png" alt="Appeton" class="footer-sponsor">
                </div>
            </div>

            <!-- Right: Social Links -->
            <div class="footer-right">
                <p class="follow-text">Follow us</p>
                <div class="social-links">
                    <a href="#" class="social-link">
                        <img src="<?php echo ROOT; ?>/assets/images/team/footer/instagram.png" alt="Instagram" class="social-icon">
                    </a>
                    <a href="#" class="social-link">
                        <img src="<?php echo ROOT; ?>/assets/images/team/footer/facebook.png" alt="Facebook" class="social-icon">
                    </a>
                </div>
            </div>
        </div>

        <p class="footer-copy">UOC FOOTBALL © 2025 All rights reserved</p>
    </footer>
    <script src="<?php echo ROOT; ?>/assets/js/team/header/script.js"></script>
    <script src="<?php echo ROOT; ?>/assets/js/team/footer/script.js"></script>
    <script src="<?php echo ROOT; ?>/assets/js/team/players/script.js"></script>
</body>
</html>