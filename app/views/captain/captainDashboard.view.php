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
            <a href="<?= ROOT ?>/CaptainMealPlan">Meal Plan</a>
        </nav>
        <div class="nav-right">
            <div class="notification-icon" id="captainNotificationBell">
                <img src="<?php echo ROOT; ?>/assets/images/common/notification.png" alt="Notifications" style="width: 24px; cursor: pointer;">
                <span class="notification-count <?php echo $noticeCount > 0 ? '' : 'hidden'; ?>"><?php echo htmlspecialchars($noticeBadge); ?></span>
            </div>
            <div class="user-profile" id="profileTrigger" title="Edit Profile" role="button" tabindex="0">
                <img id="navbarProfileImage" src="<?php echo htmlspecialchars($data['captain_image'] ?? (ROOT . '/assets/images/adminDashboard/header/avatar.jpg')); ?>"
                    alt="Captain Avatar">
            </div>
            <a class="player-logout-btn" href="<?= ROOT ?>/login/logout">Logout</a>
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
                    <h3> Upcoming Training</h3>
                    <div class="event-info">
                        <p><strong><?php echo htmlspecialchars($nextTraining['title'] ?? 'No training session scheduled'); ?></strong></p>
                        <p> <?php echo htmlspecialchars($nextTraining['location'] ?? 'Ground'); ?></p>
                        <p> <?php echo htmlspecialchars($nextTraining['time'] ?? 'TBA'); ?></p>
                    </div>
                </div>

                <div class="card">
                    <h3> Next Match</h3>
                    <div class="event-info">
                        <p><strong><?php echo htmlspecialchars($nextMatch['title'] ?? 'No match scheduled'); ?></strong></p>
                        <p> <?php echo htmlspecialchars($nextMatch['location'] ?? 'Ground'); ?></p>
                        <p> <?php echo htmlspecialchars($nextMatch['date_time'] ?? 'TBA'); ?></p>
                    </div>
                </div>

                <div class="card countdown">
                    <h3>⏱ Days Until Next Match</h3>
                    <div class="countdown-number"><?php echo $daysUntilMatch; ?></div>
                    <div class="countdown-text"><?php echo $countdownLabel; ?></div>
                    <p class="countdown-subtitle"><?php echo htmlspecialchars($nextMatch['title'] ?? 'No match scheduled'); ?></p>
                </div>

            </div>

            <!-- Sidebar Section -->
            <div class="sidebar-container">

                <div class="announcements">
                    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
                        <h3> Latest Announcements</h3>
                        <button type="button" id="openCaptainNoticeModal" style="height:34px; border:none; background:#4A1150; color:#fff; border-radius:8px; padding:0 12px; cursor:pointer;">+ Add</button>
                    </div>
                    <?php foreach (array_slice($data['notices'] ?? [], 0, 3) as $notice): ?>
                        <div class="announcement-item" data-notice-id="<?php echo (int) ($notice['id'] ?? 0); ?>">
                            <h4><?php echo htmlspecialchars($notice['title'] ?? 'Notice'); ?></h4>
                            <p><?php echo htmlspecialchars($notice['content'] ?? ''); ?></p>
                            <?php if (!empty($notice['id'])): ?>
                                <div style="display:flex; gap:8px; margin-top:8px;">
                                    <button type="button" class="captain-notice-edit" style="height:30px; border:none; background:#4A1150; color:#fff; border-radius:8px; padding:0 10px; cursor:pointer;">Edit</button>
                                    <button type="button" class="captain-notice-delete" style="height:30px; border:none; background:#b91c1c; color:#fff; border-radius:8px; padding:0 10px; cursor:pointer;">Delete</button>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="quick-links">
                    <h3> Quick Links</h3>

                    <a href="<?= ROOT ?>/CaptainMealPlan" class="link-item">
                        <span class="link-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" role="img" focusable="false">
                                <path d="M3 5.5a1 1 0 0 1 1-1h16a1 1 0 0 1 .98 1.2l-1.6 8A2 2 0 0 1 17.42 15H6.58a2 2 0 0 1-1.96-1.3l-1.6-8A1 1 0 0 1 3 5.5Zm2.22 1 1.33 6.67a.5.5 0 0 0 .49.33h10.92a.5.5 0 0 0 .49-.33L19.78 6.5H5.22ZM9 18a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Zm9 0a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0Z"/>
                            </svg>
                        </span>
                        <span>Meal Plan</span>
                    </a>

                    <a href="<?= ROOT ?>/CaptainAnalyze" class="link-item">
                        <span class="link-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" role="img" focusable="false">
                                <path d="M4 20a1 1 0 0 1-1-1V5a1 1 0 1 1 2 0v13h15a1 1 0 1 1 0 2H4Zm4-4a1 1 0 0 1-1-1v-4a1 1 0 1 1 2 0v4a1 1 0 0 1-1 1Zm4 0a1 1 0 0 1-1-1V8a1 1 0 1 1 2 0v7a1 1 0 0 1-1 1Zm4 0a1 1 0 0 1-1-1v-2a1 1 0 1 1 2 0v2a1 1 0 0 1-1 1Z"/>
                            </svg>
                        </span>
                        <span>Performance Stats</span>
                    </a>

                    <a href="<?= ROOT ?>/CaptainSchedule" class="link-item">
                        <span class="link-icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" role="img" focusable="false">
                                <path d="M7 2a1 1 0 0 1 1 1v1h8V3a1 1 0 1 1 2 0v1h1.2A2.8 2.8 0 0 1 22 6.8v12.4A2.8 2.8 0 0 1 19.2 22H4.8A2.8 2.8 0 0 1 2 19.2V6.8A2.8 2.8 0 0 1 4.8 4H6V3a1 1 0 0 1 1-1Zm13 8H4v9.2c0 .44.36.8.8.8h14.4c.44 0 .8-.36.8-.8V10ZM4.8 6A.8.8 0 0 0 4 6.8V8h16V6.8a.8.8 0 0 0-.8-.8H4.8Z"/>
                            </svg>
                        </span>
                        <span>Schedule</span>
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

    <div id="captainNoticeModal" style="display:none; position:fixed; inset:0; background:rgba(17,24,39,0.45); z-index:1200; align-items:center; justify-content:center; padding:16px;">
        <div style="background:#fff; border-radius:14px; width:100%; max-width:520px; padding:18px; box-shadow:0 18px 36px rgba(17,24,39,0.25);">
            <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:12px;">
                <h3 style="margin:0; color:#4A1150;" id="captainNoticeModalTitle">Add Notice</h3>
                <button type="button" id="closeCaptainNoticeModal" style="border:none; background:transparent; font-size:22px; cursor:pointer;">&times;</button>
            </div>

            <form id="captainNoticeForm">
                <input type="hidden" id="captainNoticeId" value="">
                <label for="captainNoticeTitle" style="display:block; margin-bottom:6px; font-size:14px; color:#4b5563;">Title</label>
                <input id="captainNoticeTitle" type="text" required style="width:100%; height:40px; border:1px solid #d1d5db; border-radius:10px; padding:0 12px; margin-bottom:12px;">

                <label for="captainNoticeContent" style="display:block; margin-bottom:6px; font-size:14px; color:#4b5563;">Content</label>
                <textarea id="captainNoticeContent" rows="5" required style="width:100%; border:1px solid #d1d5db; border-radius:10px; padding:10px 12px; margin-bottom:14px; resize:vertical;"></textarea>

                <div style="display:flex; justify-content:flex-end; gap:10px;">
                    <button type="button" id="cancelCaptainNoticeBtn" style="height:38px; border:none; background:#e5e7eb; color:#374151; border-radius:10px; padding:0 14px; cursor:pointer;">Cancel</button>
                    <button type="submit" id="saveCaptainNoticeBtn" style="height:38px; border:none; background:#4A1150; color:#fff; border-radius:10px; padding:0 16px; cursor:pointer;">Save Notice</button>
                </div>
            </form>
        </div>
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
        const captainNoticeModal = document.getElementById('captainNoticeModal');
        const openCaptainNoticeModal = document.getElementById('openCaptainNoticeModal');
        const closeCaptainNoticeModal = document.getElementById('closeCaptainNoticeModal');
        const cancelCaptainNoticeBtn = document.getElementById('cancelCaptainNoticeBtn');
        const captainNoticeForm = document.getElementById('captainNoticeForm');
        const captainNoticeId = document.getElementById('captainNoticeId');
        const captainNoticeTitle = document.getElementById('captainNoticeTitle');
        const captainNoticeContent = document.getElementById('captainNoticeContent');
        const captainNoticeModalTitle = document.getElementById('captainNoticeModalTitle');
        const saveCaptainNoticeBtn = document.getElementById('saveCaptainNoticeBtn');

        let isCaptainNoticeEdit = false;

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

        function toggleCaptainNoticeModal(show) {
            captainNoticeModal.style.display = show ? 'flex' : 'none';
        }

        function openCaptainNoticeCreate() {
            isCaptainNoticeEdit = false;
            captainNoticeModalTitle.textContent = 'Add Notice';
            saveCaptainNoticeBtn.textContent = 'Save Notice';
            captainNoticeId.value = '';
            captainNoticeForm.reset();
            toggleCaptainNoticeModal(true);
        }

        function openCaptainNoticeEdit(noticeId, title, content) {
            isCaptainNoticeEdit = true;
            captainNoticeModalTitle.textContent = 'Edit Notice';
            saveCaptainNoticeBtn.textContent = 'Update Notice';
            captainNoticeId.value = String(noticeId || '');
            captainNoticeTitle.value = title || '';
            captainNoticeContent.value = content || '';
            toggleCaptainNoticeModal(true);
        }

        openCaptainNoticeModal?.addEventListener('click', openCaptainNoticeCreate);
        closeCaptainNoticeModal?.addEventListener('click', () => toggleCaptainNoticeModal(false));
        cancelCaptainNoticeBtn?.addEventListener('click', () => toggleCaptainNoticeModal(false));
        captainNoticeModal?.addEventListener('click', (e) => {
            if (e.target === captainNoticeModal) {
                toggleCaptainNoticeModal(false);
            }
        });

        document.querySelectorAll('.captain-notice-edit').forEach((btn) => {
            btn.addEventListener('click', (e) => {
                const card = e.currentTarget.closest('.announcement-item');
                const noticeId = Number(card?.dataset?.noticeId || 0);
                const title = card?.querySelector('h4')?.textContent?.trim() || '';
                const content = card?.querySelector('p')?.textContent?.trim() || '';
                openCaptainNoticeEdit(noticeId, title, content);
            });
        });

        document.querySelectorAll('.captain-notice-delete').forEach((btn) => {
            btn.addEventListener('click', async (e) => {
                const card = e.currentTarget.closest('.announcement-item');
                const noticeId = Number(card?.dataset?.noticeId || 0);
                if (!noticeId) {
                    return;
                }

                if (!confirm('Delete this notice?')) {
                    return;
                }

                const response = await fetch(`${baseUrl}/captainDashboard/deleteNotice`, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ notice_id: noticeId })
                });

                const result = await response.json();
                if (!result.success) {
                    alert(result.message || 'Failed to delete notice');
                    return;
                }

                window.location.reload();
            });
        });

        captainNoticeForm?.addEventListener('submit', async (e) => {
            e.preventDefault();

            const title = captainNoticeTitle.value.trim();
            const content = captainNoticeContent.value.trim();
            const noticeIdVal = Number(captainNoticeId.value || 0);

            const endpoint = isCaptainNoticeEdit
                ? `${baseUrl}/captainDashboard/updateNotice`
                : `${baseUrl}/captainDashboard/addNotice`;

            const payload = isCaptainNoticeEdit
                ? { notice_id: noticeIdVal, title, content }
                : { title, content };

            const response = await fetch(endpoint, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            const result = await response.json();
            if (!result.success) {
                alert(result.message || 'Failed to save notice');
                return;
            }

            window.location.reload();
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