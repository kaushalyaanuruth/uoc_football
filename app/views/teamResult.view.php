<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
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
                    <!-- <a href="<?php echo ROOT; ?>/logout" class="logout-btn">Logout</a> -->
                </div>
        </div>
        <a href="<?php echo ROOT; ?>/adminDashboard" class="back-btn">&lt; Back</a>
        
        <div class="test-results-container">
            <div class="title-container">
                <h1 class="section-title">Team Results</h1>
                <button class="add-result-btn" type="button"><span class="plus-sign">+</span>Add Test Result</button>
            </div>
            <div class="filter-container">
                <div class="filter-group search-group">
                    <label>Search:</label>
                    <input
                        type="text"
                        class="search-input"
                        placeholder="Search by player name..."
                        id="playerSearch">
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
                    <input type="date" id="dateFrom" />
                </div>

                <div class="filter-group">
                    <label>Date To</label>
                    <input type="date" id="dateTo" />
                </div>

                <div class="filter-actions">
                    <button type="button" class="search-btn" id="searchBtn">Search</button>
                </div>
            </div>
            <div class="results-container">
                <div class="result-header">Test Results</div>
                    <div class="result-topics result-structure">
                        <p>Name</p>
                        <p>Test Type</p>
                        <p>Date</p>
                        <p>Score</p>
                        <p>Notes</p>
                        <p>Actions</p>
                    </div>
                    <div class="result result-structure">
                        <p class="player-name">Anuruth Kaushalya</p>
                        <p class="test-type">Bronko</p>
                        <p class="date">2024-04-09</p>
                        <p class="score">5min 45sec</p>
                        <p class="notes">Need to improve</p>
                        <div class="result-actions">
                            <button class="icon-btn edit-btn">
                                <img class="" src="<?php echo ROOT; ?>/assets/images/common/edit.svg" alt="edit">
                            </button>
                            <button class="icon-btn delete-btn">
                                <img class="" src="<?php echo ROOT; ?>/assets/images/common/delete.svg" alt="delete">
                            </button></div>
                    </div>
                    <div class="result result-structure">
                        <p>Loading...</p>
                        <p>Loading...</p>
                        <p>Loading...</p>
                        <p>Loading...</p>
                        <p>Loading...</p>
                        <p>Loading...</p>
                    </div>
                </div>
            </div>
        </div>    
    </div>
    <div class="modal-overlay" id="addResultModal">
        <form class="modal" id="addResultForm">
            <button type="button" class="close-modal-btn" onclick="closeAddResultModal()">&times;</button>
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


    <script src="<?php echo ROOT; ?>/assets/js/teamResult/customSelect.js"></script>
    <script src="<?php echo ROOT; ?>/assets/js/teamResult/script.js"></script>
</body>
</html>