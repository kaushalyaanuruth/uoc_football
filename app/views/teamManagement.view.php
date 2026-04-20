<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@400" rel="stylesheet">
    <title>UOC_football</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/teamManagement/style.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/potal/header.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/potal/page.css">
</head>
<body>
    <div class="container">
        <div class="header">
                <div class="left-section">
                    <a href="<?php echo ROOT; ?>/admin">
                        <img class="header-logo" src="<?php echo ROOT; ?>/assets/images/adminDashboard/header/uoclogo.png" alt="UOC Football Logo">
                    </a>
                </div>
                <div class="right-section user-section">
                    <a href="<?php echo ROOT; ?>/adminDashboard?openNotifications=1" class="notification-icon" title="Notifications" aria-label="Notifications">
                        <span class="material-symbols-outlined" aria-hidden="true">notifications</span>
                    </a>
                    <a href="<?php echo ROOT; ?>/adminDashboard?openProfile=1" class="user-profile" title="Admin Profile" aria-label="Admin Profile">
                        <img class="avatar" src="<?php echo htmlspecialchars($_SESSION['admin_profile_image'] ?? (ROOT . '/assets/images/adminDashboard/header/avatar.jpg')); ?>" alt="Admin Avatar">
                    </a>
                    <a href="<?php echo ROOT; ?>/logout" class="logout-btn">Logout</a>
                </div>
        </div>
        <a href="<?php echo ROOT; ?>/adminDashboard" class="back-btn">&lt; Back</a>
        <div class="teams-grid">
            <div class="add-team-card"  onclick="event.stopPropagation(); openAddTeamModal()">
                <div class="add-icon">+</div>
                <h3>Add New Team</h3>
            </div>
            
            <?php 
            // Get teams from controller
            $teams = $data['teams'] ?? [];

            if (!empty($teams)):
                foreach ($teams as $team):
            ?>
            <div class="team-card">
                <div class="team-card-header">
                    <div class="team-shield">
                         <img class="" src="<?php echo ROOT; ?>/assets/images/teamManagement/sheild.svg" alt="sheild">
                    </div>
                    <div class="team-actions">
                            <button class="icon-btn edit-btn" onclick="editTeam(<?php echo $team->team_id; ?>)">
                                <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                            </button>
                            <button class="icon-btn delete-btn" onclick="deleteTeam(<?php echo $team->team_id; ?>)">
                                <span class="material-symbols-outlined" aria-hidden="true">delete</span>
                            </button>
                    </div>
                </div>
                <div class="team-info">
                    <h3 class="team-name"><?php echo htmlspecialchars($team->season ?? 'N/A'); ?></h3>
                    <div class="team-details">
                        <div class="detail-item">
                            <span class="detail-icon"><img src="<?php echo ROOT; ?>/assets/images/teamManagement/season.svg" alt="season"></span>
                            <span>Season: <?php echo htmlspecialchars($team->season ?? 'N/A'); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-icon"><img src="<?php echo ROOT; ?>/assets/images/teamManagement/tropy.svg" alt="tournament"></span>
                            <span>Tournament: <?php echo htmlspecialchars(implode(', ', array_column($team->tournaments, 'name')) ?? 'Not assigned'); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-icon"><img src="<?php echo ROOT; ?>/assets/images/teamManagement/tropy.svg" alt="achievement"></span>
                            <span>Achievements: <?php echo !empty($team->achievements) ? htmlspecialchars(implode(', ', array_column($team->achievements, 'achievement'))) : 'Not assigned'; ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-icon"><img src="<?php echo ROOT; ?>/assets/images/teamManagement/coach.png" alt="coach"></span>
                            <span>Coach: <?php echo htmlspecialchars($team->coach_names?? 'Not assigned'); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-text">Status: <?php echo htmlspecialchars($team->status ?? 'present'); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-icon"><img src="<?php echo ROOT; ?>/assets/images/teamManagement/players.svg" alt="players"></span>
                            <span>Players: <?php echo !empty($team->player_names) ? htmlspecialchars($team->player_names) : 'Not assigned'; ?></span>
                        </div>
                    </div>
                </div>
                <div class="team-footer">
                    <div class="player-count">
                        <span class="player-icon">
                            <img src="<?php echo ROOT; ?>/assets/images/teamManagement/players.svg" alt="players icon">
                        </span>
                        <span><?php echo isset($team->players_count) ? $team->players_count : 0; ?> Players</span>
                    </div>
                    <button class="view-details-btn" type="button" onclick="editTeam(<?php echo $team->team_id; ?>)">View Details</button>
                </div>
            </div>
            <?php 
                endforeach;
            else:
            ?>
            <div style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #999;">
                <p>No teams found. Create your first team!</p>
            </div>
            <?php 
            endif;
            ?>
            
        </div>
    </div>
    <div class="modal-overlay" id="addTeamModal">
        <form class="modal" id="addTeamForm" method="POST" action="<?php echo ROOT; ?>/teamManagement/create">
            <div class="modal-header">
                <img class="logo" src="<?php echo ROOT; ?>/assets/images/adminDashboard/header/uoclogo.png" alt="UOC Football Logo">
                <span class="close-modal-btn" onclick="closeAddTeamModal()">&times;</span>
            </div>
            <div class="modal-body">
                <h2 class="modal-title">Add Team</h2>
            
                <div style="background: #eff6ff; border: 1px dashed #3b82f6; border-radius: 8px; padding: 12px 16px; margin-bottom: 20px;">
                        <div style="display: flex; align-items: start; gap: 10px;">
                            <span style="font-size: 20px; font-weight: bold; color: #1e40af;">!</span>
                            <div style="flex: 1;">
                                <strong style="color: #1e40af; display: block; margin-bottom: 4px;">Automatic Login Accounts</strong>
                                <p style="color: #1e40af; font-size: 13px; margin: 0; line-height: 1.5;">
                                    When you add players and coaches, login accounts will be automatically created for them.
                                    <br>
                                    <strong>Username:</strong> Their NIC number | <strong>Password:</strong> 123456
                                </p>
                            </div>
                        </div>
                </div>
                

                <div class="form-group">
                    <input type="text" name="season" class="form-input" placeholder="Season" required>
                </div>

                <div class="form-section-wrapper">
                    <div class="form-section-header-main">
                        <span>Tournaments</span>
                        <button class="add-more-btn" type="button" onclick="addInputField('tournaments-container', 'name[]', 'Add tournament')">+</button>
                    </div>
                    <div class="section-content" id="tournaments-container">
                        <div class="input-item">
                            <input type="text" name="name[]" class="form-input" placeholder="Tournament name">
                            <button type="button" class="delete-input-btn" onclick="removeInputField(this)">&times;</button>
                        </div>
                    </div>
                </div>

                <div class="form-section-wrapper">
                    <div class="form-section-header-main">
                        <span>Achievements</span>
                        <button class="add-more-btn" type="button" onclick="addInputField('achievements-container', 'achievement[]', 'Add achievement')">+</button>
                    </div>
                    <div class="section-content" id="achievements-container">
                        <div class="input-item">
                            <input type="text" name="achievement[]" class="form-input" placeholder="Achievement">
                            <button type="button" class="delete-input-btn" onclick="removeInputField(this)">&times;</button>
                        </div>
                    </div>
                </div>

                <div class="form-section-wrapper">
                    <div class="form-section-header-main">
                        <span>Players</span>
                        <button class="add-more-btn" type="button" onclick="event.stopPropagation(); openAddPlayerModal()">+</button>
                    </div>
                    <div class="section-content" id="players-container">
                        <!-- Players will be dynamically added here -->
                    </div>
                </div>
                <div class="form-section-wrapper">
                    <div class="form-section-header-main">
                        <span>Coaches</span>
                        <button class="add-more-btn" type="button" onclick="event.stopPropagation(); openAddCoachModal()">+</button>
                    </div>
                    <div class="section-content" id="coaches-container">
                        <!-- Coaches will be dynamically added here -->
                    </div>
                </div>

                 <div class="form-section-wrapper">
                    <div class="form-section-header-main" >
                        <span>Team Status</span>
                        <div class="role-badges">
                            <label class="role-badge active"><input type="radio" name="status" value="present" checked> Present</label>
                            <label class="role-badge"><input type="radio" name="status" value="past"> Past</label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="submit-btn">Add team</button>
            </div>
        </form>  
    </div>
    
    <div class="modal-overlay" id="addPlayersModal">
        <form class="modal" id="addPlayerForm" enctype="multipart/form-data" onsubmit="addPlayerToForm(event)">
            <button type="button" class="close-modal-btn" onclick="closeAddPlayerModal()">✕</button>
            <div class="modal-header">
                <div class="logo-section">
                    <img src="<?php echo ROOT; ?>/assets/images/login/Logo.png" alt="UOC Football Logo" class="logo">
                </div>
            </div>
            <h2 class="modal-title">Add Player</h2>
            <p style="font-size: 12px; color: #666; margin: 10px 0; text-align: center;">Account will be created with NIC as username and password: 123456</p>
            
            <div class="modal-body">
                <!-- User Details Section -->
                <h3 style="font-size: 14px; margin: 15px 0 10px 0; color: #333; font-weight: 600;">User Details</h3>
                <div class="form-group">
                    <input type="text" id="player_first_name" class="form-input" placeholder="First Name" required>
                </div>
                <div class="form-group">
                    <input type="text" id="player_last_name" class="form-input" placeholder="Last Name" required>
                </div>
                <div class="form-group">
                    <input type="text" id="player_nic" class="form-input" placeholder="NIC" required>
                </div>
                <div class="form-group">
                    <input type="email" id="player_email" class="form-input" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <input type="tel" id="player_phone_number" class="form-input" placeholder="Phone Number" required>
                </div>
                <div class="form-group">
                    <input type="file" id="player_image" class="form-input" placeholder="Profile Image" accept="image/*">
                </div>

                <!-- Player Details Section -->
                <h3 style="font-size: 14px; margin: 15px 0 10px 0; color: #333; font-weight: 600;">Player Details</h3>
                <div class="form-group">
                    <select id="player_position" class="form-input" required>
                        <option value="">Position</option>
                        <option value="goalkeeper">Goalkeeper</option>
                        <option value="defender">Defender</option>
                        <option value="midfielder">Midfielder</option>
                        <option value="forward">Forward</option>
                    </select>
                </div>

                <div class="form-group">
                    <select id="player_role" class="form-input" required>
                        <option value="">Player Role</option>
                        <option value="Player">Player</option>
                        <option value="Vice-Captain">Vice Captain</option>
                        <option value="Captain">Captain</option>
                    </select>
                </div>

                <button type="submit" class="submit-btn">Add Player</button>
            </div>
        </form>
    </div>

    <div class="modal-overlay" id="addCoachModal">
        <form class="modal" id="addCoachForm" enctype="multipart/form-data" onsubmit="addCoachToForm(event)">
            <button type="button" class="close-modal-btn" onclick="closeAddCoachModal()">✕</button>
            <div class="modal-header">
                <div class="logo-section">
                    <img src="<?php echo ROOT; ?>/assets/images/login/Logo.png" alt="UOC Football Logo" class="logo">
                </div>
            </div>
            <h2 class="modal-title">Add Coach</h2>
            <p style="font-size: 12px; color: #666; margin: 10px 0; text-align: center;">Account will be created with NIC as username and password: 123456</p>
            
            <div class="modal-body">
                <!-- User Details Section -->
                <h3 style="font-size: 14px; margin: 15px 0 10px 0; color: #333; font-weight: 600;">User Details</h3>
                <div class="form-group">
                    <input type="text" id="coach_first_name" class="form-input" placeholder="First Name" required>
                </div>
                <div class="form-group">
                    <input type="text" id="coach_last_name" class="form-input" placeholder="Last Name" required>
                </div>
                <div class="form-group">
                    <input type="text" id="coach_nic" class="form-input" placeholder="NIC" required>
                </div>
                <div class="form-group">
                    <input type="email" id="coach_email" class="form-input" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <input type="tel" id="coach_phone_number" class="form-input" placeholder="Phone Number" required>
                </div>
                <div class="form-group">
                    <input type="file" id="coach_image" class="form-input" placeholder="Profile Image" accept="image/*">
                </div>

                <!-- Coach Details Section -->
                <h3 style="font-size: 14px; margin: 15px 0 10px 0; color: #333; font-weight: 600;">Coach Details</h3>
                <div class="form-group">
                    <input type="text" id="coach_license" class="form-input" placeholder="Coaching License" required>
                </div>

                <button type="submit" class="submit-btn">Add Coach</button>
            </div>
        </form>
    </div>

    <div class="modal-overlay" id="viewTeamModal">
        <form class="modal" id="viewTeamForm" method="POST" action="<?php echo ROOT; ?>/teamManagement/update">
            <div class="modal-header">
                <img class="logo" src="<?php echo ROOT; ?>/assets/images/adminDashboard/header/uoclogo.png" alt="UOC Football Logo">
                <span class="close-modal-btn" onclick="closeViewTeamModal()">&times;</span>
            </div>
            <div class="modal-body">
                <h2 class="modal-title">Team Details</h2>
                
                <input type="hidden" id="viewTeamId" name="team_id" value="">
                
                <div class="form-group">
                    <label style="font-size: 12px; color: #666;">Season</label>
                    <input type="text" id="viewTeamSeason" name="season" class="form-input" placeholder="Season" required>
                </div>

                <div class="form-section-wrapper">
                    <div class="form-section-header-main">
                        <span>Tournaments</span>
                        <button class="add-more-btn" type="button" onclick="addViewInputField('view-tournaments-container', 'name[]', 'Add tournament')">+</button>
                    </div>
                    <div class="section-content" id="view-tournaments-container">
                    </div>
                </div>

                <div class="form-section-wrapper">
                    <div class="form-section-header-main">
                        <span>Achievements</span>
                        <button class="add-more-btn" type="button" onclick="addViewInputField('view-achievements-container', 'achievement[]', 'Add achievement')">+</button>
                    </div>
                    <div class="section-content" id="view-achievements-container">
                    </div>
                </div>

                <div class="form-section-wrapper">
                    <div class="form-section-header-main">
                        <span>Players</span>
                        <button class="add-more-btn" type="button" onclick="event.stopPropagation(); openAddPlayerModal()">+</button>
                    </div>
                    <div class="section-content" id="view-players-container">
                    </div>
                </div>

                <div class="form-section-wrapper">
                    <div class="form-section-header-main">
                        <span>Coaches</span>
                        <button class="add-more-btn" type="button" onclick="event.stopPropagation(); openAddCoachModal()">+</button>
                    </div>
                    <div class="section-content" id="view-coaches-container">
                    </div>
                </div>

                <div class="form-section-wrapper">
                    <div class="form-section-header-main">
                        <span>Team Status</span>
                        <div class="role-badges">
                            <label class="role-badge"><input type="radio" name="status" value="present"> Present</label>
                            <label class="role-badge"><input type="radio" name="status" value="past"> Past</label>
                        </div>
                    </div>
                </div>

                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" class="submit-btn" onclick="closeViewTeamModal()" style="background: #ccc; color: #333;">Cancel</button>
                    <button type="submit" class="submit-btn">Save Changes</button>
                </div>
            </div>
        </form>
    </div>

    <div class="modal-overlay" id="editPlayerModal">
        <form class="modal" id="editPlayerForm" enctype="multipart/form-data" onsubmit="submitEditPlayerForm(event)">
            <button type="button" class="close-modal-btn" onclick="closeEditPlayerModal()">✕</button>
            <div class="modal-header">
                <div class="logo-section">
                    <img src="<?php echo ROOT; ?>/assets/images/login/Logo.png" alt="UOC Football Logo" class="logo">
                </div>
            </div>
            <h2 class="modal-title">Edit Player</h2>
            
            <div class="modal-body">
                <input type="hidden" id="editPlayerId" value="">
                
                <!-- User Details Section -->
                <h3 style="font-size: 14px; margin: 15px 0 10px 0; color: #333; font-weight: 600;">User Details</h3>
                <div class="form-group">
                    <input type="text" id="editPlayer_first_name" class="form-input" placeholder="First Name" required>
                </div>
                <div class="form-group">
                    <input type="text" id="editPlayer_last_name" class="form-input" placeholder="Last Name" required>
                </div>
                <div class="form-group">
                    <input type="text" id="editPlayer_nic" class="form-input" placeholder="NIC" disabled>
                </div>
                <div class="form-group">
                    <input type="email" id="editPlayer_email" class="form-input" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <input type="tel" id="editPlayer_phone_number" class="form-input" placeholder="Phone Number" required>
                </div>
                <div class="form-group">
                    <label for="editPlayer_image" style="display:block; font-size:12px; color:#666; margin-bottom:6px;">Profile Image (optional)</label>
                    <input type="file" id="editPlayer_image" class="form-input" accept="image/*">
                </div>

                <!-- Player Details Section -->
                <h3 style="font-size: 14px; margin: 15px 0 10px 0; color: #333; font-weight: 600;">Player Details</h3>
                <div class="form-group">
                    <select id="editPlayer_position" class="form-input" required>
                        <option value="">Position</option>
                        <option value="goalkeeper">Goalkeeper</option>
                        <option value="defender">Defender</option>
                        <option value="midfielder">Midfielder</option>
                        <option value="forward">Forward</option>
                    </select>
                </div>

                <div class="form-group">
                    <select id="editPlayer_role" class="form-input" required>
                        <option value="">Player Role</option>
                        <option value="Player">Player</option>
                        <option value="Vice-Captain">Vice Captain</option>
                        <option value="Captain">Captain</option>
                    </select>
                </div>

                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" class="submit-btn" onclick="closeEditPlayerModal()" style="background: #ccc; color: #333;">Cancel</button>
                    <button type="submit" class="submit-btn">Save Changes</button>
                </div>
            </div>
        </form>
    </div>

    <div class="modal-overlay" id="editCoachModal">
        <form class="modal" id="editCoachForm" enctype="multipart/form-data" onsubmit="submitEditCoachForm(event)">
            <button type="button" class="close-modal-btn" onclick="closeEditCoachModal()">✕</button>
            <div class="modal-header">
                <div class="logo-section">
                    <img src="<?php echo ROOT; ?>/assets/images/login/Logo.png" alt="UOC Football Logo" class="logo">
                </div>
            </div>
            <h2 class="modal-title">Edit Coach</h2>
            
            <div class="modal-body">
                <input type="hidden" id="editCoachId" value="">
                
                <!-- User Details Section -->
                <h3 style="font-size: 14px; margin: 15px 0 10px 0; color: #333; font-weight: 600;">User Details</h3>
                <div class="form-group">
                    <input type="text" id="editCoach_first_name" class="form-input" placeholder="First Name" required>
                </div>
                <div class="form-group">
                    <input type="text" id="editCoach_last_name" class="form-input" placeholder="Last Name" required>
                </div>
                <div class="form-group">
                    <input type="text" id="editCoach_nic" class="form-input" placeholder="NIC" disabled>
                </div>
                <div class="form-group">
                    <input type="email" id="editCoach_email" class="form-input" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <input type="tel" id="editCoach_phone_number" class="form-input" placeholder="Phone Number" required>
                </div>

                <!-- Coach Details Section -->
                <h3 style="font-size: 14px; margin: 15px 0 10px 0; color: #333; font-weight: 600;">Coach Details</h3>
                <div class="form-group">
                    <input type="text" id="editCoach_license" class="form-input" placeholder="Coaching License" required>
                </div>

                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <button type="button" class="submit-btn" onclick="closeEditCoachModal()" style="background: #ccc; color: #333;">Cancel</button>
                    <button type="submit" class="submit-btn">Save Changes</button>
                </div>
            </div>
        </form>
    </div>

    <script>
        const ROOT = '<?php echo ROOT; ?>';
    </script>
    <script src="<?php echo ROOT; ?>/assets/js/teamManagement/script.js"></script>
    <script src="<?php echo ROOT; ?>/assets/js/potal/adminHeaderPopup.js"></script>
</body>
</html>    


