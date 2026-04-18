<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>uoc_football</title>
    <?php
    $noticeCount = isset($data['notices']) ? count($data['notices']) : 0;
    $noticeBadge = $noticeCount > 99 ? '99+' : (string) $noticeCount;
    $nextTraining = $data['next_training'] ?? [
        'title' => 'No training session scheduled',
        'location' => 'Ground',
        'time' => 'TBA'
    ];
    $nextMatch = $data['next_match'] ?? [
        'title' => 'No match scheduled',
        'location' => 'Ground',
        'date_time' => 'TBA',
        'days_until' => 0
    ];
    $daysUntilMatch = (int) ($nextMatch['days_until'] ?? 0);
    $countdownLabel = $daysUntilMatch === 1 ? 'Day' : 'Days';
    ?>
    <link rel="stylesheet" href="<?= ROOT ?>/assets/css/captain/captainDashboard.css">
</head>

<body>


    <!-- ================= TOP NAVBAR ================= -->
    <header class="top-navbar">
        <div class="nav-left">
            <a href="<?php echo ROOT; ?>/captainDashboard">
                <img class="header-logo" src="<?php echo ROOT; ?>/assets/images/adminDashboard/header/uoclogo.png"
                    alt="UOC Football Logo">
            </a>
        </div>

        <nav class="nav-center">
            <a href="<?= ROOT ?>/captainDashboard" class="active">Home</a>
            <a href="<?= ROOT ?>/CaptainSchedule">Schedule</a>
            <a href="<?= ROOT ?>/CaptainAnalyze">Analyze</a>
            <a href="<?= ROOT ?>/CaptainAttendance">Attendance</a> <a href="<?= ROOT ?>/CaptainInventory">Inventory</a>
            <a href="<?= ROOT ?>/CaptainFinance">Finance</a>
        </nav>
        <div class="nav-right">
            <a class="player-logout-btn" href="<?= ROOT ?>/login/logout">Logout</a>
            <div class="notification-icon" id="captainNotificationBell">
                <img src="<?php echo ROOT; ?>/assets/images/common/notification.png" alt="Notifications" style="width: 24px; cursor: pointer;">
                <span class="notification-count <?php echo $noticeCount > 0 ? '' : 'hidden'; ?>"><?php echo htmlspecialchars($noticeBadge); ?></span>
            </div>
            <div class="user-profile" id="profileTrigger" title="Edit Profile" role="button" tabindex="0">
                <img id="navbarProfileImage" src="<?php echo htmlspecialchars($data['captain_image'] ?? (ROOT . '/assets/images/adminDashboard/header/avatar.jpg')); ?>"
                    alt="Captain Avatar">
            </div>
        </div>
    </header>

    <main class="content">

        <div class="container">

            <!-- Welcome Banner -->
            <div class="welcome-banner">
                <div class="welcome-text">
                    <h1>Welcome back, <?= htmlspecialchars($data['captain_name'] ?? 'Captain') ?>!</h1>
                    <p style="margin-top: 8px; color: rgba(255,255,255,0.9); font-size: 0.95rem;">
                        <?= htmlspecialchars($data['captain_position'] ?? 'Player') ?> • <?= htmlspecialchars($data['captain_role'] ?? 'Captain') ?>
                    </p>
                    <p class="welcome-date">
                        <?= date('l, jS F Y') ?>
                    </p>
                </div>
            </div>

            <!-- Dashboard Cards -->
            <div class="dashboard-grid">

                <div class="card">
                    <h3>📅 Upcoming Training</h3>
                    <div class="event-info">
                        <p><strong><?php echo htmlspecialchars($nextTraining['title'] ?? 'No training session scheduled'); ?></strong></p>
                        <p>📍 <?php echo htmlspecialchars($nextTraining['location'] ?? 'Ground'); ?></p>
                        <p>⏰ <?php echo htmlspecialchars($nextTraining['time'] ?? 'TBA'); ?></p>
                    </div>
                </div>

                <div class="card">
                    <h3>⚽ Next Match</h3>
                    <div class="event-info">
                        <p><strong><?php echo htmlspecialchars($nextMatch['title'] ?? 'No match scheduled'); ?></strong></p>
                        <p>📍 <?php echo htmlspecialchars($nextMatch['location'] ?? 'Ground'); ?></p>
                        <p>⏰ <?php echo htmlspecialchars($nextMatch['date_time'] ?? 'TBA'); ?></p>
                    </div>
                </div>

                <div class="card countdown">
                    <h3>⏱️ Days Until Next Match</h3>
                    <div class="countdown-number"><?php echo $daysUntilMatch; ?></div>
                    <div class="countdown-text"><?php echo $countdownLabel; ?></div>
                    <p class="countdown-subtitle"><?php echo htmlspecialchars($nextMatch['title'] ?? 'No match scheduled'); ?></p>
                </div>

            </div>

            <!-- Sidebar Section -->
            <div class="sidebar-container">

                <div class="announcements">
                    <h3>📢 Latest Announcements</h3>
                    <?php foreach (array_slice($data['notices'] ?? [], 0, 3) as $notice): ?>
                        <div class="announcement-item">
                            <h4><?php echo htmlspecialchars($notice['title'] ?? 'Notice'); ?></h4>
                            <p><?php echo htmlspecialchars($notice['content'] ?? ''); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="quick-links">
                    <h3>🔗 Quick Links</h3>

                    <a href="<?= ROOT ?>/CaptainMealPlan" class="link-item">
                        <!-- <div class="link-item" > -->
                        <span class="link-icon">🍽️</span>
                        <span>Meal Plan</span>
                    </a>

                    <a href="<?= ROOT ?>/CaptainAnalyze" class="link-item">
                        <span class="link-icon">📊</span>
                        <span>Performance Stats</span>
                    </a>
                </div>

            </div>

        </div>
    </main>

    <div id="captainNotificationOverlay" style="display:none; position:fixed; top:88px; right:32px; width:340px; max-height:420px; overflow:auto; background:#fff; border-radius:12px; box-shadow:0 12px 30px rgba(0,0,0,0.18); padding:14px; z-index:1200; border:1px solid #ece7f3;">
        <?php foreach (array_slice($data['notices'] ?? [], 0, 5) as $notice): ?>
            <div style="padding-bottom:10px; margin-bottom:10px; border-bottom:1px solid #eee;">
                <p style="font-size:0.86rem; color:#4a1150; font-weight:600; margin-bottom:4px;"><?php echo htmlspecialchars($notice['title'] ?? 'Notice'); ?></p>
                <p style="font-size:0.82rem; color:#4b5563; line-height:1.35;"><?php echo htmlspecialchars($notice['content'] ?? ''); ?></p>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="profile-modal" id="profileModal" style="display: none;">
        <div class="profile-modal-content">
            <div class="profile-modal-header">
                <h3>Edit Captain Profile</h3>
                <button type="button" id="closeProfileModal" aria-label="Close profile editor">&times;</button>
            </div>

            <form id="captainProfileForm" class="profile-readonly" enctype="multipart/form-data">
                <div class="profile-image-wrap">
                    <img id="profilePreviewImage" src="<?php echo htmlspecialchars($data['captain_image'] ?? (ROOT . '/assets/images/adminDashboard/header/avatar.jpg')); ?>" alt="Profile Preview">
                    <label for="profileImageInput" class="profile-image-btn profile-image-btn-disabled" id="profileImageLabel">Change Image</label>
                    <input type="file" id="profileImageInput" name="image" accept="image/*" hidden disabled>
                </div>

                <div class="profile-form-grid">
                    <div>
                        <label for="profileFirstName">First Name</label>
                        <input type="text" id="profileFirstName" data-editable="true" name="first_name" value="<?php echo htmlspecialchars($data['captain_profile']['first_name'] ?? ''); ?>" readonly required>
                    </div>
                    <div>
                        <label for="profileLastName">Last Name</label>
                        <input type="text" id="profileLastName" data-editable="true" name="last_name" value="<?php echo htmlspecialchars($data['captain_profile']['last_name'] ?? ''); ?>" readonly>
                    </div>
                    <div>
                        <label for="profileIdNumber">ID Number</label>
                        <input type="text" id="profileIdNumber" data-editable="true" name="id_number" value="<?php echo htmlspecialchars($data['captain_profile']['id_number'] ?? ''); ?>" readonly required>
                    </div>
                    <div>
                        <label for="profileNic">NIC</label>
                        <input type="text" id="profileNic" value="<?php echo htmlspecialchars($data['captain_profile']['nic'] ?? ''); ?>" readonly>
                    </div>
                    <div>
                        <label for="profileEmail">Email Address</label>
                        <input type="email" id="profileEmail" data-editable="true" name="email" value="<?php echo htmlspecialchars($data['captain_profile']['email'] ?? ''); ?>" readonly>
                    </div>
                    <div>
                        <label for="profilePhone">Phone Number</label>
                        <input type="text" id="profilePhone" data-editable="true" name="phone_number" value="<?php echo htmlspecialchars($data['captain_profile']['phone_number'] ?? ''); ?>" readonly>
                    </div>
                </div>

                <div class="profile-modal-actions">
                    <button type="button" class="btn-secondary" id="cancelProfileEdit">Close</button>
                    <button type="button" class="btn-primary" id="startProfileEdit">Edit</button>
                    <button type="submit" class="btn-primary" id="saveProfileChanges" style="display:none;">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const captainBell = document.getElementById('captainNotificationBell');
        const captainOverlay = document.getElementById('captainNotificationOverlay');
        const profileTrigger = document.getElementById('profileTrigger');
        const profileModal = document.getElementById('profileModal');
        const closeProfileModal = document.getElementById('closeProfileModal');
        const cancelProfileEdit = document.getElementById('cancelProfileEdit');
        const startProfileEdit = document.getElementById('startProfileEdit');
        const saveProfileChanges = document.getElementById('saveProfileChanges');
        const captainProfileForm = document.getElementById('captainProfileForm');
        const editableInputs = captainProfileForm.querySelectorAll('[data-editable="true"]');
        const profileImageInput = document.getElementById('profileImageInput');
        const profileImageLabel = document.getElementById('profileImageLabel');
        const profilePreviewImage = document.getElementById('profilePreviewImage');
        const navbarProfileImage = document.getElementById('navbarProfileImage');
        const welcomeName = document.querySelector('.welcome-text h1');
        const baseUrl = '<?php echo rtrim(ROOT, '/'); ?>';

        const initialProfileState = {
            first_name: document.getElementById('profileFirstName').value,
            last_name: document.getElementById('profileLastName').value,
            id_number: document.getElementById('profileIdNumber').value,
            email: document.getElementById('profileEmail').value,
            phone_number: document.getElementById('profilePhone').value,
            image: profilePreviewImage.src
        };

        let isProfileEditMode = false;

        function syncInitialProfileState() {
            initialProfileState.first_name = document.getElementById('profileFirstName').value;
            initialProfileState.last_name = document.getElementById('profileLastName').value;
            initialProfileState.id_number = document.getElementById('profileIdNumber').value;
            initialProfileState.email = document.getElementById('profileEmail').value;
            initialProfileState.phone_number = document.getElementById('profilePhone').value;
            initialProfileState.image = profilePreviewImage.src;
        }

        function restoreProfileInputs() {
            document.getElementById('profileFirstName').value = initialProfileState.first_name;
            document.getElementById('profileLastName').value = initialProfileState.last_name;
            document.getElementById('profileIdNumber').value = initialProfileState.id_number;
            document.getElementById('profileEmail').value = initialProfileState.email;
            document.getElementById('profilePhone').value = initialProfileState.phone_number;
            profilePreviewImage.src = initialProfileState.image;
            profileImageInput.value = '';
        }

        function setProfileEditMode(enabled) {
            isProfileEditMode = enabled;
            editableInputs.forEach((input) => {
                input.readOnly = !enabled;
            });

            profileImageInput.disabled = !enabled;
            profileImageLabel.classList.toggle('profile-image-btn-disabled', !enabled);
            startProfileEdit.style.display = enabled ? 'none' : 'inline-flex';
            saveProfileChanges.style.display = enabled ? 'inline-flex' : 'none';
            cancelProfileEdit.textContent = enabled ? 'Cancel Edit' : 'Close';
            captainProfileForm.classList.toggle('profile-readonly', !enabled);
        }

        captainBell.addEventListener('click', (e) => {
            e.stopPropagation();
            captainOverlay.style.display = captainOverlay.style.display === 'none' ? 'block' : 'none';
        });

        function openProfileModal() {
            syncInitialProfileState();
            setProfileEditMode(false);
            profileModal.style.display = 'flex';
        }

        function closeProfileEditor() {
            setProfileEditMode(false);
            profileModal.style.display = 'none';
        }

        profileTrigger.addEventListener('click', openProfileModal);
        profileTrigger.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                openProfileModal();
            }
        });

        closeProfileModal.addEventListener('click', closeProfileEditor);
        startProfileEdit.addEventListener('click', () => setProfileEditMode(true));
        cancelProfileEdit.addEventListener('click', () => {
            if (isProfileEditMode) {
                restoreProfileInputs();
                setProfileEditMode(false);
                return;
            }
            closeProfileEditor();
        });

        profileModal.addEventListener('click', (e) => {
            if (e.target === profileModal) {
                closeProfileEditor();
            }
        });

        profileImageInput.addEventListener('change', () => {
            if (!isProfileEditMode) {
                return;
            }

            const file = profileImageInput.files && profileImageInput.files[0];
            if (!file) {
                return;
            }

            const reader = new FileReader();
            reader.onload = (event) => {
                profilePreviewImage.src = event.target.result;
            };
            reader.readAsDataURL(file);
        });

        captainProfileForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            if (!isProfileEditMode) {
                return;
            }

            const formData = new FormData(captainProfileForm);

            try {
                const response = await fetch(`${baseUrl}/captainDashboard/updateProfile`, {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                if (!result.success) {
                    alert(result.message || 'Failed to update profile');
                    return;
                }

                if (result.profile && result.profile.image_url) {
                    navbarProfileImage.src = result.profile.image_url;
                    profilePreviewImage.src = result.profile.image_url;
                }

                if (result.profile && result.profile.name && welcomeName) {
                    const safeName = result.profile.name.replace(/</g, '&lt;').replace(/>/g, '&gt;');
                    welcomeName.innerHTML = `Welcome back, ${safeName}!`;
                }

                syncInitialProfileState();
                setProfileEditMode(false);
                closeProfileEditor();
                alert(result.message || 'Profile updated successfully');
            } catch (error) {
                alert('Failed to update profile');
            }
        });

        document.addEventListener('click', (e) => {
            if (!captainOverlay.contains(e.target) && e.target !== captainBell && !captainBell.contains(e.target)) {
                captainOverlay.style.display = 'none';
            }
        });
    </script>
</body>

</html>