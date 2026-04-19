 <?php

class InventoryManagement extends Controller
{
    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    private function resolveTeamId()
    {
        $teamId = (int) ($_GET['team_id'] ?? $_POST['team_id'] ?? $_SESSION['team_id'] ?? 0);
        if ($teamId > 0) {
            $_SESSION['team_id'] = $teamId;
            return $teamId;
        }

        $teamModel = $this->model('TeamModel');

        // Older local schemas may not have teams.status; detect and fall back safely.
        $hasStatus = false;
        try {
            $statusColumnRows = $teamModel->query("SHOW COLUMNS FROM teams LIKE 'status'");
            $hasStatus = !empty($statusColumnRows);
        } catch (Exception $e) {
            $hasStatus = false;
        }

        if ($hasStatus) {
            $rows = $teamModel->query("SELECT team_id FROM teams WHERE status = 'present' ORDER BY team_id DESC LIMIT 1");
        } else {
            $rows = [];
        }

        if (empty($rows)) {
            $rows = $teamModel->query("SELECT team_id FROM teams ORDER BY team_id DESC LIMIT 1");
        }

        $resolved = (int) ($rows[0]->team_id ?? 0);
        if ($resolved > 0) {
            $_SESSION['team_id'] = $resolved;
        }

        return $resolved;
    }

    private function iconFromName($name)
    {
        $normalizedName = strtolower(trim((string) $name));

        $nameMap = [
            'bibs' => 'checkroom',
            'footballs' => 'sports_soccer',
            'markers' => 'sports_bar',
            'cones' => 'sports_bar',
            'resistance band' => 'fitness_center',
            'water bottles' => 'sports_bar'
        ];

        return $nameMap[$normalizedName] ?? 'inventory_2';
    }

    private function normalizeStoredIcon($icon, $name)
    {
        $normalizedIcon = strtolower(trim((string) $icon));

        $iconMap = [
            'bips' => 'checkroom',
            'bibs' => 'checkroom',
            'football' => 'sports_soccer',
            'footballs' => 'sports_soccer',
            'sports_soccer' => 'sports_soccer',
            'checkroom' => 'checkroom',
            'resistance_band' => 'fitness_center',
            'fitness_center' => 'fitness_center',
            'markers' => 'sports_bar',
            'cones' => 'sports_bar',
            'sports_bar' => 'sports_bar',
            'water_bottle' => 'sports_bar',
            'sports_bottle' => 'sports_bar'
        ];

        if ($normalizedIcon === '' || $normalizedIcon === 'inventory_2' || $normalizedIcon === 'inventory.svg') {
            return $this->iconFromName($name);
        }

        return $iconMap[$normalizedIcon] ?? $this->iconFromName($name);
    }

    private function normalizeStatusKey($status)
    {
        $value = strtolower(trim((string) $status));
        $value = str_replace(['-', '_'], ' ', $value);

        if ($value === 'damaged') {
            return 'damaged';
        }

        if (in_array($value, ['in use', 'inuse', 'low', 'reserved'], true)) {
            return 'in_use';
        }

        return 'available';
    }

    private function statusLabelFromKey($statusKey)
    {
        if ($statusKey === 'damaged') {
            return 'Damaged';
        }

        if ($statusKey === 'in_use') {
            return 'In Use';
        }

        return 'Available';
    }

    private function mapDbItemToView($row)
    {
        $statusKey = $this->normalizeStatusKey($row->status ?? 'Available');

        return [
            'id' => (int) ($row->item_id ?? 0),
            'name' => trim((string) ($row->item_name ?? '')),
            'category' => trim((string) ($row->category ?? 'General')),
            'quantity' => (int) ($row->total_count ?? 0),
            'unit' => trim((string) ($row->unit ?? 'pcs')),
            'status' => $statusKey,
            'status_label' => $this->statusLabelFromKey($statusKey),
            'location' => trim((string) ($row->location ?? '')),
            'description' => trim((string) ($row->description ?? '')),
            'icon' => $this->normalizeStoredIcon($row->icon ?? '', $row->item_name ?? ''),
            'last_updated' => (string) ($row->last_updated ?? ''),
        ];
    }

    private function ensureAuth()
    {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . ROOT . '/login');
            exit;
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

    public function index()
    {
        $this->ensureAuth();
        $teamId = $this->resolveTeamId();

        $model = new InventoryModel();
        $model->seedDummyItemsIfEmpty($teamId, (string) ($_SESSION['nic'] ?? $_SESSION['user_nic'] ?? ''));
        $rows = $model->getAllItems($teamId);
        $items = [];
        foreach ($rows as $row) {
            $items[] = $this->mapDbItemToView($row);
        }

        $stats = $model->getStats($teamId);

        $data = [
            'items' => $items,
            'stats' => [
                'totalItems' => (int) ($stats->total ?? 0),
                'availableItems' => (int) ($stats->available ?? 0),
                'inUseItems' => (int) ($stats->in_use ?? 0),
                'damagedItems' => (int) ($stats->damaged ?? 0)
            ],
            'team_id' => $teamId,
            'title' => 'Inventory Management'
        ];

        $this->view('inventoryManagement', $data);
    }

    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->respond(['success' => false, 'message' => 'Invalid request method']);
        }

        try {
            $this->ensureAuth();
            $teamId = $this->resolveTeamId();
            $input = $this->jsonInput();

            if (empty(trim($input['name'] ?? ''))) {
                throw new Exception('Item name is required');
            }

            $statusKey = $this->normalizeStatusKey($input['status'] ?? 'available');
            $model = new InventoryModel();
            $model->addItem([
                'item_name' => trim((string) ($input['name'] ?? '')),
                'category' => trim((string) ($input['category'] ?? 'General')),
                'total_count' => max(0, (int) ($input['quantity'] ?? 0)),
                'available_count' => max(0, (int) ($input['quantity'] ?? 0)),
                'status' => $this->statusLabelFromKey($statusKey),
                'location' => trim((string) ($input['location'] ?? '')),
                'description' => trim((string) ($input['description'] ?? '')),
                'unit' => trim((string) ($input['unit'] ?? 'pcs')),
                'icon' => trim((string) ($input['icon'] ?? 'inventory_2')),
                'updated_by' => (string) ($_SESSION['nic'] ?? ''),
            ], $teamId);

            $this->respond([
                'success' => true,
                'message' => 'Item added successfully'
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
            $this->ensureAuth();
            $teamId = $this->resolveTeamId();
            $input = $this->jsonInput();
            $id = (int) ($input['id'] ?? 0);

            if (!$id) {
                throw new Exception('Item ID is required');
            }

            $model = new InventoryModel();
            $existing = $model->getItemById($id, $teamId);
            if ($existing === null) {
                throw new Exception('Item not found');
            }

            $statusKey = $this->normalizeStatusKey($input['status'] ?? ($existing->status ?? 'available'));
            $quantity = max(0, (int) ($input['quantity'] ?? $existing->total_count ?? 0));
            $model->updateItem([
                'item_id' => $id,
                'item_name' => trim((string) ($input['name'] ?? $existing->item_name ?? '')),
                'category' => trim((string) ($input['category'] ?? $existing->category ?? 'General')),
                'total_count' => $quantity,
                'available_count' => $quantity,
                'status' => $this->statusLabelFromKey($statusKey),
                'location' => trim((string) ($input['location'] ?? $existing->location ?? '')),
                'description' => trim((string) ($input['description'] ?? $existing->description ?? '')),
                'unit' => trim((string) ($input['unit'] ?? $existing->unit ?? 'pcs')),
                'icon' => trim((string) ($input['icon'] ?? $existing->icon ?? 'inventory_2')),
                'updated_by' => (string) ($_SESSION['nic'] ?? ''),
            ], $teamId);

            $this->respond([
                'success' => true,
                'message' => 'Item updated successfully'
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
            $this->ensureAuth();
            $teamId = $this->resolveTeamId();
            $input = $this->jsonInput();
            $id = (int) ($input['id'] ?? 0);

            if (!$id) {
                throw new Exception('Item ID is required');
            }

            $model = new InventoryModel();
            $existing = $model->getItemById($id, $teamId);
            if ($existing === null) {
                throw new Exception('Item not found');
            }

            $model->deleteItem($id, $teamId);

            $this->respond([
                'success' => true,
                'message' => 'Item deleted successfully'
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
            $this->ensureAuth();
            $teamId = $this->resolveTeamId();
            $id = (int) ($_GET['id'] ?? 0);

            if (!$id) {
                throw new Exception('Item ID is required');
            }

            $model = new InventoryModel();
            $row = $model->getItemById($id, $teamId);
            if ($row === null) {
                throw new Exception('Item not found');
            }

            $this->respond([
                'success' => true,
                'data' => $this->mapDbItemToView($row)
            ]);
        } catch (Exception $e) {
            $this->respond([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function snapshot()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->respond(['success' => false, 'message' => 'Invalid request method']);
        }

        try {
            $this->ensureAuth();
            $teamId = $this->resolveTeamId();

            $model = new InventoryModel();
            $model->seedDummyItemsIfEmpty($teamId, (string) ($_SESSION['nic'] ?? $_SESSION['user_nic'] ?? ''));
            $snapshot = $model->getSnapshot($teamId);

            $items = [];
            foreach ($snapshot['items'] as $row) {
                $items[] = $this->mapDbItemToView($row);
            }

            $stats = $snapshot['stats'];

            $this->respond([
                'success' => true,
                'data' => [
                    'items' => $items,
                    'stats' => [
                        'total' => (int) ($stats->total ?? 0),
                        'available' => (int) ($stats->available ?? 0),
                        'in_use' => (int) ($stats->in_use ?? 0),
                        'damaged' => (int) ($stats->damaged ?? 0),
                    ],
                    'category_stats' => $snapshot['category_stats'],
                    'team_id' => $teamId,
                ]
            ]);
        } catch (Exception $e) {
            $this->respond(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
