    loadPlayerData() {
        // Sample player data - you can replace this with API calls
        this.players = [
            // Goalkeepers
            { id: 1, name: 'Leão', position: 'goalkeepers', role: 'Goalkeeper', image: 'https://via.placeholder.com/200x250/667eea/ffffff?text=Player' },
            { id: 2, name: 'Silva', position: 'goalkeepers', role: 'Goalkeeper', image: 'https://via.placeholder.com/200x250/667eea/ffffff?text=Player' },
            { id: 3, name: 'Santos', position: 'goalkeepers', role: 'Goalkeeper', image: 'https://via.placeholder.com/200x250/667eea/ffffff?text=Player' },
            
            // Defenders
            { id: 4, name: 'Costa', position: 'defenders', role: 'Defender', image: 'https://via.placeholder.com/200x250/764ba2/ffffff?text=Player' },
            { id: 5, name: 'Pereira', position: 'defenders', role: 'Defender', image: 'https://via.placeholder.com/200x250/764ba2/ffffff?text=Player' },
            { id: 6, name: 'Oliveira', position: 'defenders', role: 'Defender', image: 'https://via.placeholder.com/200x250/764ba2/ffffff?text=Player' },
            { id: 7, name: 'Rodrigues', position: 'defenders', role: 'Defender', image: 'https://via.placeholder.com/200x250/764ba2/ffffff?text=Player' },
            { id: 8, name: 'Fernandes', position: 'defenders', role: 'Defender', image: 'https://via.placeholder.com/200x250/764ba2/ffffff?text=Player' },
            
            // Midfielders
            { id: 9, name: 'Martins', position: 'midfielders', role: 'Midfielder', image: 'https://via.placeholder.com/200x250/f093fb/ffffff?text=Player' },
            { id: 10, name: 'Alves', position: 'midfielders', role: 'Midfielder', image: 'https://via.placeholder.com/200x250/f093fb/ffffff?text=Player' },
            { id: 11, name: 'Sousa', position: 'midfielders', role: 'Midfielder', image: 'https://via.placeholder.com/200x250/f093fb/ffffff?text=Player' },
            { id: 12, name: 'Cardoso', position: 'midfielders', role: 'Midfielder', image: 'https://via.placeholder.com/200x250/f093fb/ffffff?text=Player' },
            { id: 13, name: 'Ribeiro', position: 'midfielders', role: 'Midfielder', image: 'https://via.placeholder.com/200x250/f093fb/ffffff?text=Player' },
            { id: 14, name: 'Nunes', position: 'midfielders', role: 'Midfielder', image: 'https://via.placeholder.com/200x250/f093fb/ffffff?text=Player' },
            { id: 15, name: 'Mendes', position: 'midfielders', role: 'Midfielder', image: 'https://via.placeholder.com/200x250/f093fb/ffffff?text=Player' },
            
            // Strikers
            { id: 16, name: 'Gonçalves', position: 'strikers', role: 'Striker', image: 'https://via.placeholder.com/200x250/4facfe/ffffff?text=Player' },
            { id: 17, name: 'Lopes', position: 'strikers', role: 'Striker', image: 'https://via.placeholder.com/200x250/4facfe/ffffff?text=Player' },
            { id: 18, name: 'Ferreira', position: 'strikers', role: 'Striker', image: 'https://via.placeholder.com/200x250/4facfe/ffffff?text=Player' },
            { id: 19, name: 'Pinto', position: 'strikers', role: 'Striker', image: 'https://via.placeholder.com/200x250/4facfe/ffffff?text=Player' },
            { id: 20, name: 'Barbosa', position: 'strikers', role: 'Striker', image: 'https://via.placeholder.com/200x250/4facfe/ffffff?text=Player' },
            { id: 21, name: 'Teixeira', position: 'strikers', role: 'Striker', image: 'https://via.placeholder.com/200x250/4facfe/ffffff?text=Player' },
            { id: 22, name: 'Carvalho', position: 'strikers', role: 'Striker', image: 'https://via.placeholder.com/200x250/4facfe/ffffff?text=Player' },
            
            // Coach
            { id: 23, name: 'Coach Silva', position: 'coach', role: 'Coach', image: 'https://via.placeholder.com/200x250/ff9a9e/ffffff?text=Coach' }
        ];

        this.renderPlayers();
    }

    initializePositionTabs() {
        const positionTabs = document.querySelectorAll('.position-tab');
        
        positionTabs.forEach(tab => {
            tab.addEventListener('click', (e) => {
                e.preventDefault();
                
                // Remove active class from all tabs
                positionTabs.forEach(t => t.classList.remove('active'));
                
                // Add active class to clicked tab
                tab.classList.add('active');
                
                // Get the position to filter
                const position = tab.getAttribute('data-position');
                this.currentFilter = position;
                
                // Filter and render players
                this.renderPlayers();
                
                // Smooth scroll to players container
                this.scrollToPlayers();
            });
        });
    }

    renderPlayers() {
        const playersContainer = document.getElementById('players-container');
        
        if (!playersContainer) return;

        // Filter players based on current filter
        const filteredPlayers = this.currentFilter === 'all' 
            ? this.players 
            : this.players.filter(player => player.position === this.currentFilter);

        // Group players by position
        const groupedPlayers = this.groupPlayersByPosition(filteredPlayers);

        // Generate HTML
        let html = '';
        
        for (const [position, players] of Object.entries(groupedPlayers)) {
            if (players.length === 0) continue;

            html += `
                <section class="position-section" data-section="${position}">
                    <h2 class="position-title">${this.getPositionTitle(position)}</h2>
                    <div class="players-grid">
                        ${players.map(player => this.createPlayerCard(player)).join('')}
                    </div>
                </section>
            `;
        }

        playersContainer.innerHTML = html;

        // Reinitialize player cards after rendering
        this.initializePlayerCards();
    }

    groupPlayersByPosition(players) {
        const positions = ['goalkeepers', 'defenders', 'midfielders', 'strikers', 'coach'];
        const grouped = {};

        positions.forEach(position => {
            grouped[position] = players.filter(player => player.position === position);
        });

        return grouped;
    }

    getPositionTitle(position) {
        const titles = {
            'goalkeepers': 'GOALKEEPERS',
            'defenders': 'DEFENDERS',
            'midfielders': 'MIDFIELDERS',
            'strikers': 'STRIKERS',
            'coach': 'COACH'
        };
        return titles[position] || position.toUpperCase();
    }

    createPlayerCard(player) {
        return `
            <div class="player-card" data-player-id="${player.id}">
                <div class="player-image">
                    <img src="${player.image}" alt="${player.name}" class="player-photo" loading="lazy">
                    <div class="player-overlay">
                        <div class="player-number">${player.role}</div>
                        <div class="player-name">${player.name}</div>
                    </div>
                </div>
            </div>
        `;
    }

    initializePlayerCards() {
        const playerCards = document.querySelectorAll('.player-card');
        
        playerCards.forEach(card => {
            // Remove existing event listeners to prevent duplicates
            card.replaceWith(card.cloneNode(true));
        });

        // Re-query after cloning
        const freshPlayerCards = document.querySelectorAll('.player-card');
        
        freshPlayerCards.forEach(card => {
            card.addEventListener('click', (e) => {
                const playerId = card.getAttribute('data-player-id');
                this.handlePlayerClick(playerId);
            });

            card.addEventListener('mouseenter', (e) => {
                this.handlePlayerHover(card, true);
            });

            card.addEventListener('mouseleave', (e) => {
                this.handlePlayerHover(card, false);
            });
        });
    }

    handlePlayerClick(playerId) {
        const player = this.players.find(p => p.id == playerId);
        
        if (player) {
            console.log(`Player clicked: ${player.name} - ${player.role}`);
            this.showPlayerModal(player);
        }
    }

    handlePlayerHover(card, isHovering) {
        if (isHovering) {
            card.style.transform = 'translateY(-8px)';
            card.style.boxShadow = '0 12px 30px rgba(0,0,0,0.2)';
        } else {
            card.style.transform = '';
            card.style.boxShadow = '';
        }
    }

    showPlayerModal(player) {
        // Create modal HTML
        const modalHTML = `
            <div class="player-modal" id="player-modal">
                <div class="modal-overlay"></div>
                <div class="modal-content">
                    <button class="modal-close" id="modal-close">&times;</button>
                    <div class="modal-player-info">
                        <img src="${player.image}" alt="${player.name}" class="modal-player-image">
                        <div class="modal-player-details">
                            <h2>${player.name}</h2>
                            <p class="modal-player-position">${player.role}</p>
                            <div class="modal-player-stats">
                                <div class="stat">
                                    <span class="stat-label">Position:</span>
                                    <span class="stat-value">${player.role}</span>
                                </div>
                                <div class="stat">
                                    <span class="stat-label">Team:</span>
                                    <span class="stat-value">UCC Football</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Add modal CSS
        const modalCSS = `
            <style id="modal-styles">
                .player-modal {
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    z-index: 1000;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }
                .modal-overlay {
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: rgba(0,0,0,0.8);
                    backdrop-filter: blur(5px);
                }
                .modal-content {
                    background: white;
                    border-radius: 15px;
                    padding: 2rem;
                    max-width: 500px;
                    width: 90%;
                    position: relative;
                    transform: scale(0.8);
                    transition: transform 0.3s ease;
                }
                .player-modal.show .modal-content {
                    transform: scale(1);
                }
                .modal-close {
                    position: absolute;
                    top: 15px;
                    right: 15px;
                    background: none;
                    border: none;
                    font-size: 2rem;
                    cursor: pointer;
                    color: #666;
                }
                .modal-player-info {
                    display: flex;
                    gap: 1.5rem;
                    align-items: center;
                }
                .modal-player-image {
                    width: 120px;
                    height: 120px;
                    border-radius: 50%;
                    object-fit: cover;
                }
                .modal-player-details h2 {
                    margin-bottom: 0.5rem;
                    color: #333;
                }
                .modal-player-position {
                    color: #8B4513;
                    font-weight: 600;
                    margin-bottom: 1rem;
                }
                .modal-player-stats {
                    display: flex;
                    flex-direction: column;
                    gap: 0.5rem;
                }
                .stat {
                    display: flex;
                    justify-content: space-between;
                }
                .stat-label {
                    font-weight: 600;
                }
            </style>
        `;

        // Insert modal
        document.head.insertAdjacentHTML('beforeend', modalCSS);
        document.body.insertAdjacentHTML('beforeend', modalHTML);

        // Show modal with animation
        const modal = document.getElementById('player-modal');
        setTimeout(() => modal.classList.add('show'), 10);

        // Add event listeners
        document.getElementById('modal-close').addEventListener('click', this.closePlayerModal);
        modal.querySelector('.modal-overlay').addEventListener('click', this.closePlayerModal);
    }

    closePlayerModal() {
        const modal = document.getElementById('player-modal');
        const modalStyles = document.getElementById('modal-styles');
        
        if (modal) {
            modal.classList.remove('show');
            setTimeout(() => {
                modal.remove();
                if (modalStyles) modalStyles.remove();
            }, 300);
        }
    }

    initializeBackButton() {
        const backBtn = document.getElementById('back-btn');
        
        if (backBtn) {
            backBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.handleBackNavigation();
            });
        }
    }

    handleBackNavigation() {
        // You can customize this based on your navigation needs
        console.log('Back button clicked');
        
        // Example: go back in browser history
        if (window.history.length > 1) {
            window.history.back();
        } else {
            // Or navigate to a specific page
            this.showNotification('No previous page to go back to');
        }
    }

    scrollToPlayers() {
        const playersContainer = document.getElementById('players-container');
        if (playersContainer) {
            playersContainer.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    }

    showNotification(message) {
        const notification = document.createElement('div');
        notification.className = 'body-notification';
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            background: #8B4513;
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            z-index: 1000;
            transform: translateY(100px);
            transition: transform 0.3s ease;
        `;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.transform = 'translateY(0)';
        }, 100);

        setTimeout(() => {
            notification.style.transform = 'translateY(100px)';
            setTimeout(() => {
                if (document.body.contains(notification)) {
                    document.body.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }

    // Public methods
    filterPlayers(position) {
        this.currentFilter = position;
        this.renderPlayers();
        
        // Update active tab
        const tabs = document.querySelectorAll('.position-tab');
        tabs.forEach(tab => {
            tab.classList.remove('active');
            if (tab.getAttribute('data-position') === position) {
                tab.classList.add('active');
            }
        });
    }

    addPlayer(player) {
        this.players.push({ ...player, id: Date.now() });
        this.renderPlayers();
    }

    removePlayer(playerId) {
        this.players = this.players.filter(p => p.id !== playerId);
        this.renderPlayers();
    }
}

// Initialize body component when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.bodyComponent = new BodyComponent();
});

// Export for use in other modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = BodyComponent;
}