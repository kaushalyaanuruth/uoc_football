function openAddTestResultModal() {
    const modal = document.getElementById("addTestResultModal");
    if (modal) {
        modal.classList.add("active");
        // Initialize autocomplete when modal opens
        initPlayerNameAutocomplete();
    }
}

function closeAddTestResultModal() {
    const modal = document.getElementById("addTestResultModal");
    const form = document.getElementById("addTestResultForm");
    const dropdownContainer = document.getElementById("playerNameDropdown");

    if (modal) {
        modal.classList.remove("active");
    }

    if (form) {
        form.reset();
    }
    
    if (dropdownContainer) {
        dropdownContainer.classList.remove("active");
        dropdownContainer.innerHTML = "";
    }
    
    // Reset autocomplete flag so it can be reinitialized on next open
    autocompleteInitialized = false;
}

function openAddMatchResultModal() {
    const modal = document.getElementById("addMatchResultModal");
    if (modal) {
        modal.classList.add("active");
    }
}

function closeAddMatchResultModal() {
    const modal = document.getElementById("addMatchResultModal");
    const form = document.getElementById("addMatchResultForm");

    if (modal) {
        modal.classList.remove("active");
    }

    if (form) {
        form.reset();
    }
    
    // Reset player stats array
    resetPlayerStatsArray();
}

/**
 * Submit test result form
 */
function submitTestResultForm(event) {
    event.preventDefault();
    
    const form = document.getElementById("addTestResultForm");
    const formData = new FormData(form);

    const baseUrl = window.ROOT || window.location.origin;
    fetch(`${baseUrl}/teamResult/addTestResult`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            closeAddTestResultModal();
            location.reload(); // Reload to show new data
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while submitting the form');
    });
}

/**
 * Submit match result form
 */
/**
 * Submit match result form with player stats
 */
function submitMatchResultForm(event) {
    event.preventDefault();
    
    const form = document.getElementById("addMatchResultForm");
    const formData = new FormData(form);
    
    // Append player stats as JSON, filtering out display-only fields
    const playerStats = getPlayerStatsArray();
    const cleanedStats = playerStats.map(stat => {
        const cleaned = { ...stat };
        delete cleaned._playerName; // Remove display-only field
        return cleaned;
    });
    formData.append('player_stats', JSON.stringify(cleanedStats));

    const baseUrl = window.ROOT || window.location.origin;
    fetch(`${baseUrl}/teamResult/addMatchResult`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            closeAddMatchResultModal();
            location.reload(); // Reload to show new data
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while submitting the form');
    });
}

/**
 * Initialize autocomplete for player names
 */
let autocompleteInitialized = false;

function initPlayerNameAutocomplete() {
    // Only initialize once
    if (autocompleteInitialized) return;
    
    const playerNameInput = document.getElementById("playerName");
    const dropdownContainer = document.getElementById("playerNameDropdown");
    
    if (!playerNameInput || !dropdownContainer) {
        console.error("Autocomplete elements not found");
        return;
    }

    autocompleteInitialized = true;
    let debounceTimer;
    
    playerNameInput.addEventListener("input", function() {
        const query = this.value.trim();
        
        clearTimeout(debounceTimer);
        
        if (query.length < 1) {
            dropdownContainer.classList.remove("active");
            dropdownContainer.innerHTML = "";
            return;
        }

        debounceTimer = setTimeout(() => {
            const baseUrl = window.ROOT || window.location.origin;
            const url = `${baseUrl}/teamResult/getTeamMembers?q=${encodeURIComponent(query)}`;
            console.log("Fetching from:", url);
            
            fetch(url)
                .then(response => {
                    console.log("Response status:", response.status);
                    console.log("Response headers:", response.headers.get('content-type'));
                    return response.text();
                })
                .then(text => {
                    console.log("Raw response:", text);
                    try {
                        const data = JSON.parse(text);
                        console.log("Parsed JSON data:", data);
                        if (data.success && data.members && data.members.length > 0) {
                            renderAutocompleteSuggestions(data.members, playerNameInput, dropdownContainer);
                            dropdownContainer.classList.add("active");
                        } else {
                            dropdownContainer.innerHTML = '<div class="autocomplete-no-results">No team members found</div>';}
                        dropdownContainer.classList.add("active");
                        }catch (jsonError) {
                        console.error("JSON parse error:", jsonError);
                        console.log("First 200 chars of response:", text.substring(0, 200));
                        dropdownContainer.innerHTML = '<div class="autocomplete-no-results">Error parsing response</div>';
                        dropdownContainer.classList.add("active");
                    }
                })
                .catch(error => {
                    console.error("Error fetching team members:", error);
                    dropdownContainer.classList.remove("active");
                });
        }, 300);
    });

    // Close dropdown when clicking outside
    document.addEventListener("click", function(event) {
        if (!event.target.closest(".autocomplete-container")) {
            dropdownContainer.classList.remove("active");
        }
    });
}

/**
 * Render autocomplete suggestions
 */
function renderAutocompleteSuggestions(members, inputField, dropdownContainer) {
    dropdownContainer.innerHTML = "";
    
    members.forEach(member => {
        const item = document.createElement("div");
        item.className = "autocomplete-item";
        item.textContent = member.full_name;
        item.setAttribute("data-member-id", member.player_id);
        item.setAttribute("data-member-name", member.full_name);
        
        item.addEventListener("click", function() {
            inputField.value = member.full_name;
            dropdownContainer.classList.remove("active");
            dropdownContainer.innerHTML = "";
        });
        
        item.addEventListener("mouseover", function() {
            // Remove previous highlight
            document.querySelectorAll(".autocomplete-item.highlighted").forEach(el => {
                el.classList.remove("highlighted");
            });
            this.classList.add("highlighted");
        });
        
        dropdownContainer.appendChild(item);
    });
}

document.addEventListener("DOMContentLoaded", function () {
    const openButtons = document.querySelectorAll(".add-result-btn");
    const closeButtons = document.querySelectorAll(".close-modal-btn");
    const testModal = document.getElementById("addTestResultModal");
    const matchModal = document.getElementById("addMatchResultModal");
    const searchBtn = document.getElementById("searchBtn");
    const searchResultInput = document.getElementById("playerSearch");
    const testTypeFilter = document.getElementById("testType");
    const dateFromInput = document.getElementById("dateFrom");
    const dateToInput = document.getElementById("dateTo");
    const testResultForm = document.getElementById("addTestResultForm");
    const matchResultForm = document.getElementById("addMatchResultForm");

    // Open modals
    openButtons.forEach(function (button) {
        const buttonText = button.textContent.trim().toLowerCase();

        if (buttonText.includes("match")) {
            button.addEventListener("click", openAddMatchResultModal);
        } else {
            button.addEventListener("click", openAddTestResultModal);
        }
    });

    // Close modals
    closeButtons.forEach(function (button) {
        button.addEventListener("click", function () {
            if(button.closest("#addTestResultModal")) {
                closeAddTestResultModal();
            } else if(button.closest("#addMatchResultModal")) {
                closeAddMatchResultModal();}      
        });
    });

    // Handle form submissions
    if (testResultForm) {
        testResultForm.addEventListener("submit", submitTestResultForm);
    }

    if (matchResultForm) {
        matchResultForm.addEventListener("submit", submitMatchResultForm);
    }

    // Handle delete buttons
    const deleteButtons = document.querySelectorAll(".delete-btn");
    deleteButtons.forEach(button => {
        button.addEventListener("click", function() {
            const row = this.closest(".result-row");
            const resultId = row.getAttribute("data-result-id");
            
            // Determine if it's a test result or match result based on table
            const isTestResult = row.closest(".test-results-container") !== null;
            const resultType = isTestResult ? 'test' : 'match';
            
            deleteResult(resultId, resultType);
        });
    });

    // Handle test results filtering
    if (searchBtn) {
        searchBtn.addEventListener("click", function() {
            filterTestResults();
        });
    }

    // Filter on Enter key in search input
    if (searchResultInput) {
        searchResultInput.addEventListener("keypress", function(e) {
            if (e.key === "Enter") {
                filterTestResults();
            }
        });
    }

    // Also filter when date inputs change
    if (dateFromInput) {
        dateFromInput.addEventListener("change", function() {
            filterTestResults();
        });
    }

    if (dateToInput) {
        dateToInput.addEventListener("change", function() {
            filterTestResults();
        });
    }

    // Handle test type filter changes
    const testTypeElements = document.querySelectorAll("#testType .dataType");
    testTypeElements.forEach(element => {
        element.addEventListener("click", function() {
            // Delay filter call to allow custom select to update
            setTimeout(filterTestResults, 100);
        });
    });

    // Handle match results filtering
    const matchSearchBtn = document.getElementById("matchSearchBtn");
    const matchSearch = document.getElementById("matchSearch");
    const matchDateFrom = document.getElementById("matchDateFrom");
    const matchDateTo = document.getElementById("matchDateTo");

    if (matchSearchBtn) {
        matchSearchBtn.addEventListener("click", function() {
            filterMatchResults();
        });
    }

    // Filter on Enter key in match search input
    if (matchSearch) {
        matchSearch.addEventListener("keypress", function(e) {
            if (e.key === "Enter") {
                filterMatchResults();
            }
        });
    }

    // Also filter when match date inputs change
    if (matchDateFrom) {
        matchDateFrom.addEventListener("change", function() {
            filterMatchResults();
        });
    }

    if (matchDateTo) {
        matchDateTo.addEventListener("change", function() {
            filterMatchResults();
        });
    }

    // Handle edit form submissions
    const editTestResultForm = document.getElementById("editTestResultForm");
    if (editTestResultForm) {
        editTestResultForm.addEventListener("submit", function(event) {
            event.preventDefault();
            
            const formData = new FormData(this);
            const baseUrl = window.ROOT || window.location.origin;
            
            fetch(`${baseUrl}/teamResult/editTestResult`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    closeEditTestResultModal();
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the test result');
            });
        });
    }

    const editMatchResultForm = document.getElementById("editMatchResultForm");
    if (editMatchResultForm) {
        editMatchResultForm.addEventListener("submit", function(event) {
            event.preventDefault();
            
            const formData = new FormData(this);
            
            // Include new player stats if any were added in edit mode
            const playerStats = getPlayerStatsArray();
            if (playerStats.length > 0) {
                const cleanedStats = playerStats.map(stat => {
                    const cleaned = { ...stat };
                    delete cleaned._playerName;
                    return cleaned;
                });
                formData.append('player_stats', JSON.stringify(cleanedStats));
            }
            
            const baseUrl = window.ROOT || window.location.origin;
            
            fetch(`${baseUrl}/teamResult/editMatchResult`, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    closeEditMatchResultModal();
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the match result');
            });
        });
    }
});

/**
 * Edit test result
 */
function editTestResult(resultId) {
    const baseUrl = window.ROOT || window.location.origin;
    fetch(`${baseUrl}/teamResult/getTestResult?id=${resultId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.result) {
                populateEditTestResultModal(data.result);
                document.getElementById("editTestResultModal").classList.add("active");
            } else {
                alert('Error: ' + (data.message || 'Failed to load test result'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while loading the test result');
        });
}

/**
 * Populate edit test result modal
 */
function populateEditTestResultModal(result) {
    document.getElementById("editTestResultId").value = result.result_id || result.id;
    document.getElementById("editPlayerName").value = result.player_name || '';
    document.getElementById("editTestTypeSelect").value = result.test_type || '';
    document.getElementById("editTestDate").value = result.date || '';
    document.getElementById("editScore").value = result.score || '';
    document.getElementById("editNotes").value = result.notes || '';
}

/**
 * Close edit test result modal
 */
function closeEditTestResultModal() {
    const modal = document.getElementById("editTestResultModal");
    const form = document.getElementById("editTestResultForm");
    
    if (modal) {
        modal.classList.remove("active");
    }
    
    if (form) {
        form.reset();
    }
}

/**
 * Edit match result
 */
function editMatchResult(resultId) {
    const baseUrl = window.ROOT || window.location.origin;
    fetch(`${baseUrl}/teamResult/getMatchResult?id=${resultId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.result) {
                currentMatchId = resultId; // Set for player stats
                resetPlayerStatsArray(); // Clear any previous additions
                populateEditMatchResultModal(data.result);
                
                // Load existing player stats for this match
                loadExistingPlayerStatsForEdit(resultId);
                
                document.getElementById("editMatchResultModal").classList.add("active");
            } else {
                alert('Error: ' + (data.message || 'Failed to load match result'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while loading the match result');
        });
}

/**
 * Populate edit match result modal
 */
function populateEditMatchResultModal(result) {
    document.getElementById("editMatchResultId").value = result.result_id || result.id;
    document.getElementById("editOpponentName").value = result.opponent_team || '';
    document.getElementById("editMatchResult").value = result.result || '';
    document.getElementById("editGoalsScored").value = result.goals_scored || '';
    document.getElementById("editGoalsConceded").value = result.goals_conceded || '';
    document.getElementById("editShots").value = result.shots || '';
    document.getElementById("editShotsOnTarget").value = result.shots_on_target || '';
    document.getElementById("editPossession").value = result.possession || '';
    document.getElementById("editPasses").value = result.passes || '';
    document.getElementById("editPassesAccuracy").value = result.passes_accuracy || '';
    document.getElementById("editCorners").value = result.corners || '';
    document.getElementById("editMatchDate").value = result.date || '';
    document.getElementById("editMatchNotes").value = result.notes || '';
}

/**
 * Load existing player stats for edit modal
 */
function loadExistingPlayerStatsForEdit(matchId) {
    const container = document.getElementById("editMatchPlayerStatsContainer");
    if (!container) {
        console.log('editMatchPlayerStatsContainer not found');
        return;
    }
    
    const baseUrl = window.ROOT || window.location.origin;
    console.log('Fetching player stats for match:', matchId);
    
    fetch(`${baseUrl}/teamResult/getMatchPlayerStats?match_id=${matchId}`)
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Player stats response:', data);
            
            // Extract stats from response object
            const stats = data.stats || [];
            
            if (!stats || stats.length === 0) {
                container.innerHTML = '<p style="color: #999; text-align: center; padding: 15px;">No players added yet. Click "Add Player Stats" to add.</p>';
                return;
            }
            
            // Display existing player stats with edit and delete options
            let html = '';
            stats.forEach((stat) => {
                html += `
                    <div style="background: white; border-left: 4px solid #7c3aed; padding: 12px; margin-bottom: 10px; border-radius: 4px; display: flex; justify-content: space-between; align-items: center;">
                        <div>
                            <strong>${stat.player_name || 'Unknown'}</strong><br>
                            <small style="color: #666;">Position: ${stat.position_played || '-'} | Minutes: ${stat.minutes_played || 0}' | Goals: ${stat.goals_scored || 0}</small>
                        </div>
                        <div style="display: flex; gap: 8px;">
                            <button type="button" style="background: none; border: none; color: #7c3aed; cursor: pointer; padding: 4px;" onclick="editPlayerStatFromMatch(${stat.stat_id || stat.id})" title="Edit player">
                                <span class="material-symbols-outlined" style="font-size: 20px;">edit</span>
                            </button>
                            <button type="button" style="background: none; border: none; color: red; cursor: pointer; padding: 4px;" onclick="deletePlayerStatFromMatch(${stat.stat_id || stat.id})" title="Delete player">
                                <span class="material-symbols-outlined" style="font-size: 20px;">delete</span>
                            </button>
                        </div>
                    </div>
                `;
            });
            container.innerHTML = html;
        })
        .catch(error => {
            console.error('Error loading player stats:', error);
            container.innerHTML = '<p style="color: #d9534f; text-align: center; padding: 15px;">Failed to load players. Click "Add Player Stats" to add.</p>';
        });
}

/**
 * Edit player stat from match
 */
function editPlayerStatFromMatch(statId) {
    const baseUrl = window.ROOT || window.location.origin;
    
    fetch(`${baseUrl}/teamResult/getPlayerStat?stat_id=${statId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.stat) {
                // Populate the edit modal with existing data
                populateEditPlayerStatModal(data.stat);
                
                // Store stat ID for update
                document.getElementById("editStatId").value = statId;
                
                // Open edit modal
                document.getElementById("editPlayerStatsModal").classList.add("active");
                
                // Close edit match modal
                document.getElementById("editMatchResultModal").classList.remove("active");
            } else {
                alert('Error: Failed to load player stats');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while loading player stats');
        });
}

/**
 * Populate edit player stat modal with existing data
 */
function populateEditPlayerStatModal(stat) {
    document.getElementById("editPositionPlayed").value = stat.position_played || '';
    document.getElementById("editMinutesPlayed").value = stat.minutes_played || 90;
    document.getElementById("editSubstitutionStatus").value = stat.substitution_status || 'Started';
    document.getElementById("editGoalsScored").value = stat.goals_scored || 0;
    document.getElementById("editAssists").value = stat.assists || 0;
    document.getElementById("editShotsOnTarget").value = stat.shots_on_target || 0;
    document.getElementById("editShotsOffTarget").value = stat.shots_off_target || 0;
    document.getElementById("editKeyPasses").value = stat.key_passes || 0;
    document.getElementById("editSuccessfulDribbles").value = stat.successful_dribbles || 0;
    document.getElementById("editCompletedPasses").value = stat.completed_passes || 0;
    document.getElementById("editLineBreakingPasses").value = stat.line_breaking_passes || 0;
    document.getElementById("editTacklesWon").value = stat.tackles_won || 0;
    document.getElementById("editInterceptions").value = stat.interceptions || 0;
    document.getElementById("editDefensiveDuelsWon").value = stat.defensive_duels_won || 0;
    document.getElementById("editAerialDuelsWon").value = stat.aerial_duels_won || 0;
    document.getElementById("editYellowCards").value = stat.yellow_cards || 0;
    document.getElementById("editRedCards").value = stat.red_cards || 0;
    document.getElementById("editFoulsCommitted").value = stat.fouls_committed || 0;
    document.getElementById("editFoulsWon").value = stat.fouls_won || 0;
    document.getElementById("editNotes").value = stat.notes || '';
}

/**
 * Submit edit player stats form
 */
function submitEditPlayerStatsForm(event) {
    event.preventDefault();
    
    const form = document.getElementById("editPlayerStatsForm");
    const statId = document.getElementById("editStatId").value;
    
    if (!statId) {
        alert('Error: Stat ID not found');
        return;
    }
    
    const formData = new FormData(form);
    const baseUrl = window.ROOT || window.location.origin;
    
    fetch(`${baseUrl}/teamResult/updatePlayerMatchStats`, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Player statistics updated successfully');
            closeEditPlayerStatsModal();
            
            // Reload player stats in edit match modal
            if (currentMatchId) {
                loadExistingPlayerStatsForEdit(currentMatchId);
                
                // Reopen edit match modal
                document.getElementById("editMatchResultModal").classList.add("active");
            }
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating player statistics');
    });
}

/**
 * Close edit player stats modal
 */
function closeEditPlayerStatsModal() {
    const modal = document.getElementById("editPlayerStatsModal");
    const form = document.getElementById("editPlayerStatsForm");
    
    if (modal) {
        modal.classList.remove("active");
    }
    
    if (form) {
        form.reset();
        document.getElementById("editStatId").value = '';
    }
}

/**
 * Delete player stat from match
 */
function deletePlayerStatFromMatch(statId) {
    if (!confirm('Are you sure you want to remove this player from the match?')) return;
    
    const baseUrl = window.ROOT || window.location.origin;
    fetch(`${baseUrl}/teamResult/deletePlayerMatchStats`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `stat_id=${statId}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload existing stats display
            loadExistingPlayerStatsForEdit(currentMatchId);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while deleting player stats');
    });
}

/**
 * Close edit match result modal
 */
function closeEditMatchResultModal() {
    const modal = document.getElementById("editMatchResultModal");
    const form = document.getElementById("editMatchResultForm");
    
    if (modal) {
        modal.classList.remove("active");
    }
    
    if (form) {
        form.reset();
    }
    
    resetPlayerStatsArray();
}

/**
 * View match result details
 */
function viewMatchResultDetails(resultId) {
    const baseUrl = window.ROOT || window.location.origin;
    fetch(`${baseUrl}/teamResult/getMatchResult?id=${resultId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.result) {
                currentMatchId = resultId; // Set for player stats
                populateViewMatchResultModal(data.result);
                document.getElementById("viewMatchResultModal").classList.add("active");
                // Load player stats for this match
                loadMatchPlayerStats(resultId);
            } else {
                alert('Error: ' + (data.message || 'Failed to load match result'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while loading the match result');
        });
}

/**
 * Populate view match result modal with statistics
 */
function populateViewMatchResultModal(result) {
    const resultColor = result.result === 'Won' ? '#28a745' : (result.result === 'Draw' ? '#ffc107' : '#dc3545');
    
    document.getElementById("viewOpponentName").textContent = result.opponent_team || 'Unknown';
    document.getElementById("viewMatchDate").textContent = result.date || '';
    document.getElementById("viewResultBadge").innerHTML = `<span class="result-badge-text" style="background-color: ${resultColor};">${result.result}</span>`;
    
    document.getElementById("viewGoals").textContent = `${result.goals_scored || 0} - ${result.goals_conceded || 0}`;
    document.getElementById("viewShots").textContent = `${result.shots || 0} (${result.shots_on_target || 0} on target)`;
    document.getElementById("viewShotsOnTarget").textContent = result.shots_on_target || '-';
    document.getElementById("viewPossession").textContent = result.possession ? `${result.possession}%` : '-';
    document.getElementById("viewPasses").textContent = result.passes || '-';
    document.getElementById("viewPassAccuracy").textContent = result.passes_accuracy ? `${result.passes_accuracy}%` : '-';
    document.getElementById("viewCorners").textContent = result.corners || '-';
    
    const notesSection = document.getElementById("notesSection");
    const notesElement = document.getElementById("viewNotes");
    if (result.notes && result.notes.trim() !== '') {
        notesElement.textContent = result.notes;
        notesSection.style.display = 'block';
    } else {
        notesSection.style.display = 'none';
    }
}

/**
 * Close view match result modal
 */
function closeViewMatchResultModal() {
    const modal = document.getElementById("viewMatchResultModal");
    if (modal) {
        modal.classList.remove("active");
    }
}

/**
 * Load player stats for viewing in match detail modal
 */
function loadMatchPlayerStats(matchId) {
    const baseUrl = window.ROOT || window.location.origin;
    const container = document.getElementById("viewMatchPlayerStatsContainer");
    
    if (!container) return;
    
    fetch(`${baseUrl}/teamResult/getMatchPlayerStats?match_id=${matchId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayViewMatchPlayerStats(data.stats || []);
            } else {
                container.innerHTML = '<p style="color: #999; text-align: center; padding: 20px;">No player statistics available</p>';
            }
        })
        .catch(error => {
            console.error('Error loading player stats:', error);
            container.innerHTML = '<p style="color: #e74c3c; text-align: center; padding: 20px;">Error loading player statistics</p>';
        });
}

/**
 * Display player stats in the view modal with card layout
 */
function displayViewMatchPlayerStats(stats) {
    const container = document.getElementById("viewMatchPlayerStatsContainer");
    
    if (!container) return;
    
    if (stats.length === 0) {
        container.innerHTML = '<p style="color: #999; text-align: center; padding: 20px; width: 100%;">No player statistics recorded for this match</p>';
        return;
    }
    
    let html = `<div style="width: 100%; display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 15px;">`;
    
    stats.forEach(stat => {
        const statusBg = stat.substitution_status === 'Started' ? '#d4edda' : (stat.substitution_status === 'Substitute' ? '#fff3cd' : '#f8d7da');
        const statusColor = stat.substitution_status === 'Started' ? '#155724' : (stat.substitution_status === 'Substitute' ? '#856404' : '#721c24');
        const cards = (stat.yellow_cards || 0) + (stat.red_cards || 0);
        
        html += `
            <div style="background: white; border: 1px solid #ddd; border-radius: 8px; padding: 15px; border-left: 4px solid #7c3aed;">
                <div style="margin-bottom: 12px;">
                    <h4 style="margin: 0 0 4px 0; font-size: 14px; font-weight: 600; color: #333;">${stat.player_name || 'Unknown'}</h4>
                    <p style="margin: 0; font-size: 12px; color: #666;">Position: <strong>${stat.position_played || '-'}</strong></p>
                </div>
                
                <div style="background: #f9f9f9; padding: 10px; border-radius: 6px; margin-bottom: 12px;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px; font-size: 12px;">
                        <div>
                            <span style="color: #666;">Minutes:</span><br>
                            <strong style="font-size: 14px; color: #333;">${stat.minutes_played || 0}'</strong>
                        </div>
                        <div>
                            <span style="color: #666;">Status:</span><br>
                            <span style="display: inline-block; padding: 2px 8px; border-radius: 10px; background: ${statusBg}; color: ${statusColor}; font-size: 11px; font-weight: 500;">${stat.substitution_status || '-'}</span>
                        </div>
                    </div>
                </div>
                
                <div style="border-top: 1px solid #eee; padding-top: 10px;">
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; font-size: 12px;">
                        <div style="text-align: center; padding: 8px; background: #f3e8ff; border-radius: 4px;">
                            <span style="color: #666; font-size: 11px;">Goals</span><br>
                            <strong style="font-size: 16px; color: #7c3aed;">${stat.goals_scored || 0}</strong>
                        </div>
                        <div style="text-align: center; padding: 8px; background: #f5f5f5; border-radius: 4px;">
                            <span style="color: #666; font-size: 11px;">Assists</span><br>
                            <strong style="font-size: 16px; color: #333;">${stat.assists || 0}</strong>
                        </div>
                        <div style="text-align: center; padding: 8px; background: #f5f5f5; border-radius: 4px;">
                            <span style="color: #666; font-size: 11px;">Shots</span><br>
                            <strong style="font-size: 14px; color: #333;">${stat.shots_on_target || 0}/${stat.shots_off_target || 0}</strong>
                        </div>
                        <div style="text-align: center; padding: 8px; background: #f5f5f5; border-radius: 4px;">
                            <span style="color: #666; font-size: 11px;">Tackles</span><br>
                            <strong style="font-size: 16px; color: #333;">${stat.tackles_won || 0}</strong>
                        </div>
                        <div style="text-align: center; padding: 8px; background: #f5f5f5; border-radius: 4px;">
                            <span style="color: #666; font-size: 11px;">Passes</span><br>
                            <strong style="font-size: 16px; color: #333;">${stat.completed_passes || 0}</strong>
                        </div>
                        <div style="text-align: center; padding: 8px; background: ${cards > 0 ? '#f8d7da' : '#f5f5f5'}; border-radius: 4px;">
                            <span style="color: #666; font-size: 11px;">Cards</span><br>
                            <strong style="font-size: 12px; color: ${cards > 0 ? '#721c24' : '#333'};">${stat.yellow_cards || 0}Y ${stat.red_cards || 0}R</strong>
                        </div>
                    </div>
                </div>
                
                ${stat.notes ? `<div style="margin-top: 10px; padding-top: 10px; border-top: 1px solid #eee; font-size: 12px; color: #666;"><strong>Notes:</strong> ${stat.notes}</div>` : ''}
            </div>
        `;
    });
    
    html += `</div>`;
    
    container.innerHTML = html;
}

/**
 * Delete test result
 */
function deleteTestResult(resultId) {
    if (confirm('Are you sure you want to delete this test result? This cannot be undone.')) {
        const baseUrl = window.ROOT || window.location.origin;
        const formData = new FormData();
        formData.append('result_id', resultId);
        
        fetch(`${baseUrl}/teamResult/deleteTestResult`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the test result');
        });
    }
}

/**
 * Delete match result
 */
function deleteMatchResult(resultId) {
    if (confirm('Are you sure you want to delete this match result? This cannot be undone.')) {
        const baseUrl = window.ROOT || window.location.origin;
        const formData = new FormData();
        formData.append('result_id', resultId);
        
        fetch(`${baseUrl}/teamResult/deleteMatchResult`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the match result');
        });
    }
}

/**
 * Filter test results based on criteria
 */
function filterTestResults() {
    const searchInput = document.getElementById("playerSearch");
    const testTypeSelect = document.getElementById("testType");
    const dateFromInput = document.getElementById("dateFrom");
    const dateToInput = document.getElementById("dateTo");
    const tableBody = document.getElementById("testResultsTableBody");
    
    // Get filter values
    const searchQuery = (searchInput?.value || '').toLowerCase().trim();
    const selectedTestTypeText = testTypeSelect?.querySelector(".select-selected")?.textContent?.trim() || 'All Types';
    const dateFrom = dateFromInput?.value || '';
    const dateTo = dateToInput?.value || '';
    
    console.log('Filter criteria:', { searchQuery, selectedTestTypeText, dateFrom, dateTo });
    
    // Get all rows
    const rows = tableBody?.querySelectorAll(".result-row");
    if (!rows || rows.length === 0) return;
    
    let visibleCount = 0;
    
    rows.forEach(row => {
        const playerName = row.querySelector(".result-name")?.textContent?.trim() || '';
        const testTypeElement = row.querySelector(".type-pill");
        const testTypeText = testTypeElement?.textContent?.trim() || '';
        const dateCell = row.querySelectorAll("td")[2]?.textContent?.trim() || '';
        
        // Check player name match (case insensitive)
        const playerMatch = searchQuery === '' || playerName.toLowerCase().includes(searchQuery);
        
        // Check test type match (case insensitive)
        let typeMatch = selectedTestTypeText === 'All Types';
        if (!typeMatch) {
            // Normalize both strings for comparison (remove extra spaces, convert to lowercase)
            const normalizedSelected = selectedTestTypeText.toLowerCase().replace(/\s+/g, '');
            const normalizedTestType = testTypeText.toLowerCase().replace(/\s+/g, '');
            typeMatch = normalizedSelected === normalizedTestType;
        }
        
        // Check date range match
        let dateMatch = true;
        if (dateFrom || dateTo) {
            try {
                const resultDate = new Date(dateCell);
                if (dateFrom) {
                    const fromDate = new Date(dateFrom);
                    if (resultDate < fromDate) dateMatch = false;
                }
                if (dateTo) {
                    const toDate = new Date(dateTo);
                    // Add 1 day to make the end date inclusive
                    toDate.setDate(toDate.getDate() + 1);
                    if (resultDate > toDate) dateMatch = false;
                }
            } catch (e) {
                console.error('Date parsing error:', e);
                dateMatch = true;
            }
        }
        
        // Show or hide row based on all criteria
        if (playerMatch && typeMatch && dateMatch) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Show "no results" message if all rows are hidden
    if (visibleCount === 0 && rows.length > 0) {
        // Check if all rows are actually test result rows (not the empty state message)
        const actualDataRows = Array.from(rows).filter(row => !row.textContent.includes("No test results found"));
        if (actualDataRows.length > 0) {
            let noResultsRow = tableBody?.querySelector(".no-results-row");
            if (!noResultsRow) {
                noResultsRow = document.createElement("tr");
                noResultsRow.className = "no-results-row";
                noResultsRow.innerHTML = '<td colspan="6" style="text-align: center; padding: 20px; color: #999;">No test results match your filters.</td>';
                tableBody?.appendChild(noResultsRow);
            }
            noResultsRow.style.display = '';
        }
    } else if (tableBody?.querySelector(".no-results-row")) {
        tableBody.querySelector(".no-results-row").style.display = 'none';
    }
}

/**
 * Filter match results based on opponent team name and date range
 */
function filterMatchResults() {
    const searchInput = document.getElementById("matchSearch");
    const dateFromInput = document.getElementById("matchDateFrom");
    const dateToInput = document.getElementById("matchDateTo");
    const tableBody = document.getElementById("matchResultsTableBody");
    
    // Get filter values
    const searchQuery = (searchInput?.value || '').toLowerCase().trim();
    const dateFrom = dateFromInput?.value || '';
    const dateTo = dateToInput?.value || '';
    
    console.log('Match filter criteria:', { searchQuery, dateFrom, dateTo });
    
    // Get all rows
    const rows = tableBody?.querySelectorAll(".result-row");
    if (!rows || rows.length === 0) return;
    
    let visibleCount = 0;
    
    rows.forEach(row => {
        const opponentTeam = row.querySelector(".result-name")?.textContent?.trim() || '';
        const dateCell = row.querySelectorAll("td")[6]?.textContent?.trim() || ''; // Date is in the 7th column (0-indexed: 6)
        
        // Check opponent team name match (case insensitive)
        const teamMatch = searchQuery === '' || opponentTeam.toLowerCase().includes(searchQuery);
        
        // Check date range match
        let dateMatch = true;
        if (dateFrom || dateTo) {
            try {
                const resultDate = new Date(dateCell);
                if (dateFrom) {
                    const fromDate = new Date(dateFrom);
                    if (resultDate < fromDate) dateMatch = false;
                }
                if (dateTo) {
                    const toDate = new Date(dateTo);
                    // Add 1 day to make the end date inclusive
                    toDate.setDate(toDate.getDate() + 1);
                    if (resultDate > toDate) dateMatch = false;
                }
            } catch (e) {
                console.error('Date parsing error:', e);
                dateMatch = true;
            }
        }
        
        // Show or hide row based on all criteria
        if (teamMatch && dateMatch) {
            row.style.display = '';
            visibleCount++;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Show "no results" message if all rows are hidden
    if (visibleCount === 0 && rows.length > 0) {
        // Check if all rows are actually match result rows (not the empty state message)
        const actualDataRows = Array.from(rows).filter(row => !row.textContent.includes("No match results found"));
        if (actualDataRows.length > 0) {
            let noResultsRow = tableBody?.querySelector(".no-results-row");
            if (!noResultsRow) {
                noResultsRow = document.createElement("tr");
                noResultsRow.className = "no-results-row";
                noResultsRow.innerHTML = '<td colspan="8" style="text-align: center; padding: 20px; color: #999;">No match results match your filters.</td>';
                tableBody?.appendChild(noResultsRow);
            }
            noResultsRow.style.display = '';
        }
    } else if (tableBody?.querySelector(".no-results-row")) {
        tableBody.querySelector(".no-results-row").style.display = 'none';
    }
}