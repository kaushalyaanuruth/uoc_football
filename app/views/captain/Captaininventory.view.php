<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventory Management</title>

    <link rel="stylesheet" href="<?= ROOT ?>/assets/css/captain/Captaininventory.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

    <!-- ================= TOP NAVBAR ================= -->
    <header class="top-navbar">
        <div class="nav-left">
            <a href="<?php echo ROOT; ?>/dashboard">
                <img class="header-logo" src="<?php echo ROOT; ?>/assets/images/adminDashboard/header/uoclogo.png"
                    alt="UOC Football Logo">
            </a>
        </div>

        <nav class="nav-center">
            <a href="<?= ROOT ?>/Captaindashboard">Home</a>
            <a href="<?= ROOT ?>/dashboard">Schedule</a>
            <a href="<?= ROOT ?>/CaptainAnalyze">Analyze</a>
            <a href="<?= ROOT ?>/CaptainAttendance">Attendance</a>
            <a href="#" class="active">Inventory</a>
            <a href="<?= ROOT ?>/CaptainNotices">Notices</a>
            <!-- <a href="<?= ROOT ?>/CaptainMealPlan">Meal Plan</a> -->
        </nav>

        <div class="nav-right">
            <button class="icon-btn">🔔</button>
            <div class="profile">
                <img class="avatar" src="<?php echo ROOT; ?>../assets/images/adminDashboard/header/avatar.jpg"
                    alt="Admin Avatar">

            </div>
        </div>
    </header>

    <!-- ================= MAIN CONTENT ================= -->
    <main class="content">

        <!-- Page Header -->
        <header class="page-header">
            <div>
                <h1>Inventory Management</h1>
                <p>Manage team equipment and supplies</p>
            </div>
        </header>

        <!-- ================= STATS ================= -->
        <section class="stats">
            <div class="stat-card">
                <h3>Total Items</h3>
                <span>248 <span class="stat-icon purple"></span></span>
            </div>

            <div class="stat-card warning">
                <h3>Items In Use</h3>
                <span>142 <span class="stat-icon orange"></span></span>
            </div>

            <div class="stat-card success">
                <h3>Available Items</h3>
                <span>89 <span class="stat-icon green"></span></span>
            </div>

            <div class="stat-card danger">
                <h3>Damaged Items</h3>
                <span>17 <span class="stat-icon red"></span></span>
            </div>
        </section>

        <!-- ================= CHARTS ================= -->
        <div class="dashboard-grid">
            <section class="chart-card">
                <h2>Equipment Usage</h2>
                <canvas id="equipmentUsageChart"></canvas>
            </section>

            <section class="chart-card">
                <h2>Item Status Distribution</h2>
                <canvas id="statusDistributionChart"></canvas>
            </section>
        </div>

        <!-- ================= INVENTORY TABLE ================= -->
        <section class="inventory-section">
            <div class="section-header">
                <h2>Inventory Items</h2>
                <select>
                    <option>All Categories</option>
                    <option>Kits</option>
                    <option>Balls</option>
                    <option>Equipment</option>
                    <option>Accessories</option>
                </select>
            </div>

            <table class="inventory-table">
                <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Category</th>
                        <th>Quantity</th>
                        <th>Status</th>
                        <th>Last Updated</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td>Training Jerseys</td>
                        <td>Kits</td>
                        <td>25</td>
                        <td><span class="status available">Available</span></td>
                        <td>2 hours ago</td>
                        <td class="actions">
                            ✏️ 🗑
                        </td>
                    </tr>

                    <tr>
                        <td>Match Footballs</td>
                        <td>Balls</td>
                        <td>12</td>
                        <td><span class="status inuse">In Use</span></td>
                        <td>1 day ago</td>
                        <td class="actions">
                            ✏️ 🗑
                        </td>
                    </tr>

                    <tr>
                        <td>Training Cones</td>
                        <td>Equipment</td>
                        <td>50</td>
                        <td><span class="status available">Available</span></td>
                        <td>3 days ago</td>
                        <td class="actions">
                            ✏️ 🗑
                        </td>
                    </tr>

                    <tr>
                        <td>Shin Guards</td>
                        <td>Accessories</td>
                        <td>8</td>
                        <td><span class="status damaged">Damaged</span></td>
                        <td>1 week ago</td>
                        <td class="actions">
                            ✏️ 🗑
                        </td>
                    </tr>
                </tbody>
            </table>
        </section>

    </main>

    <script src="<?= ROOT ?>/assets/js/captain/Captaininventory.js"></script>
</body>

</html>