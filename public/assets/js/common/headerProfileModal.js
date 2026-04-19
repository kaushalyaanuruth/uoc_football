(function () {
    function ensureModalStyles() {
        if (document.getElementById('headerProfileModalStyles')) {
            return;
        }

        var style = document.createElement('style');
        style.id = 'headerProfileModalStyles';
        style.textContent = [
            '.profile-modal{position:fixed;inset:0;background:rgba(17,24,39,0.55);display:none;align-items:center;justify-content:center;padding:20px;z-index:1300;}',
            '.profile-modal-content{background:#fff;border-radius:14px;max-width:640px;width:100%;max-height:90vh;overflow:auto;box-shadow:0 20px 45px rgba(0,0,0,0.2);}',
            '.profile-modal-header{display:flex;justify-content:space-between;align-items:center;padding:16px 20px;border-bottom:1px solid #e5e7eb;}',
            '.profile-modal-header h3{margin:0;font-size:1.05rem;color:#1f2937;}',
            '.profile-modal-header button{border:0;background:transparent;font-size:1.4rem;line-height:1;cursor:pointer;color:#6b7280;}',
            '.profile-image-wrap{display:flex;flex-direction:column;align-items:center;gap:10px;padding:18px 20px 8px;}',
            '.profile-image-wrap img{width:96px;height:96px;border-radius:50%;object-fit:cover;border:3px solid #ede9fe;}',
            '.profile-image-btn{display:inline-flex;align-items:center;justify-content:center;height:34px;padding:0 12px;border-radius:8px;background:#4a1150;color:#fff;font-size:0.85rem;cursor:pointer;}',
            '.profile-image-btn.profile-image-btn-disabled{opacity:.45;pointer-events:none;}',
            '.profile-form-grid{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px;padding:10px 20px 6px;}',
            '.profile-form-grid label{display:block;font-size:.8rem;font-weight:600;color:#4b5563;margin-bottom:5px;}',
            '.profile-form-grid input{width:100%;height:40px;border:1px solid #d1d5db;border-radius:8px;padding:0 10px;font-size:.9rem;}',
            '.profile-readonly .profile-form-grid input[readonly]{background:#f9fafb;color:#6b7280;}',
            '.profile-modal-actions{display:flex;justify-content:flex-end;gap:10px;padding:14px 20px 20px;}',
            '.profile-modal-actions .btn-primary,.profile-modal-actions .btn-secondary{height:36px;padding:0 14px;border-radius:8px;border:0;cursor:pointer;font-size:.88rem;font-weight:600;display:inline-flex;align-items:center;justify-content:center;line-height:1;}',
            '.profile-modal-actions .btn-primary{background:#4a1150;color:#fff;}',
            '.profile-modal-actions .btn-secondary{background:#e5e7eb;color:#374151;}',
            '@media (max-width: 700px){.profile-form-grid{grid-template-columns:1fr;}}'
        ].join('');
        document.head.appendChild(style);
    }

    function createModal() {
        var modal = document.createElement('div');
        modal.className = 'profile-modal';
        modal.id = 'sharedHeaderProfileModal';
        modal.innerHTML = [
            '<div class="profile-modal-content">',
            '  <div class="profile-modal-header">',
            '    <h3>Edit Profile</h3>',
            '    <button type="button" id="sharedHeaderCloseProfile" aria-label="Close profile editor">&times;</button>',
            '  </div>',
            '  <form id="sharedHeaderProfileForm" class="profile-readonly" enctype="multipart/form-data">',
            '    <div class="profile-image-wrap">',
            '      <img id="sharedHeaderProfilePreview" alt="Profile Preview">',
            '      <label for="sharedHeaderProfileImageInput" class="profile-image-btn profile-image-btn-disabled" id="sharedHeaderProfileImageLabel">Change Image</label>',
            '      <input type="file" id="sharedHeaderProfileImageInput" name="image" accept="image/*" hidden disabled>',
            '    </div>',
            '    <div class="profile-form-grid">',
            '      <div><label for="sharedHeaderFirstName">First Name</label><input type="text" id="sharedHeaderFirstName" name="first_name" data-editable="true" readonly required></div>',
            '      <div><label for="sharedHeaderLastName">Last Name</label><input type="text" id="sharedHeaderLastName" name="last_name" data-editable="true" readonly></div>',
            '      <div><label for="sharedHeaderIdNumber">ID Number</label><input type="text" id="sharedHeaderIdNumber" name="id_number" data-editable="true" readonly required></div>',
            '      <div><label for="sharedHeaderNic">NIC</label><input type="text" id="sharedHeaderNic" readonly></div>',
            '      <div><label for="sharedHeaderEmail">Email Address</label><input type="email" id="sharedHeaderEmail" name="email" data-editable="true" readonly></div>',
            '      <div><label for="sharedHeaderPhone">Phone Number</label><input type="text" id="sharedHeaderPhone" name="phone_number" data-editable="true" readonly></div>',
            '    </div>',
            '    <div class="profile-modal-actions">',
            '      <button type="button" class="btn-secondary" id="sharedHeaderCancelProfile">Close</button>',
            '      <button type="button" class="btn-primary" id="sharedHeaderStartEdit">Edit</button>',
            '      <button type="submit" class="btn-primary" id="sharedHeaderSaveProfile" style="display:none;">Save Changes</button>',
            '    </div>',
            '  </form>',
            '</div>'
        ].join('');

        document.body.appendChild(modal);
        return modal;
    }

    function textValue(value) {
        return (value == null ? '' : String(value));
    }

    document.addEventListener('DOMContentLoaded', function () {
        var config = window.HEADER_PROFILE_MODAL_CONFIG || {};
        var fetchUrl = textValue(config.fetchUrl);
        var updateUrl = textValue(config.updateUrl);

        if (!fetchUrl || !updateUrl) {
            return;
        }

        var trigger = document.querySelector(textValue(config.triggerSelector) || '.user-profile');
        if (!trigger) {
            return;
        }

        // Skip pages that already have an inline profile modal implementation.
        if (document.getElementById('profileModal') || document.getElementById('profileTrigger')) {
            return;
        }

        ensureModalStyles();
        var modal = createModal();
        var form = document.getElementById('sharedHeaderProfileForm');
        var editableInputs = form.querySelectorAll('[data-editable="true"]');
        var imageInput = document.getElementById('sharedHeaderProfileImageInput');
        var imageLabel = document.getElementById('sharedHeaderProfileImageLabel');
        var previewImage = document.getElementById('sharedHeaderProfilePreview');
        var closeButton = document.getElementById('sharedHeaderCloseProfile');
        var cancelButton = document.getElementById('sharedHeaderCancelProfile');
        var startEditButton = document.getElementById('sharedHeaderStartEdit');
        var saveButton = document.getElementById('sharedHeaderSaveProfile');
        var headerImages = document.querySelectorAll('.user-profile img');
        var welcomeSelector = textValue(config.welcomeNameSelector);
        var welcomeName = welcomeSelector ? document.querySelector(welcomeSelector) : null;

        var initial = {
            first_name: '',
            last_name: '',
            id_number: '',
            nic: '',
            email: '',
            phone_number: '',
            image: ''
        };
        var editMode = false;

        function setEditMode(enabled) {
            editMode = enabled;
            editableInputs.forEach(function (input) {
                input.readOnly = !enabled;
            });
            imageInput.disabled = !enabled;
            imageLabel.classList.toggle('profile-image-btn-disabled', !enabled);
            startEditButton.style.display = enabled ? 'none' : 'inline-flex';
            saveButton.style.display = enabled ? 'inline-flex' : 'none';
            cancelButton.textContent = enabled ? 'Cancel Edit' : 'Close';
            form.classList.toggle('profile-readonly', !enabled);
        }

        function fillProfile(profile) {
            document.getElementById('sharedHeaderFirstName').value = textValue(profile.first_name);
            document.getElementById('sharedHeaderLastName').value = textValue(profile.last_name);
            document.getElementById('sharedHeaderIdNumber').value = textValue(profile.id_number);
            document.getElementById('sharedHeaderNic').value = textValue(profile.nic);
            document.getElementById('sharedHeaderEmail').value = textValue(profile.email);
            document.getElementById('sharedHeaderPhone').value = textValue(profile.phone_number);

            if (profile.image_url) {
                previewImage.src = profile.image_url;
                headerImages.forEach(function (image) {
                    image.src = profile.image_url;
                });
            }

            if (welcomeName && profile.name) {
                var safeName = String(profile.name).replace(/</g, '&lt;').replace(/>/g, '&gt;');
                if (welcomeName.innerHTML.indexOf('<br>') !== -1) {
                    welcomeName.innerHTML = 'Welcome,<br>' + safeName;
                } else {
                    welcomeName.textContent = safeName;
                }
            }
        }

        function syncInitial() {
            initial.first_name = document.getElementById('sharedHeaderFirstName').value;
            initial.last_name = document.getElementById('sharedHeaderLastName').value;
            initial.id_number = document.getElementById('sharedHeaderIdNumber').value;
            initial.nic = document.getElementById('sharedHeaderNic').value;
            initial.email = document.getElementById('sharedHeaderEmail').value;
            initial.phone_number = document.getElementById('sharedHeaderPhone').value;
            initial.image = previewImage.src;
        }

        function restoreInitial() {
            fillProfile({
                first_name: initial.first_name,
                last_name: initial.last_name,
                id_number: initial.id_number,
                nic: initial.nic,
                email: initial.email,
                phone_number: initial.phone_number,
                image_url: initial.image
            });
            imageInput.value = '';
        }

        function closeModal() {
            setEditMode(false);
            modal.style.display = 'none';
        }

        async function openModal() {
            modal.style.display = 'flex';
            setEditMode(false);

            try {
                var response = await fetch(fetchUrl);
                var result = await response.json();
                if (!result.success || !result.profile) {
                    alert(result.message || 'Failed to load profile');
                    return;
                }

                fillProfile(result.profile);
                syncInitial();
            } catch (error) {
                alert('Failed to load profile');
            }
        }

        trigger.addEventListener('click', function (event) {
            event.preventDefault();
            openModal();
        });

        if (!trigger.hasAttribute('tabindex')) {
            trigger.setAttribute('tabindex', '0');
        }
        trigger.setAttribute('role', 'button');

        trigger.addEventListener('keydown', function (event) {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                openModal();
            }
        });

        closeButton.addEventListener('click', closeModal);
        cancelButton.addEventListener('click', function () {
            if (editMode) {
                restoreInitial();
                setEditMode(false);
                return;
            }
            closeModal();
        });

        startEditButton.addEventListener('click', function () {
            setEditMode(true);
        });

        modal.addEventListener('click', function (event) {
            if (event.target === modal) {
                closeModal();
            }
        });

        imageInput.addEventListener('change', function () {
            if (!editMode) {
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

        form.addEventListener('submit', async function (event) {
            event.preventDefault();
            if (!editMode) {
                return;
            }

            try {
                var formData = new FormData(form);
                var response = await fetch(updateUrl, {
                    method: 'POST',
                    body: formData
                });

                var result = await response.json();
                if (!result.success || !result.profile) {
                    alert(result.message || 'Failed to update profile');
                    return;
                }

                fillProfile(result.profile);
                syncInitial();
                closeModal();
                alert(result.message || 'Profile updated successfully');
            } catch (error) {
                alert('Failed to update profile');
            }
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape' && modal.style.display === 'flex') {
                closeModal();
            }
        });
    });
})();
