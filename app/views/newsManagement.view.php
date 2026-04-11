<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>News Management</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/adminDashboard/style.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/newsManagement/style.css">
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
        
        .btn-secondary {
            background: #e0e0e0;
            color: #333;
        }
        
        .btn-secondary:hover {
            background: #d0d0d0;
        }
        
        .btn-danger {
            background: #ff6b6b;
            color: white;
        }
        
        .btn-danger:hover {
            background: #ff5252;
        }
        
        .btn-success {
            background: #51cf66;
            color: white;
        }
        
        .btn-success:hover {
            background: #40c057;
        }
        
        .news-table {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        
        .news-table table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .news-table th {
            background: #f5f5f5;
            padding: 15px;
            text-align: left;
            font-weight: 600;
            color: #333;
            border-bottom: 2px solid #e0e0e0;
        }
        
        .news-table td {
            padding: 15px;
            border-bottom: 1px solid #e0e0e0;
            color: #666;
        }
        
        .news-table tr:hover {
            background: #f9f9f9;
        }
        
        .action-buttons {
            display: flex;
            gap: 10px;
        }
        
        .action-buttons button {
            padding: 6px 12px;
            font-size: 0.85rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif;
            font-weight: 600;
        }
        
        .btn-edit {
            background: #4dabf7;
            color: white;
        }
        
        .btn-edit:hover {
            background: #3980d1;
        }
        
        .btn-delete {
            background: #ff6b6b;
            color: white;
        }
        
        .btn-delete:hover {
            background: #ff5252;
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
            background-color: rgba(0, 0, 0, 0.5);
            padding: 20px;
        }
        
        .modal.show {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .modal-content {
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            width: 100%;
            max-width: 600px;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }
        
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e0e0e0;
        }
        
        .modal-header h2 {
            font-size: 1.5rem;
            color: #333;
        }
        
        .close-btn {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #666;
        }
        
        .close-btn:hover {
            color: #333;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #333;
        }
        
        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-family: 'Poppins', sans-serif;
            font-size: 0.95rem;
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 150px;
        }
        
        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: #663399;
            box-shadow: 0 0 0 3px rgba(102, 51, 153, 0.1);
        }
        
        .modal-footer {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            display: none;
        }
        
        .alert.show {
            display: block;
        }
        
        .alert-success {
            background: #d4f5e0;
            color: #2d5f3a;
            border-left: 4px solid #51cf66;
        }
        
        .alert-error {
            background: #ffe0e0;
            color: #5f2d2d;
            border-left: 4px solid #ff6b6b;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #999;
        }
        
        .empty-state p {
            font-size: 1.1rem;
            margin-bottom: 20px;
        }
        
        .news-title {
            max-width: 200px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            font-weight: 600;
            color: #333;
        }
        
        .news-date {
            color: #999;
            font-size: 0.9rem;
        }
        
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .status-published {
            background: #d4f5e0;
            color: #2d5f3a;
        }
        
        .status-draft {
            background: #fff3bf;
            color: #664d00;
        }
        
        .loading {
            display: none;
            text-align: center;
            padding: 20px;
        }
        
        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #663399;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📰 News Management</h1>
            <button class="btn btn-primary" onclick="openAddModal()">+ Add New News</button>
        </div>
        
        <div id="alertMessage" class="alert"></div>
        
        <?php if (!empty($data['news'])): ?>
            <div class="news-table">
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th width="150">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="newsTableBody">
                        <?php foreach ($data['news'] as $article): ?>
                            <tr data-id="<?php echo $article->id; ?>">
                                <td><div class="news-title"><?php echo htmlspecialchars($article->title); ?></div></td>
                                <td><span class="news-date"><?php echo date('F j, Y', strtotime($article->publish_date)); ?></span></td>
                                <td><span class="status-badge status-<?php echo strtolower($article->status ?? 'draft'); ?>"><?php echo ucfirst($article->status ?? 'Draft'); ?></span></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn-edit" onclick="openEditModal(<?php echo $article->id; ?>)">Edit</button>
                                        <button class="btn-delete" onclick="deleteNews(<?php echo $article->id; ?>)">Delete</button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="news-table">
                <div class="empty-state">
                    <p>No news articles found. Click "Add New News" to create one!</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- Add/Edit Modal -->
    <div id="newsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Add New News</h2>
                <button class="close-btn" onclick="closeModal()">&times;</button>
            </div>
            
            <form id="newsForm" onsubmit="saveNews(event)">
                <div class="form-group">
                    <label for="newsTitle">Title</label>
                    <input type="text" id="newsTitle" name="news_heading" required>
                </div>
                
                <div class="form-group">
                    <label for="newsContent">Content</label>
                    <textarea id="newsContent" name="news_body" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="newsDate">Date</label>
                    <input type="date" id="newsDate" name="news_date" required>
                </div>
                
                <div class="form-group">
                    <label for="newsStatus">Status</label>
                    <select id="newsStatus" name="status" required>
                        <option value="published">Published</option>
                        <option value="draft">Draft</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="newsImage">Image (Optional)</label>
                    <input type="file" id="newsImage" name="image" accept="image/*">
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                    <button type="submit" class="btn btn-success">Save News</button>
                </div>
            </form>
        </div>
    </div>
    
    <script>
        let currentEditId = null;
        
        function openAddModal() {
            currentEditId = null;
            document.getElementById('modalTitle').textContent = 'Add New News';
            document.getElementById('newsForm').reset();
            document.getElementById('newsDate').valueAsDate = new Date();
            document.getElementById('newsModal').classList.add('show');
        }
        
        function openEditModal(id) {
            currentEditId = id;
            document.getElementById('modalTitle').textContent = 'Edit News';
            
            fetch('<?php echo ROOT; ?>/newsManagement/get?id=' + id)
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        const news = result.data;
                        document.getElementById('newsTitle').value = news.title;
                        document.getElementById('newsContent').value = news.content;
                        document.getElementById('newsDate').value = news.publish_date;
                        document.getElementById('newsStatus').value = news.status || 'published';
                        document.getElementById('newsModal').classList.add('show');
                    } else {
                        showAlert('Error loading news', 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('Error loading news', 'error');
                });
        }
        
        function closeModal() {
            document.getElementById('newsModal').classList.remove('show');
            currentEditId = null;
        }
        
        function saveNews(event) {
            event.preventDefault();
            
            const title = document.getElementById('newsTitle').value;
            const content = document.getElementById('newsContent').value;
            const date = document.getElementById('newsDate').value;
            const status = document.getElementById('newsStatus').value;
            const imageFile = document.getElementById('newsImage').files[0];
            
            const data = {
                news_heading: title,
                news_body: content,
                news_date: date,
                status: status
            };
            
            if (currentEditId) {
                data.id = currentEditId;
            }
            
            // Handle image upload
            if (imageFile) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    data.image_data = e.target.result;
                    sendSaveRequest(data);
                };
                reader.readAsDataURL(imageFile);
            } else {
                sendSaveRequest(data);
            }
        }
        
        function sendSaveRequest(data) {
            const url = currentEditId 
                ? '<?php echo ROOT; ?>/newsManagement/update'
                : '<?php echo ROOT; ?>/newsManagement/add';
            
            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    showAlert(result.message, 'success');
                    closeModal();
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showAlert(result.message, 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('An error occurred', 'error');
            });
        }
        
        function deleteNews(id) {
            if (confirm('Are you sure you want to delete this news article?')) {
                fetch('<?php echo ROOT; ?>/newsManagement/delete', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id: id })
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        showAlert(result.message, 'success');
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        showAlert(result.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showAlert('An error occurred', 'error');
                });
            }
        }
        
        function showAlert(message, type) {
            const alert = document.getElementById('alertMessage');
            alert.textContent = message;
            alert.className = 'alert show alert-' + type;
            setTimeout(() => {
                alert.classList.remove('show');
            }, 4000);
        }
        
        // Set today's date as default
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('newsDate').valueAsDate = new Date();
        });
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('newsModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>
