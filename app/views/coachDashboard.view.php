<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    $base = rtrim(ROOT, '/');
    $noticeCount = isset($data['notices']) ? count($data['notices']) : 0;
    $noticeBadge = $noticeCount > 99 ? '99+' : (string) $noticeCount;
    ?>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <title>UOC_football - Coach Dashboard</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/coachDashboard/style.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/coachDashboard/common.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="left-section">
                <a href="<?php echo ROOT; ?>/coachDashboard">
                    <img class="header-logo" src="<?php echo ROOT; ?>/assets/images/adminDashboard/header/uoclogo.png" alt="UOC Football Logo">
                </a>
            </div>
            <nav class="nav-menu">
                <a href="<?php echo ROOT; ?>/coachDashboard" class="nav-link active">Home</a>
                <a href="<?php echo ROOT; ?>/coachEvents" class="nav-link">Events</a>
                <a href="<?php echo ROOT; ?>/coachMealPlan" class="nav-link">Meal Plan</a>
                <a href="<?php echo ROOT; ?>/coachPerformance" class="nav-link">Performance</a>
                <a href="<?php echo ROOT; ?>/coachAttendance" class="nav-link">Attendance</a>
                <a href="<?php echo ROOT; ?>/coachNotices" class="nav-link">Notices</a>
            </nav>
            <div class="right-section">
                <a class="player-logout-btn" href="<?php echo $base; ?>/login/logout">Logout</a>
                <div class="notification-icon" id="coachNotificationBell">
                    <img src="<?php echo $base; ?>/assets/images/common/notification.png" alt="Notifications" style="width: 24px; cursor: pointer;">
                    <span class="notification-count <?php echo $noticeCount > 0 ? '' : 'hidden'; ?>"><?php echo htmlspecialchars($noticeBadge); ?></span>
                </div>
                <div class="user-profile" id="profileTrigger" title="Edit Profile" role="button" tabindex="0">
                    <img id="navbarProfileImage" src="<?php echo htmlspecialchars($data['coach_image'] ?? ($base . '/assets/images/adminDashboard/header/avatar.jpg')); ?>" alt="Coach Avatar">
                </div>
            </div>
        </div>

        <div class="welcome-banner">
            <div class="welcome-content">
                <h1 class="welcome-title">Welcome,<br><?php echo htmlspecialchars($data['coach_name'] ?? 'Coach'); ?>!</h1>
                <div class="welcome-datetime">
                    <p class="date"><?php echo date('l, F j, Y'); ?></p>
                    <p class="time"><?php echo date('h:i A'); ?></p>
                </div>
            </div>
        </div>

        <div class="main-grid">
            <!-- Next Event Card -->
            <div class="nextEvent card">
                <div class="card-header">
                    <div class="card-title">
                        <h2>What is next?</h2>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="event-list">
                        <?php foreach (($data['next_events'] ?? []) as $event): ?>
                            <li class="event-item">
                                <div class="event-detail">
                                    <h3 class="event-title"><?php echo htmlspecialchars($event['title'] ?? 'Event'); ?></h3>
                                    <p class="event-date"><?php echo htmlspecialchars($event['detail'] ?? ''); ?></p>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>

            <!-- Meal Plan Card -->
            <div class="mealPlan card">
                <div class="card-header">
                    <div class="card-title">
                        <h2>Meal Plan</h2>
                    </div>
                   
                </div>
                <div class="card-body">
                    <div class="meal-tabs">
                        <button class="meal-tab active" data-meal="breakfast">Breakfast</button>
                        <button class="meal-tab" data-meal="lunch">Lunch</button>
                        <button class="meal-tab" data-meal="dinner">Dinner</button>
                    </div>
                    <div class="meal-items" id="mealItems">
                        <div class="meal-item">
                            <span>Basmati or Red Rice</span>
                            <i class="meal-icon">⋮</i>
                        </div>
                        <div class="meal-item">
                            <span>Chicken, Egg, Fish</span>
                            <i class="meal-icon">⋮</i>
                        </div>
                        <div class="meal-item">
                            <span>Vegetable(Minimum 3)</span>
                            <i class="meal-icon">⋮</i>
                        </div>
                        <div class="meal-item">
                            <span>Paip</span>
                            <i class="meal-icon">⋮</i>
                        </div>
                        <div class="meal-item">
                            <span>Yogurt</span>
                            <i class="meal-icon">⋮</i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notices Card -->
            <div class="notices card">
                <div class="card-header">
                    <div class="card-title">
                        <h2>Notices</h2>
                    </div>                
                </div>
                <div class="card-body">
                    <ul class="notices-list">
                        <?php foreach (array_slice($data['notices'] ?? [], 0, 3) as $notice): ?>
                            <li class="notice-item">
                                <h3 class="notice-title"><?php echo htmlspecialchars($notice['title'] ?? 'Notice'); ?></h3>
                                <p class="notice-date"><?php echo htmlspecialchars($notice['date'] ?? ''); ?></p>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="notification-overlay" id="coachNotificationOverlay" style="display: none;">
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
                <h3>Edit Coach Profile</h3>
                <button type="button" id="closeProfileModal" aria-label="Close profile editor">&times;</button>
            </div>

            <form id="coachProfileForm" class="profile-readonly" enctype="multipart/form-data">
                <div class="profile-image-wrap">
                    <img id="profilePreviewImage" src="<?php echo htmlspecialchars($data['coach_image'] ?? ($base . '/assets/images/adminDashboard/header/avatar.jpg')); ?>" alt="Profile Preview">
                    <label for="profileImageInput" class="profile-image-btn profile-image-btn-disabled" id="profileImageLabel">Change Image</label>
                    <input type="file" id="profileImageInput" name="image" accept="image/*" hidden disabled>
                </div>

                <div class="profile-form-grid">
                    <div>
                        <label for="profileFirstName">First Name</label>
                        <input type="text" id="profileFirstName" data-editable="true" name="first_name" value="<?php echo htmlspecialchars($data['coach_profile']['first_name'] ?? ''); ?>" readonly required>
                    </div>
                    <div>
                        <label for="profileLastName">Last Name</label>
                        <input type="text" id="profileLastName" data-editable="true" name="last_name" value="<?php echo htmlspecialchars($data['coach_profile']['last_name'] ?? ''); ?>" readonly>
                    </div>
                    <div>
                        <label for="profileIdNumber">ID Number</label>
                        <input type="text" id="profileIdNumber" data-editable="true" name="id_number" value="<?php echo htmlspecialchars($data['coach_profile']['id_number'] ?? ''); ?>" readonly required>
                    </div>
                    <div>
                        <label for="profileNic">NIC</label>
                        <input type="text" id="profileNic" value="<?php echo htmlspecialchars($data['coach_profile']['nic'] ?? ''); ?>" readonly>
                    </div>
                    <div>
                        <label for="profileEmail">Email Address</label>
                        <input type="email" id="profileEmail" data-editable="true" name="email" value="<?php echo htmlspecialchars($data['coach_profile']['email'] ?? ''); ?>" readonly>
                    </div>
                    <div>
                        <label for="profilePhone">Phone Number</label>
                        <input type="text" id="profilePhone" data-editable="true" name="phone_number" value="<?php echo htmlspecialchars($data['coach_profile']['phone_number'] ?? ''); ?>" readonly>
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

    <script src="<?php echo ROOT; ?>/assets/js/coachDashboard/script.js"></script>
    <script>
        const coachBell = document.getElementById('coachNotificationBell');
        const coachOverlay = document.getElementById('coachNotificationOverlay');
        const profileTrigger = document.getElementById('profileTrigger');
        const profileModal = document.getElementById('profileModal');
        const closeProfileModal = document.getElementById('closeProfileModal');
        const cancelProfileEdit = document.getElementById('cancelProfileEdit');
        const startProfileEdit = document.getElementById('startProfileEdit');
        const saveProfileChanges = document.getElementById('saveProfileChanges');
        const coachProfileForm = document.getElementById('coachProfileForm');
        const editableInputs = coachProfileForm.querySelectorAll('[data-editable="true"]');
        const profileImageInput = document.getElementById('profileImageInput');
        const profileImageLabel = document.getElementById('profileImageLabel');
        const profilePreviewImage = document.getElementById('profilePreviewImage');
        const navbarProfileImage = document.getElementById('navbarProfileImage');
        const welcomeName = document.querySelector('.welcome-title');
        const baseUrl = '<?php echo $base; ?>';

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
            coachProfileForm.classList.toggle('profile-readonly', !enabled);
        }

        function openProfileModal() {
            syncInitialProfileState();
            setProfileEditMode(false);
            profileModal.style.display = 'flex';
        }

        function closeProfileEditor() {
            setProfileEditMode(false);
            profileModal.style.display = 'none';
        }

        coachBell.addEventListener('click', (e) => {
            e.stopPropagation();
            coachOverlay.style.display = coachOverlay.style.display === 'none' ? 'block' : 'none';
        });

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

        coachProfileForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            if (!isProfileEditMode) {
                return;
            }

            const formData = new FormData(coachProfileForm);

            try {
                const response = await fetch(`${baseUrl}/coachDashboard/updateProfile`, {
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
                    welcomeName.innerHTML = `Welcome,<br>${safeName}!`;
                }

                syncInitialProfileState();
                setProfileEditMode(false);
                closeProfileEditor();
                alert(result.message || 'Profile updated successfully');
            } catch (error) {
                alert('Failed to update profile. Please try again.');
            }
        });

        document.addEventListener('click', (e) => {
            if (
                coachOverlay.style.display === 'block' &&
                !coachOverlay.contains(e.target) &&
                !coachBell.contains(e.target)
            ) {
                coachOverlay.style.display = 'none';
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                coachOverlay.style.display = 'none';
                if (profileModal.style.display === 'flex') {
                    closeProfileEditor();
                }
            }
        });

        if (window.location.hash === '#profile') {
            openProfileModal();
        }
    </script>
</body>
</html>