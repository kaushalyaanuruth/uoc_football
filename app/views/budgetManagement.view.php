<?php
$transactions = $data['transactions'] ?? [];
$summary = $data['summary'] ?? [];

$totalIncome = (float) ($summary['totalIncome'] ?? 0);
$totalExpense = (float) ($summary['totalExpense'] ?? 0);
$netFlow = (float) ($summary['netFlow'] ?? 0);
$seasonTotals = $summary['seasonTotals'] ?? [];
$monthWise = $summary['monthWise'] ?? [];
$categoryWise = $summary['categoryWise'] ?? [];

function budgetMoney($amount)
{
    return 'LKR ' . number_format((float) $amount, 2);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@400" rel="stylesheet">
    <title>UOC Football - Budget Management</title>
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/budgetManagement/style.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/potal/header.css">
    <link rel="stylesheet" href="<?php echo ROOT; ?>/assets/css/potal/page.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="left-section">
                <a href="<?php echo ROOT; ?>/admin">
                    <img class="header-logo" src="<?php echo ROOT; ?>/assets/images/adminDashboard/header/uoclogo.png" alt="UOC Football Logo">
                </a>
            </div>
            <div class="right-section">
                <img class="avatar" src="<?php echo ROOT; ?>/assets/images/adminDashboard/header/avatar.jpg" alt="Admin Avatar">
            </div>
        </div>

        <a href="<?php echo ROOT; ?>/inventoryManagement" class="back-btn">&lt; Back</a>

        <section class="stats-grid">
            <article class="stat-card income">
                <p class="stat-title">Total Income</p>
                <h3 class="stat-value"><?php echo htmlspecialchars(budgetMoney($totalIncome), ENT_QUOTES, 'UTF-8'); ?></h3>
            </article>
            <article class="stat-card expense">
                <p class="stat-title">Total Expense</p>
                <h3 class="stat-value"><?php echo htmlspecialchars(budgetMoney($totalExpense), ENT_QUOTES, 'UTF-8'); ?></h3>
            </article>
            <article class="stat-card net <?php echo $netFlow >= 0 ? 'positive' : 'negative'; ?>">
                <p class="stat-title">Net Flow (Season Total)</p>
                <h3 class="stat-value"><?php echo htmlspecialchars(budgetMoney($netFlow), ENT_QUOTES, 'UTF-8'); ?></h3>
            </article>
        </section>

        <section class="toolbar-card">
            <div class="toolbar-left">
                <button class="add-entry-btn" type="button" id="openEntryModalBtn">+ Add Entry</button>
            </div>
            <div class="toolbar-right">
                <div class="search-wrap">
                    <span class="search-icon material-symbols-outlined" aria-hidden="true">search</span>
                    <input type="text" id="entrySearch" class="search-input" placeholder="Search by description/category...">
                </div>
                <select id="typeFilter" class="type-filter" aria-label="Filter by type">
                    <option value="all">All Types</option>
                    <option value="income">Income</option>
                    <option value="expense">Expense</option>
                </select>
            </div>
        </section>

        <section class="budget-panel">
            <div class="budget-panel-head">
                <h2>Budget Entries</h2>
            </div>
            <div class="budget-list">
                <div class="budget-list-head">
                    <span>Date</span>
                    <span>Type</span>
                    <span>Category</span>
                    <span>Description</span>
                    <span>Season</span>
                    <span>Amount</span>
                    <span>Actions</span>
                </div>
                <div id="budgetListBody">
                    <?php foreach ($transactions as $item): ?>
                        <div class="budget-item" data-id="<?php echo (int) $item['id']; ?>" data-type="<?php echo htmlspecialchars($item['type'], ENT_QUOTES, 'UTF-8'); ?>" data-search="<?php echo htmlspecialchars(strtolower(($item['description'] ?? '') . ' ' . ($item['category'] ?? '')), ENT_QUOTES, 'UTF-8'); ?>">
                            <span><?php echo htmlspecialchars(date('M d, Y', strtotime($item['date'])), ENT_QUOTES, 'UTF-8'); ?></span>
                            <span class="type-badge type-<?php echo htmlspecialchars($item['type'], ENT_QUOTES, 'UTF-8'); ?>"><?php echo ucfirst(htmlspecialchars($item['type'], ENT_QUOTES, 'UTF-8')); ?></span>
                            <span><?php echo htmlspecialchars($item['category'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <span class="entry-description"><?php echo htmlspecialchars($item['description'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <span><?php echo htmlspecialchars($item['season'], ENT_QUOTES, 'UTF-8'); ?></span>
                            <span class="amount <?php echo $item['type'] === 'income' ? 'amount-income' : 'amount-expense'; ?>">
                                <?php echo htmlspecialchars(budgetMoney((float) $item['amount']), ENT_QUOTES, 'UTF-8'); ?>
                            </span>
                            <div class="row-actions">
                                <button type="button" class="icon-btn edit-btn" data-id="<?php echo (int) $item['id']; ?>" aria-label="Edit entry">
                                    <span class="material-symbols-outlined" aria-hidden="true">edit</span>
                                </button>
                                <button type="button" class="icon-btn delete-btn" data-id="<?php echo (int) $item['id']; ?>" aria-label="Delete entry">
                                    <span class="material-symbols-outlined" aria-hidden="true">delete</span>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php if (empty($transactions)): ?>
                    <div class="empty-state" id="emptyState">No budget entries found. Add your first transaction.</div>
                <?php endif; ?>
            </div>
        </section>

        <section class="summary-grid">
            <article class="summary-card">
                <div class="summary-head">
                    <h3>Month Wise Summary</h3>
                </div>
                <div class="summary-table-wrap">
                    <table class="summary-table">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>Income</th>
                                <th>Expense</th>
                                <th>Net</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($monthWise)): ?>
                                <?php foreach ($monthWise as $row): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['monthLabel'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars(budgetMoney($row['income']), ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars(budgetMoney($row['expense']), ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td class="<?php echo $row['net'] >= 0 ? 'net-positive' : 'net-negative'; ?>"><?php echo htmlspecialchars(budgetMoney($row['net']), ENT_QUOTES, 'UTF-8'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4">No monthly summary available.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </article>

            <article class="summary-card">
                <div class="summary-head">
                    <h3>Category Wise Summary</h3>
                </div>
                <div class="summary-table-wrap">
                    <table class="summary-table">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Income</th>
                                <th>Expense</th>
                                <th>Net</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($categoryWise)): ?>
                                <?php foreach ($categoryWise as $row): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['category'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars(budgetMoney($row['income']), ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars(budgetMoney($row['expense']), ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td class="<?php echo $row['net'] >= 0 ? 'net-positive' : 'net-negative'; ?>"><?php echo htmlspecialchars(budgetMoney($row['net']), ENT_QUOTES, 'UTF-8'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4">No category summary available.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </article>

            <article class="summary-card">
                <div class="summary-head">
                    <h3>Total By Season</h3>
                </div>
                <div class="summary-table-wrap">
                    <table class="summary-table">
                        <thead>
                            <tr>
                                <th>Season</th>
                                <th>Income</th>
                                <th>Expense</th>
                                <th>Net</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($seasonTotals)): ?>
                                <?php foreach ($seasonTotals as $row): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['season'], ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars(budgetMoney($row['income']), ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td><?php echo htmlspecialchars(budgetMoney($row['expense']), ENT_QUOTES, 'UTF-8'); ?></td>
                                        <td class="<?php echo $row['net'] >= 0 ? 'net-positive' : 'net-negative'; ?>"><?php echo htmlspecialchars(budgetMoney($row['net']), ENT_QUOTES, 'UTF-8'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4">No season totals available.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </article>
        </section>
    </div>

    <div class="modal-overlay" id="entryModal">
        <form class="modal" id="entryForm">
            <button type="button" class="close-modal-btn" id="closeEntryModalBtn">&times;</button>
            <div class="modal-header">
                <img class="logo" src="<?php echo ROOT; ?>/assets/images/adminDashboard/header/uoclogo.png" alt="UOC Football Logo">
            </div>
            <h2 class="modal-title" id="modalTitle">Add Budget Entry</h2>
            <div class="modal-body">
                <input type="hidden" id="entryId" name="id">

                <div class="form-group two-col">
                    <div>
                        <label class="input-label" for="entryDate">Date</label>
                        <input type="date" class="form-input" id="entryDate" name="date" required>
                    </div>
                    <div>
                        <label class="input-label" for="entryType">Type</label>
                        <select class="form-input" id="entryType" name="type" required>
                            <option value="income">Income</option>
                            <option value="expense">Expense</option>
                        </select>
                    </div>
                </div>

                <div class="form-group two-col">
                    <div>
                        <label class="input-label" for="entryCategory">Category</label>
                        <input type="text" class="form-input" id="entryCategory" name="category" placeholder="Sponsorship, Equipment, Travel..." required>
                    </div>
                    <div>
                        <label class="input-label" for="entryAmount">Amount (LKR)</label>
                        <input type="number" class="form-input" id="entryAmount" name="amount" min="0.01" step="0.01" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="input-label" for="entryDescription">Description</label>
                    <input type="text" class="form-input" id="entryDescription" name="description" required>
                </div>

                <div class="form-group">
                    <label class="input-label" for="entrySeason">Season</label>
                    <input type="text" class="form-input" id="entrySeason" name="season" placeholder="2025/2026" required>
                </div>

                <p class="form-message" id="formMessage" aria-live="polite"></p>
                <div class="form-actions">
                    <button type="button" class="secondary-btn" id="cancelEntryModalBtn">Cancel</button>
                    <button type="submit" class="submit-btn" id="entrySubmitBtn">Save Entry</button>
                </div>
            </div>
        </form>
    </div>

    <script>
        window.BUDGET_MANAGEMENT_CONFIG = {
            root: "<?php echo ROOT; ?>"
        };
    </script>
    <script src="<?php echo ROOT; ?>/assets/js/budgetManagement/script.js"></script>
</body>
</html>
