// Initialize arrays to track players and coaches
let playersArray = [];
let coachesArray = [];

function openAddTeamModal(){
    document.querySelector('#addTeamModal').classList.add('active');
}
function closeAddTeamModal(){
    document.querySelector('#addTeamModal').classList.remove('active');
    document.querySelector('#addTeamForm').reset();
    document.querySelector('#addCoachForm').reset();
    document.querySelector('#addPlayerForm').reset();
    playersArray = [];
    coachesArray = [];
}
function toggleSection(header) {
    const section = header.closest('.form-section-wrapper');
    section.classList.toggle('collapsed');
}
function addInputField(containerId, inputName, placeholder) {
            const container = document.getElementById(containerId);
            const inputItem = document.createElement('div');
            inputItem.className = 'input-item';
            inputItem.innerHTML = `
                <input type="text" name="${inputName}" class="form-input" placeholder="${placeholder}">
                <button type="button" class="delete-input-btn" onclick="removeInputField(this)">&times;</button>
            `;
            container.appendChild(inputItem);
        }

function removeInputField(button) {
    const inputItem = button.closest('.input-item');
    const container = inputItem.parentElement;
    // Only remove if there's more than one input
    if (container.children.length > 1) {
        inputItem.remove();
    }
}

function openAddPlayerModal() {
    // Check if adding to existing team (from view modal) - no limit
    const teamId = document.getElementById('viewTeamId').value;
    if (!teamId && playersArray.length >= 30) {
        alert('Maximum of 30 players can be added to a team.');
        return;
    }
    document.querySelector('#addPlayersModal').classList.add('active');
    // Clear form fields
    document.getElementById('addPlayerForm').reset();
    document.getElementById('player_first_name').focus();
}

function closeAddPlayerModal() {
    document.querySelector('#addPlayersModal').classList.remove('active');
    document.querySelector('#addPlayerForm').reset();
    document.getElementById('player_first_name').value = '';
    document.getElementById('player_last_name').value = '';
    document.getElementById('player_nic').value = '';
    document.getElementById('player_email').value = '';
    document.getElementById('player_phone_number').value = '';
    document.getElementById('player_position').value = '';
    document.getElementById('player_role').value = '';
}

function addPlayerToForm(e) {
    e.preventDefault();

    const firstName = document.getElementById('player_first_name').value.trim();
    const lastName = document.getElementById('player_last_name').value.trim();
    const nic = document.getElementById('player_nic').value.trim();
    const email = document.getElementById('player_email').value.trim();
    const phoneNumber = document.getElementById('player_phone_number').value.trim();
    const position = document.getElementById('player_position').value.trim();
    const role = document.getElementById('player_role').value.trim();
    
    if (!firstName || !lastName || !nic || !email || !phoneNumber || !position || !role) {
        alert('Please fill in all required fields');
        return;
    }

    if (playersArray.some(p => p.nic === nic)) {
        alert('A player with this NIC already exists');
        return;
    }
    
    const playerData = {
        first_name: firstName,
        last_name: lastName,
        nic: nic,
        email: email,
        phone_number: phoneNumber,
        position: position,
        role: role,
        image: document.getElementById('player_image').files[0] || null
    };
    
    // Check if we're adding to an existing team (view modal) or new team (add modal)
    const teamId = document.getElementById('viewTeamId').value;
    if (teamId) {
        // Adding to existing team
        const formData = new FormData();
        formData.append('team_id', teamId);
        formData.append('first_name', playerData.first_name);
        formData.append('last_name', playerData.last_name);
        formData.append('nic', playerData.nic);
        formData.append('email', playerData.email);
        formData.append('phone_number', playerData.phone_number);
        formData.append('position', playerData.position);
        formData.append('role', playerData.role);
        
        fetch(ROOT + '/teamManagement/addPlayerToTeam', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Player added successfully!');
                closeAddPlayerModal();
                // Reload the team details
                const teamId = document.getElementById('viewTeamId').value;
                fetchAndShowTeamDetails(teamId);
            } else {
                alert('Error: ' + (data.message || 'Failed to add player'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while adding the player');
        });
    } else {
        // Adding to new team (store in array)
        playersArray.push(playerData);
        displayPlayers();
        closeAddPlayerModal();
    }
}

function addCoachToForm(e) {
    e.preventDefault();
    
    const firstName = document.getElementById('coach_first_name').value.trim();
    const lastName = document.getElementById('coach_last_name').value.trim();
    const nic = document.getElementById('coach_nic').value.trim();
    const email = document.getElementById('coach_email').value.trim();
    const phoneNumber = document.getElementById('coach_phone_number').value.trim();
    const license = document.getElementById('coach_license').value.trim();
    
    if (!firstName || !lastName || !nic || !email || !phoneNumber || !license) {
        alert('Please fill in all required fields');
        return;
    }
    
    if (coachesArray.some(c => c.nic === nic)) {
        alert('A coach with this NIC already exists');
        return;
    }
    
    const coachData = {
        first_name: firstName,
        last_name: lastName,
        nic: nic,
        email: email,
        phone_number: phoneNumber,
        license: license,
        image: document.getElementById('coach_image').files[0] || null
    };
    
    // Check if we're adding to an existing team (view modal) or new team (add modal)
    const teamId = document.getElementById('viewTeamId').value;
    if (teamId) {
        // Adding to existing team
        const formData = new FormData();
        formData.append('team_id', teamId);
        formData.append('first_name', coachData.first_name);
        formData.append('last_name', coachData.last_name);
        formData.append('nic', coachData.nic);
        formData.append('email', coachData.email);
        formData.append('phone_number', coachData.phone_number);
        formData.append('license', coachData.license);
        
        fetch(ROOT + '/teamManagement/addCoachToTeam', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Coach added successfully!');
                closeAddCoachModal();
                // Reload the team details
                const teamId = document.getElementById('viewTeamId').value;
                fetchAndShowTeamDetails(teamId);
            } else {
                alert('Error: ' + (data.message || 'Failed to add coach'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while adding the coach');
        });
    } else {
        // Adding to new team (store in array)
        coachesArray.push(coachData);
        displayCoaches();
        closeAddCoachModal();
    }
}

function closeAddCoachModal() {
    document.querySelector('#addCoachModal').classList.remove('active');
    document.querySelector('#addCoachForm').reset();
    document.getElementById('coach_first_name').value = '';
    document.getElementById('coach_last_name').value = '';
    document.getElementById('coach_nic').value = '';
    document.getElementById('coach_email').value = '';
    document.getElementById('coach_phone_number').value = '';
    document.getElementById('coach_license').value = '';
}

function openAddCoachModal() {
    // Check if adding to existing team (from view modal) - no limit
    const teamId = document.getElementById('viewTeamId').value;
    if (!teamId && coachesArray.length >= 5) {
        alert('Maximum of 5 coaches can be added to a team.');
        return;
    }
    document.querySelector('#addCoachModal').classList.add('active');
    // Clear form fields
    document.getElementById('addCoachForm').reset();
    document.getElementById('coach_first_name').focus();
}

function openViewTeamModal() {
    alert('Opening team details...');
}

function closeViewTeamModal() {
    document.querySelector('#viewTeamModal').classList.remove('active');
}

function displayPlayers() {
    const container = document.getElementById('players-container');
    container.innerHTML = '';
    
    playersArray.forEach((player, index) => {
        const playerDiv = document.createElement('div');
        playerDiv.className = 'input-item';
        playerDiv.style.display = 'flex';
        playerDiv.style.justifyContent = 'space-between';
        playerDiv.style.alignItems = 'center';
        playerDiv.style.padding = '10px';
        playerDiv.style.backgroundColor = '#f5f5f5';
        playerDiv.style.borderRadius = '4px';
        playerDiv.style.marginBottom = '8px';
        
        playerDiv.innerHTML = `
            <div style="flex: 1;">
                <strong>${player.first_name} ${player.last_name}</strong><br>
                <small style="color: #666;">NIC: ${player.nic} | Position: ${player.position} | Role: ${player.role}</small>
            </div>
            <button type="button" class="delete-input-btn" onclick="removePlayer(${index})" style="margin-left: 10px;">&times;</button>
        `;
        
        container.appendChild(playerDiv);
    });
}

function displayCoaches() {
    const container = document.getElementById('coaches-container');
    container.innerHTML = '';
    
    coachesArray.forEach((coach, index) => {
        const coachDiv = document.createElement('div');
        coachDiv.className = 'input-item';
        coachDiv.style.display = 'flex';
        coachDiv.style.justifyContent = 'space-between';
        coachDiv.style.alignItems = 'center';
        coachDiv.style.padding = '10px';
        coachDiv.style.backgroundColor = '#f5f5f5';
        coachDiv.style.borderRadius = '4px';
        coachDiv.style.marginBottom = '8px';
        
        coachDiv.innerHTML = `
            <div style="flex: 1;">
                <strong>${coach.first_name} ${coach.last_name}</strong><br>
                <small style="color: #666;">NIC: ${coach.nic} | License: ${coach.license}</small>
            </div>
            <button type="button" class="delete-input-btn" onclick="removeCoach(${index})" style="margin-left: 10px;">&times;</button>
        `;
        
        container.appendChild(coachDiv);
    });
}

function removePlayer(index) {
    playersArray.splice(index, 1);
    displayPlayers();
}

function removeCoach(index) {
    coachesArray.splice(index, 1);
    displayCoaches();
}

// Handle Add Team Form Submission
document.addEventListener('DOMContentLoaded', function() {
    const roleBadges = document.querySelectorAll('.role-badge');
    
    roleBadges.forEach(badge => {
        badge.addEventListener('click', function() {
            const radio = this.querySelector('input[type="radio"]');
            const radioName = radio.name;
            
            document.querySelectorAll(`input[name="${radioName}"]`).forEach(r => {
                r.closest('.role-badge').classList.remove('active');
            });
            
            this.classList.add('active');
            radio.checked = true;
        });
    });

    // Handle Add Team Form Submission
    const addTeamForm = document.getElementById('addTeamForm');
    if (addTeamForm) {
        addTeamForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            
            // Add players and coaches data
            playersArray.forEach(player => {
                formData.append('players[]', JSON.stringify(player));
            });
            
            coachesArray.forEach(coach => {
                formData.append('coaches[]', JSON.stringify(coach));
            });

            // Submit form via AJAX
            fetch(this.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Team created successfully!');
                    closeAddTeamModal();
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to create team'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while creating the team');
            });
        });
    }

    // Handle View Team Form Submission (update)
    const viewTeamForm = document.getElementById('viewTeamForm');
    if (viewTeamForm) {
        viewTeamForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);

            // Submit form via AJAX
            fetch(this.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Team updated successfully!');
                    closeViewTeamModal();
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to update team'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the team');
            });
        });
    }
});


function editTeam(teamId) {
    fetchAndShowTeamDetails(teamId);
}

function deleteTeam(teamId) {
    if (confirm('Are you sure you want to delete this team? This cannot be undone.')) {
        fetch(ROOT + '/teamManagement/delete', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ team_id: teamId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Team deleted successfully!');
                location.reload();
            } else {
                alert('Error: ' + (data.message || 'Failed to delete team'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting the team');
        });
    }
}

function deletePlayerFromTeam(playerId, button) {
    if (confirm('Are you sure you want to remove this player?')) {
        fetch(ROOT + '/teamManagement/deletePlayer', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: playerId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                button.closest('div').remove();
                alert('Player removed successfully!');
            } else {
                alert('Error: ' + (data.message || 'Failed to remove player'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while removing the player');
        });
    }
}

function deleteCoachFromTeam(coachId, button) {
    if (confirm('Are you sure you want to remove this coach?')) {
        fetch(ROOT + '/teamManagement/deleteCoach', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ id: coachId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                button.closest('div').remove();
                alert('Coach removed successfully!');
            } else {
                alert('Error: ' + (data.message || 'Failed to remove coach'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while removing the coach');
        });
    }
}

function editPlayer(playerId) {
    // Store player ID for later use
    document.getElementById('editPlayerId').value = playerId;
    
    // Fetch player details
    fetch(ROOT + '/teamManagement/getPlayerData?id=' + playerId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('editPlayer_first_name').value = data.player.first_name || '';
                document.getElementById('editPlayer_last_name').value = data.player.last_name || '';
                document.getElementById('editPlayer_nic').value = data.player.nic || '';
                document.getElementById('editPlayer_email').value = data.player.email || '';
                document.getElementById('editPlayer_phone_number').value = data.player.phone_number || '';
                document.getElementById('editPlayer_position').value = data.player.position || '';
                document.getElementById('editPlayer_role').value = data.player.role || '';
                
                // Close view modal and open edit modal
                document.querySelector('#viewTeamModal').classList.remove('active');
                document.querySelector('#editPlayerModal').classList.add('active');
            } else {
                alert('Error: ' + (data.message || 'Failed to load player data'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while loading player data');
        });
}

function editCoach(coachId) {
    // Store coach ID for later use
    document.getElementById('editCoachId').value = coachId;
    
    // Fetch coach details
    fetch(ROOT + '/teamManagement/getCoachData?id=' + coachId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('editCoach_first_name').value = data.coach.first_name || '';
                document.getElementById('editCoach_last_name').value = data.coach.last_name || '';
                document.getElementById('editCoach_nic').value = data.coach.nic || '';
                document.getElementById('editCoach_email').value = data.coach.email || '';
                document.getElementById('editCoach_phone_number').value = data.coach.phone_number || '';
                document.getElementById('editCoach_license').value = data.coach.license || '';
                
                // Close view modal and open edit modal
                document.querySelector('#viewTeamModal').classList.remove('active');
                document.querySelector('#editCoachModal').classList.add('active');
            } else {
                alert('Error: ' + (data.message || 'Failed to load coach data'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while loading coach data');
        });
}

function openEditPlayerModal() {
    document.querySelector('#editPlayerModal').classList.add('active');
}

function closeEditPlayerModal() {
    document.querySelector('#editPlayerModal').classList.remove('active');
    document.querySelector('#editPlayerForm').reset();
}

function openEditCoachModal() {
    document.querySelector('#editCoachModal').classList.add('active');
}

function closeEditCoachModal() {
    document.querySelector('#editCoachModal').classList.remove('active');
    document.querySelector('#editCoachForm').reset();
}

function submitEditPlayerForm(e) {
    e.preventDefault();
    
    const playerId = document.getElementById('editPlayerId').value;
    const formData = new FormData();
    formData.append('id', playerId);
    formData.append('first_name', document.getElementById('editPlayer_first_name').value);
    formData.append('last_name', document.getElementById('editPlayer_last_name').value);
    formData.append('nic', document.getElementById('editPlayer_nic').value);
    formData.append('email', document.getElementById('editPlayer_email').value);
    formData.append('phone_number', document.getElementById('editPlayer_phone_number').value);
    formData.append('position', document.getElementById('editPlayer_position').value);
    formData.append('role', document.getElementById('editPlayer_role').value);
    
    fetch(ROOT + '/teamManagement/updatePlayer', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Player updated successfully!');
            closeEditPlayerModal();
            const teamId = document.getElementById('viewTeamId').value || document.querySelector('[name="team_id"]').value;
            if (teamId) {
                fetchAndShowTeamDetails(teamId);
            }
        } else {
            alert('Error: ' + (data.message || 'Failed to update player'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the player');
    });
}

function submitEditCoachForm(e) {
    e.preventDefault();
    
    const coachId = document.getElementById('editCoachId').value;
    const formData = new FormData();
    formData.append('id', coachId);
    formData.append('first_name', document.getElementById('editCoach_first_name').value);
    formData.append('last_name', document.getElementById('editCoach_last_name').value);
    formData.append('nic', document.getElementById('editCoach_nic').value);
    formData.append('email', document.getElementById('editCoach_email').value);
    formData.append('phone_number', document.getElementById('editCoach_phone_number').value);
    formData.append('license', document.getElementById('editCoach_license').value);
    
    fetch(ROOT + '/teamManagement/updateCoach', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Coach updated successfully!');
            closeEditCoachModal();
            const teamId = document.getElementById('viewTeamId').value || document.querySelector('[name="team_id"]').value;
            if (teamId) {
                fetchAndShowTeamDetails(teamId);
            }
        } else {
            alert('Error: ' + (data.message || 'Failed to update coach'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the coach');
    });
}


function fetchAndShowTeamDetails(teamId) {
    fetch(ROOT + '/teamManagement/getTeamData?id=' + teamId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateViewTeamModal(data.team, data.tournaments, data.achievements, data.players, data.coaches);
                document.querySelector('#viewTeamModal').classList.add('active');
            } else {
                alert('Error: ' + (data.message || 'Failed to load team data'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while loading team data');
        });
}

function populateViewTeamModal(team, tournaments, achievements, players, coaches) {
    // Set hidden team ID
    document.getElementById('viewTeamId').value = team.team_id;
    
    // Set basic info
    document.getElementById('viewTeamSeason').value = team.season;
    
    // Set team status
    const statusRadios = document.querySelectorAll('#viewTeamForm input[name="status"]');
    statusRadios.forEach(radio => {
        radio.checked = (radio.value === team.status);
        if (radio.checked) {
            radio.closest('.role-badge').classList.add('active');
        } else {
            radio.closest('.role-badge').classList.remove('active');
        }
    });
    
    // Clear and populate tournaments
    const tournamentsContainer = document.getElementById('view-tournaments-container');
    tournamentsContainer.innerHTML = '';
    if (tournaments && tournaments.length > 0) {
        tournaments.forEach(tournament => {
            const item = document.createElement('div');
            item.className = 'input-item';
            item.innerHTML = `
                <input type="text" name="name[]" value="${tournament.name}" class="form-input" placeholder="Add tournament">
                <button type="button" class="delete-input-btn" onclick="removeInputField(this)">&times;</button>
            `;
            tournamentsContainer.appendChild(item);
        });
    }
    // Add at least one empty field
    if (tournaments.length === 0) {
        const item = document.createElement('div');
        item.className = 'input-item';
        item.innerHTML = `
            <input type="text" name="name[]" class="form-input" placeholder="Add tournament">
            <button type="button" class="delete-input-btn" onclick="removeInputField(this)">&times;</button>
        `;
        tournamentsContainer.appendChild(item);
    }
    
    // Clear and populate achievements
    const achievementsContainer = document.getElementById('view-achievements-container');
    achievementsContainer.innerHTML = '';
    if (achievements && achievements.length > 0) {
        achievements.forEach(achievement => {
            const item = document.createElement('div');
            item.className = 'input-item';
            item.innerHTML = `
                <input type="text" name="achievement[]" value="${achievement.achievement}" class="form-input" placeholder="Add achievement">
                <button type="button" class="delete-input-btn" onclick="removeInputField(this)">&times;</button>
            `;
            achievementsContainer.appendChild(item);
        });
    }
    // Add at least one empty field
    if (achievements.length === 0) {
        const item = document.createElement('div');
        item.className = 'input-item';
        item.innerHTML = `
            <input type="text" name="achievement[]" class="form-input" placeholder="Add achievement">
            <button type="button" class="delete-input-btn" onclick="removeInputField(this)">&times;</button>
        `;
        achievementsContainer.appendChild(item);
    }
    
    // Display players (with edit and delete options)
    const playersContainer = document.getElementById('view-players-container');
    playersContainer.innerHTML = '';
    if (players && players.length > 0) {
        players.forEach(player => {
            const playerDiv = document.createElement('div');
            playerDiv.style.cssText = 'padding: 10px; background: #f5f5f5; margin-bottom: 8px; border-radius: 4px; display: flex; justify-content: space-between; align-items: center;';
            const playerName = player.first_name && player.last_name ? `${player.first_name} ${player.last_name}` : 'Unknown';
            playerDiv.innerHTML = `
                <div>
                    <strong>${playerName}</strong> (${player.role || 'N/A'}) - ${player.position || 'N/A'}
                    <br><small>NIC: ${player.nic || 'N/A'}</small>
                </div>
                <div style="display: flex; gap: 5px;">
                    <button type="button" class="delete-input-btn" onclick="editPlayer(${player.player_id})" title="Edit player" style="background: #4CAF50; color: white;">✎</button>
                    <button type="button" class="delete-input-btn" onclick="deletePlayerFromTeam(${player.player_id}, this)" title="Remove player">&times;</button>
                </div>
            `;
            playersContainer.appendChild(playerDiv);
        });
    } else {
        playersContainer.innerHTML = '<p style="color: #999;">No players added yet</p>';
    }
    
    // Display coaches (with edit and delete options)
    const coachesContainer = document.getElementById('view-coaches-container');
    coachesContainer.innerHTML = '';
    if (coaches && coaches.length > 0) {
        coaches.forEach(coach => {
            const coachDiv = document.createElement('div');
            coachDiv.style.cssText = 'padding: 10px; background: #f5f5f5; margin-bottom: 8px; border-radius: 4px; display: flex; justify-content: space-between; align-items: center;';
            const coachName = coach.first_name && coach.last_name ? `${coach.first_name} ${coach.last_name}` : 'Unknown';
            coachDiv.innerHTML = `
                <div>
                    <strong>${coachName}</strong>
                    <br><small>NIC: ${coach.nic || 'N/A'} | Phone: ${coach.phone_number || 'N/A'}</small>
                </div>
                <div style="display: flex; gap: 5px;">
                    <button type="button" class="delete-input-btn" onclick="editCoach(${coach.coach_id})" title="Edit coach" style="background: #4CAF50; color: white;">✎</button>
                    <button type="button" class="delete-input-btn" onclick="deleteCoachFromTeam(${coach.coach_id}, this)" title="Remove coach">&times;</button>
                </div>
            `;
            coachesContainer.appendChild(coachDiv);
        });
    } else {
        coachesContainer.innerHTML = '<p style="color: #999;">No coaches added yet</p>';
    }
}

function addViewInputField(containerId, inputName, placeholder) {
    const container = document.getElementById(containerId);
    const inputItem = document.createElement('div');
    inputItem.className = 'input-item';
    inputItem.innerHTML = `
        <input type="text" name="${inputName}" class="form-input" placeholder="${placeholder}">
        <button type="button" class="delete-input-btn" onclick="removeInputField(this)">&times;</button>
    `;
    container.appendChild(inputItem);
}