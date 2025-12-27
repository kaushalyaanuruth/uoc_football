<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Captain Finance Dashboard</title>
    <link rel="stylesheet" href="<?= ROOT ?>/assets/css/captain/CaptainFinance.css">
</head>

<body>

    <!-- ================= TOP NAVBAR ================= -->
    <header class="top-navbar">
        <div class="nav-left">
            <a href="<?php echo ROOT; ?>/captain/dashboard">
                <img class="header-logo" src="<?php echo ROOT; ?>../assets/images/adminDashboard/header/uoclogo.png"
                    alt="UOC Football Logo">
            </a>
        </div>

        <nav class="nav-center">
            <a href="<?= ROOT ?>/CaptainDashboard">Home</a>
            <a href="<?= ROOT ?>/captain/dashboard">Schedule</a>
            <a href="<?= ROOT ?>/CaptainAnalyze">Analyze</a>
            <a href="<?= ROOT ?>/CaptainAttendance">Attendance</a>
            <a href="<?= ROOT ?>/CaptainInventory">Inventory</a>
            <a href="#" class="active">Finance</a>

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

        <!-- ================= Header ================= -->
        <div class="page-header">
            <div>
                <h1>Finance Dashboard</h1>
                <p>Manage team expenses, funds, and overall budget</p>
            </div>
            <div class="header-actions">
                <select>
                    <option>This Month</option>
                    <option>Last Month</option>
                </select>
                <button class="btn-export">Export Report</button>
            </div>
        </div>

        <!-- ================= Stats ================= -->
        <section class="stats">
            <div class="card stat-income">
                <div class="stat-title">Total Income</div>
                <div class="stat-value">$45,250</div>
                <small>+12.5% from last month</small>
            </div>

            <div class="card stat-expense">
                <div class="stat-title">Total Expenses</div>
                <div class="stat-value">$32,180</div>
                <small>+8.2% from last month</small>
            </div>

            <div class="card stat-balance">
                <div class="stat-title">Current Balance</div>
                <div class="stat-value">$13,070</div>
                <small>Healthy balance</small>
            </div>
        </section>

        <!-- Budget Overview -->
        <section class="finance-card">
            <div class="chart-header">
                <h2>Budget Overview</h2>
                <div class="chart-toggle">
                    <button class="active">Monthly</button>
                    <button>Quarterly</button>
                </div>
            </div>

            <div class="bar-chart">
                <?php
                $months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct"];
                foreach ($months as $m) {
                    echo "
      <div class='month'>
        <div class='bars'>
          <div class='bar income' style='height: " . rand(60, 100) . "%'></div>
          <div class='bar expense' style='height: " . rand(30, 70) . "%'></div>
        </div>
        <span>$m</span>
      </div>";
                }
                ?>
            </div>
        </section>

        <!-- ================= Forms ================= -->
        <section class="form-grid">

            <!-- Income Form -->
            <div class="finance-card">
                <h2>Record Income</h2>

                <form class="finance-form">
                    <label>Source Name</label>
                    <input type="text" placeholder="Sponsorship, Fundraising" />

                    <label>Amount</label>
                    <input type="number" placeholder="5000" />

                    <label>Date</label>
                    <input type="date" />

                    <label>Description</label>
                    <textarea placeholder="Optional notes"></textarea>

                    <button class="btn-income">Add Income</button>
                </form>
            </div>

            <!-- Expense Form -->
            <div class="finance-card">
                <h2>Record Expense</h2>

                <form class="finance-form">
                    <label>Expense Type</label>
                    <select>
                        <option>Equipment</option>
                        <option>Travel</option>
                        <option>Food</option>
                    </select>

                    <label>Amount</label>
                    <input type="number" placeholder="1200" />

                    <label>Date</label>
                    <input type="date" />

                    <label>Notes</label>
                    <textarea placeholder="Optional notes"></textarea>

                    <button class="btn-expense">Add Expense</button>
                </form>
            </div>
        </section>

        <!-- ================= Table ================= -->
        <section class="finance-card">
            <h2>Transaction History</h2>
            <table>
                <tr>
                    <th>Type</th>
                    <th>Category</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
                <tr>
                    <td><span class="badge badge-income">Income</span></td>
                    <td>Sponsorship</td>
                    <td>$5,000</td>
                    <td>Oct 15, 2024</td>
                    <td>Nike Partnership</td>
                    <td>✏️ 🗑</td>
                </tr>
                <tr>
                    <td><span class="badge badge-expense">Expense</span></td>
                    <td>Equipment</td>
                    <td>$1,250</td>
                    <td>Oct 12, 2024</td>
                    <td>Training gear</td>
                    <td>✏️ 🗑</td>
                </tr>
            </table>
        </section>

        <!-- Cash Flow -->
        <section class="card cashflow">
            <div>
                <h3>+15%</h3>
                <p>Monthly Growth</p>
            </div>
            <div>
                <h3>$2,890</h3>
                <p>Avg Monthly Surplus</p>
            </div>
            <div>
                <h3>71%</h3>
                <p>Expense Ratio</p>
            </div>
        </section>
        <!-- ================= Summary ================= -->

        <div class="balance-summary">
            ✅ This month’s balance is up by 15%. Great financial management!
        </div>

    </main>
</body>

</html>