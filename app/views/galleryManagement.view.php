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

        <section class="upload-section">
            <h2 class="section-title">Gallery Management</h2>
            <form id="uploadForm" enctype="multipart/form-data">
                <div class="upload-area" id="uploadArea">
                    <div class="upload-icon">
                        <span class="material-symbols-outlined" aria-hidden="true">cloud_upload</span>
                    </div>
                    <p class="upload-text">Drag and drop images here</p>
                    <p class="upload-info">or click browse to upload JPG, PNG, GIF images (max 10MB each)</p>
                    <button type="button" class="browse-btn" id="browseBtn">Browse Images</button>
                    <input type="file" id="imageInput" name="images[]" accept="image/jpeg,image/png,image/gif" multiple hidden>
                </div>

                <div id="preview" style="display:none;">
                    <p id="fileCount"></p>
                    <div id="previewGrid" class="gallery-grid"></div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label" for="description">Description</label>
                        <textarea class="form-textarea" id="description" name="description" placeholder="Describe this set of photos"></textarea>
                    </div>
                    <div class="form-group-right">
                        <div class="form-group">
                            <label class="form-label" for="category">Category</label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="">Select Category</option>
                                <option value="practice">Practice</option>
                                <option value="matches">Matches</option>
                                <option value="events">Events</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="tags">Tags</label>
                            <input class="form-input" type="text" id="tags" name="tags" placeholder="captain, finals, training">
                        </div>
                    </div>
                </div>

                <button class="upload-submit-btn" type="submit" id="uploadBtn">Upload Images</button>
            </form>
        </section>

        <section class="filter-section">
            <div class="filter-left">
                <span class="filter-label">Filter by category:</span>
                <select class="filter-select" id="filterCategory">
                    <option value="all">All</option>
                    <option value="practice">Practice</option>
                    <option value="matches">Matches</option>
                    <option value="events">Events</option>
                </select>
            </div>
        </section>

        <section>
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
                        <article class="gallery-card" data-image-id="<?php echo $imageId; ?>" data-category="<?php echo $imageCategory; ?>" data-description="<?php echo $imageDescription; ?>" data-tags="<?php echo $imageTagsEscaped; ?>">
                            <div class="card-image-container" style="position:relative;">
                                <img src="<?php echo ROOT . '/' . $imagePath; ?>" alt="<?php echo $imageDescription; ?>" class="gallery-image">
                                <div class="card-overlay">
                                    <button class="action-btn edit-btn" type="button" data-id="<?php echo $imageId; ?>" aria-label="Edit image">
                                        <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                                    </button>
                                    <button class="action-btn delete-btn" type="button" data-id="<?php echo $imageId; ?>" aria-label="Delete image">
                                        <span class="material-symbols-outlined" aria-hidden="true">delete</span>
                                    </button>
                                </div>
                            </div>
                            <div class="card-content">
                                <p class="card-description"><?php echo $imageDescription !== '' ? $imageDescription : 'No description'; ?></p>
                                <div class="card-footer">
                                    <span class="tag tag-purple"><?php echo ucfirst($imageCategory); ?></span>
                                    <span class="card-meta"><?php echo htmlspecialchars($imageDate, ENT_QUOTES, 'UTF-8'); ?></span>
                                </div>
                                <?php if (!empty($tags)): ?>
                                    <div class="tags" style="margin-top:8px;">
                                        <?php foreach ($tags as $tag): ?>
                                            <span class="tag tag-purple"><?php echo htmlspecialchars($tag, ENT_QUOTES, 'UTF-8'); ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </article>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <h3>No images yet</h3>
                        <p>Upload your first gallery images above.</p>
                    </div>
                <?php endif; ?>
            </div>
        </section>
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
