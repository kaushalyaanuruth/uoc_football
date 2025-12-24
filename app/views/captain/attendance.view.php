<!-- ================= TOP NAVBAR ================= -->
<header class="top-navbar">
    <div class="nav-left">
        <span class="brand">UOC Football</span>
    </div>

    <nav class="nav-center">
        <a href="<?= ROOT  ?>/captain/dashboard" class="active">Home</a>
        <a href="#">Schedule</a>
        <a href="#">Analytics</a>
        <a href="#">Notices</a>
    </nav>

    <div class="nav-right">
        <button class="icon-btn">🔔</button>
        <div class="profile">C</div>
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
            </select>
            <input type="date" value="<?= date('Y-m-d') ?>">
            <select>
                <option>All Teams</option>
            </select>
        </div>
    </header>

    <!-- Stats -->
    <section class="stats">
        <div class="stat-card">
            <h3>Total Players</h3>
            <span><?= $data['totalPlayers'] ?></span>
        </div>

        <div class="stat-card success">
            <h3>Present</h3>
            <span><?= $data['present'] ?></span>
        </div>

        <div class="stat-card danger">
            <h3>Absent</h3>
            <span><?= $data['absent'] ?></span>
        </div>
    </section>

    <div class="dashboard-grid">

        <!-- Player Attendance -->
        <section class="attendance-section">
            <div class="section-header">
                <h2>Player Attendance</h2>
                <button class="btn primary">Mark All Present</button>
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

            <section class="attendance-rate">
                <h2>Attendance Rate</h2>
                <div class="chart">
                    <?php foreach ($data['weekly'] as $day => $value): ?>
                        <div class="bar" style="height:<?= $value ?>%"><?= $day ?></div>
                    <?php endforeach; ?>
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

<link rel="stylesheet" href="<?= ROOT  ?>/assets/css/captain/attendance.css">
<script src="<?= ROOT  ?>/assets/js/captain/attendance.js"></script>
