<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
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
                <div class="right-section">
                    <img class="avatar" src="<?php echo ROOT; ?>/assets/images/adminDashboard/header/avatar.jpg" alt="Admin Avatar">
                    <!-- <a href="<?php echo ROOT; ?>/logout" class="logout-btn">Logout</a> -->
                </div>
        </div>
        <a href="<?php echo ROOT; ?>/admin" class="back-btn">&lt; Back</a>
        <div class="teams-grid">
            <div class="add-team-card"  onclick="event.stopPropagation(); openAddTeamModal()">
                <div class="add-icon">+</div>
                <h3>Add New Team</h3>
            </div>
            <div class="team-card">
                <div class="team-card-header">
                    <div class="team-shield">
                         <img class="" src="<?php echo ROOT; ?>/assets/images/teamManagement/sheild.svg" alt="sheild">
                    </div>
                    <div class="team-actions">
                            <button class="icon-btn edit-btn">
                                <img class="" src="<?php echo ROOT; ?>/assets/images/teamManagement/edit.svg" alt="edit">
                            </button>
                            <button class="icon-btn delete-btn">
                                <img class="" src="<?php echo ROOT; ?>/assets/images/teamManagement/delete.svg" alt="delete">
                            </button>
                    </div>
                </div>
                <div class="team-info">
                    <h3 class="team-name">Team Name</h3>
                    <div class="team-details">
                        <div class="detail-item">
                            <span class="detail-icon"><img src="<?php echo ROOT; ?>/assets/images/teamManagement/season.svg" alt="season"></span>
                            <span>Season: 2025 Season</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-icon"><img src="<?php echo ROOT; ?>/assets/images/teamManagement/tropy.svg" alt="tournament"></span>
                            <span>Tournament: Inter-uni</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-icon"><img src="<?php echo ROOT; ?>/assets/images/teamManagement/coach.png" alt="coach"></span>
                            <span>Coach: Jony Rushan</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-text">Status: present</span>
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
                    <button class="view-details-btn" onclick="openViewTeamModal()">View Details</button>
                </div>
            </div>
            
        </div>
    </div>
    <div class="modal-overlay" id="addTeamModal">
        <form class="modal" id="addTeamForm">
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
                    <input type="text" name="team_name" class="form-input" placeholder="Team name" required>
                </div>

                <div class="form-group">
                    <input type="text" name="season" class="form-input" placeholder="Season" required>
                </div>

                <div class="form-section-wrapper">
                    <div class="form-section-header-main">
                        <span>Tournaments</span>
                        <button class="add-more-btn" type="button" onclick="addInputField('tournaments-container', 'tournaments[]', 'Add tournament')">+</button>
                    </div>
                    <div class="section-content" id="tournaments-container">
                        <div class="input-item">
                            <input type="text" name="tournaments[]" class="form-input" placeholder="Add tournament">
                            <button type="button" class="delete-input-btn" onclick="removeInputField(this)">&times;</button>
                        </div>
                    </div>
                </div>

                <div class="form-section-wrapper">
                    <div class="form-section-header-main">
                        <span>Achivements</span>
                        <button class="add-more-btn" type="button" onclick="addInputField('achivements-container', 'achivements[]', 'Add achivement')">+</button>
                    </div>
                    <div class="section-content" id="achivements-container">
                        <div class="input-item">
                            <input type="text" name="achivements[]" class="form-input" placeholder="Add achivement">
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
                            <label class="role-badge active"><input type="radio" name="team_status" value="present" checked> Present</label>
                            <label class="role-badge"><input type="radio" name="team_status" value="past"> Past</label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="submit-btn">Add team</button>
            </div>
        </form>  
    </div>
    
    <div class="modal-overlay" id="addPlayersModal">
        <form class="modal" id="addPlayerForm" method="post" action="<?php echo ROOT; ?>/teamManagement/addPlayer" enctype="multipart/form-data">
            <button type="button" class="close-modal-btn" onclick="closeAddPlayerModal()">✕</button>
            <div class="modal-header">
                <div class="logo-section">
                    <img src="<?php echo ROOT; ?>/assets/images/login/Logo.png" alt="UOC Football Logo" class="logo">
                </div>
            </div>
            <h2 class="modal-title">Add Players</h2>
            
            <div class="modal-body">
                <div class="form-group">
                    <label for="player-image" class="input-label">Player Image</label>
                    <input type="file" id="player-image" name="player_image" class="form-input" accept="image/*">
                </div>

                <div class="form-group">
                    <input type="text" name="full_name" class="form-input" placeholder="Full Name" required>
                </div>

                <div class="form-group">
                    <input type="text" name="name_with_initials" class="form-input" placeholder="Name with initials" required>
                </div>

                <div class="form-group">
                    <select name="faculty" class="form-input" required>
                        <option value="">Faculty</option>
                        <option value="engineering">Engineering</option>
                        <option value="science">Science</option>
                        <option value="arts">Arts</option>
                        <option value="management">Management</option>
                    </select>
                </div>

                <div class="form-group">
                    <select name="position" class="form-input" required>
                        <option value="">Position</option>
                        <option value="goalkeeper">Goalkeeper</option>
                        <option value="defender">Defender</option>
                        <option value="midfielder">Midfielder</option>
                        <option value="forward">Forward</option>
                    </select>
                </div>

                <div class="form-group">
                    <select name="role" class="form-input" required>
                        <option value="">Player Role</option>
                        <option value="player">Player</option>
                        <option value="vice_captain">Vice Captain</option>
                        <option value="captain">Captain</option>
                    </select>
                </div>

                <div class="form-group">
                    <input type="number" name="jersey_number" class="form-input" placeholder="Jersey Number" required>
                </div>

                <div class="form-group">
                    <input type="text" name="nic" class="form-input" placeholder="NIC" required>
                </div>

                <div class="form-group">
                    <input type="text" name="uni_register_number" class="form-input" placeholder="Uni Register Number" required>
                </div>

                <div class="form-group">
                    <input type="tel" name="mobile_number" class="form-input" placeholder="Mobile Number" required>
                </div>

                <div class="form-group">
                    <input type="text" name="address" class="form-input" placeholder="Address" required>
                </div>

                <div class="form-group">
                    <input type="number" name="height" class="form-input" placeholder="Height(cm)" step="0.01">
                </div>

                <div class="form-group">
                    <input type="number" name="weight" class="form-input" placeholder="Weight(kg)" step="0.01">
                </div>

                <button type="submit" class="submit-btn">Add Player</button>
            </div>
        </form>
    </div>
    <div class="modal-overlay" id="addCoachModal">
        <form class="modal" id="addCoachForm" method="post" action="<?php echo ROOT; ?>/teamManagement/addCoach" enctype="multipart/form-data">
            <button type="button" class="close-modal-btn" onclick="closeAddCoachModal()">✕</button>
            <div class="modal-header">
                <div class="logo-section">
                    <img src="<?php echo ROOT; ?>/assets/images/login/Logo.png" alt="UOC Football Logo" class="logo">
                </div>
            </div>
            <h2 class="modal-title">Add Coach</h2>
            
            <div class="modal-body">
                <div class="form-group">
                    <label for="coach-image" class="input-label">Coach Image</label>
                    <input type="file" id="coach-image" name="coach_image" class="form-input" accept="image/*">
                </div>

                <div class="form-group">
                    <input type="text" name="full_name" class="form-input" placeholder="Full Name" required>
                </div>

                <div class="form-group">
                    <input type="text" name="name_with_initials" class="form-input" placeholder="Name with initials" required>
                </div>

                <div class="form-group">
                    <input type="number" name="age" class="form-input" placeholder="Age" required min="18" max="100">
                </div>

                <div class="form-group">
                    <input type="text" name="nic" class="form-input" placeholder="NIC" required>
                </div>

                <div class="form-group">
                    <input type="tel" name="phone_number" class="form-input" placeholder="Phone Number" required>
                </div>

                <div class="form-group">
                    <textarea name="address" class="form-input" placeholder="Address" required rows="3"></textarea>
                </div>

                <div class="form-group">
                    <input type="text" name="licence" class="form-input" placeholder="Coaching Licence" required>
                </div>

                <button type="submit" class="submit-btn">Add Coach</button>
            </div>
        </form>
    </div>
    <script src="<?php echo ROOT; ?>/assets/js/teamManagement/script.js"></script>
</body>
</html>    