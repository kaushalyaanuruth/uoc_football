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
            <a href="<?= ROOT ?>/captainDashboard">
                <img class="header-logo" src="<?= ROOT ?>/assets/images/adminDashboard/header/uoclogo.png"
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
                <img class="avatar" src="<?= ROOT ?>../assets/images/adminDashboard/header/avatar.jpg"
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
                    <option>This Quarter</option>

                </select>
<button class="btn-export" id="exportReport">Export Report</button>
            </div>
        </div>

        <!-- ================= Stats ================= -->
        <section class="stats">
            <div class="card stat-income">
                <div class="stat-content">

                    <div>
                        <div class="stat-title">Total Income</div>
                        <div class="stat-value">$45,250</div>
                        <small>+12.5% from last month</small>
                    </div>
                    <div class="stat-icon income">
                        $
                    </div>
                </div>
            </div>

            <div class="card stat-expense">
                <div class="stat-content">
                    <div>
                        <div class="stat-title">Total Expenses</div>
                        <div class="stat-value">$32,180</div>
                        <small>+8.2% from last month</small>
                    </div>
                    <div class="stat-icon expense">
                    </div>
                </div>
            </div>


            <div class="card stat-balance">
                <div class="stat-content">
                    <div>
                        <div class="stat-title">Current Balance</div>
                        <div class="stat-value">$13,070</div>
                        <small>Healthy balance</small>
                    </div>
                    <div class="stat-icon balance">

                    </div>
                </div>
            </div>
        </section>

        <!-- Budget Overview -->
        <section class="finance-card">
            <div class="chart-header">
                <h2>Budget Overview</h2>
                <div class="chart-toggle">
                    <button class="active" data-view="monthly">Monthly</button>
                    <button data-view="quarterly">Quarterly</button>
                </div>

            </div>

            <div class="bar-chart">

                <?php
                $months = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct"];
                foreach ($months as $m) {
                    $income = rand(60, 100);
                    $expense = rand(30, 70);

                    echo "
    <div class='month monthly'>
        <div class='bars'>
            <div class='bar income' data-month='$income'></div>
            <div class='bar expense' data-month='$expense'></div>
        </div>
        <span>$m</span>
    </div>
    ";
                }
                ?>

                <!-- Quarterly -->
                <div class="month quarterly" style="display:none">
                    <div class="bars">
                        <div class="bar income" data-quarter="80"></div>
                        <div class="bar expense" data-quarter="50"></div>
                    </div>
                    <span>Q1</span>
                </div>

                <div class="month quarterly" style="display:none">
                    <div class="bars">
                        <div class="bar income" data-quarter="90"></div>
                        <div class="bar expense" data-quarter="60"></div>
                    </div>
                    <span>Q2</span>
                </div>

                <div class="month quarterly" style="display:none">
                    <div class="bars">
                        <div class="bar income" data-quarter="85"></div>
                        <div class="bar expense" data-quarter="55"></div>
                    </div>
                    <span>Q3</span>
                </div>

                <div class="month quarterly" style="display:none">
                    <div class="bars">
                        <div class="bar income" data-quarter="95"></div>
                        <div class="bar expense" data-quarter="65"></div>
                    </div>
                    <span>Q4</span>
                </div>

            </div>

        </section>

        <!-- ================= Forms ================= -->
        <section class="form-grid">

            <!-- Income Form -->
            <div class="finance-card">
                <h2><span class="finance-icon green">+</span>Record Income</h2>

                <form class="finance-form">
                    <label>Source Name</label>
                    <input type="text" placeholder="Sponsorship, Fundraising" required />

                    <label>Amount</label>
                    <input type="number" placeholder="5000" min="0" required />

                    <label>Date</label>
                    <input type="date" required />

                    <label>Description</label>
                    <textarea placeholder="Optional notes"></textarea>

                    <button class="btn-income">Add Income</button>
                </form>
            </div>

            <!-- Expense Form -->
            <div class="finance-card">
                <h2><span class="finance-icon red">-</span>Record Expense</h2>

                <form class="finance-form">
                    <label>Expense Type</label>
                    <select>
                        <option>Equipment</option>
                        <option>Travel</option>
                        <option>Food</option>
                    </select>

                    <label>Amount</label>
                    <input type="number" placeholder="1200" min="0" required />

                    <label>Date</label>
                    <input type="date" required />

                    <label>Notes</label>
                    <textarea placeholder="Optional notes"></textarea>

                    <button class="btn-expense">Add Expense</button>
                </form>
            </div>
        </section>

        <!-- ================= Table ================= -->
        <section class="finance-card">

            <h2 class="finance-history">Transaction History
                <div class="finance-charttoggle">
                    <button class="">All</button>
                    <button class="finance_income">Income</button>
                    <button class="finance_expense">Expense</button>
                </div>
            </h2>


            <table class="finance-table">
                <tr>
                    <th>Type</th>
                    <th>Category</th>
                    <th>Amount</th>
                    <th>Date</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
                <tr>
                    <td><span class="badge badge-income"> Income</span></td>
                    <td>Sponsorship</td>
                    <td class="amount_icome">+$5,000</td>
                    <td>Oct 15, 2024</td>
                    <td>Nike Partnership</td>
                    <td class="actions">
                        <button class="btn-edit">Edit</button>
                        <button class="btn-delete">Delete</button>
                    </td>

                </tr>
                <tr>
                    <td><span class="badge badge-expense"> Expense</span></td>
                    <td>Equipment</td>
                    <td class="amount_expense">-$1,250</td>
                    <td>Oct 12, 2024</td>
                    <td>Training gear</td>
                    <td class="actions">
                        <button class="btn-edit">Edit</button>
                        <button class="btn-delete">Delete</button>
                    </td>

                </tr>
            </table>
        </section>
        <!-- ================= EDIT TRANSACTION MODAL ================= -->
        <div class="modal" id="financeModal">
            <div class="modal-content">
                <div class="modal-header">
                    <span>Edit Transaction</span>
                    <span id="closeFinanceModal">×</span>
                </div>

                <form id="financeEditForm">
                    <input type="hidden" id="editRowIndex">

                    <label>Type</label>
                    <select id="editType">
                        <option>Income</option>
                        <option>Expense</option>
                    </select>

                    <label>Category</label>
                    <input type="text" id="editCategory" required>

                    <label>Amount</label>
                    <input type="number" id="editAmount" min="0" required>

                    <label>Date</label>
                    <input type="date" id="editDate" required>

                    <label>Description</label>
                    <textarea id="editDescription"></textarea>

                    <div class="modal-actions">
                        <button type="button" id="cancelFinanceEdit">Cancel</button>
                        <button type="submit" class="save">Save</button>
                    </div>
                </form>
            </div>
        </div>


        <!-- Cash Flow -->
        <section class="finance-card">
            <h2>Cash Flow Trend</h2>
            <div class=" cashflow">
                <div>
                    <h3 class="grow">+15%</h3>
                    <p>Monthly Growth</p>
                </div>
                <div>
                    <h3 class="surplus">$2,890</h3>
                    <p>Avg Monthly Surplus</p>
                </div>
                <div>
                    <h3 class="expense">71%</h3>
                    <p>Expense Ratio</p>
                </div>
            </div>
        </section>
        <!-- ================= Summary ================= -->

        <div class="balance-summary">
            ✅ This month’s balance is up by 15%. Great financial management!
        </div>

    </main>
    <!-- SUCCESS TOAST -->
    <!-- <div id="toast" class="toast">
    <span id="toastMessage">Saved successfully</span>
</div> -->
    <!-- CENTER SUCCESS POPUP -->
    <div class="modal" id="centerToast">
        <div class="modal-content center-toast">
            <h3>Success</h3>
            <p id="centerToastMessage">Action completed successfully</p>
            <button class="save" id="centerToastOk">OK</button>
        </div>
    </div>

</body>
<script>
    window.APP_ROOT = "<?= ROOT ?>";
</script>

<script src="<?= ROOT ?>/assets/js/captain/CaptainFinance.js"></script>

</html>