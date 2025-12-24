<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Captain Attendance - UOC Football</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="captain-attendence">
    <div class="body">
        <div class="div">
            <!-- Sidebar -->
            <nav class="nav">
                <div class="div-2">
                    <div class="div-3">
                        <div class="i-wrapper">
                            <div class="i">
                                <div class="svg">
                                    <div class="frame">
                                        <!-- Replace with your logo path -->
                                        <img class="vector" src="../assets/images/uoc-football-logo.png" alt="UOC Football" style="width:40px;">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="div-4">
                            <div class="text-wrapper">UOC Football</div>
                            <div class="text-wrapper-2">Captain Portal</div>
                        </div>
                    </div>
                    <ul class="nav-2">
                        <li><a href="#" class="nav-link">Dashboard</a></li>
                        <li><a href="#" class="nav-link">Players</a></li>
                        <li><a href="#" class="nav-link">Coaches</a></li>
                        <li><a href="#" class="nav-link nav-link-active">Attendance</a></li>
                        <li><a href="#" class="nav-link">Finance</a></li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="main">
                <header class="header">
                    <div class="div-5">
                        <div class="div-6">
                            <h1 class="text-wrapper-8">Attendance Dashboard</h1>
                            <p class="view-and-update">View and update player attendance for today's session</p>
                        </div>
                        <div class="div-7">
                            <select id="session-type">
                                <option>Practice</option>
                                <option>Match</option>
                            </select>
                            <input type="date" id="session-date" value="<?php echo date('Y-m-d'); ?>">
                            <select id="team-filter">
                                <option>All Teams</option>
                            </select>
                        </div>
                    </div>
                </header>

                <!-- Stats Cards -->
                <section class="section">
                    <div class="div-8">
                        <div class="div-9">
                            <div class="text-wrapper-12">Total Players</div>
                            <div class="text-wrapper-13 total-count">6</div>
                        </div>
                    </div>
                    <div class="div-8">
                        <div class="div-12">
                            <div class="text-wrapper-14">Present Players</div>
                            <div class="text-wrapper-15 present-count">0</div>
                        </div>
                    </div>
                    <div class="div-8">
                        <div class="div-15">
                            <div class="text-wrapper-16">Absent Players</div>
                            <div class="text-wrapper-17 absent-count">6</div>
                        </div>
                    </div>
                </section>

                <!-- Player Table -->
                <section class="section-2">
                    <h2 class="text-wrapper-18">Player Attendance</h2>
                    <button class="button mark-all-present">Mark All Present</button>
                    <div class="table-wrapper">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Player</th>
                                    <th>Position</th>
                                    <th>Status</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="div-18">
                                            <div class="img-2" style="background:#8b5cf6;border-radius:50%;width:40px;height:40px;"></div>
                                            <div class="div-19">
                                                <div class="text-wrapper-20">John Smith</div>
                                                <div class="text-wrapper-21">#007</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>Forward</td>
                                    <td><span class="badge badge-absent">Absent</span></td>
                                    <td><div class="action-buttons">
                                        <button class="action-button action-button-check"></button>
                                        <button class="action-button action-button-x"></button>
                                        <button class="action-button action-button-clock"></button>
                                    </div></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="div-18">
                                            <div class="img-2" style="background:#8b5cf6;border-radius:50%;width:40px;height:40px;"></div>
                                            <div class="div-19">
                                                <div class="text-wrapper-20">Mike Johnson</div>
                                                <div class="text-wrapper-21">#010</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>Midfielder</td>
                                    <td><span class="badge badge-absent">Absent</span></td>
                                    <td><div class="action-buttons">
                                        <button class="action-button action-button-check"></button>
                                        <button class="action-button action-button-x"></button>
                                        <button class="action-button action-button-clock"></button>
                                    </div></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="div-18">
                                            <div class="img-2" style="background:#8b5cf6;border-radius:50%;width:40px;height:40px;"></div>
                                            <div class="div-19">
                                                <div class="text-wrapper-20">David Wilson</div>
                                                <div class="text-wrapper-21">#004</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>Defender</td>
                                    <td><span class="badge badge-absent">Absent</span></td>
                                    <td><div class="action-buttons">
                                        <button class="action-button action-button-check"></button>
                                        <button class="action-button action-button-x"></button>
                                        <button class="action-button action-button-clock"></button>
                                    </div></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="div-18">
                                            <div class="img-2" style="background:#8b5cf6;border-radius:50%;width:40px;height:40px;"></div>
                                            <div class="div-19">
                                                <div class="text-wrapper-20">Sarah Lee</div>
                                                <div class="text-wrapper-21">#011</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>Winger</td>
                                    <td><span class="badge badge-absent">Absent</span></td>
                                    <td><div class="action-buttons">
                                        <button class="action-button action-button-check"></button>
                                        <button class="action-button action-button-x"></button>
                                        <button class="action-button action-button-clock"></button>
                                    </div></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="div-18">
                                            <div class="img-2" style="background:#8b5cf6;border-radius:50%;width:40px;height:40px;"></div>
                                            <div class="div-19">
                                                <div class="text-wrapper-20">Alex Brown</div>
                                                <div class="text-wrapper-21">#001</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>Goalkeeper</td>
                                    <td><span class="badge badge-absent">Absent</span></td>
                                    <td><div class="action-buttons">
                                        <button class="action-button action-button-check"></button>
                                        <button class="action-button action-button-x"></button>
                                        <button class="action-button action-button-clock"></button>
                                    </div></td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="div-18">
                                            <div class="img-2" style="background:#8b5cf6;border-radius:50%;width:40px;height:40px;"></div>
                                            <div class="div-19">
                                                <div class="text-wrapper-20">Tom Harris</div>
                                                <div class="text-wrapper-21">#008</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>Striker</td>
                                    <td><span class="badge badge-absent">Absent</span></td>
                                    <td><div class="action-buttons">
                                        <button class="action-button action-button-check"></button>
                                        <button class="action-button action-button-x"></button>
                                        <button class="action-button action-button-clock"></button>
                                    </div></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </section>

                <!-- Attendance Rate & Quick Actions -->
                <aside class="section-3">
                    <div class="div-28">
                        <h2 class="text-wrapper-35">Attendance Rate</h2>
                        <div class="div-29">
                            <div class="div-30">
                                <div class="svg-7">
                                    <!-- Your full SVG chart from Anima -->
                                    <svg viewBox="0 0 302 250" xmlns="http://www.w3.org/2000/svg">
                                        <!-- Paste your full SVG code here -->
                                        <polyline fill="none" stroke="#8b5cf6" stroke-width="3" points="40,140 80,100 120,140 160,60 200,100 240,140 280,80 320,100" />
                                        <!-- Add dots and grid as in your Anima -->
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="div-31">
                        <h2 class="text-wrapper-49">Quick Actions</h2>
                        <div class="div-32">
                            <button class="button-2 save-attendance">Save Attendance</button>
                            <button class="button-3 reset-changes">Reset Changes</button>
                            <button class="button-3 export-report">Export Report</button>
                        </div>
                    </div>
                </aside>
            </main>
        </div>
    </div>
</div>

<script src="script.js"></script>
</body>
</html>