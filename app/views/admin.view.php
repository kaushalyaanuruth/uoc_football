<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <title>UOC_football</title>
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
        
        .header {
            border-radius: 12px;
            padding: 20px 30px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logout-btn {
            background: #663399;
            color: white;
            border: 2px solid #663399;
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
            text-decoration: none;
            transition: all 0.3s ease;
            font-weight: 700;
            font-size: 1rem;
            letter-spacing: 0.5px;
            display: flex;
            align-items: center;
            cursor: pointer;
        }
        
        .logout-btn:hover {
            background: #552288;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 51, 153, 0.3);
            border-color: #552288;
        }
        
        .header h1 {
            font-family: 'Poppins', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: #333;
        }
        
        .quickActions {
            margin-bottom: 40px;
        }
        
        .quickActions .section-title {
            font-family: 'Poppins', sans-serif;
            font-size: 1.75rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 25px;
        }
        
        .actions-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .action-card {
            background: white;
            border-radius: 12px;
            padding: 25px 20px;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            text-decoration: none;
            cursor: pointer;
        }
        
        .action-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 20px rgba(102, 51, 153, 0.15);
        }
        
        .action-label {
            font-family: 'Poppins', sans-serif;
            font-size: 1rem;
            font-weight: 600;
            margin-top: 12px;
            color: #333;
        }
        
        .welcome-banner {
            background: linear-gradient(135deg, #663399 0%, #9966cc 100%);
            border-radius: 12px;
            padding: 40px;
            color: white;
            margin-bottom: 40px;
            box-shadow: 0 4px 20px rgba(102, 51, 153, 0.2);
        }
        
        .welcome-title {
            font-family: 'Poppins', sans-serif;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 15px;
        }
        
        .welcome-datetime {
            display: flex;
            gap: 30px;
            font-size: 1.1rem;
        }
        
        .card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.08);
            margin-bottom: 20px;
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #f0f0f0;
            padding-bottom: 15px;
        }
        
        .card-title h2 {
            font-family: 'Poppins', sans-serif;
            font-size: 1.5rem;
            font-weight: 700;
            color: #333;
            margin: 0;
        }
    </style>
</head>
<body style="background: linear-gradient(135deg, #ffe6ff 0%, #FFFAFF 100%); margin: 0; padding: 0; min-height: 100vh; position: relative;">
    <div class="container">
        <div class="header">
                <div class="left-section">
                    <a href="<?php echo ROOT; ?>/admin">
                        <img class="header-logo" src="<?php echo ROOT; ?>/assets/images/adminDashboard/header/uoclogo.png" alt="UOC Football Logo">
                    </a>
                </div>
                <div class="right-section">
                    <a href="<?php echo ROOT; ?>/login" class="logout-btn">Logout</a>
                </div>
        </div>
        <div class="quickActions">
            <h2 class="section-title">Quick Actions</h2>
            <div class="actions-grid">
                <a class="action-card icon-purple" href="<?php echo ROOT; ?>/teamManagement">
                    <div class="icon-container">
                        <img src="<?php echo ROOT; ?>/assets/images/adminDashboard/icons/teams.svg" alt="team icon" class="action-icon">
                    </div>
                    <p class="action-label">Teams</p>
                </a>
                <a class="action-card icon-blue" href="">
                    <div class="icon-container">
                        <img src="<?php echo ROOT; ?>/assets/images/adminDashboard/icons/test.svg" alt="results icon" class="action-icon">
                    </div>
                    <p class="action-label">Results</p>
                </a>
                <a class="action-card icon-green" href="<?php echo ROOT; ?>/eventManagement">
                    <div class="icon-container">
                        <img src="<?php echo ROOT; ?>/assets/images/adminDashboard/icons/event.svg" alt="event icon" class="action-icon">
                    </div>
                    <p class="action-label">Events</p>
                </a>
                <a class="action-card icon-red" href="">
                    <div class="icon-container">
                        <img src="<?php echo ROOT; ?>/assets/images/adminDashboard/icons/inventory.svg" alt="inventory icon" class="action-icon">
                    </div>
                    <p class="action-label">Inventory</p>
                </a>
                <a class="action-card icon-pink" href="">
                    <div class="icon-container">
                        <img src="<?php echo ROOT; ?>/assets/images/adminDashboard/icons/gallery.svg" alt="gallery icon" class="action-icon">
                    </div>
                    <p class="action-label">Gallery</p>
                </a>
                <a class="action-card icon-indigo" href="<?php echo ROOT; ?>/newsManagement">
                    <div class="icon-container">
                        <img src="<?php echo ROOT; ?>/assets/images/adminDashboard/icons/news.svg" alt="news icon" class="action-icon">
                    </div>
                    <p class="action-label">News</p>
                </a>
                <a class="action-card icon-yellow" href="">
                    <div class="icon-container">
                        <img src="<?php echo ROOT; ?>/assets/images/adminDashboard/icons/store.svg" alt="store icon" class="action-icon">
                    </div>
                    <p class="action-label">Store</p>
                </a>
                <a class="action-card icon-orange" href="">
                    <div class="icon-container">
                        <img src="<?php echo ROOT; ?>/assets/images/adminDashboard/icons/budget.svg" alt="budget icon" class="action-icon">
                    </div>
                    <p class="action-label">Budget</p>
                </a>
            </div>
        </div>
        <div class="welcome-banner">
            <div class="welcome-content">
                <h1 class="welcome-title">Welcome back,</br> Admin!</h2>
                <div class="welcome-datetime">
                    <p class="date"><?php echo date('l, F j, Y'); ?></p>
                    <p class="time"><?php echo date('h:i A'); ?></p>
                </div>
            </div>
        </div>
        <div class="main-grid">
            <div class="notices card">
                <div class="card-header">
                    <div class="card-title">
                        <h2>Notices</h2>
                    </div>
                    <button class="add-btn">+ Add</button>
                </div>
                <div class="card-body">
                    <ul class="notices-list">
                        <li class="notice-item">
                            <h3 class="notice-title">Upcoming Match Schedule</h3>
                            <p class="notice-date">August 15, 2024</p>
                        </li>
                        <li class="notice-item">
                            <h3 class="notice-title">New Training Sessions</h3>
                            <p class="notice-date">August 10, 2024</p>
                        </li>
                        <li class="notice-item">
                            <h3 class="notice-title">Inventory Update Required</h3>
                            <p class="notice-date">August 5, 2024</p>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="nextEvent card">
                <div class="card-header">
                    <div class="card-title">
                        <h2>What is next?</h2>
                    </div>
                </div>
                <div class="card-body">
                        <ul class="event-list">
                            <li class="event-item">
                                <div class="event-detail">
                                    <h3 class="event-title">Upcoming Match Schedule</h3>
                                    <p class="event-date">August 15, 2024</p>
                                </div>
                                <div class="icon-container">
                                    <img src="<?php echo ROOT; ?>/assets/images/adminDashboard/icons/match.svg" alt="match icon" class="action-icon">
                                </div>
                                
                            </li>
                            <li class="event-item">
                                <div class="event-detail">
                                    <h3 class="event-title">New Training Sessions</h3>
                                    <p class="event-date">August 10, 2024</p>
                                </div>
                                <div class="icon-container">
                                    <img src="<?php echo ROOT; ?>/assets/images/adminDashboard/icons/training.svg" alt="training icon" class="action-icon">
                                </div>
                            </li>
                        </ul>
                </div>
            </div>
            <div class="inventory card">
                <div class="card-header">
                    <div class="card-title">
                        <h2>Inventory Status</h2>
                    </div>
                </div>
                <div class="inventory-list">
                    <div class="inventory-item">
                        <div class="inventory-left">
                              <div class="icon-container">
                                    <img src="<?php echo ROOT; ?>/assets/images/adminDashboard/icons/match.svg" alt="match icon" class="action-icon">
                                </div>
                            <span class="inventory-name">Bibs</span>
                        </div>
                        <span class="inventory-stock stock-high">45 in stock</span>
                    </div>
                    <div class="inventory-item">
                        <div class="inventory-left">
                              <div class="icon-container">
                                    <img src="<?php echo ROOT; ?>/assets/images/adminDashboard/icons/match.svg" alt="match icon" class="action-icon">
                                </div>
                            <span class="inventory-name">Footballs</span>
                        </div>
                        <span class="inventory-stock stock-medium">12 in stock</span>
                    </div>

                    <div class="inventory-item">
                        <div class="inventory-left">
                              <div class="icon-container">
                                    <img src="<?php echo ROOT; ?>/assets/images/adminDashboard/icons/match.svg" alt="match icon" class="action-icon">
                                </div>
                            <span class="inventory-name">Markers</span>
                        </div>
                        <span class="inventory-stock stock-low">3 in stock</span>
                    </div>
                    <div class="inventory-item">
                        <div class="inventory-left">
                              <div class="icon-container">
                                    <img src="<?php echo ROOT; ?>/assets/images/adminDashboard/icons/match.svg" alt="match icon" class="action-icon">
                                </div>
                            <span class="inventory-name">Cones</span>
                        </div>
                        <span class="inventory-stock stock-low">3 in stock</span>
                    </div>
                    <div class="inventory-item">
                        <div class="inventory-left">
                              <div class="icon-container">
                                    <img src="<?php echo ROOT; ?>/assets/images/adminDashboard/icons/match.svg" alt="match icon" class="action-icon">
                                </div>
                            <span class="inventory-name">Resistant band</span>
                        </div>
                        <span class="inventory-stock stock-low">3 in stock</span>
                    </div>
                    <div class="inventory-item">
                        <div class="inventory-left">
                              <div class="icon-container">
                                    <img src="<?php echo ROOT; ?>/assets/images/adminDashboard/icons/match.svg" alt="match icon" class="action-icon">
                                </div>
                            <span class="inventory-name">Bottles</span>
                        </div>
                        <span class="inventory-stock stock-low">3 in stock</span>
                    </div>

                </div>
            </div>
        </div>    
    </div>
</body>
</html>