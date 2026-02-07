// Initialize arrays to track players and coaches
let playersArray = [];
let coachesArray = [];

function openAddTeamModal(){
    document.querySelector('#addTeamModal').classList.add('active');
}
function closeAddTeamModal(){
    document.querySelector('#addTeamModal').classList.remove('active');
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
    if (playersArray.length >= 30) {
    alert('Maximum of 30 players can be added to a team.');
    return;
    }
    document.querySelector('#addPlayersModal').classList.add('active');
}

function openAddCoachModal() {
    if (coachesArray.length >= 5) {
        alert('Maximum of 5 coaches can be added to a team.');
        return;
    }
    document.querySelector('#addCoachModal').classList.add('active');
}

function closeAddPlayerModal() {
    document.querySelector('#addPlayersModal').classList.remove('active');
    document.querySelector('#addPlayerForm').reset();
}

function closeAddCoachModal() {
    document.querySelector('#addCoachModal').classList.remove('active');
    document.querySelector('#addCoachForm').reset();
}

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
});