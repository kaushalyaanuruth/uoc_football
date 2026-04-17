<?php

class FinanceModel
{
    use Model;
    private $columnCache = [];

    private function getColumns($table)
    {
        if (isset($this->columnCache[$table])) {
            return $this->columnCache[$table];
        }

        $this->columnCache[$table] = [];
        $rows = $this->query("SHOW COLUMNS FROM {$table}");

        foreach ($rows as $row) {
            if (isset($row->Field)) {
                $this->columnCache[$table][$row->Field] = true;
            }
        }

        return $this->columnCache[$table];
    }

    private function hasColumn($table, $column)
    {
        $columns = $this->getColumns($table);
        return isset($columns[$column]);
    }

    private function ensureBudgetForDate($date)
    {
        $budgetRows = $this->query(
            "SELECT budget_id FROM budgets WHERE date = :date ORDER BY budget_id DESC LIMIT 1",
            ['date' => $date]
        );

        if (!empty($budgetRows)) {
            return (int) $budgetRows[0]->budget_id;
        }

        $teamRows = $this->query("SELECT team_id FROM teams ORDER BY team_id ASC LIMIT 1");

        if (empty($teamRows)) {
            $season = date('Y');
            $this->query("INSERT INTO teams (season) VALUES (:season)", ['season' => $season]);
            $teamRows = $this->query("SELECT team_id FROM teams ORDER BY team_id DESC LIMIT 1");
        }

        $teamId = (int) $teamRows[0]->team_id;

        $this->query(
            "INSERT INTO budgets (team_id, date) VALUES (:team_id, :date)",
            [
                'team_id' => $teamId,
                'date' => $date
            ]
        );

        $created = $this->query("SELECT budget_id FROM budgets ORDER BY budget_id DESC LIMIT 1");
        return (int) $created[0]->budget_id;
    }

    private function buildDescriptionWithCategory($description, $category)
    {
        $description = trim((string) $description);
        $category = trim((string) $category);

        if ($category === '') {
            return $description;
        }

        if (preg_match('/^\[(.*?)\]\s*/', $description)) {
            return $description;
        }

        return '[' . $category . '] ' . $description;
    }

    private function selectCategoryExpression($tableAlias)
    {
        if ($this->hasColumn(str_replace('.', '', $tableAlias), 'category')) {
            return "{$tableAlias}.category";
        }

        return "COALESCE(
            TRIM(BOTH ']' FROM SUBSTRING_INDEX(SUBSTRING_INDEX({$tableAlias}.description, ']', 1), '[', -1)),
            'General'
        )";
    }

    /* ================= GET ALL TRANSACTIONS ================= */
    public function getAllTransactions()
    {
        $incomeCategoryExpr = $this->hasColumn('incomes', 'category')
            ? "i.category"
            : "CASE
                WHEN i.description LIKE '[%] %' THEN TRIM(BOTH ']' FROM SUBSTRING_INDEX(SUBSTRING_INDEX(i.description, ']', 1), '[', -1))
                ELSE 'General'
            END";

        $expenseCategoryExpr = $this->hasColumn('expenses', 'category')
            ? "e.category"
            : "CASE
                WHEN e.description LIKE '[%] %' THEN TRIM(BOTH ']' FROM SUBSTRING_INDEX(SUBSTRING_INDEX(e.description, ']', 1), '[', -1))
                ELSE 'General'
            END";

        $incomeDateExpr = $this->hasColumn('incomes', 'date') ? "i.date" : "b.date";
        $expenseDateExpr = $this->hasColumn('expenses', 'date') ? "e.date" : "b.date";

        $query = "
            SELECT
                'Income' AS type,
                i.income_id AS id,
                i.description,
                i.amount,
                {$incomeDateExpr} AS date,
                {$incomeCategoryExpr} AS category
            FROM incomes i
            LEFT JOIN budgets b ON b.budget_id = i.budget_id

            UNION ALL

            SELECT
                'Expense' AS type,
                e.expense_id AS id,
                e.description,
                e.amount,
                {$expenseDateExpr} AS date,
                {$expenseCategoryExpr} AS category
            FROM expenses e
            LEFT JOIN budgets b ON b.budget_id = e.budget_id

            ORDER BY date DESC
        ";

        return $this->query($query);
    }

    /* ================= GET STATS ================= */
    public function getStats()
    {
        $query = "
            SELECT 
                COALESCE((SELECT SUM(amount) FROM incomes), 0) AS total_income,
                COALESCE((SELECT SUM(amount) FROM expenses), 0) AS total_expense
        ";

        $result = $this->query($query)[0];

        $result->balance = $result->total_income - $result->total_expense;

        return $result;
    }
    public function getMonthlyStats()
    {
        $incomeDateExpr = $this->hasColumn('incomes', 'date') ? "i.date" : "b.date";
        $expenseDateExpr = $this->hasColumn('expenses', 'date') ? "e.date" : "b.date";

        $query = "
    SELECT 
    DATE_FORMAT(MIN(date), '%b') as month,
    YEAR(date) as year,
    MONTH(date) as month_num,
    SUM(CASE WHEN type='income' THEN amount ELSE 0 END) as income,
    SUM(CASE WHEN type='expense' THEN amount ELSE 0 END) as expense
FROM (
    SELECT 'income' as type, i.amount, {$incomeDateExpr} AS date
    FROM incomes i
    LEFT JOIN budgets b ON b.budget_id = i.budget_id

    UNION ALL
    SELECT 'expense' as type, e.amount, {$expenseDateExpr} AS date
    FROM expenses e
    LEFT JOIN budgets b ON b.budget_id = e.budget_id
) t
WHERE date IS NOT NULL
GROUP BY YEAR(date), MONTH(date)
ORDER BY year, month_num
    ";

        return $this->query($query);
    }
    /* ================= ADD INCOME ================= */
    public function addIncome($data)
    {
        $insertColumns = ['description', 'amount', 'budget_id'];
        $insertValues = [':description', ':amount', ':budget_id'];

        $params = [
            'description' => $this->buildDescriptionWithCategory($data['description'] ?? '', $data['category'] ?? ''),
            'amount' => $data['amount'],
            'budget_id' => $this->ensureBudgetForDate($data['date'])
        ];

        if ($this->hasColumn('incomes', 'date')) {
            $insertColumns[] = 'date';
            $insertValues[] = ':date';
            $params['date'] = $data['date'];
        }

        if ($this->hasColumn('incomes', 'category')) {
            $insertColumns[] = 'category';
            $insertValues[] = ':category';
            $params['category'] = $data['category'] ?? 'General';
        }

        $query = "INSERT INTO incomes (" . implode(', ', $insertColumns) . ") VALUES (" . implode(', ', $insertValues) . ")";

        return $this->query($query, $params);
    }

    /* ================= ADD EXPENSE ================= */
    public function addExpense($data)
    {
        $insertColumns = ['description', 'amount', 'budget_id'];
        $insertValues = [':description', ':amount', ':budget_id'];

        $params = [
            'description' => $this->buildDescriptionWithCategory($data['description'] ?? '', $data['category'] ?? ''),
            'amount' => $data['amount'],
            'budget_id' => $this->ensureBudgetForDate($data['date'])
        ];

        if ($this->hasColumn('expenses', 'date')) {
            $insertColumns[] = 'date';
            $insertValues[] = ':date';
            $params['date'] = $data['date'];
        }

        if ($this->hasColumn('expenses', 'category')) {
            $insertColumns[] = 'category';
            $insertValues[] = ':category';
            $params['category'] = $data['category'] ?? 'General';
        }

        $query = "INSERT INTO expenses (" . implode(', ', $insertColumns) . ") VALUES (" . implode(', ', $insertValues) . ")";

        return $this->query($query, $params);
    }

    /* ================= DELETE ================= */
    public function deleteTransaction($type, $id)
    {
        if ($type === "Income") {
            $query = "DELETE FROM incomes WHERE income_id = :id";
        } else {
            $query = "DELETE FROM expenses WHERE expense_id = :id";
        }

        return $this->query($query, ['id' => $id]);
    }
    public function updateTransaction($type, $id, $data)
    {
        $budgetId = $this->ensureBudgetForDate($data['date']);

        if ($type === "Income") {
            $set = [
                'amount = :amount',
                'description = :description',
                'budget_id = :budget_id'
            ];

            if ($this->hasColumn('incomes', 'category')) {
                $set[] = 'category = :category';
            }

            if ($this->hasColumn('incomes', 'date')) {
                $set[] = 'date = :date';
            }

            $query = "UPDATE incomes SET " . implode(', ', $set) . " WHERE income_id = :id";
        } else {
            $set = [
                'amount = :amount',
                'description = :description',
                'budget_id = :budget_id'
            ];

            if ($this->hasColumn('expenses', 'category')) {
                $set[] = 'category = :category';
            }

            if ($this->hasColumn('expenses', 'date')) {
                $set[] = 'date = :date';
            }

            $query = "UPDATE expenses SET " . implode(', ', $set) . " WHERE expense_id = :id";
        }

        $params = [
            'id' => $id,
            'amount' => $data['amount'],
            'description' => $this->buildDescriptionWithCategory($data['description'] ?? '', $data['category'] ?? ''),
            'budget_id' => $budgetId
        ];

        if ($type === "Income") {
            if ($this->hasColumn('incomes', 'category')) {
                $params['category'] = $data['category'] ?? 'General';
            }
            if ($this->hasColumn('incomes', 'date')) {
                $params['date'] = $data['date'];
            }
        } else {
            if ($this->hasColumn('expenses', 'category')) {
                $params['category'] = $data['category'] ?? 'General';
            }
            if ($this->hasColumn('expenses', 'date')) {
                $params['date'] = $data['date'];
            }
        }

        return $this->query($query, $params);
    }
}
