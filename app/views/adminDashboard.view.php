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
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/adminDashboard/style.css?v=20260419a">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/potal/header.css?v=20260419a">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/potal/page.css?v=20260419a">
</head>
<body>
    <?php
    $noticeCount = isset($data['notices']) ? count($data['notices']) : 0;
    $noticeBadge = $noticeCount > 99 ? '99+' : (string)$noticeCount;
    $adminDisplayName = (string)($data['admin_name'] ?? ($_SESSION['user_id'] ?? 'Admin'));
    $adminImage = (string)($data['admin_image'] ?? ($_SESSION['admin_profile_image'] ?? (ROOT . '/assets/images/adminDashboard/header/avatar.jpg')));
    $adminProfile = $data['admin_profile'] ?? [];
    ?>
    <div class="container">
        <div class="header admin-topbar">
                <div class="left-section admin-left-section">
                    <a href="<?php echo ROOT; ?>/adminDashboard">
                        <img class="header-logo" src="<?php echo ROOT; ?>/assets/images/adminDashboard/header/uoclogo.png" alt="UOC Football Logo">
                    </a>
                </div>
                <div class="right-section admin-right-section user-section">
                    <div class="notification-icon" id="notificationBell" role="button" tabindex="0" title="View latest notices" aria-label="View latest notices">
                        <span class="material-symbols-outlined" aria-hidden="true">notifications</span>
                        <span class="notification-count <?php echo $noticeCount > 0 ? '' : 'hidden'; ?>"><?php echo htmlspecialchars($noticeBadge); ?></span>
                    </div>
                    <div class="user-profile" id="profileTrigger" title="Admin Profile" role="button" tabindex="0">
                        <img id="navbarProfileImage" class="avatar" src="<?php echo htmlspecialchars($adminImage); ?>" alt="Admin Avatar">
                    </div>
                    <a href="<?php echo ROOT; ?>/logout" class="logout-btn">Logout</a>
                </div>
        </div>
        <div class="quickActions">
            <h2 class="section-title">Quick Actions</h2>
            <div class="actions-grid">
                <a class="action-card icon-purple" href="<?php echo ROOT; ?>/teamManagement">
                    <div class="icon-container">
                        <img src="<?php echo ROOT; ?>/assets/images/adminDashboard/icons/teams.svg" alt="team icon" class="action-icon">
                    </div>
                    <p class="action-label">Teams</p>
                </a>
                <a class="action-card icon-blue" href="<?php echo ROOT; ?>/teamResult">
                    <div class="icon-container">
                        <img src="<?php echo ROOT; ?>/assets/images/adminDashboard/icons/test.svg" alt="results icon" class="action-icon">
                    </div>
                    <p class="action-label">Results</p>
                </a>
                <a class="action-card icon-green" href="<?php echo ROOT; ?>/eventManagement">
                    <div class="icon-container">
                        <img src="<?php echo ROOT; ?>/assets/images/adminDashboard/icons/event.svg" alt="event icon" class="action-icon">
                    </div>
                    <p class="action-label">Events</p>
                </a>
                <a class="action-card icon-red" href="<?php echo ROOT; ?>/inventoryManagement">
                    <div class="icon-container">
                        <img src="<?php echo ROOT; ?>/assets/images/adminDashboard/icons/inventory.svg" alt="inventory icon" class="action-icon">
                    </div>
                    <p class="action-label">Inventory</p>
                </a>
                <a class="action-card icon-pink" href="<?php echo ROOT; ?>/galleryManagement">
                    <div class="icon-container">
                        <img src="<?php echo ROOT; ?>/assets/images/adminDashboard/icons/gallery.svg" alt="gallery icon" class="action-icon">
                    </div>
                    <p class="action-label">Gallery</p>
                </a>
                <a class="action-card icon-indigo" href="<?php echo ROOT; ?>/newsManagement">
                    <div class="icon-container">
                        <img src="<?php echo ROOT; ?>/assets/images/adminDashboard/icons/news.svg" alt="news icon" class="action-icon">
                    </div>
                    <p class="action-label">News</p>
                </a>
                <a class="action-card icon-yellow" href="<?php echo ROOT; ?>/StoreManagement">
                    <div class="icon-container">
                        <img src="<?php echo ROOT; ?>/assets/images/adminDashboard/icons/store.svg" alt="store icon" class="action-icon">
                    </div>
                    <p class="action-label">Store</p>
                </a>
                <a class="action-card icon-orange" href="<?php echo ROOT; ?>/budgetManagement">
                    <div class="icon-container">
                        <img src="<?php echo ROOT; ?>/assets/images/adminDashboard/icons/budget.svg" alt="budget icon" class="action-icon">
                    </div>
                    <p class="action-label">Budget</p>
                </a>
            </div>
        </div>
        <div class="welcome-banner">
            <div class="welcome-content">
                <h1 class="welcome-title">Welcome back,<br> Admin!</h1>
                <div class="welcome-datetime">
                    <p class="date"><?php echo date('l, F j, Y'); ?></p>
                    <p class="time"><?php echo date('h:i A'); ?></p>
                </div>
            </div>
        </div>
        <div class="main-grid">
            <div class="notices card">
                <div class="card-header">
                    <div class="card-title">
                        <h2>Notices</h2>
                    </div>
                    <button class="add-btn" id="openNoticeModal">+ Add</button>
                </div>
                <div class="card-body">
                    <ul class="notices-list">
                        <?php if (!empty($data['notices'])): ?>
                            <?php foreach ($data['notices'] as $notice): ?>
                                <li class="notice-item" data-id="<?php echo (int) ($notice->notice_id ?? 0); ?>">
                                    <h3 class="notice-title"><?php echo htmlspecialchars($notice->title ?? 'Notice'); ?></h3>
                                    <p class="notice-date"><?php echo date('F j, Y g:i A', strtotime($notice->created_at)); ?></p>
                                    <p class="admin-notice-content"><?php echo htmlspecialchars($notice->content ?? ''); ?></p>
                                    <div class="notice-actions">
                                        <button type="button" class="icon-btn edit-btn admin-notice-edit" title="Edit notice" aria-label="Edit notice">
                                            <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                                        </button>
                                        <button type="button" class="icon-btn delete-btn admin-notice-delete" title="Delete notice" aria-label="Delete notice">
                                            <span class="material-symbols-outlined" aria-hidden="true">delete</span>
                                        </button>
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="notice-item">
                                <h3 class="notice-title">No notices yet</h3>
                                <p class="notice-date">Click + Add to create a notice</p>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            <div class="nextEvent card">
                <div class="card-header">
                    <div class="card-title">
                        <h2>What is next?</h2>
                    </div>
                </div>
                <div class="card-body">
                    <ul class="event-list">
                        <?php
                        $events = $data['upcomingEvents'] ?? [];
                        $eventIcons = [
                            'match' => 'match.svg',
                            'training' => 'training.svg',
                            'tournament' => 'event.svg',
                            'meeting' => 'event.svg',
                            'social' => 'event.svg',
                            'other' => 'event.svg',
                        ];
                        ?>

                        <?php if (!empty($events)): ?>
                            <?php foreach ($events as $event): ?>
                                <?php
                                $eventType = strtolower(trim((string)($event->event_type ?? 'other')));
                                $iconFile = $eventIcons[$eventType] ?? 'event.svg';

                                $dateRaw = $event->date ?? null;
                                $timeRaw = $event->event_time ?? null;
                                $formattedDate = 'Date not set';
                                if (!empty($dateRaw)) {
                                    $eventTs = strtotime((string)$dateRaw . (!empty($timeRaw) ? (' ' . (string)$timeRaw) : ''));
                                    if ($eventTs !== false) {
                                        $formattedDate = date('F j, Y g:i A', $eventTs);
                                    }
                                }
                                ?>
                                <li class="event-item">
                                    <div class="event-detail">
                                        <h3 class="event-title"><?php echo htmlspecialchars($event->title ?? 'UOC Football Event'); ?></h3>
                                        <p class="event-date"><?php echo htmlspecialchars($formattedDate); ?></p>
                                    </div>
                                    <div class="icon-container">
                                        <img src="<?php echo ROOT; ?>/assets/images/adminDashboard/icons/<?php echo htmlspecialchars($iconFile); ?>" alt="<?php echo htmlspecialchars($eventType); ?> icon" class="action-icon">
                                    </div>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="event-item">
                                <div class="event-detail">
                                    <h3 class="event-title">No upcoming events</h3>
                                    <p class="event-date">Add events from Event Management</p>
                                </div>
                                <div class="icon-container">
                                    <img src="<?php echo ROOT; ?>/assets/images/adminDashboard/icons/event.svg" alt="event icon" class="action-icon">
                                </div>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            <div class="inventory card">
                <div class="card-header">
                    <div class="card-title">
                        <h2>Inventory Status</h2>
                    </div>
                </div>
                <div class="inventory-list">
                    <?php if (!empty($data['inventoryItems'])): ?>
                        <?php foreach ($data['inventoryItems'] as $item): ?>
                            <?php
                            $totalCount = (int) ($item['total_count'] ?? 0);
                            $availableCount = (int) ($item['available_count'] ?? 0);
                            $stockClass = 'stock-low';

                            if ($totalCount > 0) {
                                $ratio = $availableCount / $totalCount;
                                if ($ratio > 0.5) {
                                    $stockClass = 'stock-high';
                                } elseif ($ratio > 0.2) {
                                    $stockClass = 'stock-medium';
                                }
                            }
                            ?>
                            <div class="inventory-item">
                                <div class="inventory-left">
                                    <div class="icon-container">
                                        <span class="material-symbols-outlined inventory-symbol" aria-hidden="true"><?php echo htmlspecialchars((string) ($item['icon'] ?? 'inventory_2')); ?></span>
                                    </div>
                                    <span class="inventory-name"><?php echo htmlspecialchars((string) ($item['name'] ?? 'Item')); ?></span>
                                </div>
                                <span class="inventory-stock <?php echo $stockClass; ?>"><?php echo (int) $availableCount; ?> in stock</span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="inventory-item">
                            <div class="inventory-left">
                                <div class="icon-container">
                                    <span class="material-symbols-outlined inventory-symbol" aria-hidden="true">inventory_2</span>
                                </div>
                                <span class="inventory-name">No inventory data</span>
                            </div>
                            <span class="inventory-stock stock-low">0 in stock</span>
                        </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>    
    </div>

    <div class="notification-overlay" id="notificationOverlay" style="display:none;">
        <p class="notification-heading">Latest Notices</p>
        <?php if (!empty($data['notices'])): ?>
            <?php foreach (array_slice($data['notices'], 0, 5) as $notice): ?>
                <div class="notification-item">
                    <p class="notification-title"><?php echo htmlspecialchars($notice->title ?? 'Notice'); ?></p>
                    <p class="notification-text"><?php echo htmlspecialchars($notice->content ?? ''); ?></p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="notification-empty">No notices available.</p>
        <?php endif; ?>
    </div>

    <div class="profile-modal" id="profileModal" style="display:none;">
        <div class="profile-modal-content">
            <div class="profile-modal-header">
                <h3>Edit Profile</h3>
                <button type="button" id="closeProfileModal" aria-label="Close profile editor">&times;</button>
            </div>

            <form id="adminProfileForm" class="profile-readonly" enctype="multipart/form-data">
                <div class="profile-image-wrap">
                    <img id="profilePreviewImage" src="<?php echo htmlspecialchars($adminImage); ?>" alt="Profile Preview">
                    <label for="profileImageInput" class="profile-image-btn profile-image-btn-disabled" id="profileImageLabel">Change Image</label>
                    <input type="file" id="profileImageInput" name="image" accept="image/*" hidden disabled>
                </div>

                <div class="profile-form-grid">
                    <div>
                        <label for="profileFirstName">First Name</label>
                        <input type="text" id="profileFirstName" data-editable="true" name="first_name" value="<?php echo htmlspecialchars((string)($adminProfile['first_name'] ?? 'Admin')); ?>" readonly required>
                    </div>
                    <div>
                        <label for="profileLastName">Last Name</label>
                        <input type="text" id="profileLastName" data-editable="true" name="last_name" value="<?php echo htmlspecialchars((string)($adminProfile['last_name'] ?? '')); ?>" readonly>
                    </div>
                    <div>
                        <label for="profileIdNumber">ID Number</label>
                        <input type="text" id="profileIdNumber" data-editable="true" name="id_number" value="<?php echo htmlspecialchars((string)($adminProfile['id_number'] ?? $adminDisplayName)); ?>" readonly required>
                    </div>
                    <div>
                        <label for="profileNic">NIC</label>
                        <input type="text" id="profileNic" data-editable="true" name="nic" value="<?php echo htmlspecialchars((string)($adminProfile['nic'] ?? $_SESSION['nic'] ?? '')); ?>" readonly required>
                    </div>
                    <div>
                        <label for="profileEmail">Email Address</label>
                        <input type="email" id="profileEmail" data-editable="true" name="email" value="<?php echo htmlspecialchars((string)($adminProfile['email'] ?? '')); ?>" readonly>
                    </div>
                    <div>
                        <label for="profilePhone">Phone Number</label>
                        <input type="text" id="profilePhone" data-editable="true" name="phone_number" value="<?php echo htmlspecialchars((string)($adminProfile['phone_number'] ?? '')); ?>" readonly>
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

    <div id="noticeModal" class="notice-modal-overlay" style="display:none;">
        <div class="notice-modal-card">
            <div class="notice-modal-head">
                <h3 id="noticeModalTitle">Add Notice</h3>
                <button type="button" id="closeNoticeModal" class="notice-modal-close">&times;</button>
            </div>

            <form id="adminNoticeForm" class="notice-modal-form">
                <input type="hidden" id="noticeId" value="">
                <label for="noticeTitle" class="notice-modal-label">Title</label>
                <input id="noticeTitle" type="text" required class="notice-modal-input">

                <label for="noticeContent" class="notice-modal-label">Content</label>
                <textarea id="noticeContent" rows="5" required class="notice-modal-textarea"></textarea>

                <div class="notice-modal-actions">
                    <button type="button" id="cancelNoticeBtn" class="btn-secondary">Cancel</button>
                    <button type="submit" id="saveNoticeBtn" class="btn-primary">Save Notice</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const noticeModal = document.getElementById('noticeModal');
        const openNoticeModal = document.getElementById('openNoticeModal');
        const closeNoticeModal = document.getElementById('closeNoticeModal');
        const cancelNoticeBtn = document.getElementById('cancelNoticeBtn');
        const adminNoticeForm = document.getElementById('adminNoticeForm');
        const noticeIdInput = document.getElementById('noticeId');
        const noticeTitleInput = document.getElementById('noticeTitle');
        const noticeContentInput = document.getElementById('noticeContent');
        const noticeModalTitle = document.getElementById('noticeModalTitle');
        const saveNoticeBtn = document.getElementById('saveNoticeBtn');
        const bell = document.getElementById('notificationBell');
        const overlay = document.getElementById('notificationOverlay');
        const profileTrigger = document.getElementById('profileTrigger');
        const profileModal = document.getElementById('profileModal');
        const closeProfileModal = document.getElementById('closeProfileModal');
        const cancelProfileEdit = document.getElementById('cancelProfileEdit');
        const startProfileEdit = document.getElementById('startProfileEdit');
        const saveProfileChanges = document.getElementById('saveProfileChanges');
        const adminProfileForm = document.getElementById('adminProfileForm');
        const editableInputs = adminProfileForm.querySelectorAll('[data-editable="true"]');
        const passwordInputs = adminProfileForm.querySelectorAll('[data-password-field="true"]');
        const profileImageInput = document.getElementById('profileImageInput');
        const profileImageLabel = document.getElementById('profileImageLabel');
        const profilePreviewImage = document.getElementById('profilePreviewImage');
        const navbarProfileImage = document.getElementById('navbarProfileImage');
        const welcomeTitle = document.querySelector('.welcome-title');

        const initialProfileState = {
            first_name: document.getElementById('profileFirstName').value,
            last_name: document.getElementById('profileLastName').value,
            id_number: document.getElementById('profileIdNumber').value,
            nic: document.getElementById('profileNic').value,
            email: document.getElementById('profileEmail').value,
            phone_number: document.getElementById('profilePhone').value,
            image: profilePreviewImage.src
        };

        let isEditMode = false;
        let isProfileEditMode = false;

        function toggleNoticeModal(show) {
            noticeModal.style.display = show ? 'flex' : 'none';
        }

        function openAddNotice() {
            isEditMode = false;
            noticeModalTitle.textContent = 'Add Notice';
            saveNoticeBtn.textContent = 'Save Notice';
            noticeIdInput.value = '';
            adminNoticeForm.reset();
            toggleNoticeModal(true);
        }

        function openEditNotice(noticeId, title, content) {
            isEditMode = true;
            noticeModalTitle.textContent = 'Edit Notice';
            saveNoticeBtn.textContent = 'Update Notice';
            noticeIdInput.value = String(noticeId || '');
            noticeTitleInput.value = title || '';
            noticeContentInput.value = content || '';
            toggleNoticeModal(true);
        }

        function openProfile() {
            syncInitialProfileState();
            setProfileEditMode(false);
            profileModal.style.display = 'flex';
        }

        function closeProfile() {
            setProfileEditMode(false);
            profileModal.style.display = 'none';
        }

        function syncInitialProfileState() {
            initialProfileState.first_name = document.getElementById('profileFirstName').value;
            initialProfileState.last_name = document.getElementById('profileLastName').value;
            initialProfileState.id_number = document.getElementById('profileIdNumber').value;
            initialProfileState.nic = document.getElementById('profileNic').value;
            initialProfileState.email = document.getElementById('profileEmail').value;
            initialProfileState.phone_number = document.getElementById('profilePhone').value;
            initialProfileState.image = profilePreviewImage.src;
        }

        function restoreProfileInputs() {
            document.getElementById('profileFirstName').value = initialProfileState.first_name;
            document.getElementById('profileLastName').value = initialProfileState.last_name;
            document.getElementById('profileIdNumber').value = initialProfileState.id_number;
            document.getElementById('profileNic').value = initialProfileState.nic;
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
            adminProfileForm.classList.toggle('profile-readonly', !enabled);
        }

        function toggleNotifications() {
            overlay.style.display = overlay.style.display === 'none' ? 'block' : 'none';
        }

        openNoticeModal.addEventListener('click', openAddNotice);
        closeNoticeModal.addEventListener('click', () => toggleNoticeModal(false));
        cancelNoticeBtn.addEventListener('click', () => toggleNoticeModal(false));
        noticeModal.addEventListener('click', (e) => {
            if (e.target === noticeModal) {
                toggleNoticeModal(false);
            }
        });

        bell.addEventListener('click', (e) => {
            e.stopPropagation();
            toggleNotifications();
        });

        bell.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                toggleNotifications();
            }
        });

        profileTrigger.addEventListener('click', openProfile);
        profileTrigger.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                openProfile();
            }
        });

        closeProfileModal.addEventListener('click', closeProfile);
        startProfileEdit.addEventListener('click', () => setProfileEditMode(true));
        cancelProfileEdit.addEventListener('click', () => {
            if (isProfileEditMode) {
                restoreProfileInputs();
                setProfileEditMode(false);
                return;
            }

            closeProfile();
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

        profileModal.addEventListener('click', (e) => {
            if (e.target === profileModal) {
                closeProfile();
            }
        });

        adminProfileForm.addEventListener('submit', async (e) => {
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

            const formData = new FormData(adminProfileForm);

            try {
                const response = await fetch('<?php echo ROOT; ?>/adminDashboard/updateProfile', {
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

                if (result.profile) {
                    document.getElementById('profileFirstName').value = result.profile.first_name || '';
                    document.getElementById('profileLastName').value = result.profile.last_name || '';
                    document.getElementById('profileIdNumber').value = result.profile.id_number || '';
                    document.getElementById('profileNic').value = result.profile.nic || '';
                    document.getElementById('profileEmail').value = result.profile.email || '';
                    document.getElementById('profilePhone').value = result.profile.phone_number || '';

                    if (welcomeTitle && result.profile.name) {
                        const safeName = String(result.profile.name)
                            .replace(/&/g, '&amp;')
                            .replace(/</g, '&lt;')
                            .replace(/>/g, '&gt;')
                            .replace(/"/g, '&quot;')
                            .replace(/'/g, '&#39;');
                        welcomeTitle.innerHTML = `Welcome back,<br>${safeName}!`;
                    }
                }

                syncInitialProfileState();
                setProfileEditMode(false);
                closeProfile();
                alert(result.message || 'Profile updated successfully');
            } catch (error) {
                alert('Failed to update profile');
            }
        });

        adminNoticeForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const title = noticeTitleInput.value.trim();
            const content = noticeContentInput.value.trim();
            const noticeId = Number(noticeIdInput.value || 0);
            const endpoint = isEditMode
                ? '<?php echo ROOT; ?>/adminDashboard/updateNotice'
                : '<?php echo ROOT; ?>/adminDashboard/addNotice';

            const payload = isEditMode
                ? { notice_id: noticeId, title, content }
                : { title, content };

            const response = await fetch(endpoint, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            const result = await response.json();
            if (!result.success) {
                alert(result.message || 'Failed to add notice');
                return;
            }

            window.location.reload();
        });

        document.querySelectorAll('.admin-notice-edit').forEach((btn) => {
            btn.addEventListener('click', (e) => {
                const item = e.currentTarget.closest('.notice-item');
                const noticeId = Number(item.dataset.id || 0);
                const title = item.querySelector('.notice-title')?.textContent?.trim() || '';
                const content = item.querySelector('.admin-notice-content')?.textContent?.trim() || '';
                openEditNotice(noticeId, title, content);
            });
        });

        document.querySelectorAll('.admin-notice-delete').forEach((btn) => {
            btn.addEventListener('click', async (e) => {
                const item = e.currentTarget.closest('.notice-item');
                const noticeId = Number(item.dataset.id || 0);
                if (!noticeId) {
                    return;
                }

                if (!confirm('Delete this notice?')) {
                    return;
                }

                const response = await fetch('<?php echo ROOT; ?>/adminDashboard/deleteNotice', {
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

        document.addEventListener('click', (e) => {
            if (!overlay.contains(e.target) && e.target !== bell && !bell.contains(e.target)) {
                overlay.style.display = 'none';
            }
        });

        const shouldOpenProfile = <?php echo (isset($_GET['openProfile']) && $_GET['openProfile'] === '1') ? 'true' : 'false'; ?>;
        const shouldOpenNotifications = <?php echo (isset($_GET['openNotifications']) && $_GET['openNotifications'] === '1') ? 'true' : 'false'; ?>;

        if (shouldOpenProfile) {
            openProfile();
        }
        if (shouldOpenNotifications) {
            overlay.style.display = 'block';
        }
    </script>
</body>
</html>