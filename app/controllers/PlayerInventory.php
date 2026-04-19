<?php

class PlayerInventory extends Controller
{
    private function respond(array $payload)
    {
        header('Content-Type: application/json');
        echo json_encode($payload);
        exit;
    }

    private function jsonInput()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [];
        }

        return is_array($input) ? $input : [];
    }

    private function normalizeImageUrl($imagePath)
    {
        if (empty($imagePath)) {
            return ROOT . '/assets/images/adminDashboard/header/avatar.jpg';
        }

        $normalized = str_replace('\\', '/', ltrim((string) $imagePath, '/'));
        return ROOT . '/' . $normalized;
    }

    private function getPlayerImageBySession()
    {
        $nic = $_SESSION['nic'] ?? '';
        if ($nic === '') {
            return $this->normalizeImageUrl('');
        }

        $playerModel = $this->model('PlayerModel');
        $rows = $playerModel->query(
            "SELECT u.image FROM users u WHERE u.nic = :nic LIMIT 1",
            ['nic' => $nic]
        );

        return $this->normalizeImageUrl($rows[0]->image ?? '');
    }

    private function getPlayerNotices($limit = 6)
    {
        $noticeModel = $this->model('NoticeModel');
        $rows = [];

        try {
            $rows = $noticeModel->getRecent($limit, 'present_team');
        } catch (Exception $e) {
            $rows = [];
        }

        $notices = [];
        foreach ($rows as $row) {
            $notices[] = [
                'id' => (int) ($row->notice_id ?? 0),
                'title' => $row->title ?? 'Notice',
                'content' => $row->content ?? '',
                'author' => $row->created_by ?? 'Admin',
                'date' => !empty($row->created_at) ? date('M d, Y h:i A', strtotime($row->created_at)) : '',
            ];
        }

        return $notices;
    }

    private function ensurePlayerAccess()
    {
        if (!isset($_SESSION['user_id'], $_SESSION['nic'])) {
            header('Location: ' . ROOT . '/login');
            exit();
        }

        if (strtolower((string) ($_SESSION['user_type'] ?? '')) !== 'player') {
            header('Location: ' . ROOT . '/login');
            exit();
        }
    }

    private function resolvePlayerContext()
    {
        $nic = (string) ($_SESSION['nic'] ?? '');
        if ($nic === '') {
            throw new Exception('Player session is invalid');
        }

        $playerModel = $this->model('PlayerModel');
        $rows = $playerModel->query(
            "SELECT
                p.player_id,
                tp.team_id,
                CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) AS player_name
             FROM players p
             JOIN users u ON u.nic = p.nic
             LEFT JOIN team_players tp ON tp.player_id = p.player_id
             WHERE p.nic = :nic
             ORDER BY tp.team_id DESC
             LIMIT 1",
            ['nic' => $nic]
        );

        $row = $rows[0] ?? null;
        if ($row === null) {
            throw new Exception('Player profile not found');
        }

        return [
            'player_id' => (int) ($row->player_id ?? 0),
            'team_id' => (int) ($row->team_id ?? 0),
            'player_name' => trim((string) ($row->player_name ?? 'Player')),
            'nic' => $nic,
        ];
    }

    private function mapItem($row)
    {
        $status = trim((string) ($row->status ?? 'Available'));
        $statusKey = strtolower(str_replace([' ', '-'], '_', $status));
        if ($statusKey === 'inuse') {
            $statusKey = 'in_use';
        }

        $availableCount = (int) ($row->available_count ?? 0);
        $canTake = $availableCount > 0 && strtolower($status) !== 'damaged';

        return [
            'item_id' => (int) ($row->item_id ?? 0),
            'item_name' => trim((string) ($row->item_name ?? '')),
            'category' => trim((string) ($row->category ?? 'General')),
            'total_count' => (int) ($row->total_count ?? 0),
            'available_count' => $availableCount,
            'unit' => trim((string) ($row->unit ?? 'pcs')),
            'status' => $status,
            'status_key' => $statusKey,
            'last_updated' => (string) ($row->last_updated ?? ''),
            'can_take' => $canTake,
        ];
    }

    private function mapLog($row)
    {
        return [
            'log_id' => (int) ($row->log_id ?? 0),
            'item_id' => (int) ($row->item_id ?? 0),
            'item_name' => trim((string) ($row->item_name ?? '')),
            'category' => trim((string) ($row->category ?? 'General')),
            'quantity' => (int) ($row->quantity ?? 0),
            'unit' => trim((string) ($row->unit ?? 'pcs')),
            'taken_date' => (string) ($row->taken_date ?? ''),
            'return_date' => (string) ($row->return_date ?? ''),
            'is_open' => empty($row->return_date),
        ];
    }

    private function buildSnapshot(InventoryModel $model, $teamId, $playerId)
    {
        $items = [];
        foreach ($model->getAllItems($teamId) as $row) {
            $items[] = $this->mapItem($row);
        }

        $openLogs = [];
        foreach ($model->getPlayerOpenLogs($playerId, $teamId) as $row) {
            $openLogs[] = $this->mapLog($row);
        }

        $historyLogs = [];
        foreach ($model->getPlayerRecentLogs($playerId, $teamId, 15) as $row) {
            $historyLogs[] = $this->mapLog($row);
        }

        $stats = $model->getStats($teamId);

        return [
            'items' => $items,
            'open_logs' => $openLogs,
            'history_logs' => $historyLogs,
            'stats' => [
                'total' => (int) ($stats->total ?? 0),
                'in_use' => (int) ($stats->in_use ?? 0),
                'available' => (int) ($stats->available ?? 0),
                'damaged' => (int) ($stats->damaged ?? 0),
            ],
        ];
    }

    public function index()
    {
        $this->ensurePlayerAccess();

        try {
            $ctx = $this->resolvePlayerContext();
            $inventoryModel = new InventoryModel();
            $inventoryModel->seedDummyItemsIfEmpty($ctx['team_id'], (string) $ctx['nic']);

            $snapshot = $this->buildSnapshot($inventoryModel, $ctx['team_id'], $ctx['player_id']);

            $data = [
                'player_name' => $ctx['player_name'] !== '' ? $ctx['player_name'] : 'Player',
                'player_image' => $this->getPlayerImageBySession(),
                'notices' => $this->getPlayerNotices(),
                'team_id' => $ctx['team_id'],
                'inventory' => $snapshot['items'],
                'open_logs' => $snapshot['open_logs'],
                'history_logs' => $snapshot['history_logs'],
                'stats' => $snapshot['stats'],
            ];

            $this->view('player/playerInventory', $data);
        } catch (Exception $e) {
            $this->view('player/playerInventory', [
                'player_name' => 'Player',
                'player_image' => $this->getPlayerImageBySession(),
                'notices' => $this->getPlayerNotices(),
                'team_id' => 0,
                'inventory' => [],
                'open_logs' => [],
                'history_logs' => [],
                'stats' => ['total' => 0, 'in_use' => 0, 'available' => 0, 'damaged' => 0],
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function snapshot()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->respond(['status' => 'error', 'message' => 'Invalid request method']);
        }

        try {
            $this->ensurePlayerAccess();
            $ctx = $this->resolvePlayerContext();

            $inventoryModel = new InventoryModel();
            $snapshot = $this->buildSnapshot($inventoryModel, $ctx['team_id'], $ctx['player_id']);

            $this->respond([
                'status' => 'success',
                'data' => $snapshot,
            ]);
        } catch (Exception $e) {
            $this->respond([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function take()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->respond(['status' => 'error', 'message' => 'Invalid request method']);
        }

        try {
            $this->ensurePlayerAccess();
            $ctx = $this->resolvePlayerContext();
            $input = $this->jsonInput();
            if (empty($input)) {
                $input = $_POST;
            }

            $itemId = (int) ($input['item_id'] ?? 0);
            $quantity = max(1, (int) ($input['quantity'] ?? 1));

            if ($itemId <= 0) {
                throw new Exception('Item ID is required');
            }

            $inventoryModel = new InventoryModel();
            $result = $inventoryModel->takeItemForPlayer(
                $itemId,
                $ctx['player_id'],
                $ctx['team_id'],
                $quantity,
                (string) $ctx['nic']
            );

            $this->respond([
                'status' => 'success',
                'message' => 'Item taken successfully',
                'data' => $result,
            ]);
        } catch (Exception $e) {
            $this->respond([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function returnItem()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->respond(['status' => 'error', 'message' => 'Invalid request method']);
        }

        try {
            $this->ensurePlayerAccess();
            $ctx = $this->resolvePlayerContext();
            $input = $this->jsonInput();
            if (empty($input)) {
                $input = $_POST;
            }

            $logId = (int) ($input['log_id'] ?? 0);
            if ($logId <= 0) {
                throw new Exception('Log ID is required');
            }

            $inventoryModel = new InventoryModel();
            $result = $inventoryModel->returnItemForPlayer(
                $logId,
                $ctx['player_id'],
                $ctx['team_id'],
                (string) $ctx['nic']
            );

            $this->respond([
                'status' => 'success',
                'message' => 'Item returned successfully',
                'data' => $result,
            ]);
        } catch (Exception $e) {
            $this->respond([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }
}
