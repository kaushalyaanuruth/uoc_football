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
                populateEditMatchResultModal(data.result);
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