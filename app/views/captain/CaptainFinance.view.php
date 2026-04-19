<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Captain Finance Dashboard</title>
    <?php
    $noticeCount = isset($data['notices']) ? count($data['notices']) : 0;
    $noticeBadge = $noticeCount > 99 ? '99+' : (string) $noticeCount;
    ?>
    <link rel="stylesheet" href="<?= ROOT ?>/assets/css/captain/CaptainFinance.css">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            <a href="<?= ROOT ?>/captainDashboard">Home</a>
            <a href="<?= ROOT ?>/CaptainSchedule">Schedule</a>
            <a href="<?= ROOT ?>/CaptainAnalyze">Analyze</a>
            <a href="<?= ROOT ?>/CaptainAttendance">Attendance</a>
            <a href="<?= ROOT ?>/CaptainInventory">Inventory</a>
            <a href="#" class="active">Finance</a>
            <a href="<?= ROOT ?>/CaptainMealPlan">Meal Plan</a>
        </nav>

        <div class="nav-right">
            <div class="notification-icon" id="captainNotificationBell">
                <img src="<?php echo ROOT; ?>/assets/images/common/notification.png" alt="Notifications" style="width: 24px; cursor: pointer;">
                <span class="notification-count <?php echo $noticeCount > 0 ? '' : 'hidden'; ?>"><?php echo htmlspecialchars($noticeBadge); ?></span>
            </div>
            <a class="user-profile" href="<?= ROOT ?>/captainDashboard" title="Profile">
                <img src="<?php echo htmlspecialchars($data['captain_image'] ?? (ROOT . '/assets/images/adminDashboard/header/avatar.jpg')); ?>" alt="Captain Avatar">
            </a>
            <a class="player-logout-btn" href="<?= ROOT ?>/login/logout">Logout</a>
        </div>
    </header>

    <main class="content">
        <?php
            $stats = $data['stats'] ?? ['income' => 0, 'expense' => 0, 'balance' => 0];
            $metrics = $data['metrics'] ?? [
                'income_change_pct' => 0,
                'expense_change_pct' => 0,
                'monthly_growth_pct' => 0,
                'avg_monthly_surplus' => 0,
                'expense_ratio' => 0,
                'balance_message' => 'No finance data available yet.'
            ];

            $incomeChange = (float) ($metrics['income_change_pct'] ?? 0);
            $expenseChange = (float) ($metrics['expense_change_pct'] ?? 0);
            $monthlyGrowth = (float) ($metrics['monthly_growth_pct'] ?? 0);
            $avgSurplus = (float) ($metrics['avg_monthly_surplus'] ?? 0);
            $expenseRatio = (float) ($metrics['expense_ratio'] ?? 0);

            $incomeChangeLabel = ($incomeChange >= 0 ? '+' : '') . number_format($incomeChange, 1) . '% from last month';
            $expenseChangeLabel = ($expenseChange >= 0 ? '+' : '') . number_format($expenseChange, 1) . '% from last month';
            $growthLabel = ($monthlyGrowth >= 0 ? '+' : '') . number_format($monthlyGrowth, 1) . '%';
        ?>

        <!-- ================= Header ================= -->
        <div class="page-header">
            <div>
                <h1>Finance Dashboard</h1>
                <p>Manage team expenses, funds, and overall budget</p>
            </div>
            <div class="header-actions">
                
                <button class="btn-export" id="exportReport">Export Report</button>
            </div>
        </div>

        <!-- ================= Stats ================= -->
        <section class="stats">
            <div class="card stat-income">
                <div class="stat-content">

                    <div>
                        <div class="stat-title">Total Income</div>
                        <div class="stat-value">
                            LKR <?= number_format($stats['income'], 2) ?>
                        </div>
                        <small><?= htmlspecialchars($incomeChangeLabel) ?></small>
                    </div>
                    <div class="stat-icon income">
                        LKR
                    </div>
                </div>
            </div>

            <div class="card stat-expense">
                <div class="stat-content">
                    <div>
                        <div class="stat-title">Total Expenses</div>
                        <div class="stat-value">
                            LKR <?= number_format($stats['expense'], 2) ?>
                        </div>
                        <small><?= htmlspecialchars($expenseChangeLabel) ?></small>
                    </div>
                    <div class="stat-icon expense">
                    </div>
                </div>
            </div>


            <div class="card stat-balance">
                <div class="stat-content">
                    <div>
                        <div class="stat-title">Current Balance</div>
                        <div class="stat-value">
                            LKR
                            <?= number_format($stats['balance'], 2) ?>
                        </div>
                        <small><?= htmlspecialchars($metrics['balance_message'] ?? 'No finance data available yet.') ?></small>
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

            <div class="chart-wrapper">
                <canvas id="financeChart"></canvas>
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

                <?php foreach ($data['transactions'] as $t): ?>
                    <tr data-id="<?= $t->id ?>">
                        <td>
                            <?php if ($t->type === "Income"): ?>
                                <span class="badge badge-income">Income</span>
                            <?php else: ?>
                                <span class="badge badge-expense">Expense</span>
                            <?php endif; ?>
                        </td>

                        <td><?= htmlspecialchars($t->category) ?></td>

                        <td class="<?= $t->type === 'Income' ? 'amount_income' : 'amount_expense' ?>">
                            <?= $t->type === 'Income' ? '+' : '-' ?>LKR <?= number_format($t->amount, 2) ?>
                        </td>

                        <td><?= date("M d, Y", strtotime($t->date)) ?></td>

                        <td><?= htmlspecialchars($t->description) ?></td>

                        <td class="actions">
                            <button class="btn-edit">Edit</button>
                            <button class="btn-delete">Delete</button>

                        </td>
                    </tr>
                <?php endforeach; ?>
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
                    <h3 class="grow"><?= htmlspecialchars($growthLabel) ?></h3>
                    <p>Monthly Growth</p>
                </div>
                <div>
                    <h3 class="surplus">LKR <?= number_format($avgSurplus, 2) ?></h3>
                    <p>Avg Monthly Surplus</p>
                </div>
                <div>
                    <h3 class="expense"><?= number_format($expenseRatio, 1) ?>%</h3>
                    <p>Expense Ratio</p>
                </div>
            </div>
        </section>
        <!-- ================= Summary ================= -->

        <div class="balance-summary">
            <?= htmlspecialchars($metrics['balance_message'] ?? 'No finance data available yet.') ?>
        </div>

    </main>
    <!-- CENTER SUCCESS POPUP -->
    <div class="modal" id="centerToast">
        <div class="modal-content center-toast">
            <h3>Success</h3>
            <p id="centerToastMessage">Action completed successfully</p>
            <button class="save" id="centerToastOk">OK</button>
        </div>
    </div>

    <div id="captainNotificationOverlay" style="display:none; position:fixed; top:88px; right:32px; width:340px; max-height:420px; overflow:auto; background:#fff; border-radius:12px; box-shadow:0 12px 30px rgba(0,0,0,0.18); padding:14px; z-index:1200; border:1px solid #ece7f3;">
        <?php foreach (array_slice($data['notices'] ?? [], 0, 5) as $notice): ?>
            <div style="padding-bottom:10px; margin-bottom:10px; border-bottom:1px solid #eee;">
                <p style="font-size:0.86rem; color:#4a1150; font-weight:600; margin-bottom:4px;"><?php echo htmlspecialchars($notice['title'] ?? 'Notice'); ?></p>
                <p style="font-size:0.82rem; color:#4b5563; line-height:1.35;"><?php echo htmlspecialchars($notice['content'] ?? ''); ?></p>
            </div>
        <?php endforeach; ?>
    </div>

</body>
<script>
    window.APP_ROOT = "<?= ROOT ?>";
    window.HEADER_PROFILE_MODAL_CONFIG = {
        fetchUrl: '<?= ROOT ?>/captainDashboard/profileData',
        updateUrl: '<?= ROOT ?>/captainDashboard/updateProfile',
        triggerSelector: '.user-profile'
    };

    const captainBell = document.getElementById('captainNotificationBell');
    const captainOverlay = document.getElementById('captainNotificationOverlay');

    captainBell.addEventListener('click', (e) => {
        e.stopPropagation();
        captainOverlay.style.display = captainOverlay.style.display === 'none' ? 'block' : 'none';
    });

    document.addEventListener('click', (e) => {
        if (!captainOverlay.contains(e.target) && e.target !== captainBell && !captainBell.contains(e.target)) {
            captainOverlay.style.display = 'none';
        }
    });
</script>

<script src="<?= ROOT ?>/assets/js/common/headerProfileModal.js"></script>
<script src="<?= ROOT ?>/assets/js/captain/CaptainFinance.js"></script>

</html>