<?php
// Dummy summary data used for the static mockup.
$upcomingCount = 8;
$typeCounts = [
    'training' => 12,
    'meeting' => 3,
    'other' => 5
];

// Dummy event rows used to render the page without database data.
$events = [
    (object) [
        'id' => 1,
        'title' => 'League Match vs Barcelona FC',
        'description' => 'Important championship match',
        'event_date' => '2024-12-25',
        'event_time' => '15:00:00',
        'location' => 'Camp Nou Stadium',
        'event_type' => 'match',
        'status' => 'upcoming'
    ],
    (object) [
        'id' => 2,
        'title' => 'Weekly Training Session',
        'description' => 'Regular team practice',
        'event_date' => '2024-12-22',
        'event_time' => '09:00:00',
        'location' => 'Training Ground A',
        'event_type' => 'training',
        'status' => 'upcoming'
    ],
    (object) [
        'id' => 3,
        'title' => 'Team Strategy Meeting',
        'description' => 'Discuss upcoming matches',
        'event_date' => '2024-12-20',
        'event_time' => '14:30:00',
        'location' => 'Conference Room',
        'event_type' => 'meeting',
        'status' => 'upcoming'
    ]
];
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
    <title>UOC Football - Event Management</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/eventManagement/style.css">
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

        <section class="stats-grid">
            <article class="stat-card">
                <div class="stat-content">
                    <p class="stat-title">Upcoming Matches</p>
                    <h3 class="stat-value"><?php echo (int) $upcomingCount; ?></h3>
                </div>
                <div class="stat-icon match">trophy</div>
            </article>
            <article class="stat-card">
                <div class="stat-content">
                    <p class="stat-title">Training Sessions</p>
                    <h3 class="stat-value"><?php echo (int) $typeCounts['training']; ?></h3>
                </div>
                <div class="stat-icon training">fitness_center</div>
            </article>
            <article class="stat-card">
                <div class="stat-content">
                    <p class="stat-title">Meetings</p>
                    <h3 class="stat-value"><?php echo (int) $typeCounts['meeting']; ?></h3>
                </div>
                <div class="stat-icon meeting">groups</div>
            </article>
            <article class="stat-card">
                <div class="stat-content">
                    <p class="stat-title">Other Events</p>
                    <h3 class="stat-value"><?php echo (int) $typeCounts['other']; ?></h3>
                </div>
                <div class="stat-icon other">event</div>
            </article>
        </section>

        <!-- Search and filter toolbar -->
        <!-- Toolbar with the add button, quick view icons, and filters. -->
        <section class="toolbar-card">
            <div class="toolbar-left">
                <button class="add-event-btn" type="button" id="openEventModalBtn">+ Add Event</button>
                <button class="toolbar-icon-btn" type="button" aria-label="List view">list</button>
                <button class="toolbar-icon-btn" type="button" aria-label="Calendar view">calendar_month</button>
            </div>

            <div class="toolbar-right">
                <div class="search-wrap">
                    <span class="search-icon">search</span>
                    <input type="text" id="eventSearch" class="search-input" placeholder="Search events...">
                </div>

                <select id="typeFilter" class="type-filter" aria-label="Filter by event type">
                    <option value="all">All Types</option>
                    <option value="match">Match</option>
                    <option value="training">Training</option>
                    <option value="meeting">Meeting</option>
                    <option value="other">Other</option>
                </select>
            </div>
        </section>


        <section class="events-panel">
            <div class="events-panel-head">
                <h2>Events</h2>
            </div>

            <div class="events-table-wrap">
                <table class="events-table">
                    <thead>
                        <tr>
                            <th>Event</th>
                            <th>Type</th>
                            <th>Date &amp; Time</th>
                            <th>Location</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="eventsTableBody">
                        <?php if (!empty($events)): ?>
                            <?php foreach ($events as $event): ?>
                                <?php
                                // Normalize dummy row values for the table UI.
                                $type = strtolower($event->event_type ?? 'other');
                                if (!in_array($type, ['match', 'training', 'meeting', 'other'])) {
                                    $type = 'other';
                                }

                                $title = $event->title ?? 'Untitled Event';
                                $description = $event->description ?? '';
                                $location = $event->location ?? 'TBA';
                                $eventDate = $event->event_date ?? '';
                                $eventTime = $event->event_time ?? '00:00:00';

                                $formattedDate = $eventDate ? date('M d, Y', strtotime($eventDate)) : 'Date not set';
                                $formattedTime = $eventTime ? date('h:i A', strtotime($eventTime)) : 'Time not set';
                                ?>
                                <tr
                                    class="event-row"
                                    data-event-id="<?php echo (int) $event->id; ?>"
                                    data-title="<?php echo htmlspecialchars(strtolower($title), ENT_QUOTES, 'UTF-8'); ?>"
                                    data-type="<?php echo htmlspecialchars($type, ENT_QUOTES, 'UTF-8'); ?>"
                                >
                                    <td>
                                        <div class="event-title-wrap">
                                            <p class="event-name"><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></p>
                                            <p class="event-sub"><?php echo htmlspecialchars($description ?: 'No description provided', ENT_QUOTES, 'UTF-8'); ?></p>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="type-pill type-<?php echo htmlspecialchars($type, ENT_QUOTES, 'UTF-8'); ?>">
                                            <?php echo ucfirst($type); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="date-wrap">
                                            <p><?php echo htmlspecialchars($formattedDate, ENT_QUOTES, 'UTF-8'); ?></p>
                                            <span><?php echo htmlspecialchars($formattedTime, ENT_QUOTES, 'UTF-8'); ?></span>
                                        </div>
                                    </td>
                                    <td><?php echo htmlspecialchars($location, ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td>
                                        <div class="row-actions">
                                            <button type="button" class="icon-btn edit-btn" data-id="<?php echo (int) $event->id; ?>" aria-label="Edit event">
                                                <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                                            </button>
                                            <button type="button" class="icon-btn delete-btn" data-id="<?php echo (int) $event->id; ?>" aria-label="Delete event">
                                                <span class="material-symbols-outlined" aria-hidden="true">delete</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr id="emptyStateRow">
                                <td colspan="5" class="empty-state">No events yet. Click Add Event to create one.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>

    <!-- Add/Edit event modal -->
    <!-- Modal used as a static add/edit form mockup. -->
    <div class="modal-overlay" id="eventModal">
        <form class="modal" id="eventForm">
            <button type="button" class="close-modal-btn" id="closeEventModalBtn">&times;</button>
            <div class="modal-header">
                <img class="logo" src="<?php echo ROOT; ?>/assets/images/adminDashboard/header/uoclogo.png" alt="UOC Football Logo">
            </div>
            <h2 class="modal-title" id="modalTitle">Add Event</h2>
            <div class="modal-body">
                <input type="hidden" id="eventId" name="id">

                <div class="form-group">
                    <label class="input-label" for="eventTitle">Event Title</label>
                    <input type="text" class="form-input" id="eventTitle" name="title" value="League Match vs Barcelona FC" required>
                </div>

                <div class="form-group">
                    <label class="input-label" for="eventDate">Date &amp; Time</label>
                    <input type="datetime-local" class="form-input" id="eventDate" name="event_date" value="2024-12-25T15:00" required>
                </div>

                <div class="form-group two-col">
                    <div>
                        <label class="input-label" for="eventCategory">Type</label>
                        <select class="form-input" id="eventCategory" name="category" required>
                            <option value="match" selected>Match</option>
                            <option value="training">Training</option>
                            <option value="meeting">Meeting</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="input-label" for="eventStatus">Status</label>
                        <select class="form-input" id="eventStatus" name="status" required>
                            <option value="upcoming" selected>Upcoming</option>
                            <option value="ongoing">Ongoing</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="input-label" for="eventLocation">Location</label>
                    <input type="text" class="form-input" id="eventLocation" name="location" value="Camp Nou Stadium" required>
                </div>

                <div class="form-group notes-group">
                    <label class="input-label" for="eventDescription">Description</label>
                    <textarea class="form-input" id="eventDescription" name="description" rows="4" placeholder="Add event notes">Important championship match</textarea>
                </div>

                <p class="form-message" id="formMessage" aria-live="polite"></p>

                <div class="form-actions">
                    <button type="button" class="secondary-btn" id="cancelEventModalBtn">Cancel</button>
                    <button type="submit" class="submit-btn" id="eventSubmitBtn">Save Event</button>
                </div>
            </div>
        </form>
    </div>

    <script>
        window.EVENT_MANAGEMENT_CONFIG = {
            root: "<?php echo ROOT; ?>"
        };
    </script>
    <script src="<?php echo ROOT; ?>/assets/js/eventManagement/script.js"></script>
</body>
</html>
