<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="<?= ROOT ?>/assets/css/captain/attendance.css">

</head>

<body>


    <!-- ================= TOP NAVBAR ================= -->
    <header class="top-navbar">
        <div class="nav-left">
            <a href="<?php echo ROOT; ?>/captainDashboard">
                <img class="header-logo" src="<?php echo ROOT; ?>../assets/images/adminDashboard/header/uoclogo.png"
                    alt="UOC Football Logo">
            </a>
        </div>

        <nav class="nav-center">
            <a href="<?= ROOT ?>/CaptainDashboard">Home</a>
            <a href="<?= ROOT ?>/captain/dashboard">Schedule</a>
            <a href="<?= ROOT ?>/CaptainAnalyze">Analyze</a>
            <a href="#" class="active">Attendance</a>
            <a href="<?= ROOT ?>/CaptainInventory">Inventory</a>
            <a href="<?= ROOT ?>/CaptainFinance">Finance</a>
            <!-- <a href="<?= ROOT ?>/CaptainNotices">Notices</a> -->

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

    <main class="content">

        <!-- Page Header -->
        <header class="page-header">
            <div>
                <h1>Attendance Dashboard</h1>
                <p>View and update player attendance for today’s session</p>
            </div>

            <div class="filters">
                <select>
                    <option>Practice</option>
                    <option>Match</option>
                    <option>Training</option>
                    <option>Fitness</option>
                </select>
                <input type="date" value="<?= date('Y-m-d') ?>">
                <select>
                    <option>All Teams</option>
                    <!-- <option value="">Team A</option>
                <option value="">Team B</option> -->
                </select>
            </div>
        </header>

        <!-- Stats -->
        <section class="stats">
            <div class="stat-card">
                <h3>Total Players</h3>
                <span><?= $data['totalPlayers'] ?>
                    <div class="icon-container">
                        <img src="<?php echo ROOT; ?>/assets/images/Captain/icons/teams.svg" alt="team icon"
                            class="action-icon">
                </span>

            </div>

            </div>

            <div class="stat-card success">
                <h3>Present Players</h3>
                <div class="icon-container"><span><?= $data['present'] ?>
                        <img src="<?php echo ROOT; ?>/assets/images/Captain/icons/" alt="team icon"
                            class="action-icon">
                </div>
                </span>
            </div>

            <div class="stat-card danger">
                <h3>Absent Players</h3>
                <div class="icon-container"><span><?= $data['absent'] ?>
                        <img src="<?php echo ROOT; ?>/assets/images/Captain/icons/a" alt="team icon"
                            class="action-icon">
                </div>
                </span>
            </div>
        </section>

        <div class="dashboard-grid">

            <!-- Player Attendance -->
            <section class="attendance-section">
                <div class="section-header">
                    <h2>Player Attendance</h2>
                    <button class="btn ">Mark All Present</button>
                </div>

                <table class="attendance-table">
                    <thead>
                        <tr>
                            <th>Player</th>
                            <th>Position</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php foreach ($data['players'] as $player): ?>
                            <tr>
                                <td>
                                    <strong><?= $player['name'] ?></strong><br>
                                    <small>#<?= $player['number'] ?></small>
                                </td>
                                <td><?= $player['position'] ?></td>
                                <td>
                                    <span class="status <?= strtolower($player['status']) ?>">
                                        <?= $player['status'] ?>
                                    </span>
                                </td>
                                <td class="actions">
                                    <button class="icon success">✔</button>
                                    <button class="icon danger">✖</button>
                                    <button class="icon warning">🕒</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>

            <!-- Right Panel -->
            <aside class="right-panel">

                <!-- Attendance Rate -->
                <section class="attendance-rate fitness-trends-card">
                    <h2>Attendance Rate</h2>

                    <div class="chart-container">
                        <svg class="chart-svg" viewBox="0 0 500 300" preserveAspectRatio="xMidYMid meet">
                            <!-- Grid lines -->
                            <line x1="50" y1="250" x2="370" y2="250" />
                            <line x1="50" y1="200" x2="370" y2="200" />
                            <line x1="50" y1="150" x2="370" y2="150" />
                            <line x1="50" y1="100" x2="370" y2="100" />
                            <line x1="50" y1="50" x2="370" y2="50" />

                            <!-- Y-axis labels -->
                            <text x="30" y="255">0</text>
                            <text x="30" y="205">25</text>
                            <text x="30" y="155">50</text>
                            <text x="30" y="105">75</text>
                            <text x="25" y="55">100</text>

                            <!-- X-axis labels -->
                            <text x="50" y="270">Mon</text>
                            <text x="100" y="270">Tue</text>
                            <text x="150" y="270">Wed</text>
                            <text x="200" y="270">Thu</text>
                            <text x="250" y="270">Fri</text>
                            <text x="300" y="270">Sat</text>
                            <text x="350" y="270">Sun</text>

                            <!-- Attendance line -->
                            <polyline points="50,80 100,70 150,95 200,60 250,75 300,74 350,71"
                                class="attendance-line" />

                            <!-- Data points -->
                            <circle cx="50" cy="80" r="4" />
                            <circle cx="100" cy="70" r="4" />
                            <circle cx="150" cy="95" r="4" />
                            <circle cx="200" cy="60" r="4" />
                            <circle cx="250" cy="75" r="4" />
                            <circle cx="300" cy="74" r="4" />
                            <circle cx="350" cy="71" r="4" />
                        </svg>
                    </div>
                </section>


                <section class="quick-actions">
                    <h2>Quick Actions</h2>
                    <div class="action-btns">
                        <button class="btn save-attendance">Save Attendance</button>
                        <button class="btn reset-changes">Reset Changes</button>
                        <button class="btn exportreport">Export Report</button>
                    </div>
                </section>

            </aside>
        </div>

    </main>

    <script src="<?= ROOT ?>/assets/js/captain/attendance.js"></script>
</body>

</html>