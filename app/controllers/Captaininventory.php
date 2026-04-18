<?php

class CaptainInventory extends Controller
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

    public function index()
    {
        $this->ensureCaptainAccess();

        $inventoryModel = new InventoryModel();

        $items = $inventoryModel->getAllItems();

        $stats = $inventoryModel->getStats();
        $uiData = $this->buildCaptainUiData();
        $notices = $this->getCaptainNotices();

        $data = [
            'total' => $stats->total,
            'in_use' => $stats->in_use,
            'available' => $stats->available,
            'damaged' => $stats->damaged,
            'inventory' => $items,
            'captain_name' => $uiData['captain_name'],
            'captain_image' => $uiData['captain_image'],
            'notices' => $notices,
        ];

        $this->view('captain/CaptainInventory', $data);
    }
    public function store()
    {
        $this->ensureCaptainAccess();

        $model = new InventoryModel();

        $data = [
            'item_name' => $_POST['item_name'],
            'category' => $_POST['category'],
            'total_count' => $_POST['quantity'],
            'available_count' => $_POST['quantity'],
            'status' => $_POST['status'],
            'updated_by' => $_SESSION['user_nic'] ?? null
        ];

        $model->addItem($data);

        echo json_encode(["status" => "success"]);
    }
public function update()
{
    $this->ensureCaptainAccess();

    $model = new InventoryModel();

    $data = [
        'item_id' => $_POST['item_id'],
        'item_name' => $_POST['item_name'],
        'category' => $_POST['category'],
        'total_count' => $_POST['quantity'],
        'available_count' => $_POST['quantity'],
        'status' => $_POST['status']
    ];

    $model->updateItem($data);

    echo json_encode(["status" => "success"]);
}
public function delete()
{
    $this->ensureCaptainAccess();

    $model = new InventoryModel();

    $id = $_POST['item_id'];

    $model->deleteItem($id);

    echo json_encode(["status" => "success"]);
}
public function getChartData()
{
    $this->ensureCaptainAccess();

    $model = new InventoryModel();

    $data = $model->getCategoryStats();

    echo json_encode($data);
}
}
