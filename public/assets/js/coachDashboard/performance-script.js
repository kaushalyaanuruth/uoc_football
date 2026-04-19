// Player Performance Dashboard JavaScript

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeFilters();
    initializePlayerComparison();
    initializeNotes();
    initializeDetailedMatchBreakdown();
});

// Filter functionality
function initializeFilters() {
    const matchSelect = document.getElementById('matchSelect');
    const playerFilter = document.getElementById('playerFilter');

    if (matchSelect) {
        matchSelect.addEventListener('change', function() {
            console.log('Match changed to:', this.value);
            // Add your filter logic here
            updateDashboard();
        });
    }

    if (playerFilter) {
        playerFilter.addEventListener('change', function() {
            console.log('Player filter changed to:', this.value);
            // Add your filter logic here
            updateDashboard();
        });
    }
}

// Update dashboard based on filters
function updateDashboard() {
    const matchSelect = document.getElementById('matchSelect');
    const playerFilter = document.getElementById('playerFilter');

    const params = new URLSearchParams(window.location.search);
    params.set('match_id', matchSelect ? matchSelect.value : '0');
    params.set('player_id', playerFilter ? playerFilter.value : '0');

    window.location.search = params.toString();
}

// Animate stat cards on update
function animateStats() {
    const statCards = document.querySelectorAll('.stat-card');
    statCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
}

// Player comparison functionality
function initializePlayerComparison() {
    const player1Select = document.getElementById('player1');
    const player2Select = document.getElementById('player2');

    if (!player1Select || !player2Select) {
        return;
    }

    const rows = (window.COACH_PERFORMANCE_DATA && Array.isArray(window.COACH_PERFORMANCE_DATA.comparisonRows))
        ? window.COACH_PERFORMANCE_DATA.comparisonRows
        : [];

    if (rows.length > 0) {
        if (!player1Select.value && rows[0] && rows[0].player_id) {
            player1Select.value = String(rows[0].player_id);
        }
        if (!player2Select.value && rows[1] && rows[1].player_id) {
            player2Select.value = String(rows[1].player_id);
        } else if (!player2Select.value && rows[0] && rows[0].player_id) {
            player2Select.value = String(rows[0].player_id);
        }
    }

    player1Select.addEventListener('change', updateComparison);
    player2Select.addEventListener('change', updateComparison);

    updateComparison();
}

// Update comparison metrics
function updateComparison() {
    const rows = (window.COACH_PERFORMANCE_DATA && Array.isArray(window.COACH_PERFORMANCE_DATA.comparisonRows))
        ? window.COACH_PERFORMANCE_DATA.comparisonRows
        : [];
    const byId = {};
    rows.forEach((row) => {
        byId[String(row.player_id)] = row;
    });

    const player1 = byId[String((document.getElementById('player1') || {}).value || '')] || null;
    const player2 = byId[String((document.getElementById('player2') || {}).value || '')] || null;

    const v = (player, key, suffix = '') => {
        if (!player || player[key] === undefined || player[key] === null || player[key] === '') {
            return '0' + suffix;
        }
        const n = Number(player[key]);
        if (Number.isNaN(n)) {
            return String(player[key]) + suffix;
        }
        return String(Math.round(n)) + suffix;
    };

    const setText = (id, text) => {
        const el = document.getElementById(id);
        if (el) {
            el.textContent = text;
        }
    };

    setText('cmpGoals', v(player1, 'goals') + ' / ' + v(player2, 'goals'));
    setText('cmpAssists', v(player1, 'assists') + ' / ' + v(player2, 'assists'));
    setText('cmpPassAccuracy', v(player1, 'pass_accuracy', '%') + ' / ' + v(player2, 'pass_accuracy', '%'));
    setText('cmpStamina', v(player1, 'stamina', '%') + ' / ' + v(player2, 'stamina', '%'));
    setText('cmpOverall', v(player1, 'overall_rating') + ' / ' + v(player2, 'overall_rating'));
    
    // Animate the comparison cards
    const metricItems = document.querySelectorAll('.metric-item');
    metricItems.forEach((item, index) => {
        item.style.opacity = '0';
        item.style.transform = 'scale(0.9)';
        
        setTimeout(() => {
            item.style.transition = 'all 0.4s ease';
            item.style.opacity = '1';
            item.style.transform = 'scale(1)';
        }, index * 80);
    });
}

// Notes functionality
function initializeNotes() {
    const saveBtn = document.querySelector('.save-btn');
    const notesTextarea = document.querySelector('.notes-textarea');

    if (saveBtn && notesTextarea) {
        saveBtn.addEventListener('click', async function() {
            const notes = notesTextarea.value.trim();
            const config = window.COACH_PERFORMANCE_DATA || {};
            const saveUrl = config.saveNotesUrl || '/coachPerformance/saveNotes';

            const originalText = this.textContent;

            this.disabled = true;
            this.textContent = 'Saving...';

            try {
                const response = await fetch(saveUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ note: notes })
                });

                const result = await response.json();
                if (!response.ok || !result || !result.success) {
                    throw new Error((result && result.message) ? result.message : 'Failed to save notes');
                }

                this.textContent = 'Saved!';
                this.style.background = 'linear-gradient(135deg, #10b981 0%, #34d399 100%)';
            } catch (error) {
                alert(error.message || 'Failed to save notes.');
                this.textContent = 'Save Failed';
                this.style.background = 'linear-gradient(135deg, #ef4444 0%, #f87171 100%)';
            }

            setTimeout(() => {
                this.textContent = originalText;
                this.style.background = '#340134';
                this.disabled = false;
            }, 1600);
        });
    }
}

function initializeDetailedMatchBreakdown() {
    const rows = document.querySelectorAll('.breakdown-row[data-player-id]');
    if (!rows.length) {
        return;
    }

    rows.forEach((row) => {
        row.addEventListener('click', () => {
            const playerId = Number(row.dataset.playerId || 0);
            if (!playerId) {
                return;
            }

            rows.forEach((r) => r.classList.remove('active-row'));
            row.classList.add('active-row');
            fetchAndRenderPlayerMatchStat(playerId);
        });
    });
}

function formatMatchDate(dateText) {
    if (!dateText) {
        return 'N/A';
    }

    const date = new Date(dateText);
    if (Number.isNaN(date.getTime())) {
        return dateText;
    }

    return date.toLocaleDateString(undefined, {
        year: 'numeric',
        month: 'short',
        day: '2-digit'
    });
}

function renderPlayerMatchCard(stat) {
    const section = document.getElementById('playerMatchDetailSection');
    const title = document.getElementById('playerMatchDetailTitle');
    const meta = document.getElementById('playerMatchMeta');
    const grid = document.getElementById('playerMatchGrid');
    const notes = document.getElementById('playerMatchNotes');

    if (!section || !title || !meta || !grid || !notes) {
        return;
    }

    section.style.display = '';
    title.textContent = (stat.player_name || 'Player') + ' - Match Detail';
    meta.textContent = (stat.opponent_team || 'Opponent') + ' | ' + formatMatchDate(stat.match_date) + ' | Result: ' + (stat.match_result || 'N/A');

    const shotsTotal = Number(stat.shots_on_target || 0) + Number(stat.shots_off_target || 0);
    const items = [
        ['Minutes Played', stat.minutes_played || 0],
        ['Goals', stat.goals_scored || 0],
        ['Assists', stat.assists || 0],
        ['Completed Passes', stat.completed_passes || 0],
        ['Shots on Target', stat.shots_on_target || 0],
        ['Shots off Target', stat.shots_off_target || 0],
        ['Total Shots', shotsTotal],
        ['Tackles Won', stat.tackles_won || 0],
        ['Interceptions', stat.interceptions || 0],
        ['Fouls Committed', stat.fouls_committed || 0]
    ];

    grid.innerHTML = items.map(([label, value]) => {
        return '<div class="player-match-item">' +
            '<div class="player-match-item-label">' + label + '</div>' +
            '<div class="player-match-item-value">' + String(value) + '</div>' +
            '</div>';
    }).join('');

    const text = String(stat.notes || '').trim();
    if (text !== '') {
        notes.style.display = '';
        notes.textContent = 'Coach/Match Notes: ' + text;
    } else {
        notes.style.display = 'none';
        notes.textContent = '';
    }
}

async function fetchAndRenderPlayerMatchStat(playerId) {
    const config = window.COACH_PERFORMANCE_DATA || {};
    const endpoint = config.playerMatchStatUrl || '/coachPerformance/playerMatchStat';
    const selectedMatchId = Number(config.selected_match_id || 0);

    const params = new URLSearchParams();
    params.set('player_id', String(playerId));
    if (selectedMatchId > 0) {
        params.set('match_id', String(selectedMatchId));
    }

    try {
        const response = await fetch(endpoint + '?' + params.toString(), {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        });

        const result = await response.json();
        if (!response.ok || !result || !result.success || !result.stat) {
            throw new Error((result && result.message) ? result.message : 'No detailed match stats found for this player.');
        }

        renderPlayerMatchCard(result.stat);
    } catch (error) {
        alert(error.message || 'Failed to load match details for this player.');
    }
}

// Add smooth scroll to sections
function smoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

// Initialize smooth scroll
smoothScroll();

// Console log for debugging
console.log('Player Performance Dashboard initialized successfully');