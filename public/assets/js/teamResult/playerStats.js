/**
 * Player Match Statistics - Inline Form Management
 * Similar to how players are added when creating teams
 */

let playerStatsArray = [];
let currentMatchId = null;

/**
 * Open add player stats modal
 */
function openAddPlayerStatsModal() {
    const modal = document.getElementById("addPlayerStatsModal");
    const form = document.getElementById("addPlayerStatsForm");
    
    if (form) {
        form.reset();
    }
    
    // Load team players for dropdown
    loadTeamPlayersForDropdown();
    
    // Setup position field listener
    setupPlayerSelectionListener();
    
    if (modal) {
        modal.classList.add("active");
    }
}

/**
 * Close add player stats modal
 */
function closeAddPlayerStatsModal() {
    const modal = document.getElementById("addPlayerStatsModal");
    const form = document.getElementById("addPlayerStatsForm");
    
    if (modal) {
        modal.classList.remove("active");
    }
    
    if (form) {
        form.reset();
    }
}

/**
 * Load team players for dropdown
 */
function loadTeamPlayersForDropdown() {
    const baseUrl = window.ROOT || window.location.origin;
    const playerSelect = document.getElementById("playerSelect");
    
    if (!playerSelect) return;
    
    // Fetch team players
    fetch(`${baseUrl}/teamResult/getTeamMembers`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.members) {
                playerSelect.innerHTML = '<option value="">Select Player</option>';
                
                // Get already added player IDs
                const addedPlayerIds = playerStatsArray.map(p => p.player_id);
                
                data.members.forEach(player => {
                    const option = document.createElement('option');
                    option.value = player.player_id;
                    option.textContent = player.full_name || player.first_name + ' ' + player.last_name;
                    option.dataset.position = player.position || '';
                    
                    // Disable if already added
                    if (addedPlayerIds.includes(parseInt(player.player_id))) {
                        option.disabled = true;
                        option.textContent += ' (✓ Added)';
                    }
                    
                    playerSelect.appendChild(option);
                });
            }
        })
        .catch(error => console.error('Error loading players:', error));
}

/**
 * Add player stats to the form array
 */
function addPlayerStatsToForm(event) {
    event.preventDefault();
    
    const form = document.getElementById("addPlayerStatsForm");
    const playerSelect = document.getElementById("playerSelect");
    const playerName = playerSelect.options[playerSelect.selectedIndex].text.split(' (')[0];
    
    const stats = {
        player_id: parseInt(playerSelect.value),
        position_played: document.getElementById("positionPlayed").value.trim(),
        minutes_played: parseInt(document.getElementById("minutesPlayed").value) || 90,
        substitution_status: document.getElementById("substitutionStatus").value,
        goals_scored: parseInt(document.getElementById("goalsScored").value) || 0,
        assists: parseInt(document.getElementById("assists").value) || 0,
        shots_on_target: parseInt(document.getElementById("shotsOnTarget").value) || 0,
        shots_off_target: parseInt(document.getElementById("shotsOffTarget").value) || 0,
        key_passes: parseInt(document.getElementById("keyPasses").value) || 0,
        successful_dribbles: parseInt(document.getElementById("successfulDribbles").value) || 0,
        completed_passes: parseInt(document.getElementById("completedPasses").value) || 0,
        line_breaking_passes: parseInt(document.getElementById("lineBreakingPasses").value) || 0,
        tackles_won: parseInt(document.getElementById("tacklesWon").value) || 0,
        interceptions: parseInt(document.getElementById("interceptions").value) || 0,
        defensive_duels_won: parseInt(document.getElementById("defensiveDuelsWon").value) || 0,
        aerial_duels_won: parseInt(document.getElementById("aerialDuelsWon").value) || 0,
        yellow_cards: parseInt(document.getElementById("yellowCards").value) || 0,
        red_cards: parseInt(document.getElementById("redCards").value) || 0,
        fouls_committed: parseInt(document.getElementById("foulsCommitted").value) || 0,
        fouls_won: parseInt(document.getElementById("foulsWon").value) || 0,
        notes: document.getElementById("notes").value.trim()
    };
    
    // Store player name for display (not sent to backend)
    stats._playerName = playerName;
    
    // Validate
    if (!stats.player_id) {
        alert('Please select a player');
        return;
    }
    
    // Check for duplicate
    if (playerStatsArray.some(p => p.player_id === stats.player_id)) {
        alert('This player already has stats added');
        return;
    }
    
    // Add to array
    playerStatsArray.push(stats);
    
    // Update display
    displayPlayerStats();
    closeAddPlayerStatsModal();
}

/**
 * Display player stats in container
 */
function displayPlayerStats() {
    const container = document.getElementById("matchPlayerStatsContainer");
    container.innerHTML = '';
    
    if (playerStatsArray.length === 0) {
        container.innerHTML = '<p style="color: #999; text-align: center; padding: 15px;">No players added yet</p>';
        return;
    }
    
    playerStatsArray.forEach((stats, index) => {
        const playerDiv = document.createElement('div');
        playerDiv.className = 'input-item';
        playerDiv.style.display = 'flex';
        playerDiv.style.justifyContent = 'space-between';
        playerDiv.style.alignItems = 'center';
        playerDiv.style.padding = '12px';
        playerDiv.style.backgroundColor = '#f5f5f5';
        playerDiv.style.borderRadius = '4px';
        playerDiv.style.marginBottom = '8px';
        playerDiv.style.borderLeft = '4px solid #7c3aed';
        
        const statsText = `
            Goals: <strong>${stats.goals_scored}</strong> | 
            Assists: <strong>${stats.assists}</strong> | 
            Minutes: <strong>${stats.minutes_played}'</strong> | 
            Status: <strong>${stats.substitution_status}</strong>
        `;
        
        playerDiv.innerHTML = `
            <div style="flex: 1;">
                <strong style="color: #333; font-size: 14px;">${stats._playerName}</strong><br>
                <small style="color: #666; font-size: 12px;">Position: ${stats.position_played || 'N/A'}</small><br>
                <small style="color: #666; font-size: 11px;">${statsText}</small>
            </div>
            <button type="button" class="delete-input-btn" onclick="removePlayerStats(${index})" style="margin-left: 10px;">&times;</button>
        `;
        
        container.appendChild(playerDiv);
    });
}

/**
 * Remove player from stats array
 */
function removePlayerStats(index) {
    playerStatsArray.splice(index, 1);
    displayPlayerStats();
}

/**
 * Reset player stats array (called when closing add match modal)
 */
function resetPlayerStatsArray() {
    playerStatsArray = [];
}

/**
 * Get player stats array for form submission
 */
function getPlayerStatsArray() {
    return playerStatsArray;
}

/**
 * Update position field when player is selected
 */
function setupPlayerSelectionListener() {
    const playerSelect = document.getElementById("playerSelect");
    const positionField = document.getElementById("positionPlayed");
    
    if (playerSelect && positionField) {
        playerSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            if (selectedOption.dataset.position) {
                positionField.value = selectedOption.dataset.position;
            } else {
                positionField.value = '';
            }
        });
    }
}

// Setup listener when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', setupPlayerSelectionListener);
} else {
    setupPlayerSelectionListener();
}
