<?php

class BudgetManagement extends Controller
{
    private $teamModel;

    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $this->teamModel = $this->model('TeamModel');
    }

    private function getTeams()
    {
        if (!$this->teamModel) {
            return [];
        }

        try {
            $rows = $this->teamModel->getAll();
            $teams = [];

            foreach ($rows as $row) {
                $teamId = isset($row->id) ? (string) $row->id : (isset($row->team_id) ? (string) $row->team_id : '');
                $teamName = isset($row->team_name) ? trim((string) $row->team_name) : (isset($row->name) ? trim((string) $row->name) : '');

                if ($teamId !== '' && $teamName !== '') {
                    $teams[] = [
                        'id' => $teamId,
                        'name' => $teamName
                    ];
                }
            }

            return $teams;
        } catch (Exception $e) {
            error_log('Failed to load teams for budget module: ' . $e->getMessage());
            return [];
        }
    }

    private function getTeamMap()
    {
        $map = [];
        foreach ($this->getTeams() as $team) {
            $map[(string) $team['id']] = $team['name'];
        }
        return $map;
    }

    private function seedTransactions()
    {
        $teamMap = $this->getTeamMap();
        $defaultTeamId = '';
        $defaultTeamName = 'Unknown Team';

        if (!empty($teamMap)) {
            $defaultTeamId = (string) array_key_first($teamMap);
            $defaultTeamName = (string) $teamMap[$defaultTeamId];
        }

        if (!isset($_SESSION['budget_transactions']) || !is_array($_SESSION['budget_transactions']) || empty($_SESSION['budget_transactions'])) {
            $_SESSION['budget_transactions'] = [
                [
                    'id' => 1,
                    'date' => date('Y-m-d', strtotime('first day of this month')),
                    'type' => 'income',
                    'team_id' => $defaultTeamId,
                    'team_name' => $defaultTeamName,
                    'description' => 'Monthly sponsor installment',
                    'amount' => 150000,
                    'bill_image' => null
                ],
                [
                    'id' => 2,
                    'date' => date('Y-m-d', strtotime('first day of this month +2 days')),
                    'type' => 'expense',
                    'team_id' => $defaultTeamId,
                    'team_name' => $defaultTeamName,
                    'description' => 'Training cones and bibs',
                    'amount' => 23500,
                    'bill_image' => null
                ],
                [
                    'id' => 3,
                    'date' => date('Y-m-d', strtotime('first day of this month +5 days')),
                    'type' => 'expense',
                    'team_id' => $defaultTeamId,
                    'team_name' => $defaultTeamName,
                    'description' => 'Away match transport',
                    'amount' => 38000,
                    'bill_image' => null
                ],
                [
                    'id' => 4,
                    'date' => date('Y-m-d', strtotime('first day of last month +1 day')),
                    'type' => 'income',
                    'team_id' => $defaultTeamId,
                    'team_name' => $defaultTeamName,
                    'description' => 'Home match ticket income',
                    'amount' => 92000,
                    'bill_image' => null
                ],
                [
                    'id' => 5,
                    'date' => date('Y-m-d', strtotime('first day of last month +4 days')),
                    'type' => 'expense',
                    'team_id' => $defaultTeamId,
                    'team_name' => $defaultTeamName,
                    'description' => 'Physio and medical consumables',
                    'amount' => 14000,
                    'bill_image' => null
                ],
                [
                    'id' => 6,
                    'date' => date('Y-m-d', strtotime('first day of last month +8 days')),
                    'type' => 'income',
                    'team_id' => $defaultTeamId,
                    'team_name' => $defaultTeamName,
                    'description' => 'Alumni football fund contribution',
                    'amount' => 30000,
                    'bill_image' => null
                ]
            ];
        }

        foreach ($_SESSION['budget_transactions'] as &$item) {
            if (!isset($item['team_id'])) {
                $item['team_id'] = $defaultTeamId;
            }

            if (!isset($item['team_name']) || trim((string) $item['team_name']) === '') {
                $resolvedName = isset($teamMap[(string) $item['team_id']]) ? $teamMap[(string) $item['team_id']] : $defaultTeamName;
                $item['team_name'] = $resolvedName;
            }

            if (!isset($item['bill_image'])) {
                $item['bill_image'] = null;
            }

            unset($item['category'], $item['season']);
        }
        unset($item);
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

    private function sanitizeTeam($value, array $teamMap)
    {
        $teamId = trim((string) $value);
        if ($teamId === '') {
            throw new Exception('Team is required');
        }

        if (!isset($teamMap[$teamId])) {
            throw new Exception('Selected team is invalid');
        }

        return [
            'id' => $teamId,
            'name' => $teamMap[$teamId]
        ];
    }

    private function uploadBillImage($fileKey, $existingPath = null)
    {
        if (!isset($_FILES[$fileKey]) || !is_array($_FILES[$fileKey]) || ($_FILES[$fileKey]['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
            return $existingPath;
        }

        $file = $_FILES[$fileKey];
        if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK) {
            throw new Exception('Bill image upload failed');
        }

        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        $originalName = (string) ($file['name'] ?? '');
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

        if (!in_array($extension, $allowed, true)) {
            throw new Exception('Bill image must be JPG, PNG, or WEBP');
        }

        $uploadDir = __DIR__ . '/../../public/uploads/budget_bills';
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true)) {
            throw new Exception('Unable to create bill image upload directory');
        }

        $fileName = 'bill_' . bin2hex(random_bytes(12)) . '.' . $extension;
        $absolutePath = $uploadDir . '/' . $fileName;
        $relativePath = 'uploads/budget_bills/' . $fileName;

        if (!move_uploaded_file((string) $file['tmp_name'], $absolutePath)) {
            throw new Exception('Unable to save bill image');
        }

        if (!empty($existingPath)) {
            $oldPath = __DIR__ . '/../../public/' . ltrim((string) $existingPath, '/');
            if (is_file($oldPath)) {
                @unlink($oldPath);
            }
        }

        return $relativePath;
    }

    private function buildSummary(array $transactions)
    {
        $summary = [
            'totalIncome' => 0,
            'totalExpense' => 0,
            'netFlow' => 0,
            'monthWise' => [],
            'teamWise' => []
        ];

        foreach ($transactions as $entry) {
            $amount = (float) ($entry['amount'] ?? 0);
            $type = $this->sanitizeType($entry['type'] ?? 'income');
            $teamId = (string) ($entry['team_id'] ?? '');
            $teamName = trim((string) ($entry['team_name'] ?? 'Unknown Team'));
            $teamKey = $teamId !== '' ? $teamId : strtolower($teamName);
            $monthKey = date('Y-m', strtotime($entry['date'] ?? date('Y-m-d')));
            $monthLabel = date('M Y', strtotime($entry['date'] ?? date('Y-m-d')));

            if (!isset($summary['monthWise'][$monthKey])) {
                $summary['monthWise'][$monthKey] = [
                    'monthKey' => $monthKey,
                    'monthLabel' => $monthLabel,
                    'income' => 0,
                    'expense' => 0,
                    'net' => 0
                ];
            }

            if (!isset($summary['teamWise'][$teamKey])) {
                $summary['teamWise'][$teamKey] = [
                    'team' => $teamName,
                    'income' => 0,
                    'expense' => 0,
                    'net' => 0
                ];
            }

            if ($type === 'income') {
                $summary['totalIncome'] += $amount;
                $summary['monthWise'][$monthKey]['income'] += $amount;
                $summary['teamWise'][$teamKey]['income'] += $amount;
            } else {
                $summary['totalExpense'] += $amount;
                $summary['monthWise'][$monthKey]['expense'] += $amount;
                $summary['teamWise'][$teamKey]['expense'] += $amount;
            }
        }

        $summary['netFlow'] = $summary['totalIncome'] - $summary['totalExpense'];

        foreach ($summary['monthWise'] as &$monthRow) {
            $monthRow['net'] = $monthRow['income'] - $monthRow['expense'];
        }
        unset($monthRow);

        foreach ($summary['teamWise'] as &$teamRow) {
            $teamRow['net'] = $teamRow['income'] - $teamRow['expense'];
        }
        unset($teamRow);

        usort($summary['monthWise'], function ($a, $b) {
            return strcmp($b['monthKey'], $a['monthKey']);
        });

        usort($summary['teamWise'], function ($a, $b) {
            return strcmp($a['team'], $b['team']);
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
        $teams = $this->getTeams();

        $data = [
            'transactions' => $transactions,
            'summary' => $summary,
            'teams' => $teams,
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
            $teamMap = $this->getTeamMap();

            if (empty($teamMap)) {
                throw new Exception('No teams found. Please create a team first.');
            }

            $input = $_POST;

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

            $team = $this->sanitizeTeam($input['team_id'] ?? '', $teamMap);
            $billImage = $this->uploadBillImage('bill_image');

            $transaction = [
                'id' => $nextId,
                'date' => $this->sanitizeDate($input['date'] ?? ''),
                'type' => $this->sanitizeType($input['type'] ?? 'income'),
                'team_id' => $team['id'],
                'team_name' => $team['name'],
                'description' => $description,
                'amount' => $amount,
                'bill_image' => $billImage
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
            $teamMap = $this->getTeamMap();
            $input = $_POST;
            $id = (int) ($input['id'] ?? 0);

            if (!$id) {
                throw new Exception('Entry ID is required');
            }

            if (empty($teamMap)) {
                throw new Exception('No teams found. Please create a team first.');
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
                    $team = $this->sanitizeTeam($input['team_id'] ?? ($item['team_id'] ?? ''), $teamMap);

                    $item['date'] = $this->sanitizeDate($input['date'] ?? $item['date']);
                    $item['type'] = $this->sanitizeType($input['type'] ?? $item['type']);
                    $item['team_id'] = $team['id'];
                    $item['team_name'] = $team['name'];
                    $item['description'] = $description;
                    $item['amount'] = $amount;
                    $item['bill_image'] = $this->uploadBillImage('bill_image', $item['bill_image'] ?? null);
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
                        $imagePath = $item['bill_image'] ?? null;
                        if (!empty($imagePath)) {
                            $absolutePath = __DIR__ . '/../../public/' . ltrim((string) $imagePath, '/');
                            if (is_file($absolutePath)) {
                                @unlink($absolutePath);
                            }
                        }
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
