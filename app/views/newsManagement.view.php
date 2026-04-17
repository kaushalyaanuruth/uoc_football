<?php
$newsItems = $data['news'] ?? [];

function newsDateParts($date)
{
    $timestamp = strtotime((string) $date);
    if (!$timestamp) {
        return ['day' => '--', 'month' => '---'];
    }

    return [
        'day' => date('d', $timestamp),
        'month' => date('M', $timestamp)
    ];
}
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
    <title>UOC Football - News Management</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/newsManagement/style.css">
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

        <a href="<?php echo ROOT; ?>/inventoryManagement" class="back-btn">&lt; Back</a>

        <section class="toolbar-card">
            <div class="toolbar-left">
                <button class="add-news-btn" type="button" id="openNewsModalBtn">+ Add News</button>
            </div>
            <div class="toolbar-right">
                <div class="search-wrap">
                    <span class="search-icon material-symbols-outlined" aria-hidden="true">search</span>
                    <input type="text" id="newsSearch" class="search-input" placeholder="Search title or description...">
                </div>
            </div>
        </section>

        <section class="news-panel">
            <div class="news-panel-head">
                <h2>News Articles</h2>
            </div>
            <div class="news-list">
                <div class="news-list-head">
                    <span>Date</span>
                    <span>Title</span>
                    <span>Description</span>
                    <span>Image</span>
                    <span>Actions</span>
                </div>
                <div id="newsListBody">
                    <?php foreach ($newsItems as $item): ?>
                        <?php $dateParts = newsDateParts($item->date ?? $item->publish_date ?? ''); ?>
                        <div class="news-item" data-news-id="<?php echo (int) $item->id; ?>" data-search="<?php echo htmlspecialchars(strtolower(($item->title ?? '') . ' ' . ($item->discription ?? $item->content ?? '')), ENT_QUOTES, 'UTF-8'); ?>">
                            <span class="news-date-block">
                                <strong><?php echo htmlspecialchars($dateParts['day'], ENT_QUOTES, 'UTF-8'); ?></strong>
                                <small><?php echo htmlspecialchars($dateParts['month'], ENT_QUOTES, 'UTF-8'); ?></small>
                            </span>
                            <span class="news-title-cell"><?php echo htmlspecialchars($item->title ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                            <span class="news-desc-cell"><?php echo htmlspecialchars($item->discription ?? $item->content ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
                            <span class="news-image-cell">
                                <?php if (!empty($item->image)): ?>
                                    <img src="<?php echo ROOT; ?>/uploads/news_images/<?php echo htmlspecialchars($item->image, ENT_QUOTES, 'UTF-8'); ?>" alt="News image">
                                <?php else: ?>
                                    <span class="image-placeholder">No image</span>
                                <?php endif; ?>
                            </span>
                            <div class="row-actions">
                                <button type="button" class="icon-btn edit-btn" data-id="<?php echo (int) $item->id; ?>" aria-label="Edit news">
                                    <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                                </button>
                                <button type="button" class="icon-btn delete-btn" data-id="<?php echo (int) $item->id; ?>" aria-label="Delete news">
                                    <span class="material-symbols-outlined" aria-hidden="true">delete</span>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if (empty($newsItems)): ?>
                    <div class="empty-state" id="emptyState">No news articles yet. Add the first news post.</div>
                <?php endif; ?>
            </div>
        </section>
    </div>

    <div class="modal-overlay" id="newsModal">
        <form class="modal" id="newsForm">
            <button type="button" class="close-modal-btn" id="closeNewsModalBtn">&times;</button>
            <div class="modal-header">
                <img class="logo" src="<?php echo ROOT; ?>/assets/images/adminDashboard/header/uoclogo.png" alt="UOC Football Logo">
            </div>
            <h2 class="modal-title" id="modalTitle">Add News</h2>
            <div class="modal-body">
                <input type="hidden" id="newsId" name="id">
                <input type="hidden" id="existingImage" name="existing_image">

                <div class="form-group">
                    <label class="input-label" for="newsTitle">Title</label>
                    <input type="text" class="form-input" id="newsTitle" name="title" required>
                </div>

                <div class="form-group two-col">
                    <div>
                        <label class="input-label" for="newsDate">Date</label>
                        <input type="date" class="form-input" id="newsDate" name="date" required>
                    </div>
                    <div>
                        <label class="input-label" for="newsImage">Image</label>
                        <input type="file" class="form-input" id="newsImage" name="image" accept="image/*">
                    </div>
                </div>

                <div class="form-group notes-group">
                    <label class="input-label" for="newsDescription">Description</label>
                    <textarea class="form-input" id="newsDescription" name="discription" rows="5" required></textarea>
                </div>

                <div class="image-preview" id="imagePreview" style="display:none;">
                    <img id="previewImg" alt="News preview">
                    <button type="button" class="remove-image-btn" id="removeImageBtn">Remove Image</button>
                </div>

                <p class="form-message" id="formMessage" aria-live="polite"></p>
                <div class="form-actions">
                    <button type="button" class="secondary-btn" id="cancelNewsModalBtn">Cancel</button>
                    <button type="submit" class="submit-btn" id="newsSubmitBtn">Save News</button>
                </div>
            </div>
        </form>
    </div>

    <script>
        window.NEWS_MANAGEMENT_CONFIG = {
            root: "<?php echo ROOT; ?>"
        };
    </script>
    <script src="<?php echo ROOT; ?>/assets/js/newsManagement/script.js"></script>
</body>
</html>