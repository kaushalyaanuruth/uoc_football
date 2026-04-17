<?php
class CaptainFinance extends Controller
{
    public function index()
    {
        $model = new FinanceModel();

        $transactions = $model->getAllTransactions();
        $stats = $model->getStats();

        $data = [
            "transactions" => $transactions,
            "stats" => [
                "income" => $stats->total_income ?? 0,
                "expense" => $stats->total_expense ?? 0,
                "balance" => $stats->balance ?? 0
            ]
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
