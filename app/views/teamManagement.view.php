<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <title>Team Management - UOC Football</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/adminDashboard/style.css">
    <style>
        * {
            font-family: 'Poppins', sans-serif;
        }
        
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('<?php echo ROOT; ?>/assets/images/common/bgimage.png');
            background-size: 1298px 1298px;
            background-position: -517px -125px;
            background-repeat: no-repeat;
            opacity: 0.1;
            z-index: 0;
            pointer-events: none;
        }
        
        .container {
            position: relative;
            z-index: 1;
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .page-title {
            font-family: 'Poppins', sans-serif;
            font-size: 2rem;
            font-weight: 700;
            color: #333;
            margin: 0 0 30px 0;
        }

        .actions-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .btn-add {
            background: linear-gradient(135deg, #663399 0%, #9966cc 100%);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 51, 153, 0.3);
        }

        .players-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .player-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }

        .player-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 20px rgba(102, 51, 153, 0.15);
        }

        .player-image {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, #ffe6ff 0%, #FFFAFF 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            margin-bottom: 15px;
            color: #999;
        }

        .player-info {
            margin-bottom: 15px;
        }

        .player-name {
            font-size: 1.1rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 5px;
        }

        .player-role {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 8px;
        }

        .player-details {
            font-size: 0.85rem;
            color: #999;
            line-height: 1.6;
        }

        .player-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
            border-top: 1px solid #f0f0f0;
            padding-top: 15px;
        }

        .btn-edit, .btn-delete {
            flex: 1;
            padding: 8px 12px;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }

        .btn-edit {
            background: #663399;
            color: white;
        }

        .btn-edit:hover {
            background: #4C0E54;
        }

        .btn-delete {
            background: #ff6b6b;
            color: white;
        }

        .btn-delete:hover {
            background: #ee5a52;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        .empty-state-icon {
            font-size: 4rem;
            margin-bottom: 20px;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 100;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal.active {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background: white;
            border-radius: 12px;
            padding: 30px;
            max-width: 600px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                transform: translateY(-50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .modal-header {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            color: #333;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 5px;
            color: #333;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
            box-sizing: border-box;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #663399;
            box-shadow: 0 0 0 3px rgba(102, 51, 153, 0.1);
        }

        .form-actions {
            display: flex;
            gap: 10px;
            margin-top: 25px;
        }

        .btn-cancel {
            flex: 1;
            padding: 12px;
            background: #f0f0f0;
            color: #333;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-cancel:hover {
            background: #e0e0e0;
        }

        .btn-submit {
            flex: 1;
            padding: 12px;
            background: #663399;
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            background: #4C0E54;
        }

        .image-preview {
            width: 100%;
            max-height: 200px;
            border-radius: 6px;
            margin-bottom: 10px;
            display: none;
            object-fit: cover;
        }

        .image-preview.show {
            display: block;
        }

        .badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 0.85rem;
            font-weight: 600;
            margin-top: 5px;
        }

        .badge-captain {
            background: #ffe6cc;
            color: #cc8000;
        }

        .badge-vice-captain {
            background: #e6f2ff;
            color: #0066cc;
        }

        .badge-player {
            background: #e6f2e6;
            color: #009900;
        }

        .back-btn {
            display: inline-block;
            margin-bottom: 20px;
            padding: 10px 20px;
            background: #f0f0f0;
            color: #333;
            text-decoration: none;
            border-radius: 6px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .back-btn:hover {
            background: #e0e0e0;
        }
    </style>
</head>
<body style="background: linear-gradient(135deg, #ffe6ff 0%, #FFFAFF 100%); margin: 0; padding: 0; min-height: 100vh; position: relative;">
    <div class="container">

        <h1 class="page-title">Team Management</h1>

        <div class="actions-bar">
            <button class="btn-add" onclick="openAddPlayerModal()">+ Add Player</button>
        </div>

        <div class="players-grid" id="playersGrid">
            <?php if (!empty($data['players'])): ?>
                <?php foreach ($data['players'] as $player): ?>
                    <div class="player-card">
                        <div class="player-image">
                            <?php
                            if (!empty($player->image)) {
                                echo '<img src="' . htmlspecialchars($player->image) . '" alt="' . htmlspecialchars($player->full_name) . '" style="width: 100%; height: 100%; object-fit: cover;">';
                            } else {
                                $name = $player->full_name ?? $player->name_with_initials ?? 'P';
                                $initials = strtoupper(substr($name, 0, 2));
                                echo $initials;
                            }
                            ?>
                        </div>
                        <div class="player-info">
                            <div class="player-name"><?php echo htmlspecialchars($player->full_name ?? ''); ?></div>
                            <div class="player-role">
                                <?php 
                                $role = $player->role ?? 'Player';
                                echo htmlspecialchars($role);
                                ?>
                                <div class="badge badge-<?php echo strtolower(str_replace(' ', '-', $role)); ?>">
                                    <?php echo ucfirst($role); ?>
                                </div>
                            </div>
                            <div class="player-details">
                                <?php if (!empty($player->position)): ?>
                                    <strong>Position:</strong> <?php echo htmlspecialchars($player->position); ?><br>
                                <?php endif; ?>
                                <?php if (!empty($player->jersey_number)): ?>
                                    <strong>Jersey:</strong> #<?php echo htmlspecialchars($player->jersey_number); ?><br>
                                <?php endif; ?>
                                <?php if (!empty($player->faculty)): ?>
                                    <strong>Faculty:</strong> <?php echo htmlspecialchars($player->faculty); ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="player-actions">
                            <button class="btn-edit" onclick="editPlayer(<?php echo $player->id; ?>)">Edit</button>
                            <button class="btn-delete" onclick="deletePlayer(<?php echo $player->id; ?>)">Delete</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1/-1;">
                    <div class="empty-state">
                        <div class="empty-state-icon">👥</div>
                        <h3>No Players Yet</h3>
                        <p>Add your first player to get started!</p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Add/Edit Player Modal -->
    <div class="modal" id="playerModal">
        <div class="modal-content">
            <div class="modal-header" id="modalTitle">Add New Player</div>
            <form id="playerForm" onsubmit="submitPlayerForm(event)">
                <div class="form-group">
                    <label for="fullName">Full Name *</label>
                    <input type="text" id="fullName" name="full_name" required>
                </div>

                <div class="form-group">
                    <label for="nameInitials">Name with Initials</label>
                    <input type="text" id="nameInitials" name="name_with_initials">
                </div>

                <div class="form-group">
                    <label for="role">Role *</label>
                    <select id="role" name="role" required>
                        <option value="player">Player</option>
                        <option value="captain">Captain</option>
                        <option value="vice-captain">Vice Captain</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="position">Position *</label>
                    <input type="text" id="position" name="position" placeholder="e.g., Forward, Midfielder, Defender, Goalkeeper" required>
                </div>

                <div class="form-group">
                    <label for="jerseyNumber">Jersey Number</label>
                    <input type="number" id="jerseyNumber" name="jersey_number" min="1" max="99">
                </div>

                <div class="form-group">
                    <label for="faculty">Faculty</label>
                    <input type="text" id="faculty" name="faculty">
                </div>

                <div class="form-group">
                    <label for="nic">NIC</label>
                    <input type="text" id="nic" name="nic">
                </div>

                <div class="form-group">
                    <label for="regNumber">University Registration Number</label>
                    <input type="text" id="regNumber" name="uni_register_number">
                </div>

                <div class="form-group">
                    <label for="mobile">Mobile Number</label>
                    <input type="tel" id="mobile" name="mobile_number">
                </div>

                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address"></textarea>
                </div>

                <div class="form-group">
                    <label for="height">Height (cm)</label>
                    <input type="number" id="height" name="height" step="0.1">
                </div>

                <div class="form-group">
                    <label for="weight">Weight (kg)</label>
                    <input type="number" id="weight" name="weight" step="0.1">
                </div>

                <div class="form-group">
                    <label for="playerImage">Player Image</label>
                    <input type="file" id="playerImage" name="image" accept="image/*">
                    <img id="imagePreview" class="image-preview" alt="Image preview">
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closePlayerModal()">Cancel</button>
                    <button type="submit" class="btn-submit">Save Player</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let currentPlayerId = null;
        let imageFile = null;

        // Handle image preview
        document.getElementById('playerImage').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                imageFile = file;
                const reader = new FileReader();
                reader.onload = function(event) {
                    const preview = document.getElementById('imagePreview');
                    preview.src = event.target.result;
                    preview.classList.add('show');
                };
                reader.readAsDataURL(file);
            }
        });

        function openAddPlayerModal() {
            currentPlayerId = null;
            imageFile = null;
            document.getElementById('modalTitle').textContent = 'Add New Player';
            document.getElementById('playerForm').reset();
            document.getElementById('imagePreview').classList.remove('show');
            document.getElementById('playerModal').classList.add('active');
        }

        function editPlayer(playerId) {
            currentPlayerId = playerId;
            imageFile = null;
            document.getElementById('modalTitle').textContent = 'Edit Player';
            
            // Fetch player data
            fetch('<?php echo ROOT; ?>/teamManagement/getPlayer?id=' + playerId)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const player = data.player;
                        document.getElementById('fullName').value = player.full_name || '';
                        document.getElementById('nameInitials').value = player.name_with_initials || '';
                        // Show existing image if available
                        if (player.image) {
                            const preview = document.getElementById('imagePreview');
                            preview.src = player.image;
                            preview.classList.add('show');
                        } else {
                            document.getElementById('imagePreview').classList.remove('show');
                        }
                        document.getElementById('role').value = player.role || 'player';
                        document.getElementById('position').value = player.position || '';
                        document.getElementById('jerseyNumber').value = player.jersey_number || '';
                        document.getElementById('faculty').value = player.faculty || '';
                        document.getElementById('nic').value = player.nic || '';
                        document.getElementById('regNumber').value = player.uni_register_number || '';
                        document.getElementById('mobile').value = player.mobile_number || '';
                        document.getElementById('address').value = player.address || '';
                        document.getElementById('height').value = player.height || '';
                        document.getElementById('weight').value = player.weight || '';
                        document.getElementById('playerModal').classList.add('active');
                    }
                });
        }

        function closePlayerModal() {
            document.getElementById('playerModal').classList.remove('active');
            document.getElementById('playerForm').reset();
            document.getElementById('imagePreview').classList.remove('show');
            currentPlayerId = null;
            imageFile = null;
        }

        function submitPlayerForm(e) {
            e.preventDefault();
            
            const formData = new FormData(document.getElementById('playerForm'));
            const url = currentPlayerId 
                ? '<?php echo ROOT; ?>/teamManagement/updatePlayer?id=' + currentPlayerId
                : '<?php echo ROOT; ?>/teamManagement/addPlayer';

            fetch(url, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    closePlayerModal();
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while saving the player');
            });
        }

        function deletePlayer(playerId) {
            if (confirm('Are you sure you want to delete this player?')) {
                fetch('<?php echo ROOT; ?>/teamManagement/deletePlayer?id=' + playerId, {
                    method: 'POST'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the player');
                });
            }
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('playerModal');
            if (event.target === modal) {
                closePlayerModal();
            }
        }
    </script>
</body>
</html>
