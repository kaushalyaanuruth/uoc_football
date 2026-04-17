<?php
$testResults = [
    (object) [
        'id' => 1,
        'name' => 'Anuruth Kaushalya',
        'test_type' => 'Bronko',
        'date' => '2024-04-09',
        'score' => '5min 45sec',
        'notes' => 'Need to improve'
    ],
    (object) [
        'id' => 2,
        'name' => 'Kasun Madushan',
        'test_type' => 'YOYO',
        'date' => '2024-04-11',
        'score' => 'Level 18.2',
        'notes' => 'Good endurance'
    ]
];

$matchResults = [
    (object) [
        'id' => 1,
        'opponent' => 'University of Moratuwa',
        'result' => 'Won',
        'score' => '2 - 1',
        'date' => '2024-03-29',
        'notes' => 'Strong second-half performance'
    ],
    (object) [
        'id' => 2,
        'opponent' => 'University of Colombo',
        'result' => 'Draw',
        'score' => '1 - 1',
        'date' => '2024-04-05',
        'notes' => 'Missed late chances'
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
    <title>UOC_football</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/teamResult/teamResult.css">
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
                <a href="<?php echo ROOT; ?>/logout" class="logout-btn">Logout</a>
            </div>
        </div>

        <a href="<?php echo ROOT; ?>/adminDashboard" class="back-btn">&lt; Back</a>

        <div class="test-results-container">
            <div class="title-container">
                <h1 class="section-title">Test Results</h1>
                <button class="add-result-btn" type="button"><span class="plus-sign">+</span>Add Test Result</button>
            </div>

            <div class="filter-container">
                <div class="filter-group search-group">
                    <label>Search:</label>
                    <input type="text" class="search-input" placeholder="Search by player name..." id="playerSearch">
                </div>

                <div class="filter-group">
                    <label>Test Type</label>
                    <div class="custom-select" id="testType">
                        <div class="select-selected">All Types</div>
                        <div class="select-items select-hide">
                            <div class="dataType" data-value="bronko">Bronko</div>
                            <div class="dataType" data-value="yoyo">YOYO</div>
                            <div class="dataType" data-value="2km">2km</div>
                            <div class="dataType" data-value="5km">5km</div>
                        </div>
                    </div>
                </div>

                <div class="filter-group">
                    <label>Date From</label>
                    <input type="date" id="dateFrom">
                </div>

                <div class="filter-group">
                    <label>Date To</label>
                    <input type="date" id="dateTo">
                </div>

                <div class="filter-actions">
                    <button type="button" class="search-btn" id="searchBtn">Search</button>
                </div>
            </div>

            <section class="results-panel">
                <div class="results-panel-head">
                    <h2>Test Results</h2>
                </div>
                <div class="results-table-wrap">
                    <table class="results-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Test Type</th>
                                <th>Date</th>
                                <th>Score</th>
                                <th>Notes</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="testResultsTableBody">
                            <?php foreach ($testResults as $result): ?>
                                <tr class="result-row" data-result-id="<?php echo (int) $result->id; ?>">
                                    <td>
                                        <div class="result-title-wrap">
                                            <p class="result-name"><?php echo htmlspecialchars($result->name, ENT_QUOTES, 'UTF-8'); ?></p>
                                        </div>
                                    </td>
                                    <td><span class="type-pill type-other"><?php echo htmlspecialchars($result->test_type, ENT_QUOTES, 'UTF-8'); ?></span></td>
                                    <td><?php echo htmlspecialchars($result->date, ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($result->score, ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><p class="result-sub"><?php echo htmlspecialchars($result->notes, ENT_QUOTES, 'UTF-8'); ?></p></td>
                                    <td>
                                        <div class="row-actions">
                                            <button type="button" class="icon-btn edit-btn" aria-label="Edit result">
                                                <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                                            </button>
                                            <button type="button" class="icon-btn delete-btn" aria-label="Delete result">
                                                <span class="material-symbols-outlined" aria-hidden="true">delete</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <div class="match-results-container">
            <div class="title-container">
                <h1 class="section-title">Match Results</h1>
                <button class="add-result-btn" type="button"><span class="plus-sign">+</span>Add Match Result</button>
            </div>

            <div class="filter-container">
                <div class="filter-group search-group">
                    <label>Search:</label>
                    <input type="text" class="search-input" placeholder="Search by opponent team..." id="matchSearch">
                </div>

                <div class="filter-group">
                    <label>Date From</label>
                    <input type="date" id="matchDateFrom">
                </div>

                <div class="filter-group">
                    <label>Date To</label>
                    <input type="date" id="matchDateTo">
                </div>

                <div class="filter-actions">
                    <button type="button" class="search-btn" id="matchSearchBtn">Search</button>
                </div>
            </div>

            <section class="results-panel">
                <div class="results-panel-head">
                    <h2>Match Results</h2>
                </div>
                <div class="results-table-wrap">
                    <table class="results-table">
                        <thead>
                            <tr>
                                <th>Opponent Team</th>
                                <th>Result</th>
                                <th>Score</th>
                                <th>Date</th>
                                <th>Notes</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="matchResultsTableBody">
                            <?php foreach ($matchResults as $result): ?>
                                <tr class="result-row" data-result-id="<?php echo (int) $result->id; ?>">
                                    <td><p class="result-name"><?php echo htmlspecialchars($result->opponent, ENT_QUOTES, 'UTF-8'); ?></p></td>
                                    <td>
                                        <span class="type-pill <?php echo strtolower($result->result) === 'won' ? 'type-meeting' : (strtolower($result->result) === 'draw' ? 'type-training' : 'type-match'); ?>">
                                            <?php echo htmlspecialchars($result->result, ENT_QUOTES, 'UTF-8'); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($result->score, ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><?php echo htmlspecialchars($result->date, ENT_QUOTES, 'UTF-8'); ?></td>
                                    <td><p class="result-sub"><?php echo htmlspecialchars($result->notes, ENT_QUOTES, 'UTF-8'); ?></p></td>
                                    <td>
                                        <div class="row-actions">
                                            <button type="button" class="icon-btn edit-btn" aria-label="Edit result">
                                                <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                                            </button>
                                            <button type="button" class="icon-btn delete-btn" aria-label="Delete result">
                                                <span class="material-symbols-outlined" aria-hidden="true">delete</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </div>

        <div class="modal-overlay" id="addTestResultModal">
            <form class="modal" id="addTestResultForm">
                <button type="button" class="close-modal-btn" onclick="closeAddTestResultModal()">&times;</button>
                <div class="modal-header">
                    <img class="logo" src="<?php echo ROOT; ?>/assets/images/adminDashboard/header/uoclogo.png" alt="UOC Football Logo">
                </div>
                <h2 class="modal-title">Add Test Result</h2>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="input-label" for="playerName">Player Name</label>
                        <input type="text" class="form-input" id="playerName" name="playerName" required>
                    </div>
                    <div class="form-group">
                        <label class="input-label" for="testTypeSelect">Test Type</label>
                        <select class="form-input" id="testTypeSelect" name="testTypeSelect" required>
                            <option value="">Select Test Type</option>
                            <option value="t20">T20</option>
                            <option value="odi">ODI</option>
                            <option value="test">Test</option>
                            <option value="t10">T10</option>
                            <option value="domestic">Domestic</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="input-label" for="testDate">Date</label>
                        <input type="date" class="form-input" id="testDate" name="testDate" required>
                    </div>
                    <div class="form-group">
                        <label class="input-label" for="score">Score</label>
                        <input type="text" class="form-input" id="score" name="score" placeholder="e.g., 5min 45sec" required>
                    </div>
                    <div class="form-group notes-group">
                        <label class="input-label" for="notes">Notes</label>
                        <textarea class="form-input" id="notes" name="notes" rows="4" placeholder="Notes"></textarea>
                    </div>
                    <button type="submit" class="submit-btn">Add Result</button>
                </div>
            </form>
        </div>

        <div class="modal-overlay" id="addMatchResultModal">
            <form class="modal" id="addMatchResultForm">
                <button type="button" class="close-modal-btn" onclick="closeAddMatchResultModal()">&times;</button>
                <div class="modal-header">
                    <img class="logo" src="<?php echo ROOT; ?>/assets/images/adminDashboard/header/uoclogo.png" alt="UOC Football Logo">
                </div>
                <h2 class="modal-title">Add Match Result</h2>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="input-label" for="opponentName">Opponent Team</label>
                        <input type="text" class="form-input" id="opponentName" name="opponentName" required>
                    </div>
                    <div class="form-group">
                        <label class="input-label" for="matchResult">Result</label>
                        <select class="form-input" id="matchResult" name="matchResult" required>
                            <option value="">Select Result</option>
                            <option value="won">Won</option>
                            <option value="draw">Draw</option>
                            <option value="lost">Lost</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="input-label" for="matchDate">Date</label>
                        <input type="date" class="form-input" id="matchDate" name="matchDate" required>
                    </div>
                    <div class="form-group">
                        <label class="input-label" for="matchScore">Score</label>
                        <input type="text" class="form-input" id="matchScore" name="matchScore" placeholder="e.g., 2 - 1" required>
                    </div>
                    <div class="form-group notes-group">
                        <label class="input-label" for="matchNotes">Notes</label>
                        <textarea class="form-input" id="matchNotes" name="matchNotes" rows="4" placeholder="Notes"></textarea>
                    </div>
                    <button type="submit" class="submit-btn">Add Result</button>
                </div>
            </form>
        </div>
    </div>

    <script src="<?php echo ROOT; ?>/assets/js/teamResult/customSelect.js"></script>
    <script src="<?php echo ROOT; ?>/assets/js/teamResult/script.js"></script>
</body>
</html>
