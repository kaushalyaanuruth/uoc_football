<?php

class BudgetModel
{
    use Model;
    
    protected $parent_table = 'budgets';
    protected $income_table = 'incomes';
    protected $expense_table = 'expenses';
    protected $team_table = 'teams';
    
    public function create($data)
    {
      
        $budgetQuery = "INSERT INTO $parent_table (team_id, date)
                        VALUES (:team_id, :date)";
        $this->query($budgetQuery, [
            'team_id' => $data['team_id'],
            'date' => $data['date']
        ]);

        $budgetId = $this->lastInsertId();

        if ($data['type'] === 'income') {
            $childQuery = "INSERT INTO $income_table (amount, description, image, budget_id)
                           VALUES (:amount, :description, :image, :budget_id)";
        } else {
            $childQuery = "INSERT INTO $expense_table (amount, description, image, budget_id)
                           VALUES (:amount, :description, :image, :budget_id)";
        }

        $this->query($childQuery, [
            'amount' => $data['amount'],
            'description' => $data['description'],
            'image' => $data['image'],
            'budget_id' => $budgetId
        ]);

        $this->commit();
        return true;

    }
    public function getAll()
    {
        $result = $this->query("SELECT $parent_table.*, $income_table.*, $expense_table.*
                            FROM $parent_table
                            LEFT JOIN $income_table ON $parent_table.budget_id = $income_table.budget_id
                            LEFT JOIN $expense_table ON $parent_table.budget_id = $expense_table.budget_id
                            ORDER BY $parent_table.date DESC;");
        
        return $result;
    }

    public function getIncomeTotalsByMonth()
        {
            $query = "SELECT 
                        DATE_FORMAT(b.date, '%Y-%m') AS month,
                        SUM(i.amount) AS total_income
                    FROM {$this->income_table} i
                    INNER JOIN {$this->parent_table} b
                    ON i.budget_id = b.budget_id
                    GROUP BY DATE_FORMAT(b.date, '%Y-%m')
                    ORDER BY month DESC";

            return $this->query($query);
        }

    public function getExpenseTotalsByMonth()
        {
            $query = "SELECT 
                        DATE_FORMAT(b.date, '%Y-%m') AS month,
                        SUM(e.amount) AS total_expense
                    FROM {$this->expense_table} e
                    INNER JOIN {$this->parent_table} b
                    ON e.budget_id = b.budget_id
                    GROUP BY DATE_FORMAT(b.date, '%Y-%m')
                    ORDER BY month DESC";

            return $this->query($query);
        }

    public function getExpenseTotalsByTeam()
    {
        $query = "SELECT 
                t.team_season AS team,
                COALESCE(exp.total_expense, 0) AS total_expense,
                COALESCE(inc.total_income, 0) AS total_income
            FROM {$this->team_table} t

            LEFT JOIN (
                SELECT 
                    b.team_id,
                    SUM(e.amount) AS total_expense
                FROM {$this->parent_table} b
                INNER JOIN {$this->expense_table} e 
                    ON b.budget_id = e.budget_id
                GROUP BY b.team_id
            ) exp ON t.team_id = exp.team_id

            LEFT JOIN (
                SELECT 
                    b.team_id,
                    SUM(i.amount) AS total_income
                FROM {$this->parent_table} b
                INNER JOIN {$this->income_table} i 
                    ON b.budget_id = i.budget_id
                GROUP BY b.team_id
            ) inc ON t.team_id = inc.team_id

            ORDER BY total_expense DESC";

        return $this->query($query);
    }
            
    public function updateIncome($id, $data)
        {
            $fields = [];
            $params = ['income_id' => $id];
            
            foreach ($data as $key => $value) {
                if ($key !== 'id') {
                    $fields[] = "{$key} = :{$key}";
                    $params[$key] = $value;
                }
            }
            
            $fieldsString = implode(', ', $fields);
            $query = "UPDATE {$this->income_table} SET {$fieldsString} WHERE income_id = :id";
            
            return $this->query($query, $params);
        }
    
    public function updateExpense($id, $data)
        {
            $fields = [];
            $params = ['expense_id' => $id];
            
            foreach ($data as $key => $value) {
                if ($key !== 'id') {
                    $fields[] = "{$key} = :{$key}";
                    $params[$key] = $value;
                }
            }
            
            $fieldsString = implode(', ', $fields);
            $query = "UPDATE {$this->expense_table} SET {$fieldsString} WHERE expense_id = :id";
            
            return $this->query($query, $params);
        }




    public function deleteIncome($id)
    {
        $income = $this->getById($id);
        
        if (income) {
            if (!empty($coach->image) && file_exists($coach->image)) {
                unlink($coach->image);
            }
            
            // Delete associated user account (if exists)
            try {
                $userQuery = "DELETE FROM users WHERE username = :nic AND role = 'coach'";
                $this->query($userQuery, ['nic' => $coach->nic]);
            } catch (Exception $e) {
                error_log("Failed to delete user account for coach: " . $e->getMessage());
            }
        }
        
        // Delete coach record (will also delete team_coaches relationships due to CASCADE)
        return $this->query("DELETE FROM {$this->table} WHERE id = :id", ['id' => $id]);
    }

}
