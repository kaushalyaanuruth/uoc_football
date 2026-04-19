// Player Performance Dashboard JavaScript

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeFilters();
    initializePlayerComparison();
    initializeNotes();
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
        saveBtn.addEventListener('click', function() {
            const notes = notesTextarea.value;
            if (notes.trim() === '') {
                alert('Please enter some notes before saving.');
                return;
            }
            
            // Here you would typically save to database via AJAX
            console.log('Saving notes:', notes);
            
            // Show success feedback
            const originalText = this.textContent;
            this.textContent = 'Saved!';
            this.style.background = 'linear-gradient(135deg, #10b981 0%, #34d399 100%)';
            
            setTimeout(() => {
                this.textContent = originalText;
                this.style.background = 'linear-gradient(135deg, #7c3aed 0%, #a855f7 100%)';
            }, 2000);
        });
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