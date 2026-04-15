<?php

class BudgetManagement extends Controller
{
    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    private function seedTransactions()
    {
        if (!isset($_SESSION['budget_transactions']) || !is_array($_SESSION['budget_transactions']) || empty($_SESSION['budget_transactions'])) {
            $_SESSION['budget_transactions'] = [
                [
                    'id' => 1,
                    'date' => date('Y-m-d', strtotime('first day of this month')),
                    'type' => 'income',
                    'category' => 'Sponsorship',
                    'description' => 'Monthly sponsor installment',
                    'amount' => 150000,
                    'season' => '2025/2026'
                ],
                [
                    'id' => 2,
                    'date' => date('Y-m-d', strtotime('first day of this month +2 days')),
                    'type' => 'expense',
                    'category' => 'Equipment',
                    'description' => 'Training cones and bibs',
                    'amount' => 23500,
                    'season' => '2025/2026'
                ],
                [
                    'id' => 3,
                    'date' => date('Y-m-d', strtotime('first day of this month +5 days')),
                    'type' => 'expense',
                    'category' => 'Travel',
                    'description' => 'Away match transport',
                    'amount' => 38000,
                    'season' => '2025/2026'
                ],
                [
                    'id' => 4,
                    'date' => date('Y-m-d', strtotime('first day of last month +1 day')),
                    'type' => 'income',
                    'category' => 'Ticket Sales',
                    'description' => 'Home match ticket income',
                    'amount' => 92000,
                    'season' => '2025/2026'
                ],
                [
                    'id' => 5,
                    'date' => date('Y-m-d', strtotime('first day of last month +4 days')),
                    'type' => 'expense',
                    'category' => 'Medical',
                    'description' => 'Physio and medical consumables',
                    'amount' => 14000,
                    'season' => '2025/2026'
                ],
                [
                    'id' => 6,
                    'date' => date('Y-m-d', strtotime('first day of last month +8 days')),
                    'type' => 'income',
                    'category' => 'Donations',
                    'description' => 'Alumni football fund contribution',
                    'amount' => 30000,
                    'season' => '2025/2026'
                ]
            ];
        }
    }

    private function jsonInput()
    {
        $input = json_decode(file_get_contents('php://input'), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON: ' . json_last_error_msg());
        }

        return is_array($input) ? $input : [];
    }

    private function respond(array $payload)
    {
        header('Content-Type: application/json');
        echo json_encode($payload);
        exit;
    }

    private function sanitizeType($value)
    {
        $type = strtolower(trim((string) $value));
        return $type === 'expense' ? 'expense' : 'income';
    }

    private function sanitizeDate($value)
    {
        $date = trim((string) $value);
        if ($date === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            throw new Exception('Valid date is required');
        }

        return $date;
    }

    private function sanitizeSeason($value)
    {
        $season = trim((string) $value);
        if ($season === '') {
            return date('Y') . '/' . ((int) date('Y') + 1);
        }

        return $season;
    }

    private function buildSummary(array $transactions)
    {
        $summary = [
            'totalIncome' => 0,
            'totalExpense' => 0,
            'netFlow' => 0,
            'seasonTotals' => [],
            'monthWise' => [],
            'categoryWise' => []
        ];

        foreach ($transactions as $entry) {
            $amount = (float) ($entry['amount'] ?? 0);
            $type = $this->sanitizeType($entry['type'] ?? 'income');
            $season = $entry['season'] ?? 'Unknown';
            $monthKey = date('Y-m', strtotime($entry['date'] ?? date('Y-m-d')));
            $monthLabel = date('M Y', strtotime($entry['date'] ?? date('Y-m-d')));
            $category = trim((string) ($entry['category'] ?? 'General'));

            if (!isset($summary['seasonTotals'][$season])) {
                $summary['seasonTotals'][$season] = [
                    'season' => $season,
                    'income' => 0,
                    'expense' => 0,
                    'net' => 0
                ];
            }

            if (!isset($summary['monthWise'][$monthKey])) {
                $summary['monthWise'][$monthKey] = [
                    'monthKey' => $monthKey,
                    'monthLabel' => $monthLabel,
                    'income' => 0,
                    'expense' => 0,
                    'net' => 0
                ];
            }

            $categoryKey = strtolower($category);
            if (!isset($summary['categoryWise'][$categoryKey])) {
                $summary['categoryWise'][$categoryKey] = [
                    'category' => $category,
                    'income' => 0,
                    'expense' => 0,
                    'net' => 0
                ];
            }

            if ($type === 'income') {
                $summary['totalIncome'] += $amount;
                $summary['seasonTotals'][$season]['income'] += $amount;
                $summary['monthWise'][$monthKey]['income'] += $amount;
                $summary['categoryWise'][$categoryKey]['income'] += $amount;
            } else {
                $summary['totalExpense'] += $amount;
                $summary['seasonTotals'][$season]['expense'] += $amount;
                $summary['monthWise'][$monthKey]['expense'] += $amount;
                $summary['categoryWise'][$categoryKey]['expense'] += $amount;
            }
        }

        $summary['netFlow'] = $summary['totalIncome'] - $summary['totalExpense'];

        foreach ($summary['seasonTotals'] as &$seasonRow) {
            $seasonRow['net'] = $seasonRow['income'] - $seasonRow['expense'];
        }
        unset($seasonRow);

        foreach ($summary['monthWise'] as &$monthRow) {
            $monthRow['net'] = $monthRow['income'] - $monthRow['expense'];
        }
        unset($monthRow);

        foreach ($summary['categoryWise'] as &$categoryRow) {
            $categoryRow['net'] = $categoryRow['income'] - $categoryRow['expense'];
        }
        unset($categoryRow);

        usort($summary['seasonTotals'], function ($a, $b) {
            return strcmp($b['season'], $a['season']);
        });

        usort($summary['monthWise'], function ($a, $b) {
            return strcmp($b['monthKey'], $a['monthKey']);
        });

        usort($summary['categoryWise'], function ($a, $b) {
            return strcmp($a['category'], $b['category']);
        });

        return $summary;
    }

    public function index()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . ROOT . '/login');
            exit;
        }

        $this->seedTransactions();

        $transactions = $_SESSION['budget_transactions'];

        usort($transactions, function ($a, $b) {
            return strcmp($b['date'], $a['date']);
        });

        $summary = $this->buildSummary($transactions);

        $data = [
            'transactions' => $transactions,
            'summary' => $summary,
            'title' => 'Budget Management'
        ];

        $this->view('budgetManagement', $data);
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->respond(['success' => false, 'message' => 'Invalid request method']);
        }

        try {
            $this->seedTransactions();
            $input = $this->jsonInput();

            $description = trim((string) ($input['description'] ?? ''));
            if ($description === '') {
                throw new Exception('Description is required');
            }

            $amount = (float) ($input['amount'] ?? 0);
            if ($amount <= 0) {
                throw new Exception('Amount must be greater than 0');
            }

            $items = $_SESSION['budget_transactions'];
            $nextId = 1;
            foreach ($items as $item) {
                $nextId = max($nextId, (int) $item['id'] + 1);
            }

            $transaction = [
                'id' => $nextId,
                'date' => $this->sanitizeDate($input['date'] ?? ''),
                'type' => $this->sanitizeType($input['type'] ?? 'income'),
                'category' => trim((string) ($input['category'] ?? 'General')),
                'description' => $description,
                'amount' => $amount,
                'season' => $this->sanitizeSeason($input['season'] ?? '')
            ];

            $_SESSION['budget_transactions'][] = $transaction;

            $this->respond([
                'success' => true,
                'message' => 'Budget entry added successfully',
                'transaction' => $transaction
            ]);
        } catch (Exception $e) {
            $this->respond([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->respond(['success' => false, 'message' => 'Invalid request method']);
        }

        try {
            $this->seedTransactions();
            $input = $this->jsonInput();
            $id = (int) ($input['id'] ?? 0);

            if (!$id) {
                throw new Exception('Entry ID is required');
            }

            $description = trim((string) ($input['description'] ?? ''));
            if ($description === '') {
                throw new Exception('Description is required');
            }

            $amount = (float) ($input['amount'] ?? 0);
            if ($amount <= 0) {
                throw new Exception('Amount must be greater than 0');
            }

            $updated = null;
            foreach ($_SESSION['budget_transactions'] as &$item) {
                if ((int) $item['id'] === $id) {
                    $item['date'] = $this->sanitizeDate($input['date'] ?? $item['date']);
                    $item['type'] = $this->sanitizeType($input['type'] ?? $item['type']);
                    $item['category'] = trim((string) ($input['category'] ?? $item['category']));
                    $item['description'] = $description;
                    $item['amount'] = $amount;
                    $item['season'] = $this->sanitizeSeason($input['season'] ?? $item['season']);
                    $updated = $item;
                    break;
                }
            }
            unset($item);

            if ($updated === null) {
                throw new Exception('Entry not found');
            }

            $this->respond([
                'success' => true,
                'message' => 'Budget entry updated successfully',
                'transaction' => $updated
            ]);
        } catch (Exception $e) {
            $this->respond([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->respond(['success' => false, 'message' => 'Invalid request method']);
        }

        try {
            $this->seedTransactions();
            $input = $this->jsonInput();
            $id = (int) ($input['id'] ?? 0);

            if (!$id) {
                throw new Exception('Entry ID is required');
            }

            $found = false;
            $_SESSION['budget_transactions'] = array_values(array_filter(
                $_SESSION['budget_transactions'],
                function ($item) use ($id, &$found) {
                    if ((int) ($item['id'] ?? 0) === $id) {
                        $found = true;
                        return false;
                    }
                    return true;
                }
            ));

            if (!$found) {
                throw new Exception('Entry not found');
            }

            $this->respond([
                'success' => true,
                'message' => 'Budget entry deleted successfully'
            ]);
        } catch (Exception $e) {
            $this->respond([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function get()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->respond(['success' => false, 'message' => 'Invalid request method']);
        }

        try {
            $this->seedTransactions();
            $id = (int) ($_GET['id'] ?? 0);

            if (!$id) {
                throw new Exception('Entry ID is required');
            }

            foreach ($_SESSION['budget_transactions'] as $item) {
                if ((int) ($item['id'] ?? 0) === $id) {
                    $this->respond([
                        'success' => true,
                        'data' => $item
                    ]);
                }
            }

            throw new Exception('Entry not found');
        } catch (Exception $e) {
            $this->respond([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
