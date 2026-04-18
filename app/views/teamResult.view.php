<?php
// Use data passed from controller, or default to empty arrays
$testResults = $testResults ?? [];
$matchResults = $matchResults ?? [];
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
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/teamResult/stats.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/teamResult/playerStats.css">
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
                            <?php if (empty($testResults)): ?>
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 20px; color: #999;">
                                        No test results found. Click "Add Test Result" to create one.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($testResults as $result): ?>
                                    <tr class="result-row" data-result-id="<?php echo (int) $result->result_id; ?>">
                                        <td>
                                            <div class="result-title-wrap">
                                                <p class="result-name"><?php echo htmlspecialchars($result->player_name ?? 'Unknown', ENT_QUOTES, 'UTF-8'); ?></p>
                                            </div>
                                        </td>
                                        <td><span class="type-pill type-other"><?php echo htmlspecialchars($result->test_type, ENT_QUOTES, 'UTF-8'); ?></span></td>
                                        <td><?php echo htmlspecialchars($result->date, ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars($result->score, ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><p class="result-sub"><?php echo htmlspecialchars($result->notes ?? '', ENT_QUOTES, 'UTF-8'); ?></p></td>
                                        <td>
                                            <div class="row-actions">
                                                <button type="button" class="icon-btn edit-btn" aria-label="Edit result" onclick="editTestResult(<?php echo (int) $result->result_id; ?>)">
                                                    <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                                                </button>
                                                <button type="button" class="icon-btn delete-btn" aria-label="Delete result" onclick="deleteTestResult(<?php echo (int) $result->result_id; ?>)">
                                                    <span class="material-symbols-outlined" aria-hidden="true">delete</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
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
                                <th>Goals</th>
                                <th>Shots</th>
                                <th>Possession</th>
                                <th>Passes</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="matchResultsTableBody">
                            <?php if (empty($matchResults)): ?>
                                <tr>
                                    <td colspan="8" style="text-align: center; padding: 20px; color: #999;">
                                        No match results found. Click "Add Match Result" to create one.
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($matchResults as $result): ?>
                                    <tr class="result-row" data-result-id="<?php echo (int) $result->result_id; ?>">
                                        <td><p class="result-name"><?php echo htmlspecialchars($result->opponent_team, ENT_QUOTES, 'UTF-8'); ?></p></td>
                                        <td>
                                            <span class="type-pill <?php echo strtolower($result->result) === 'won' ? 'type-meeting' : (strtolower($result->result) === 'draw' ? 'type-training' : 'type-match'); ?>">
                                                <?php echo htmlspecialchars($result->result, ENT_QUOTES, 'UTF-8'); ?>
                                            </span>
                                        </td>
                                        <td class="goals-cell"><strong><?php echo htmlspecialchars($result->goals_scored ?? '0', ENT_QUOTES, 'UTF-8'); ?> - <?php echo htmlspecialchars($result->goals_conceded ?? '0', ENT_QUOTES, 'UTF-8'); ?></strong></td>
                                        <td class="stats-cell"><?php echo htmlspecialchars($result->shots ?? '', ENT_QUOTES, 'UTF-8'); ?> / <?php echo htmlspecialchars($result->shots_on_target ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td class="stats-cell"><?php echo htmlspecialchars($result->possession ?? '', ENT_QUOTES, 'UTF-8'); ?>%</td>
                                        <td class="stats-cell"><?php echo htmlspecialchars($result->passes ?? '', ENT_QUOTES, 'UTF-8'); ?> (<?php echo htmlspecialchars($result->passes_accuracy ?? '', ENT_QUOTES, 'UTF-8'); ?>%)</td>
                                        <td><?php echo htmlspecialchars($result->date, ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td>
                                            <div class="row-actions">
                                                <button type="button" class="icon-btn edit-btn" aria-label="Edit result" onclick="editMatchResult(<?php echo (int) $result->result_id; ?>)">
                                                    <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                                                </button>
                                                <button type="button" class="icon-btn view-btn" aria-label="View result" onclick="viewMatchResultDetails(<?php echo (int) $result->result_id; ?>)">
                                                    <span class="material-symbols-outlined" aria-hidden="true">visibility</span>
                                                </button>
                                                <button type="button" class="icon-btn delete-btn" aria-label="Delete result" onclick="deleteMatchResult(<?php echo (int) $result->result_id; ?>)">
                                                    <span class="material-symbols-outlined" aria-hidden="true">delete</span>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
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
                        <div class="autocomplete-container">
                            <input type="text" class="form-input" id="playerName" name="playerName" placeholder="Start typing player name..." required>
                            <div class="autocomplete-dropdown" id="playerNameDropdown"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="input-label" for="testTypeSelect">Test Type</label>
                        <select class="form-input" id="testTypeSelect" name="testTypeSelect" required>
                            <option value="">Select Test Type</option>
                            <option value="bronko">Bronko</option>
                            <option value="yoyo">YoYo</option>
                            <option value="2km">2km</option>
                            <option value="5km">5km</option>
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
            <form class="modal" id="addMatchResultForm" style="max-height: 90vh; overflow-y: auto;">
                <button type="button" class="close-modal-btn" onclick="closeAddMatchResultModal()">&times;</button>
                <div class="modal-header">
                    <img class="logo" src="<?php echo ROOT; ?>/assets/images/adminDashboard/header/uoclogo.png" alt="UOC Football Logo">
                </div>
                <h2 class="modal-title">Add Match Result</h2>
                <div class="modal-body">
                    <!-- Team Match Statistics -->
                    <h3 style="font-size: 14px; margin: 15px 0 10px 0; color: #333; font-weight: 600;">Team Match Statistics</h3>
                    <div class="form-group">
                        <label class="input-label" for="opponentName">Opponent Team *</label>
                        <input type="text" class="form-input" id="opponentName" name="opponentName" placeholder="Enter opponent team name..." required>
                    </div>
                    <div class="form-group">
                        <label class="input-label" for="matchResult">Result</label>
                        <select class="form-input" id="matchResult" name="matchResult" required>
                            <option value="">Select Result</option>
                            <option value="Won">Won</option>
                            <option value="Draw">Draw</option>
                            <option value="Lost">Lost</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="input-label" for="matchDate">Date</label>
                        <input type="date" class="form-input" id="matchDate" name="matchDate" required>
                    </div>
                    <div class="form-group">
                        <label class="input-label" for="goalsScored">Goals Scored</label>
                        <input type="number" class="form-input" id="goalsScored" name="goalsScored" min="0" required>
                    </div>
                    <div class="form-group">
                        <label class="input-label" for="goalsConceded">Goals Conceded</label>
                        <input type="number" class="form-input" id="goalsConceded" name="goalsConceded" min="0" required>
                    </div>
                    <div class="form-group">
                        <label class="input-label" for="shots">Shots</label>
                        <input type="number" class="form-input" id="shots" name="shots" min="0">
                    </div>
                    <div class="form-group">
                        <label class="input-label" for="shotsOnTarget">Shots on Target</label>
                        <input type="number" class="form-input" id="shotsOnTarget" name="shotsOnTarget" min="0">
                    </div>
                    <div class="form-group">
                        <label class="input-label" for="possession">Possession (%)</label>
                        <input type="number" class="form-input" id="possession" name="possession" min="0" max="100" step="0.1">
                    </div>
                    <div class="form-group">
                        <label class="input-label" for="passes">Passes</label>
                        <input type="number" class="form-input" id="passes" name="passes" min="0">
                    </div>
                    <div class="form-group">
                        <label class="input-label" for="passesAccuracy">Passes Accuracy (%)</label>
                        <input type="number" class="form-input" id="passesAccuracy" name="passesAccuracy" min="0" max="100" step="0.1">
                    </div>
                    <div class="form-group">
                        <label class="input-label" for="corners">Corners</label>
                        <input type="number" class="form-input" id="corners" name="corners" min="0">
                    </div>
                    <div class="form-group notes-group">
                        <label class="input-label" for="matchNotes">Notes</label>
                        <textarea class="form-input" id="matchNotes" name="matchNotes" rows="4" placeholder="Notes"></textarea>
                    </div>

                    <!-- Player Match Statistics Section -->
                    <div class="form-section-wrapper" style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;">
                        <div class="form-section-header-main">
                            <span>Player Match Statistics</span>
                            <button class="add-more-btn" type="button" onclick="event.stopPropagation(); openAddPlayerStatsModal()">+</button>
                        </div>
                        <div class="section-content" id="matchPlayerStatsContainer">
                            <!-- Players will be dynamically added here -->
                        </div>
                    </div>

                    <button type="submit" class="submit-btn">Add Result</button>
                </div>
            </form>
        </div>
        <div class="modal-overlay" id="editTestResultModal">
            <form class="modal" id="editTestResultForm">
                <button type="button" class="close-modal-btn" onclick="closeEditTestResultModal()">&times;</button>
                <div class="modal-header">
                    <img class="logo" src="<?php echo ROOT; ?>/assets/images/adminDashboard/header/uoclogo.png" alt="UOC Football Logo">
                </div>
                <h2 class="modal-title">Edit Test Result</h2>
                <div class="modal-body">
                    <input type="hidden" id="editTestResultId" name="result_id">
                    <div class="form-group">
                        <label class="input-label" for="editPlayerName">Player Name</label>
                        <input type="text" class="form-input" id="editPlayerName" name="playerName" readonly>
                    </div>
                    <div class="form-group">
                        <label class="input-label" for="editTestTypeSelect">Test Type</label>
                        <select class="form-input" id="editTestTypeSelect" name="testTypeSelect" required>
                            <option value="">Select Test Type</option>
                            <option value="bronko">Bronko</option>
                            <option value="yoyo">YoYo</option>
                            <option value="2km">2km</option>
                            <option value="5km">5km</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="input-label" for="editTestDate">Date</label>
                        <input type="date" class="form-input" id="editTestDate" name="testDate" required>
                    </div>
                    <div class="form-group">
                        <label class="input-label" for="editScore">Score</label>
                        <input type="text" class="form-input" id="editScore" name="score" placeholder="e.g., 5min 45sec" required>
                    </div>
                    <div class="form-group notes-group">
                        <label class="input-label" for="editNotes">Notes</label>
                        <textarea class="form-input" id="editNotes" name="notes" rows="4" placeholder="Notes"></textarea>
                    </div>
                    <button type="submit" class="submit-btn">Update Result</button>
                </div>
            </form>
        </div>

        <div class="modal-overlay" id="editMatchResultModal">
            <form class="modal" id="editMatchResultForm">
                <button type="button" class="close-modal-btn" onclick="closeEditMatchResultModal()">&times;</button>
                <div class="modal-header">
                    <img class="logo" src="<?php echo ROOT; ?>/assets/images/adminDashboard/header/uoclogo.png" alt="UOC Football Logo">
                </div>
                <h2 class="modal-title">Edit Match Result</h2>
                <div class="modal-body">
                    <input type="hidden" id="editMatchResultId" name="result_id">
                    <div class="form-group">
                        <label class="input-label" for="editOpponentName">Opponent Team</label>
                        <input type="text" class="form-input" id="editOpponentName" name="opponentName" required>
                    </div>
                    <div class="form-group">
                        <label class="input-label" for="editMatchResult">Result</label>
                        <select class="form-input" id="editMatchResult" name="matchResult" required>
                            <option value="">Select Result</option>
                            <option value="Won">Won</option>
                            <option value="Draw">Draw</option>
                            <option value="Lost">Lost</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="input-label" for="editMatchDate">Date</label>
                        <input type="date" class="form-input" id="editMatchDate" name="matchDate" required>
                    </div>
                    <div class="form-group">
                        <label class="input-label" for="editGoalsScored">Goals Scored</label>
                        <input type="number" class="form-input" id="editGoalsScored" name="goalsScored" min="0" required>
                    </div>
                    <div class="form-group">
                        <label class="input-label" for="editGoalsConceded">Goals Conceded</label>
                        <input type="number" class="form-input" id="editGoalsConceded" name="goalsConceded" min="0" required>
                    </div>
                    <div class="form-group">
                        <label class="input-label" for="editShots">Shots</label>
                        <input type="number" class="form-input" id="editShots" name="shots" min="0">
                    </div>
                    <div class="form-group">
                        <label class="input-label" for="editShotsOnTarget">Shots on Target</label>
                        <input type="number" class="form-input" id="editShotsOnTarget" name="shotsOnTarget" min="0">
                    </div>
                    <div class="form-group">
                        <label class="input-label" for="editPossession">Possession (%)</label>
                        <input type="number" class="form-input" id="editPossession" name="possession" min="0" max="100" step="0.1">
                    </div>
                    <div class="form-group">
                        <label class="input-label" for="editPasses">Passes</label>
                        <input type="number" class="form-input" id="editPasses" name="passes" min="0">
                    </div>
                    <div class="form-group">
                        <label class="input-label" for="editPassesAccuracy">Passes Accuracy (%)</label>
                        <input type="number" class="form-input" id="editPassesAccuracy" name="passesAccuracy" min="0" max="100" step="0.1">
                    </div>
                    <div class="form-group">
                        <label class="input-label" for="editCorners">Corners</label>
                        <input type="number" class="form-input" id="editCorners" name="corners" min="0">
                    </div>
                    <div class="form-group notes-group">
                        <label class="input-label" for="editMatchNotes">Notes</label>
                        <textarea class="form-input" id="editMatchNotes" name="matchNotes" rows="4" placeholder="Notes"></textarea>
                    </div>
                    
                    <!-- Existing Player Stats Section -->
                    <div style="margin-top: 25px; padding-top: 20px; border-top: 2px solid #ddd;">
                        <h3 style="font-size: 16px; margin-bottom: 15px; color: #333; font-weight: 600; display: flex; align-items: center; gap: 10px;">
                            <span style="display: inline-block; width: 4px; height: 20px; background: #7c3aed; border-radius: 2px;"></span>
                            Player Match Statistics
                        </h3>
                        <div id="editMatchPlayerStatsContainer" style="margin-bottom: 15px;">
                            <p style="color: #999; text-align: center; padding: 15px;">Loading players...</p>
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 10px;">
                        <button type="submit" class="submit-btn" style="flex: 1;">Update Result</button>
                        <button type="button" class="submit-btn" style="flex: 1; background: #28a745;" onclick="event.stopPropagation(); openAddPlayerStatsModal(currentMatchId);">Add Player Stats</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- View Match Result Details Modal -->
        <div class="modal-overlay" id="viewMatchResultModal">
            <div class="modal stats-detail-modal">
                <button type="button" class="close-modal-btn" onclick="closeViewMatchResultModal()">&times;</button>
                <div class="modal-header">
                    <img class="logo" src="<?php echo ROOT; ?>/assets/images/adminDashboard/header/uoclogo.png" alt="UOC Football Logo">
                </div>
                <h2 class="modal-title">Match Statistics</h2>
                <div class="modal-body stats-body">
                    <div class="stats-detail-header">
                        <div class="opponent-info">
                            <h3 id="viewOpponentName"></h3>
                            <p id="viewMatchDate"></p>
                        </div>
                        <div class="result-badge" id="viewResultBadge"></div>
                    </div>

                    <div class="stats-grid">
                        <div class="stat-item">
                            <label>Goals</label>
                            <div class="stat-value" id="viewGoals"></div>
                        </div>
                        <div class="stat-item">
                            <label>Shots</label>
                            <div class="stat-value" id="viewShots"></div>
                        </div>
                        <div class="stat-item">
                            <label>Shots on Target</label>
                            <div class="stat-value" id="viewShotsOnTarget"></div>
                        </div>
                        <div class="stat-item">
                            <label>Possession</label>
                            <div class="stat-value" id="viewPossession"></div>
                        </div>
                        <div class="stat-item">
                            <label>Passes</label>
                            <div class="stat-value" id="viewPasses"></div>
                        </div>
                        <div class="stat-item">
                            <label>Pass Accuracy</label>
                            <div class="stat-value" id="viewPassAccuracy"></div>
                        </div>
                        <div class="stat-item">
                            <label>Corners</label>
                            <div class="stat-value" id="viewCorners"></div>
                        </div>
                    </div>

                    <div class="notes-section" id="notesSection" style="display: none;">
                        <h4>Notes</h4>
                        <p id="viewNotes"></p>
                    </div>

                    <!-- Player Statistics Section -->
                    <div style="margin-top: 30px; padding-top: 20px; border-top: 2px solid #ddd;">
                        <h3 style="font-size: 16px; margin-bottom: 15px; color: #333; font-weight: 600; display: flex; align-items: center; gap: 10px;">
                            <span style="display: inline-block; width: 4px; height: 20px; background: #7c3aed; border-radius: 2px;"></span>
                            Player Match Statistics
                        </h3>
                        <div id="viewMatchPlayerStatsContainer" style="min-height: 100px; display: flex; align-items: center; justify-content: center; color: #999;">
                            Loading player statistics...
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Player Match Statistics Modal -->
        <div class="modal-overlay" id="addPlayerStatsModal">
            <form class="modal player-stats-modal" id="addPlayerStatsForm" onsubmit="addPlayerStatsToForm(event)">
                <button type="button" class="close-modal-btn" onclick="closeAddPlayerStatsModal()">&times;</button>
                <div class="modal-header">
                    <img class="logo" src="<?php echo ROOT; ?>/assets/images/adminDashboard/header/uoclogo.png" alt="UOC Football Logo">
                </div>
                <h2 class="modal-title">Add Player Match Statistics</h2>
                <div class="modal-body">
                    <!-- Basic Info -->
                    <div class="stats-section-header">
                        <h3>Basic Information</h3>
                    </div>
                    <div class="stats-grid">
                        <div class="stats-form-group">
                            <label for="playerSelect">Player *</label>
                            <select id="playerSelect" name="player_id" required>
                                <option value="">Select Player</option>
                            </select>
                        </div>
                        <div class="stats-form-group">
                            <label for="positionPlayed">Position Played</label>
                            <input type="text" id="positionPlayed" name="position_played" placeholder="e.g., Forward, Midfielder">
                        </div>
                        <div class="stats-form-group">
                            <label for="minutesPlayed">Minutes Played</label>
                            <input type="number" id="minutesPlayed" name="minutes_played" min="0" max="120" value="90">
                        </div>
                        <div class="stats-form-group">
                            <label for="substitutionStatus">Substitution Status</label>
                            <select id="substitutionStatus" name="substitution_status">
                                <option value="Started">Started</option>
                                <option value="Substitute">Substitute</option>
                                <option value="Unused">Unused</option>
                            </select>
                        </div>

                        <!-- Offensive Stats -->
                        <div class="stats-section-header">
                            <h3>Offensive Statistics</h3>
                        </div>
                        <div class="stats-form-group">
                            <label for="goalsScored">Goals</label>
                            <input type="number" id="goalsScored" name="goals_scored" min="0" value="0">
                        </div>
                        <div class="stats-form-group">
                            <label for="assists">Assists</label>
                            <input type="number" id="assists" name="assists" min="0" value="0">
                        </div>
                        <div class="stats-form-group">
                            <label for="shotsOnTarget">Shots on Target</label>
                            <input type="number" id="shotsOnTarget" name="shots_on_target" min="0" value="0">
                        </div>
                        <div class="stats-form-group">
                            <label for="shotsOffTarget">Shots Off Target</label>
                            <input type="number" id="shotsOffTarget" name="shots_off_target" min="0" value="0">
                        </div>
                        <div class="stats-form-group">
                            <label for="keyPasses">Key Passes</label>
                            <input type="number" id="keyPasses" name="key_passes" min="0" value="0">
                        </div>
                        <div class="stats-form-group">
                            <label for="successfulDribbles">Successful Dribbles</label>
                            <input type="number" id="successfulDribbles" name="successful_dribbles" min="0" value="0">
                        </div>

                        <!-- Passing Stats -->
                        <div class="stats-section-header">
                            <h3>Passing Statistics</h3>
                        </div>
                        <div class="stats-form-group">
                            <label for="completedPasses">Completed Passes</label>
                            <input type="number" id="completedPasses" name="completed_passes" min="0" value="0">
                        </div>
                        <div class="stats-form-group">
                            <label for="lineBreakingPasses">Line Breaking Passes</label>
                            <input type="number" id="lineBreakingPasses" name="line_breaking_passes" min="0" value="0">
                        </div>

                        <!-- Defensive Stats -->
                        <div class="stats-section-header">
                            <h3>Defensive Statistics</h3>
                        </div>
                        <div class="stats-form-group">
                            <label for="tacklesWon">Tackles Won</label>
                            <input type="number" id="tacklesWon" name="tackles_won" min="0" value="0">
                        </div>
                        <div class="stats-form-group">
                            <label for="interceptions">Interceptions</label>
                            <input type="number" id="interceptions" name="interceptions" min="0" value="0">
                        </div>
                        <div class="stats-form-group">
                            <label for="defensiveDuelsWon">Defensive Duels Won</label>
                            <input type="number" id="defensiveDuelsWon" name="defensive_duels_won" min="0" value="0">
                        </div>
                        <div class="stats-form-group">
                            <label for="aerialDuelsWon">Aerial Duels Won</label>
                            <input type="number" id="aerialDuelsWon" name="aerial_duels_won" min="0" value="0">
                        </div>

                        <!-- Discipline -->
                        <div class="stats-section-header">
                            <h3>Discipline</h3>
                        </div>
                        <div class="stats-form-group">
                            <label for="yellowCards">Yellow Cards</label>
                            <input type="number" id="yellowCards" name="yellow_cards" min="0" max="2" value="0">
                        </div>
                        <div class="stats-form-group">
                            <label for="redCards">Red Cards</label>
                            <input type="number" id="redCards" name="red_cards" min="0" max="1" value="0">
                        </div>
                        <div class="stats-form-group">
                            <label for="foulsCommitted">Fouls Committed</label>
                            <input type="number" id="foulsCommitted" name="fouls_committed" min="0" value="0">
                        </div>
                        <div class="stats-form-group">
                            <label for="foulsWon">Fouls Won</label>
                            <input type="number" id="foulsWon" name="fouls_won" min="0" value="0">
                        </div>

                        <!-- Notes -->
                        <div style="grid-column: span 2;">
                            <div class="stats-form-group">
                                <label for="notes">Notes</label>
                                <textarea id="notes" name="notes" placeholder="Additional notes or observations about player performance..." rows="3"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="stats-action-buttons">
                        <button type="submit" class="stats-submit-btn">Save Player Statistics</button>
                        <button type="button" class="stats-reset-btn" onclick="closeAddPlayerStatsModal()">Cancel</button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Edit Player Match Statistics Modal -->
        <div class="modal-overlay" id="editPlayerStatsModal">
            <form class="modal player-stats-modal" id="editPlayerStatsForm" onsubmit="submitEditPlayerStatsForm(event)">
                <button type="button" class="close-modal-btn" onclick="closeEditPlayerStatsModal()">&times;</button>
                <div class="modal-header">
                    <img class="logo" src="<?php echo ROOT; ?>/assets/images/adminDashboard/header/uoclogo.png" alt="UOC Football Logo">
                </div>
                <h2 class="modal-title">Edit Player Match Statistics</h2>
                <div class="modal-body">
                    <input type="hidden" id="editStatId" name="stat_id">

                    <!-- Basic Info -->
                    <div class="stats-section-header">
                        <h3>Basic Information</h3>
                    </div>
                    <div class="stats-grid">
                        <div class="stats-form-group">
                            <label for="editPositionPlayed">Position Played</label>
                            <input type="text" id="editPositionPlayed" name="position_played" placeholder="e.g., Forward, Midfielder">
                        </div>
                        <div class="stats-form-group">
                            <label for="editMinutesPlayed">Minutes Played</label>
                            <input type="number" id="editMinutesPlayed" name="minutes_played" min="0" max="120" value="90">
                        </div>
                        <div class="stats-form-group">
                            <label for="editSubstitutionStatus">Substitution Status</label>
                            <select id="editSubstitutionStatus" name="substitution_status">
                                <option value="Started">Started</option>
                                <option value="Substitute">Substitute</option>
                                <option value="Unused">Unused</option>
                            </select>
                        </div>

                        <!-- Offensive Stats -->
                        <div class="stats-section-header">
                            <h3>Offensive Statistics</h3>
                        </div>
                        <div class="stats-form-group">
                            <label for="editGoalsScored">Goals</label>
                            <input type="number" id="editGoalsScored" name="goals_scored" min="0" value="0">
                        </div>
                        <div class="stats-form-group">
                            <label for="editAssists">Assists</label>
                            <input type="number" id="editAssists" name="assists" min="0" value="0">
                        </div>
                        <div class="stats-form-group">
                            <label for="editShotsOnTarget">Shots on Target</label>
                            <input type="number" id="editShotsOnTarget" name="shots_on_target" min="0" value="0">
                        </div>
                        <div class="stats-form-group">
                            <label for="editShotsOffTarget">Shots Off Target</label>
                            <input type="number" id="editShotsOffTarget" name="shots_off_target" min="0" value="0">
                        </div>
                        <div class="stats-form-group">
                            <label for="editKeyPasses">Key Passes</label>
                            <input type="number" id="editKeyPasses" name="key_passes" min="0" value="0">
                        </div>
                        <div class="stats-form-group">
                            <label for="editSuccessfulDribbles">Successful Dribbles</label>
                            <input type="number" id="editSuccessfulDribbles" name="successful_dribbles" min="0" value="0">
                        </div>

                        <!-- Passing Stats -->
                        <div class="stats-section-header">
                            <h3>Passing Statistics</h3>
                        </div>
                        <div class="stats-form-group">
                            <label for="editCompletedPasses">Completed Passes</label>
                            <input type="number" id="editCompletedPasses" name="completed_passes" min="0" value="0">
                        </div>
                        <div class="stats-form-group">
                            <label for="editLineBreakingPasses">Line Breaking Passes</label>
                            <input type="number" id="editLineBreakingPasses" name="line_breaking_passes" min="0" value="0">
                        </div>

                        <!-- Defensive Stats -->
                        <div class="stats-section-header">
                            <h3>Defensive Statistics</h3>
                        </div>
                        <div class="stats-form-group">
                            <label for="editTacklesWon">Tackles Won</label>
                            <input type="number" id="editTacklesWon" name="tackles_won" min="0" value="0">
                        </div>
                        <div class="stats-form-group">
                            <label for="editInterceptions">Interceptions</label>
                            <input type="number" id="editInterceptions" name="interceptions" min="0" value="0">
                        </div>
                        <div class="stats-form-group">
                            <label for="editDefensiveDuelsWon">Defensive Duels Won</label>
                            <input type="number" id="editDefensiveDuelsWon" name="defensive_duels_won" min="0" value="0">
                        </div>
                        <div class="stats-form-group">
                            <label for="editAerialDuelsWon">Aerial Duels Won</label>
                            <input type="number" id="editAerialDuelsWon" name="aerial_duels_won" min="0" value="0">
                        </div>

                        <!-- Discipline -->
                        <div class="stats-section-header">
                            <h3>Discipline</h3>
                        </div>
                        <div class="stats-form-group">
                            <label for="editYellowCards">Yellow Cards</label>
                            <input type="number" id="editYellowCards" name="yellow_cards" min="0" max="2" value="0">
                        </div>
                        <div class="stats-form-group">
                            <label for="editRedCards">Red Cards</label>
                            <input type="number" id="editRedCards" name="red_cards" min="0" max="1" value="0">
                        </div>
                        <div class="stats-form-group">
                            <label for="editFoulsCommitted">Fouls Committed</label>
                            <input type="number" id="editFoulsCommitted" name="fouls_committed" min="0" value="0">
                        </div>
                        <div class="stats-form-group">
                            <label for="editFoulsWon">Fouls Won</label>
                            <input type="number" id="editFoulsWon" name="fouls_won" min="0" value="0">
                        </div>

                        <!-- Notes -->
                        <div style="grid-column: span 2;">
                            <div class="stats-form-group">
                                <label for="editNotes">Notes</label>
                                <textarea id="editNotes" name="notes" placeholder="Additional notes or observations about player performance..." rows="3"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="stats-action-buttons">
                        <button type="submit" class="stats-submit-btn">Update Player Statistics</button>
                        <button type="button" class="stats-reset-btn" onclick="closeEditPlayerStatsModal()">Cancel</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="<?php echo ROOT; ?>/assets/js/teamResult/customSelect.js"></script>
    <script>
        // Define ROOT for JavaScript use
        window.ROOT = "<?php echo ROOT; ?>";
    </script>
    <script src="<?php echo ROOT; ?>/assets/js/teamResult/script.js"></script>
    <script src="<?php echo ROOT; ?>/assets/js/teamResult/playerStats.js"></script>
</body>
</html>
