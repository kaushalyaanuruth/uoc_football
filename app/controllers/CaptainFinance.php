<?php
class CaptainFinance extends Controller
{
    private function pctChange($current, $previous)
    {
        $current = (float) $current;
        $previous = (float) $previous;

        if ($previous == 0.0) {
            if ($current == 0.0) {
                return 0.0;
            }
            return 100.0;
        }

        return (($current - $previous) / abs($previous)) * 100;
    }

    private function buildMetrics($monthlyStats, $stats)
    {
        $currentYear = (int) date('Y');
        $currentMonth = (int) date('n');
        $previousMonth = $currentMonth === 1 ? 12 : $currentMonth - 1;
        $previousYear = $currentMonth === 1 ? $currentYear - 1 : $currentYear;

        $currentIncome = 0.0;
        $currentExpense = 0.0;
        $previousIncome = 0.0;
        $previousExpense = 0.0;

        $totalSurplus = 0.0;
        $monthCount = 0;

        foreach ($monthlyStats as $row) {
            $rowYear = (int) ($row->year ?? 0);
            $rowMonth = (int) ($row->month_num ?? 0);
            $rowIncome = (float) ($row->income ?? 0);
            $rowExpense = (float) ($row->expense ?? 0);

            if ($rowYear > 0 && $rowMonth > 0) {
                $totalSurplus += ($rowIncome - $rowExpense);
                $monthCount++;
            }

            if ($rowYear === $currentYear && $rowMonth === $currentMonth) {
                $currentIncome = $rowIncome;
                $currentExpense = $rowExpense;
            }

            if ($rowYear === $previousYear && $rowMonth === $previousMonth) {
                $previousIncome = $rowIncome;
                $previousExpense = $rowExpense;
            }
        }

        $currentBalance = $currentIncome - $currentExpense;
        $previousBalance = $previousIncome - $previousExpense;

        $totalIncome = (float) ($stats->total_income ?? 0);
        $totalExpense = (float) ($stats->total_expense ?? 0);
        $expenseRatio = $totalIncome > 0 ? ($totalExpense / $totalIncome) * 100 : 0;

        if ((float) ($stats->balance ?? 0) > 0) {
            $balanceMessage = 'Current balance is healthy and positive.';
        } elseif ((float) ($stats->balance ?? 0) < 0) {
            $balanceMessage = 'Current balance is negative. Reduce expenses to recover.';
        } else {
            $balanceMessage = 'Current balance is neutral.';
        }

        return [
            'income_change_pct' => $this->pctChange($currentIncome, $previousIncome),
            'expense_change_pct' => $this->pctChange($currentExpense, $previousExpense),
            'monthly_growth_pct' => $this->pctChange($currentBalance, $previousBalance),
            'avg_monthly_surplus' => $monthCount > 0 ? ($totalSurplus / $monthCount) : 0,
            'expense_ratio' => $expenseRatio,
            'balance_message' => $balanceMessage
        ];
    }

    public function index()
    {
        $model = new FinanceModel();

        $transactions = $model->getAllTransactions();
        $stats = $model->getStats();
        $monthlyStats = $model->getMonthlyStats();

        $data = [
            "transactions" => $transactions,
            "stats" => [
                "income" => $stats->total_income ?? 0,
                "expense" => $stats->total_expense ?? 0,
                "balance" => $stats->balance ?? 0
            ],
            "metrics" => $this->buildMetrics($monthlyStats, $stats)
        ];

        $this->view('captain/CaptainFinance', $data);
    }

    /* ================= ADD INCOME ================= */
    public function addIncome()
    {
        $model = new FinanceModel();

        $data = [
            "description" => $_POST['description'],
            "amount" => $_POST['amount'],
            "date" => $_POST['date'],
            "category" => $_POST['category'],
            "budget_id" => 1 // TEMP (later dynamic)
        ];

        $model->addIncome($data);

        echo json_encode(["status" => "success"]);
    }

    /* ================= ADD EXPENSE ================= */
    public function addExpense()
    {
        $model = new FinanceModel();

        $data = [
            "description" => $_POST['description'],
            "amount" => $_POST['amount'],
            "date" => $_POST['date'],
            "category" => $_POST['category'],
            "budget_id" => 1
        ];

        $model->addExpense($data);

        echo json_encode(["status" => "success"]);
    }

    /* ================= DELETE ================= */
    public function delete()
    {
        $model = new FinanceModel();

        $type = $_POST['type'];
        $id = $_POST['id'];

        $model->deleteTransaction($type, $id);

        echo json_encode(["status" => "success"]);
    }
    public function update()
{
    $model = new FinanceModel();

    $type = $_POST['type'];
    $id = $_POST['id'];

    $data = [
        "category" => $_POST['category'],
        "amount" => $_POST['amount'],
        "date" => $_POST['date'],
        "description" => $_POST['description']
    ];

    $model->updateTransaction($type, $id, $data);

    echo json_encode(["status" => "success"]);
}
public function getChartData()
{
    $model = new FinanceModel();

    $monthly = $model->getMonthlyStats();

    echo json_encode($monthly);
}
}
