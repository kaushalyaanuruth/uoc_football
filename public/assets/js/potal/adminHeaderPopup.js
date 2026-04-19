(function () {
    function getRootFromLink(link) {
        try {
            var url = new URL(link.getAttribute('href'), window.location.origin);
            return url.pathname.replace(/\/adminDashboard.*/, '');
        } catch (error) {
            return '';
        }
    }

    function createNotificationOverlay() {
        var overlay = document.createElement('div');
        overlay.className = 'admin-header-notification-overlay';
        overlay.style.display = 'none';
        overlay.innerHTML = '<p class="admin-header-notification-title">Latest Notices</p><div class="admin-header-notification-body">Loading...</div>';
        document.body.appendChild(overlay);
        return overlay;
    }

    function createProfileModal() {
        var modal = document.createElement('div');
        modal.className = 'admin-header-profile-modal';
        modal.style.display = 'none';
        modal.innerHTML = [
            '<div class="admin-header-profile-content">',
            '  <div class="admin-header-profile-head">',
            '    <h3>Edit Profile</h3>',
            '    <button type="button" class="admin-header-close" data-action="close-profile">&times;</button>',
            '  </div>',
            '  <form class="admin-header-profile-form profile-readonly" enctype="multipart/form-data">',
            '    <div class="profile-image-wrap">',
            '      <img id="adminHeaderPreviewImage" alt="Profile Preview">',
            '      <label for="adminHeaderProfileImageInput" class="profile-image-btn profile-image-btn-disabled" id="adminHeaderProfileImageLabel">Change Image</label>',
            '      <input type="file" id="adminHeaderProfileImageInput" name="image" accept="image/*" hidden disabled>',
            '    </div>',
            '    <div class="profile-form-grid">',
            '      <div><label for="adminHeaderFirstName">First Name</label><input id="adminHeaderFirstName" name="first_name" data-editable="true" readonly required></div>',
            '      <div><label for="adminHeaderLastName">Last Name</label><input id="adminHeaderLastName" name="last_name" data-editable="true" readonly></div>',
            '      <div><label for="adminHeaderIdNumber">ID Number</label><input id="adminHeaderIdNumber" name="id_number" data-editable="true" readonly required></div>',
            '      <div><label for="adminHeaderNic">NIC</label><input id="adminHeaderNic" readonly></div>',
            '      <div><label for="adminHeaderEmail">Email Address</label><input id="adminHeaderEmail" name="email" data-editable="true" readonly></div>',
            '      <div><label for="adminHeaderPhone">Phone Number</label><input id="adminHeaderPhone" name="phone_number" data-editable="true" readonly></div>',
            '    </div>',
            '    <div class="profile-modal-actions">',
            '      <button type="button" class="btn-secondary" data-action="cancel-edit">Close</button>',
            '      <button type="button" class="btn-primary" data-action="start-edit">Edit</button>',
            '      <button type="submit" class="btn-primary" data-action="save" style="display:none;">Save Changes</button>',
            '    </div>',
            '  </form>',
            '</div>'
        ].join('');
        document.body.appendChild(modal);
        return modal;
    }

    document.addEventListener('DOMContentLoaded', function () {
        var bell = document.querySelector('.right-section .notification-icon');
        var profileTrigger = document.querySelector('.right-section .user-profile');

        if (!bell || !profileTrigger) {
            return;
        }

        var root = getRootFromLink(bell) || getRootFromLink(profileTrigger);
        if (!root) {
            return;
        }

        var notificationOverlay = createNotificationOverlay();
        var profileModal = createProfileModal();
        var profileForm = profileModal.querySelector('.admin-header-profile-form');
        var editableInputs = profileForm.querySelectorAll('[data-editable="true"]');
        var imageInput = profileModal.querySelector('#adminHeaderProfileImageInput');
        var imageLabel = profileModal.querySelector('#adminHeaderProfileImageLabel');
        var previewImage = profileModal.querySelector('#adminHeaderPreviewImage');
        var navbarImage = profileTrigger.querySelector('img');

        var initialProfileState = {
            first_name: '',
            last_name: '',
            id_number: '',
            nic: '',
            email: '',
            phone_number: '',
            image: ''
        };

        var isEditMode = false;
        var noticesLoaded = false;

        function fillProfile(profile) {
            profileModal.querySelector('#adminHeaderFirstName').value = profile.first_name || '';
            profileModal.querySelector('#adminHeaderLastName').value = profile.last_name || '';
            profileModal.querySelector('#adminHeaderIdNumber').value = profile.id_number || '';
            profileModal.querySelector('#adminHeaderNic').value = profile.nic || '';
            profileModal.querySelector('#adminHeaderEmail').value = profile.email || '';
            profileModal.querySelector('#adminHeaderPhone').value = profile.phone_number || '';
            previewImage.src = profile.image_url || (navbarImage ? navbarImage.src : '');
            if (navbarImage && profile.image_url) {
                navbarImage.src = profile.image_url;
            }
        }

        function syncInitialState() {
            initialProfileState.first_name = profileModal.querySelector('#adminHeaderFirstName').value;
            initialProfileState.last_name = profileModal.querySelector('#adminHeaderLastName').value;
            initialProfileState.id_number = profileModal.querySelector('#adminHeaderIdNumber').value;
            initialProfileState.nic = profileModal.querySelector('#adminHeaderNic').value;
            initialProfileState.email = profileModal.querySelector('#adminHeaderEmail').value;
            initialProfileState.phone_number = profileModal.querySelector('#adminHeaderPhone').value;
            initialProfileState.image = previewImage.src;
        }

        function restoreProfile() {
            fillProfile({
                first_name: initialProfileState.first_name,
                last_name: initialProfileState.last_name,
                id_number: initialProfileState.id_number,
                nic: initialProfileState.nic,
                email: initialProfileState.email,
                phone_number: initialProfileState.phone_number,
                image_url: initialProfileState.image
            });
            imageInput.value = '';
        }

        function setProfileEditMode(enabled) {
            isEditMode = enabled;
            editableInputs.forEach(function (input) {
                input.readOnly = !enabled;
            });
            imageInput.disabled = !enabled;
            imageLabel.classList.toggle('profile-image-btn-disabled', !enabled);
            profileForm.querySelector('[data-action="start-edit"]').style.display = enabled ? 'none' : 'inline-flex';
            profileForm.querySelector('[data-action="save"]').style.display = enabled ? 'inline-flex' : 'none';
            profileForm.querySelector('[data-action="cancel-edit"]').textContent = enabled ? 'Cancel Edit' : 'Close';
            profileForm.classList.toggle('profile-readonly', !enabled);
        }

        async function loadNotices() {
            if (noticesLoaded) {
                return;
            }

            var body = notificationOverlay.querySelector('.admin-header-notification-body');
            body.textContent = 'Loading...';

            try {
                var response = await fetch(root + '/adminDashboard/recentNotices');
                var result = await response.json();
                if (!result.success || !Array.isArray(result.notices)) {
                    body.textContent = 'No notices available.';
                    return;
                }

                if (result.notices.length === 0) {
                    body.textContent = 'No notices available.';
                    noticesLoaded = true;
                    return;
                }

                body.innerHTML = result.notices.map(function (notice) {
                    return '<div class="admin-header-notice-item">'
                        + '<p class="admin-header-notice-head">' + String(notice.title || 'Notice') + '</p>'
                        + '<p class="admin-header-notice-text">' + String(notice.content || '') + '</p>'
                        + '</div>';
                }).join('');
                noticesLoaded = true;
            } catch (error) {
                body.textContent = 'Failed to load notices.';
            }
        }

        async function openProfile() {
            profileModal.style.display = 'flex';
            setProfileEditMode(false);
            try {
                var response = await fetch(root + '/adminDashboard/profileData');
                var result = await response.json();
                if (!result.success || !result.profile) {
                    return;
                }
                fillProfile(result.profile);
                syncInitialState();
            } catch (error) {
                // Keep modal visible so user can retry.
            }
        }

        function closeProfile() {
            setProfileEditMode(false);
            profileModal.style.display = 'none';
        }

        bell.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();
            var shouldShow = notificationOverlay.style.display === 'none';
            notificationOverlay.style.display = shouldShow ? 'block' : 'none';
            if (shouldShow) {
                loadNotices();
            }
        });

        profileTrigger.addEventListener('click', function (event) {
            event.preventDefault();
            openProfile();
        });

        profileModal.addEventListener('click', function (event) {
            if (event.target === profileModal) {
                closeProfile();
            }
        });

        profileModal.querySelector('[data-action="close-profile"]').addEventListener('click', closeProfile);

        profileForm.querySelector('[data-action="start-edit"]').addEventListener('click', function () {
            setProfileEditMode(true);
        });

        profileForm.querySelector('[data-action="cancel-edit"]').addEventListener('click', function () {
            if (isEditMode) {
                restoreProfile();
                setProfileEditMode(false);
                return;
            }
            closeProfile();
        });

        imageInput.addEventListener('change', function () {
            if (!isEditMode) {
                return;
            }

            var file = imageInput.files && imageInput.files[0];
            if (!file) {
                return;
            }

            var reader = new FileReader();
            reader.onload = function (loadEvent) {
                previewImage.src = loadEvent.target.result;
            };
            reader.readAsDataURL(file);
        });

        profileForm.addEventListener('submit', async function (event) {
            event.preventDefault();
            if (!isEditMode) {
                return;
            }

            var formData = new FormData(profileForm);
            try {
                var response = await fetch(root + '/adminDashboard/updateProfile', {
                    method: 'POST',
                    body: formData
                });

                var result = await response.json();
                if (!result.success || !result.profile) {
                    alert(result.message || 'Failed to update profile');
                    return;
                }

                fillProfile(result.profile);
                syncInitialState();
                setProfileEditMode(false);
                closeProfile();
            } catch (error) {
                alert('Failed to update profile');
            }
        });

        document.addEventListener('click', function (event) {
            if (notificationOverlay.style.display === 'none') {
                return;
            }

            if (!notificationOverlay.contains(event.target) && !bell.contains(event.target)) {
                notificationOverlay.style.display = 'none';
            }
        });
    });
})();
