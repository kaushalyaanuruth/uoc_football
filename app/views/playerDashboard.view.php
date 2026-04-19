<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Player Dashboard - UOC Football</title>
    <?php
    $base = rtrim(ROOT, '/');
    $commonFile = __DIR__ . '/../../public/assets/css/playerCommon.css';
    $commonVersion = file_exists($commonFile) ? filemtime($commonFile) : time();
    $noticeCount = isset($data['notices']) ? count($data['notices']) : 0;
    $noticeBadge = $noticeCount > 99 ? '99+' : (string)$noticeCount;
    ?>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo $base; ?>/assets/css/playerDashboard.css">
    <link rel="stylesheet" href="<?php echo $base; ?>/assets/css/playerCommon.css?v=<?php echo $commonVersion; ?>">
</head>
<body>
    <div class="dashboard-container">
        <header class="player-header">
            <div class="logo-section">
                <img src="<?php echo $base; ?>/assets/images/landingPage/header/uoclogo.png" alt="UOC Football Logo">
            </div>
            <nav class="nav-links">
                <a href="<?php echo $base; ?>/PlayerDashboard" class="active">Home</a>
                <a href="<?php echo $base; ?>/Schedule">Schedule</a>
                <a href="<?php echo $base; ?>/Analyze">Analyze</a>
                <a href="<?php echo $base; ?>/Notices">Notices</a>
                <a href="<?php echo $base; ?>/MealPlan">Meal Plan</a>
                <a href="<?php echo $base; ?>/PlayerInventory">Inventory</a>
            </nav>
            <div class="user-section">
                <div class="notification-icon" id="notificationBell">
                    <img src="<?php echo $base; ?>/assets/images/common/notification.png" alt="Notifications" style="width: 24px; cursor: pointer;">
                    <span class="notification-count <?php echo $noticeCount > 0 ? '' : 'hidden'; ?>"><?php echo htmlspecialchars($noticeBadge); ?></span>
                </div>
                <div class="user-profile" id="profileTrigger" title="Edit Profile" role="button" tabindex="0">
                    <img id="navbarProfileImage" src="<?php echo htmlspecialchars($data['player_image']); ?>" alt="Player Profile">
                </div>
                <a class="player-logout-btn" href="<?php echo $base; ?>/login/logout">Logout</a>
            </div>
        </header>

        <div class="welcome-banner">
            <div class="welcome-text">
                <h1>Welcome,<br><?php echo htmlspecialchars($data['player_name']); ?></h1>
                <p style="margin-top: 8px; color: rgba(255,255,255,0.9); font-size: 0.95rem;">
                    <?php echo htmlspecialchars($data['player_position']); ?> • <?php echo htmlspecialchars($data['player_role']); ?>
                    <?php if (!empty($data['team'])): ?>
                        • Team <?php echo (int)$data['team']->team_id; ?>
                    <?php endif; ?>
                </p>
            </div>
            <div class="banner-datetime">
                <h2><?php echo $data['date']; ?></h2>
                <p id="live-time"><?php echo $data['time']; ?></p>
            </div>
            <div class="banner-decoration"></div>
        </div>

        <div class="dashboard-grid">
            <div class="main-content">
                <div class="info-cards">
                    <div class="card next-card">
                        <h3>Next Session...</h3>
                        <p><strong><?php echo htmlspecialchars($data['next_practice']['date'] . (!empty($data['next_practice']['time_of_day']) ? ', ' . $data['next_practice']['time_of_day'] : '')); ?></strong></p>
                        <p><?php echo $data['next_practice']['time']; ?></p>
                    </div>

                    <div class="card next-card">
                        <h3>Next Event...</h3>
                        <p style="color: #888; font-size: 0.9rem;"><?php echo $data['next_event']['type']; ?></p>
                        <h4 style="margin: 5px 0; color: #333;"><?php echo $data['next_event']['title']; ?></h4>
                        <p style="font-size: 0.85rem;"> <?php echo $data['next_event']['date']; ?></p>
                        <p style="font-size: 0.85rem;"> <?php echo $data['next_event']['location']; ?></p>
                    </div>

                    <div class="card slug-countdown">
                        <span class="slug-number"><?php echo $data['slug_countdown']; ?></span>
                            <span class="slug-text"><?php echo ((int)($data['slug_countdown'] ?? 0) === 1 ? 'day' : 'days'); ?><br>more</span>
                            <p style="font-size: 0.8rem; margin-top: 10px; color: var(--primary-color);">to next match</p>
                            <p style="font-size: 0.8rem; margin-top: 4px; color: #6b7280;"><?php echo htmlspecialchars($data['next_match_countdown_title'] ?? 'No upcoming match'); ?></p>
                    </div>
                </div>

                <div class="bottom-row">
                    <div class="card">
                        <h3 style="margin-bottom: 20px; font-size: 1.1rem;">Test Summery</h3>
                        <div class="chart-container" style="height: 150px; background: #fafafa; border-radius: 8px; padding: 16px; display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                            <div style="background: #ffffff; border: 1px solid #eee; border-radius: 8px; padding: 12px;">
                                <p style="font-size: 0.8rem; color: #777; margin-bottom: 4px;">Total Tests</p>
                                <p style="font-size: 1.4rem; font-weight: 700; color: #4a1150;"><?php echo (int)$data['test_summary']['total_tests']; ?></p>
                            </div>
                            <div style="background: #ffffff; border: 1px solid #eee; border-radius: 8px; padding: 12px;">
                                <p style="font-size: 0.8rem; color: #777; margin-bottom: 4px;">Attendance</p>
                                <p style="font-size: 1.4rem; font-weight: 700; color: #4a1150;"><?php echo (int)$data['attendance']['present_rate']; ?>%</p>
                            </div>
                            <div style="grid-column: span 2; background: #ffffff; border: 1px solid #eee; border-radius: 8px; padding: 12px;">
                                <p style="font-size: 0.8rem; color: #777; margin-bottom: 4px;">Latest Test</p>
                                <p style="font-size: 0.9rem; color: #374151;"><?php echo htmlspecialchars($data['test_summary']['latest_test']); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <h3 style="margin-bottom: 20px; font-size: 1.1rem;">Notice</h3>
                        <div style="display: flex; flex-direction: column; gap: 15px;">
                            <?php foreach ($data['notices'] as $notice): ?>
                                <div style="padding-bottom: 10px; border-bottom: 1px solid #f0f0f0;">
                                    <p style="font-size: 0.88rem; color: #4a1150; font-weight: 600; margin-bottom: 4px;"><?php echo htmlspecialchars($notice['title'] ?? 'Notice'); ?></p>
                                    <p style="font-size: 0.9rem; color: #555;"><?php echo htmlspecialchars($notice['text'] ?? ''); ?></p>
                                    <?php if (!empty($notice['date'])): ?>
                                        <p style="font-size: 0.78rem; color: #8a8a8a; margin-top: 4px;"><?php echo htmlspecialchars($notice['author'] ?? 'Admin'); ?> • <?php echo htmlspecialchars($notice['date']); ?></p>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="sidebar">
                <div class="card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
                        <span style="font-weight: 600;" id="calendarHighlight">August 2025</span>
                        <span style="cursor: pointer;" onclick="alert('Next month view coming soon!')">&gt;</span>
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(7, 1fr); text-align: center; font-size: 0.8rem; row-gap: 10px;" id="calendarGrid">
                        <div style="color: #999;">Mo</div>
                        <div style="color: #999;">Tu</div>
                        <div style="color: #999;">We</div>
                        <div style="color: #999;">Th</div>
                        <div style="color: #999;">Fr</div>
                        <div style="color: #999;">Sa</div>
                        <div style="color: #999;">Su</div>
                    </div>
                </div>

                <div class="card">
                    <h3 style="margin-bottom: 15px; font-size: 1.1rem;">Attendance Summary</h3>
                    <p style="font-size: 0.9rem; color: #4b5563; margin-bottom: 8px;">Total Records: <?php echo (int)$data['attendance']['total']; ?></p>
                    <p style="font-size: 0.9rem; color: #16a34a; margin-bottom: 8px;">Present: <?php echo (int)$data['attendance']['present']; ?></p>
                    <p style="font-size: 0.9rem; color: #dc2626;">Absent: <?php echo (int)$data['attendance']['absent']; ?></p>
                </div>

                <div class="card">
                    <h3 style="margin-bottom: 15px; font-size: 1.1rem;">Meal Plan</h3>
                    <p style="font-size: 0.84rem; color: #6b7280; margin-bottom: 10px;">Showing <?php echo htmlspecialchars((string) ($data['today_day_name'] ?? date('l'))); ?> meal plan</p>
                    <div class="meal-plan-tabs">
                        <button class="meal-btn" onclick="showMeal('Breakfast', this)">Breakfast</button>
                        <button class="meal-btn active" onclick="showMeal('Lunch', this)">Lunch</button>
                        <button class="meal-btn" onclick="showMeal('Dinner', this)">Dinner</button>
                    </div>
                    <ul class="meal-list" id="mealList"></ul>
                </div>
            </div>
        </div>

        <div class="notification-overlay" id="notificationOverlay" style="display: none;">
            <p style="font-size: 0.82rem; color: #4a1150; font-weight: 700; margin-bottom: 10px;">Latest Notices</p>
            <?php foreach (array_slice($data['notices'], 0, 4) as $notice): ?>
                <div style="padding-bottom: 10px; margin-bottom: 10px; border-bottom: 1px solid #eee;">
                    <p style="font-size: 0.8rem; color: #4a1150; font-weight: 600; margin-bottom: 4px;"><?php echo htmlspecialchars($notice['title'] ?? 'Notice'); ?></p>
                    <p style="font-size: 0.82rem; color: #444; line-height: 1.4;"><?php echo htmlspecialchars($notice['text'] ?? ''); ?></p>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="profile-modal" id="profileModal" style="display: none;">
            <div class="profile-modal-content">
                <div class="profile-modal-header">
                    <h3>Edit Profile</h3>
                    <button type="button" id="closeProfileModal" aria-label="Close profile editor">&times;</button>
                </div>

                <form id="playerProfileForm" class="profile-readonly" enctype="multipart/form-data">
                    <div class="profile-image-wrap">
                        <img id="profilePreviewImage" src="<?php echo htmlspecialchars($data['player_image']); ?>" alt="Profile Preview">
                        <label for="profileImageInput" class="profile-image-btn profile-image-btn-disabled" id="profileImageLabel">Change Image</label>
                        <input type="file" id="profileImageInput" name="image" accept="image/*" hidden disabled>
                    </div>

                    <div class="profile-form-grid">
                        <div>
                            <label for="profileFirstName">First Name</label>
                            <input type="text" id="profileFirstName" data-editable="true" name="first_name" value="<?php echo htmlspecialchars($data['player_profile']['first_name'] ?? ''); ?>" readonly required>
                        </div>
                        <div>
                            <label for="profileLastName">Last Name</label>
                            <input type="text" id="profileLastName" data-editable="true" name="last_name" value="<?php echo htmlspecialchars($data['player_profile']['last_name'] ?? ''); ?>" readonly>
                        </div>
                        <div>
                            <label for="profileIdNumber">ID Number</label>
                            <input type="text" id="profileIdNumber" data-editable="true" name="id_number" value="<?php echo htmlspecialchars($data['player_profile']['id_number'] ?? ''); ?>" readonly required>
                        </div>
                        <div>
                            <label for="profileNic">NIC</label>
                            <input type="text" id="profileNic" value="<?php echo htmlspecialchars($data['player_profile']['nic'] ?? ''); ?>" readonly>
                        </div>
                        <div>
                            <label for="profileEmail">Email Address</label>
                            <input type="email" id="profileEmail" data-editable="true" name="email" value="<?php echo htmlspecialchars($data['player_profile']['email'] ?? ''); ?>" readonly>
                        </div>
                        <div>
                            <label for="profilePhone">Phone Number</label>
                            <input type="text" id="profilePhone" data-editable="true" name="phone_number" value="<?php echo htmlspecialchars($data['player_profile']['phone_number'] ?? ''); ?>" readonly>
                        </div>
                        <div>
                            <label for="profileCurrentPassword">Current Password</label>
                            <input type="password" id="profileCurrentPassword" name="current_password" data-password-field="true" disabled>
                        </div>
                        <div>
                            <label for="profileNewPassword">New Password</label>
                            <input type="password" id="profileNewPassword" name="new_password" data-password-field="true" minlength="6" disabled>
                        </div>
                        <div>
                            <label for="profileConfirmPassword">Confirm Password</label>
                            <input type="password" id="profileConfirmPassword" name="confirm_password" data-password-field="true" minlength="6" disabled>
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
            const bell = document.getElementById('notificationBell');
            const overlay = document.getElementById('notificationOverlay');
            const profileTrigger = document.getElementById('profileTrigger');
            const profileModal = document.getElementById('profileModal');
            const closeProfileModal = document.getElementById('closeProfileModal');
            const cancelProfileEdit = document.getElementById('cancelProfileEdit');
            const startProfileEdit = document.getElementById('startProfileEdit');
            const saveProfileChanges = document.getElementById('saveProfileChanges');
            const playerProfileForm = document.getElementById('playerProfileForm');
            const editableInputs = playerProfileForm.querySelectorAll('[data-editable="true"]');
            const passwordInputs = playerProfileForm.querySelectorAll('[data-password-field="true"]');
            const profileImageInput = document.getElementById('profileImageInput');
            const profileImageLabel = document.getElementById('profileImageLabel');
            const profilePreviewImage = document.getElementById('profilePreviewImage');
            const navbarProfileImage = document.getElementById('navbarProfileImage');
            const welcomeName = document.querySelector('.welcome-text h1');
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
                passwordInputs.forEach((input) => {
                    input.value = '';
                });
            }

            function setProfileEditMode(enabled) {
                isProfileEditMode = enabled;

                editableInputs.forEach((input) => {
                    input.readOnly = !enabled;
                });

                passwordInputs.forEach((input) => {
                    input.disabled = !enabled;
                    if (!enabled) {
                        input.value = '';
                    }
                });

                profileImageInput.disabled = !enabled;
                profileImageLabel.classList.toggle('profile-image-btn-disabled', !enabled);
                startProfileEdit.style.display = enabled ? 'none' : 'inline-flex';
                saveProfileChanges.style.display = enabled ? 'inline-flex' : 'none';
                cancelProfileEdit.textContent = enabled ? 'Cancel Edit' : 'Close';
                playerProfileForm.classList.toggle('profile-readonly', !enabled);
            }

            bell.addEventListener('click', (e) => {
                e.stopPropagation();
                overlay.style.display = overlay.style.display === 'none' ? 'block' : 'none';
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

            playerProfileForm.addEventListener('submit', async (e) => {
                e.preventDefault();

                if (!isProfileEditMode) {
                    return;
                }

                const currentPassword = document.getElementById('profileCurrentPassword').value;
                const newPassword = document.getElementById('profileNewPassword').value;
                const confirmPassword = document.getElementById('profileConfirmPassword').value;
                const wantsPasswordChange = currentPassword.trim() !== '' || newPassword.trim() !== '' || confirmPassword.trim() !== '';

                if (wantsPasswordChange) {
                    if (!currentPassword || !newPassword || !confirmPassword) {
                        alert('Current password, new password and confirm password are required to change password.');
                        return;
                    }

                    if (newPassword.length < 6) {
                        alert('New password must be at least 6 characters.');
                        return;
                    }

                    if (newPassword !== confirmPassword) {
                        alert('New password and confirm password do not match.');
                        return;
                    }

                    if (newPassword === '123456') {
                        alert('Please choose a password different from the default password.');
                        return;
                    }
                }

                const formData = new FormData(playerProfileForm);

                try {
                    const response = await fetch(`${baseUrl}/PlayerDashboard/updateProfile`, {
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
                        welcomeName.innerHTML = `Welcome,<br>${safeName}`;
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
                if (!overlay.contains(e.target) && e.target !== bell && !bell.contains(e.target)) {
                    overlay.style.display = 'none';
                }
            });

            const todayMeals = <?php echo json_encode($data['today_meal_plan'] ?? ($data['meal_plan'] ?? [])); ?>;

            function showMeal(mealType, btn) {
                document.querySelectorAll('.meal-btn').forEach((b) => b.classList.remove('active'));
                btn.classList.add('active');

                const list = document.getElementById('mealList');
                list.innerHTML = '';

                const items = Array.isArray(todayMeals[mealType]) ? todayMeals[mealType] : [];
                if (!items.length) {
                    const li = document.createElement('li');
                    li.style.color = '#9ca3af';
                    li.textContent = `No ${mealType.toLowerCase()} items set.`;
                    list.appendChild(li);
                    return;
                }

                items.forEach((item) => {
                    const li = document.createElement('li');
                    li.textContent = item;
                    list.appendChild(li);
                });
            }

            // Default tab state: Lunch
            const defaultMealBtn = document.querySelector('.meal-btn.active');
            if (defaultMealBtn) {
                showMeal('Lunch', defaultMealBtn);
            }

            function updateClock() {
                const now = new Date();
                let hours = now.getHours();
                const minutes = String(now.getMinutes()).padStart(2, '0');
                const ampm = hours >= 12 ? 'PM' : 'AM';
                hours = hours % 12;
                hours = hours ? hours : 12;
                const strTime = hours + ':' + minutes + ' ' + ampm;
                document.getElementById('live-time').textContent = strTime;
            }
            setInterval(updateClock, 1000);

            function generateCalendar() {
                const now = new Date();
                const year = now.getFullYear();
                const month = now.getMonth();
                const today = now.getDate();
                const monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
                document.getElementById('calendarHighlight').textContent = `${monthNames[month]} ${year}`;
                const firstDay = new Date(year, month, 1).getDay();
                let startingIndex = firstDay === 0 ? 6 : firstDay - 1;
                const daysInMonth = new Date(year, month + 1, 0).getDate();
                const grid = document.getElementById('calendarGrid');
                const headers = Array.from(grid.children).slice(0, 7);
                grid.innerHTML = '';
                headers.forEach(h => grid.appendChild(h));
                for (let i = 0; i < startingIndex; i++) {
                    const empty = document.createElement('div');
                    grid.appendChild(empty);
                }
                for (let d = 1; d <= daysInMonth; d++) {
                    const dayCell = document.createElement('div');
                    dayCell.textContent = d;
                    if (d === today) {
                        dayCell.style.background = 'var(--primary-light)';
                        dayCell.style.borderRadius = '50%';
                        dayCell.style.width = '25px';
                        dayCell.style.height = '25px';
                        dayCell.style.display = 'flex';
                        dayCell.style.alignItems = 'center';
                        dayCell.style.justifyContent = 'center';
                        dayCell.style.margin = '0 auto';
                        dayCell.style.color = 'var(--primary-color)';
                    }
                    grid.appendChild(dayCell);
                }
            }
            generateCalendar();
        </script>
    </div>
</body>
</html>
