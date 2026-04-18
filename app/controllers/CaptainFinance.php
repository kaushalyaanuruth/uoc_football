<?php
class CaptainFinance extends Controller
{
    private function buildInitialsAvatarUrl($displayName)
    {
        $name = trim((string) $displayName);
        if ($name === '') {
            $name = 'Captain';
        }

        $parts = preg_split('/\s+/', $name);
        $initials = '';
        foreach ($parts as $part) {
            if ($part === '') {
                continue;
            }
            $initials .= strtoupper(substr($part, 0, 1));
            if (strlen($initials) >= 2) {
                break;
            }
        }

        if ($initials === '') {
            $initials = 'C';
        }

        $palette = ['#4f46e5', '#0ea5e9', '#059669', '#d97706', '#dc2626', '#7c3aed'];
        $color = $palette[abs(crc32($name)) % count($palette)];

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="320" height="320" viewBox="0 0 320 320">'
            . '<rect width="320" height="320" fill="' . $color . '"/>'
            . '<text x="50%" y="52%" dominant-baseline="middle" text-anchor="middle" '
            . 'font-family="Arial, Helvetica, sans-serif" font-size="120" font-weight="700" fill="#ffffff">'
            . htmlspecialchars($initials, ENT_QUOTES, 'UTF-8')
            . '</text>'
            . '</svg>';

        return 'data:image/svg+xml;utf8,' . rawurlencode($svg);
    }

    private function normalizeImageUrl($imagePath, $displayName = '')
    {
        if (empty($imagePath)) {
            return $this->buildInitialsAvatarUrl($displayName);
        }

        $normalized = str_replace('\\', '/', ltrim((string) $imagePath, '/'));
        return ROOT . '/' . $normalized;
    }

    private function isCaptainLikeRole($role)
    {
        $normalized = strtolower(trim((string) $role));
        $normalized = str_replace(['_', ' '], '-', $normalized);

        return in_array($normalized, ['captain', 'vice-captain', 'vicecaptain'], true);
    }

    private function buildCaptainUiData()
    {
        $nic = $_SESSION['nic'] ?? '';
        $playerModel = $this->model('PlayerModel');

        $rows = $playerModel->query(
            "SELECT u.first_name, u.last_name, u.image
             FROM players p
             JOIN users u ON u.nic = p.nic
             WHERE p.nic = :nic
             LIMIT 1",
            ['nic' => $nic]
        );

        $captain = !empty($rows) ? $rows[0] : null;
        $fullName = trim((($captain->first_name ?? '') . ' ' . ($captain->last_name ?? '')));
        if ($fullName === '') {
            $fullName = 'Captain';
        }

        return [
            'captain_name' => $fullName,
            'captain_image' => $this->normalizeImageUrl($captain->image ?? '', $fullName)
        ];
    }

    private function getCaptainNotices($limit = 6)
    {
        $noticeModel = $this->model('NoticeModel');
        $noticeRows = [];

        try {
            $noticeRows = $noticeModel->getRecent($limit, 'present_team');
        } catch (Exception $e) {
            $noticeRows = [];
        }

        $notices = [];
        foreach ($noticeRows as $row) {
            $notices[] = [
                'title' => $row->title ?? 'Notice',
                'content' => $row->content ?? '',
                'author' => $row->created_by ?? 'Admin',
                'date' => !empty($row->created_at) ? date('M d, Y h:i A', strtotime($row->created_at)) : ''
            ];
        }

        if (empty($notices)) {
            $notices[] = [
                'title' => 'No notices yet',
                'content' => 'Admin notices for present team members will appear here.',
                'author' => 'System',
                'date' => ''
            ];
        }

        return $notices;
    }

    private function ensureCaptainAccess()
    {
        if (!isset($_SESSION['user_id'], $_SESSION['nic'])) {
            header('Location: ' . ROOT . '/login');
            exit();
        }

        $userType = strtolower((string) ($_SESSION['user_type'] ?? ''));
        $playerRole = (string) ($_SESSION['player_role'] ?? '');
        $isCaptain = $userType === 'captain' || ($userType === 'player' && $this->isCaptainLikeRole($playerRole));

        if (!$isCaptain) {
            header('Location: ' . ROOT . '/login');
            exit();
        }
    }

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
        $this->ensureCaptainAccess();

        $model = new FinanceModel();

        $transactions = $model->getAllTransactions();
        $stats = $model->getStats();
        $monthlyStats = $model->getMonthlyStats();
        $uiData = $this->buildCaptainUiData();
        $notices = $this->getCaptainNotices();

        $data = [
            "transactions" => $transactions,
            "stats" => [
                "income" => $stats->total_income ?? 0,
                "expense" => $stats->total_expense ?? 0,
                "balance" => $stats->balance ?? 0
            ],
            "metrics" => $this->buildMetrics($monthlyStats, $stats),
            "captain_name" => $uiData['captain_name'],
            "captain_image" => $uiData['captain_image'],
            "notices" => $notices
        ];

        $this->view('captain/CaptainFinance', $data);
    }

    /* ================= ADD INCOME ================= */
    public function addIncome()
    {
        $this->ensureCaptainAccess();

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
        $this->ensureCaptainAccess();

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
        $this->ensureCaptainAccess();

        $model = new FinanceModel();

        $type = $_POST['type'];
        $id = $_POST['id'];

        $model->deleteTransaction($type, $id);

        echo json_encode(["status" => "success"]);
    }
    public function update()
{
        $this->ensureCaptainAccess();

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
        $this->ensureCaptainAccess();

    $model = new FinanceModel();

    $monthly = $model->getMonthlyStats();

    echo json_encode($monthly);
}
}
