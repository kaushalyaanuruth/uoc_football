<?php
$events = $data['events'] ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Management</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/adminDashboard/style.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #ffe6ff 0%, #FFFAFF 100%);
            min-height: 100vh;
            position: relative;
            margin: 0;
            padding: 0;
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
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            position: relative;
            z-index: 1;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .header h1 {
            font-size: 2rem;
            color: #333;
            font-weight: 700;
        }
        
        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
        }
        
        .btn-primary {
            background: #663399;
            color: white;
        }
        
        .btn-primary:hover {
            background: #552288;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 51, 153, 0.3);
        }
        
        .btn-danger {
            background: #ff6b6b;
            color: white;
            padding: 8px 15px;
            font-size: 0.9rem;
        }
        
        .btn-danger:hover {
            background: #ff5252;
        }
        
        .btn-edit {
            background: #4CAF50;
            color: white;
            padding: 8px 15px;
            font-size: 0.9rem;
            margin-right: 5px;
        }
        
        .btn-edit:hover {
            background: #45a049;
        }
        
        .events-table {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .events-table table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .events-table th {
            background: #f5f5f5;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #333;
            border-bottom: 2px solid #e0e0e0;
        }
        
        .events-table td {
            padding: 15px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .events-table tr:hover {
            background: #f9f9f9;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #999;
        }
        
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4);
        }
        
        .modal.show {
            display: block;
        }
        
        .modal-content {
            background-color: white;
            margin: 2% auto;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            width: 90%;
            max-width: 500px;
            max-height: 90vh;
            overflow-y: auto;
        }
        
        .modal-header {
            margin-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 15px;
        }
        
        .modal-header h2 {
            color: #333;
            font-size: 1.5rem;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
            color: #333;
        }
        
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }
        
        .image-preview {
            margin-top: 10px;
            max-width: 100%;
            max-height: 150px;
            border-radius: 4px;
            display: none;
        }
        
        .form-actions {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
            border-top: 1px solid #f0f0f0;
            padding-top: 15px;
        }
        
        .btn-secondary {
            background: #e0e0e0;
            color: #333;
        }
        
        .btn-secondary:hover {
            background: #d0d0d0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📅 Event Management</h1>
            <button class="btn btn-primary" onclick="openAddModal()">+ Add Event</button>
        </div>
        
        <div class="events-table">
            <?php if (!empty($events)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Event Title</th>
                            <th>Date & Time</th>
                            <th>Location</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($events as $event): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($event->title); ?></td>
                                <td><?php echo date('M d, Y H:i', strtotime($event->event_date . ' ' . $event->event_time)); ?></td>
                                <td><?php echo htmlspecialchars($event->location ?? '-'); ?></td>
                                <td><?php echo ucfirst($event->event_type); ?></td>
                                <td>
                                    <span style="padding: 5px 10px; border-radius: 4px; font-size: 0.85rem; font-weight: 600; background: <?php echo $event->status === 'upcoming' ? '#e3f2fd' : '#fff3e0'; ?>; color: <?php echo $event->status === 'upcoming' ? '#1976d2' : '#f57c00'; ?>;">
                                        <?php echo ucfirst($event->status); ?>
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-edit" onclick="openEditModal(<?php echo $event->id; ?>)">Edit</button>
                                    <button class="btn btn-danger" onclick="deleteEvent(<?php echo $event->id; ?>)">Delete</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <p>No events yet. Click the "Add Event" button to create one!</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Add/Edit Modal -->
    <div id="eventModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Add New Event</h2>
            </div>
            
            <form id="eventForm">
                <div class="form-group">
                    <label for="title">Event Title *</label>
                    <input type="text" id="title" name="title" required placeholder="e.g., Training Session">
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" placeholder="Event description..."></textarea>
                </div>
                
                <div class="form-group">
                    <label for="event_date">Date & Time *</label>
                    <input type="datetime-local" id="event_date" name="event_date" required>
                </div>
                
                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" id="location" name="location" placeholder="Event location...">
                </div>
                
                <div class="form-group">
                    <label for="category">Event Type *</label>
                    <select id="category" name="category" required>
                        <option value="match">Match</option>
                        <option value="training">Training</option>
                        <option value="tournament">Tournament</option>
                        <option value="meeting">Meeting</option>
                        <option value="social">Social</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="image">Event Image</label>
                    <input type="file" id="image" name="image" accept="image/*">
                    <img id="imagePreview" class="image-preview" alt="Image preview">
                </div>
                
                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status">
                        <option value="upcoming">Upcoming</option>
                        <option value="completed">Completed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Event</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let editingEventId = null;
        let imageBase64 = null;

        // Image preview listener
        document.getElementById('image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(event) {
                    imageBase64 = event.target.result;
                    const preview = document.getElementById('imagePreview');
                    preview.src = imageBase64;
                    preview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });

        function openAddModal() {
            editingEventId = null;
            imageBase64 = null;
            document.getElementById('eventForm').reset();
            document.getElementById('imagePreview').style.display = 'none';
            document.getElementById('modalTitle').textContent = 'Add New Event';
            document.getElementById('eventModal').classList.add('show');
        }

        function openEditModal(id) {
            editingEventId = id;
            imageBase64 = null;
            fetch(`<?php echo ROOT; ?>/eventManagement/get?id=${id}`)
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        const event = result.data;
                        document.getElementById('title').value = event.title;
                        document.getElementById('description').value = event.description || '';
                        
                        // Convert datetime format for input
                        const dateTime = new Date(event.event_date + ' ' + event.event_time);
                        const isoDateTime = dateTime.toISOString().slice(0, 16);
                        document.getElementById('event_date').value = isoDateTime;
                        
                        document.getElementById('location').value = event.location || '';
                        document.getElementById('category').value = event.event_type;
                        document.getElementById('status').value = event.status;
                        document.getElementById('modalTitle').textContent = 'Edit Event';
                        document.getElementById('eventModal').classList.add('show');
                    } else {
                        alert('Failed to load event details');
                    }
                })
                .catch(err => console.error(err));
        }

        function closeModal() {
            document.getElementById('eventModal').classList.remove('show');
            document.getElementById('eventForm').reset();
            document.getElementById('imagePreview').style.display = 'none';
            editingEventId = null;
            imageBase64 = null;
        }

        document.getElementById('eventForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const formData = {
                title: document.getElementById('title').value,
                description: document.getElementById('description').value,
                event_date: document.getElementById('event_date').value,
                location: document.getElementById('location').value,
                category: document.getElementById('category').value,
                status: document.getElementById('status').value
            };
            
            // Add image if selected
            if (imageBase64) {
                formData.image_data = imageBase64;
            }
            
            // Add ID if editing
            if (editingEventId) {
                formData.id = editingEventId;
            }
            
            const url = editingEventId 
                ? `<?php echo ROOT; ?>/eventManagement/update`
                : `<?php echo ROOT; ?>/eventManagement/add`;
            
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(formData)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    location.reload();
                } else {
                    alert('Error: ' + (result.message || 'Failed to save event'));
                }
            } catch (err) {
                console.error('Error:', err);
                alert('An error occurred while saving the event');
            }
        });

        function deleteEvent(id) {
            if (confirm('Are you sure you want to delete this event?')) {
                fetch(`<?php echo ROOT; ?>/eventManagement/delete`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id: id })
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        location.reload();
                    } else {
                        alert('Failed to delete event');
                    }
                })
                .catch(err => console.error(err));
            }
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('eventModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>
