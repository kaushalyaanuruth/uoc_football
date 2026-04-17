<?php
// This view receives gallery records from GalleryManagement controller.
$images = $data['images'] ?? [];
$title = $data['title'] ?? 'Gallery Management';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@400" rel="stylesheet">
    <title><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/galleryManagement/style.css">
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
            </div>
        </div>

        <a href="<?php echo ROOT; ?>/adminDashboard" class="back-btn">&lt; Back</a>

        <section class="toolbar-card">
            <div class="toolbar-left">
                <button class="add-gallery-btn" type="button" id="openGalleryModalBtn">+ Upload Photos</button>
            </div>
            <div class="toolbar-right">
                <div class="search-wrap">
                    <span class="search-icon material-symbols-outlined" aria-hidden="true">search</span>
                    <input type="text" id="gallerySearch" class="search-input" placeholder="Search description...">
                </div>
                <select class="type-filter" id="filterCategory">
                    <option value="all">All Categories</option>
                    <option value="practice">Practice</option>
                    <option value="matches">Matches</option>
                    <option value="events">Events</option>
                    <option value="team">Team</option>
                    <option value="training">Training</option>
                    <option value="other">Other</option>
                </select>
            </div>
        </section>

        <section class="gallery-panel">
            <div class="gallery-panel-head">
                <h2>Gallery Photos</h2>
            </div>
            <div class="gallery-grid" id="galleryGrid">
                <?php if (!empty($images)): ?>
                    <?php foreach ($images as $image): ?>
                        <?php
                        $imageId = (int) ($image->id ?? 0);
                        $imagePath = htmlspecialchars($image->filepath ?? '', ENT_QUOTES, 'UTF-8');
                        $imageDescription = htmlspecialchars($image->description ?? '', ENT_QUOTES, 'UTF-8');
                        $imageCategory = htmlspecialchars(strtolower($image->category ?? 'events'), ENT_QUOTES, 'UTF-8');
                        $imageDate = !empty($image->created_at) ? date('Y-m-d', strtotime($image->created_at)) : '';
                        $rawTags = (string) ($image->tags ?? '');
                        $tags = array_filter(array_map('trim', explode(',', $rawTags)));
                        $imageTagsEscaped = htmlspecialchars($rawTags, ENT_QUOTES, 'UTF-8');
                        ?>
                        <div class="gallery-item" data-image-id="<?php echo $imageId; ?>" data-category="<?php echo $imageCategory; ?>" data-search="<?php echo htmlspecialchars(strtolower($imageDescription), ENT_QUOTES, 'UTF-8'); ?>">
                            <div class="gallery-image-container">
                                <img src="<?php echo ROOT . '/' . $imagePath; ?>" alt="<?php echo $imageDescription; ?>" class="gallery-image">
                                <div class="gallery-actions">
                                    <button class="action-btn edit-btn" type="button" data-id="<?php echo $imageId; ?>" aria-label="Edit image">
                                        <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                                    </button>
                                    <button class="action-btn delete-btn" type="button" data-id="<?php echo $imageId; ?>" aria-label="Delete image">
                                        <span class="material-symbols-outlined" aria-hidden="true">delete</span>
                                    </button>
                                </div>
                            </div>
                            <div class="gallery-info">
                                <p class="gallery-description"><?php echo $imageDescription !== '' ? $imageDescription : 'No description'; ?></p>
                                <div class="gallery-meta">
                                    <span class="tag"><?php echo ucfirst($imageCategory); ?></span>
                                    <span class="date"><?php echo $imageDate; ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state" id="emptyState">No photos yet. Upload the first gallery images.</div>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <!-- Upload Modal -->
    <div class="modal-overlay" id="uploadModal">
        <form class="modal" id="uploadForm" enctype="multipart/form-data">
            <button type="button" class="close-modal-btn" id="closeUploadModalBtn">&times;</button>
            <div class="modal-header">
                <img class="logo" src="<?php echo ROOT; ?>/assets/images/adminDashboard/header/uoclogo.png" alt="UOC Football Logo">
            </div>
            <h2 class="modal-title">Upload Photos</h2>
            <div class="modal-body">
                <div class="upload-area" id="uploadArea">
                    <div class="upload-icon">
                        <span class="material-symbols-outlined" aria-hidden="true">cloud_upload</span>
                    </div>
                    <p class="upload-text">Drag and drop images here</p>
                    <p class="upload-info">or click browse to upload JPG, PNG, GIF images (max 10MB each)</p>
                    <button type="button" class="browse-btn" id="browseBtn">Browse Images</button>
                    <input type="file" id="imageInput" name="images[]" accept="image/jpeg,image/png,image/gif" multiple hidden>
                </div>

                <div id="preview" style="display:none; margin-top: 20px;">
                    <p id="fileCount" style="margin-bottom: 10px; font-weight: 600;"></p>
                    <div id="previewGrid" class="preview-grid"></div>
                </div>

                <div class="form-group">
                    <label class="input-label" for="description">Description</label>
                    <textarea class="form-input" id="description" name="description" rows="3" placeholder="Describe this set of photos"></textarea>
                </div>

                <div class="form-group two-col">
                    <div>
                        <label class="input-label" for="category">Category</label>
                        <select class="form-input" id="category" name="category" required>
                            <option value="">Select Category</option>
                            <option value="practice">Practice</option>
                            <option value="matches">Matches</option>
                            <option value="events">Events</option>
                            <option value="training">Training</option>
                            <option value="team">Team</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="input-label" for="tags">Tags</label>
                        <input class="form-input" type="text" id="tags" name="tags" placeholder="captain, finals, training">
                    </div>
                </div>

                <p class="form-message" id="uploadFormMessage" aria-live="polite"></p>
                <div class="form-actions">
                    <button type="button" class="secondary-btn" id="cancelUploadBtn">Cancel</button>
                    <button type="submit" class="submit-btn" id="uploadBtn">Upload Photos</button>
                </div>
            </div>
        </form>
    </div>

    <div class="modal-overlay" id="editModal">
        <form class="modal" id="editForm">
            <button type="button" class="close-modal-btn" id="closeEditModalBtn">&times;</button>
            <div class="modal-header">
                <img class="logo" src="<?php echo ROOT; ?>/assets/images/adminDashboard/header/uoclogo.png" alt="UOC Football Logo">
            </div>
            <h2 class="modal-title">Edit Photo</h2>
            <div class="modal-body">
                <input type="hidden" id="editImageId" name="id">

                <div class="form-group">
                    <label class="input-label" for="editDescription">Description</label>
                    <textarea class="form-input" id="editDescription" name="description" rows="4" placeholder="Update photo description"></textarea>
                </div>

                <div class="form-group two-col">
                    <div>
                        <label class="input-label" for="editCategory">Category</label>
                        <select class="form-input" id="editCategory" name="category" required>
                            <option value="practice">Practice</option>
                            <option value="matches">Matches</option>
                            <option value="events">Events</option>
                            <option value="training">Training</option>
                            <option value="team">Team</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="input-label" for="editTags">Tags</label>
                        <input class="form-input" type="text" id="editTags" name="tags" placeholder="captain, finals, training">
                    </div>
                </div>

                <p class="form-message" id="editFormMessage" aria-live="polite"></p>
                <div class="form-actions">
                    <button type="button" class="secondary-btn" id="cancelEditBtn">Cancel</button>
                    <button type="submit" class="submit-btn" id="saveEditBtn">Update Photo</button>
                </div>
            </div>
        </form>
    </div>

    <div class="modal-overlay" id="deleteModal">
        <form class="modal" id="deleteForm">
            <button type="button" class="close-modal-btn" id="closeDeleteModalBtn">&times;</button>
            <div class="modal-header">
                <img class="logo" src="<?php echo ROOT; ?>/assets/images/adminDashboard/header/uoclogo.png" alt="UOC Football Logo">
            </div>
            <h2 class="modal-title">Delete Photo</h2>
            <div class="modal-body">
                <input type="hidden" id="deleteImageId" name="id">
                <p class="item-desc" id="deleteMessage">Are you sure you want to delete this photo? This action cannot be undone.</p>
                <p class="form-message" id="deleteFormMessage" aria-live="polite"></p>
                <div class="form-actions">
                    <button type="button" class="secondary-btn" id="cancelDeleteBtn">Cancel</button>
                    <button type="submit" class="submit-btn" id="confirmDeleteBtn">Delete Photo</button>
                </div>
            </div>
        </form>
    </div>

    <script>
        window.GALLERY_MANAGEMENT_CONFIG = {
            root: "<?php echo ROOT; ?>"
        };
    </script>
    <script src="<?php echo ROOT; ?>/assets/js/galleryManagement/script.js"></script>
</body>
</html>
