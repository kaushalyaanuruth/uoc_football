<?php

class CaptainInventory extends Controller
{
    private function jsonInput()
    {
        $input = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [];
        }

        return is_array($input) ? $input : [];
    }

    private function respond($payload)
    {
        header('Content-Type: application/json');
        echo json_encode($payload);
        exit;
    }

    private function normalizeStatusLabel($status)
    {
        $value = strtolower(trim((string) $status));
        $value = str_replace(['-', '_'], ' ', $value);

        if ($value === 'damaged') {
            return 'Damaged';
        }

        if (in_array($value, ['in use', 'inuse', 'low', 'reserved'], true)) {
            return 'In Use';
        }

        return 'Available';
    }

    private function statusKey($status)
    {
        $label = $this->normalizeStatusLabel($status);
        if ($label === 'Damaged') {
            return 'damaged';
        }

        if ($label === 'In Use') {
            return 'in_use';
        }

        return 'available';
    }

    private function getCaptainTeamId()
    {
        $nic = (string) ($_SESSION['nic'] ?? '');
        if ($nic === '') {
            return 0;
        }

        $playerModel = $this->model('PlayerModel');
        $rows = $playerModel->query(
            "SELECT tp.team_id
             FROM players p
             JOIN team_players tp ON tp.player_id = p.player_id
             WHERE p.nic = :nic
             ORDER BY tp.team_id DESC
             LIMIT 1",
            ['nic' => $nic]
        );

        return (int) ($rows[0]->team_id ?? 0);
    }

    private function mapItem($row)
    {
        $statusLabel = $this->normalizeStatusLabel($row->status ?? 'Available');
        return [
            'item_id' => (int) ($row->item_id ?? 0),
            'item_name' => trim((string) ($row->item_name ?? '')),
            'category' => trim((string) ($row->category ?? 'General')),
            'total_count' => (int) ($row->total_count ?? 0),
            'available_count' => (int) ($row->available_count ?? 0),
            'status' => $statusLabel,
            'status_key' => $this->statusKey($statusLabel),
            'last_updated' => (string) ($row->last_updated ?? ''),
        ];
    }
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

    public function index()
    {
        $this->ensureCaptainAccess();
        $teamId = $this->getCaptainTeamId();

        $inventoryModel = new InventoryModel();
        $inventoryModel->seedDummyItemsIfEmpty($teamId, (string) ($_SESSION['nic'] ?? $_SESSION['user_nic'] ?? ''));

        $items = $inventoryModel->getAllItems($teamId);
        $mappedItems = [];
        foreach ($items as $item) {
            $mappedItems[] = $this->mapItem($item);
        }

        $stats = $inventoryModel->getStats($teamId);
        $uiData = $this->buildCaptainUiData();
        $notices = $this->getCaptainNotices();

        $data = [
            'total' => (int) ($stats->total ?? 0),
            'in_use' => (int) ($stats->in_use ?? 0),
            'available' => (int) ($stats->available ?? 0),
            'damaged' => (int) ($stats->damaged ?? 0),
            'inventory' => $mappedItems,
            'captain_name' => $uiData['captain_name'],
            'captain_image' => $uiData['captain_image'],
            'notices' => $notices,
            'team_id' => $teamId,
        ];

        $this->view('captain/CaptainInventory', $data);
    }

    public function store()
    {
        $this->ensureCaptainAccess();
        $teamId = $this->getCaptainTeamId();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->respond(['status' => 'error', 'message' => 'Invalid request method']);
        }

        $input = $this->jsonInput();
        if (empty($input)) {
            $input = $_POST;
        }

        if (trim((string) ($input['item_name'] ?? '')) === '') {
            $this->respond(['status' => 'error', 'message' => 'Item name is required']);
        }

        $model = new InventoryModel();

        $data = [
            'item_name' => trim((string) ($input['item_name'] ?? '')),
            'category' => trim((string) ($input['category'] ?? 'General')),
            'total_count' => max(0, (int) ($input['quantity'] ?? 0)),
            'available_count' => max(0, (int) ($input['quantity'] ?? 0)),
            'status' => $this->normalizeStatusLabel($input['status'] ?? 'Available'),
            'updated_by' => (string) ($_SESSION['nic'] ?? ''),
        ];

        $model->addItem($data, $teamId);

        $this->respond(['status' => 'success']);
    }

    public function update()
    {
        $this->ensureCaptainAccess();
        $teamId = $this->getCaptainTeamId();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->respond(['status' => 'error', 'message' => 'Invalid request method']);
        }

        $input = $this->jsonInput();
        if (empty($input)) {
            $input = $_POST;
        }

        $itemId = (int) ($input['item_id'] ?? 0);
        if ($itemId <= 0) {
            $this->respond(['status' => 'error', 'message' => 'Item ID is required']);
        }

        $model = new InventoryModel();
        $existing = $model->getItemById($itemId, $teamId);
        if ($existing === null) {
            $this->respond(['status' => 'error', 'message' => 'Item not found']);
        }

        $quantity = max(0, (int) ($input['quantity'] ?? $existing->total_count ?? 0));
        $data = [
            'item_id' => $itemId,
            'item_name' => trim((string) ($input['item_name'] ?? $existing->item_name ?? '')),
            'category' => trim((string) ($input['category'] ?? $existing->category ?? 'General')),
            'total_count' => $quantity,
            'available_count' => $quantity,
            'status' => $this->normalizeStatusLabel($input['status'] ?? ($existing->status ?? 'Available')),
            'updated_by' => (string) ($_SESSION['nic'] ?? ''),
        ];

        $model->updateItem($data, $teamId);

        $this->respond(['status' => 'success']);
    }

    public function delete()
    {
        $this->ensureCaptainAccess();
        $teamId = $this->getCaptainTeamId();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->respond(['status' => 'error', 'message' => 'Invalid request method']);
        }

        $input = $this->jsonInput();
        if (empty($input)) {
            $input = $_POST;
        }

        $id = (int) ($input['item_id'] ?? 0);
        if ($id <= 0) {
            $this->respond(['status' => 'error', 'message' => 'Item ID is required']);
        }

        $model = new InventoryModel();
        $existing = $model->getItemById($id, $teamId);
        if ($existing === null) {
            $this->respond(['status' => 'error', 'message' => 'Item not found']);
        }

        $model->deleteItem($id, $teamId);
        $this->respond(['status' => 'success']);
    }

    public function getChartData()
    {
        $this->ensureCaptainAccess();
        $teamId = $this->getCaptainTeamId();

        $model = new InventoryModel();
        $data = $model->getCategoryStats($teamId);

        $this->respond($data);
    }

    public function snapshot()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->respond(['status' => 'error', 'message' => 'Invalid request method']);
        }

        $this->ensureCaptainAccess();
        $teamId = $this->getCaptainTeamId();

        $model = new InventoryModel();
        $model->seedDummyItemsIfEmpty($teamId, (string) ($_SESSION['nic'] ?? $_SESSION['user_nic'] ?? ''));
        $snapshot = $model->getSnapshot($teamId);
        $items = [];
        foreach ($snapshot['items'] as $row) {
            $items[] = $this->mapItem($row);
        }

        $stats = $snapshot['stats'];
        $this->respond([
            'status' => 'success',
            'data' => [
                'items' => $items,
                'stats' => [
                    'total' => (int) ($stats->total ?? 0),
                    'in_use' => (int) ($stats->in_use ?? 0),
                    'available' => (int) ($stats->available ?? 0),
                    'damaged' => (int) ($stats->damaged ?? 0),
                ],
                'category_stats' => $snapshot['category_stats'],
                'team_id' => $teamId,
            ],
        ]);
    }
}
