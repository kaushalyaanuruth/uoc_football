<?php

class FinanceModel
{
    use Model;

    /* ================= GET ALL TRANSACTIONS ================= */
    public function getAllTransactions()
    {
        $query = "
            SELECT 'Income' AS type, income_id AS id, description, amount, date, category 
            FROM incomes

            UNION ALL

            SELECT 'Expense' AS type, expense_id AS id, description, amount, date, category 
            FROM expenses

            ORDER BY date DESC
        ";

        return $this->query($query);
    }

    /* ================= GET STATS ================= */
    public function getStats()
    {
        $query = "
            SELECT 
                (SELECT SUM(amount) FROM incomes) AS total_income,
                (SELECT SUM(amount) FROM expenses) AS total_expense
        ";

        $result = $this->query($query)[0];

        $result->balance = $result->total_income - $result->total_expense;

        return $result;
    }
    public function getMonthlyStats()
    {
        $query = "
    SELECT 
    DATE_FORMAT(MIN(date), '%b') as month,
    YEAR(date) as year,
    MONTH(date) as month_num,
    SUM(CASE WHEN type='income' THEN amount ELSE 0 END) as income,
    SUM(CASE WHEN type='expense' THEN amount ELSE 0 END) as expense
FROM (
    SELECT 'income' as type, amount, date FROM incomes
    UNION ALL
    SELECT 'expense' as type, amount, date FROM expenses
) t
GROUP BY YEAR(date), MONTH(date)
ORDER BY year, month_num
    ";

        return $this->query($query);
    }
    /* ================= ADD INCOME ================= */
    public function addIncome($data)
    {
        $query = "INSERT INTO incomes 
        (description, amount, date, category, budget_id)
        VALUES (:description, :amount, :date, :category, :budget_id)";

        return $this->query($query, $data);
    }

    /* ================= ADD EXPENSE ================= */
    public function addExpense($data)
    {
        $query = "INSERT INTO expenses 
        (description, amount, date, category, budget_id)
        VALUES (:description, :amount, :date, :category, :budget_id)";

        return $this->query($query, $data);
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
        if ($type === "Income") {
            $query = "UPDATE incomes 
                  SET category=:category, amount=:amount, date=:date, description=:description 
                  WHERE income_id=:id";
        } else {
            $query = "UPDATE expenses 
                  SET category=:category, amount=:amount, date=:date, description=:description 
                  WHERE expense_id=:id";
        }

        $data['id'] = $id;

        return $this->query($query, $data);
    }
}
