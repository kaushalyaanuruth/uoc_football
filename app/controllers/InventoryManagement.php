 <?php

class InventoryManagement extends Controller
{
    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    private function seedInventory()
    {
        if (!isset($_SESSION['inventory_items']) || !is_array($_SESSION['inventory_items']) || empty($_SESSION['inventory_items'])) {
            $_SESSION['inventory_items'] = [
                [
                    'id' => 1,
                    'name' => 'Bibs',
                    'category' => 'Training',
                    'quantity' => 45,
                    'unit' => 'pcs',
                    'status' => 'available',
                    'location' => 'Equipment Room',
                    'description' => 'Training bibs for session drills.',
                    'icon' => 'bips'
                ],
                [
                    'id' => 2,
                    'name' => 'Footballs',
                    'category' => 'Match',
                    'quantity' => 12,
                    'unit' => 'pcs',
                    'status' => 'low',
                    'location' => 'Storage Room',
                    'description' => 'Official footballs for practice and matches.',
                    'icon' => 'football'
                ],
                [
                    'id' => 3,
                    'name' => 'Markers',
                    'category' => 'Training',
                    'quantity' => 3,
                    'unit' => 'sets',
                    'status' => 'low',
                    'location' => 'Equipment Room',
                    'description' => 'Used for pitch marking drills.',
                    'icon' => 'markers'
                ],
                [
                    'id' => 4,
                    'name' => 'Cones',
                    'category' => 'Training',
                    'quantity' => 24,
                    'unit' => 'pcs',
                    'status' => 'available',
                    'location' => 'Training Store',
                    'description' => 'Flexible training cones for drills.',
                    'icon' => 'cones'
                ],
                [
                    'id' => 5,
                    'name' => 'Resistance Band',
                    'category' => 'Fitness',
                    'quantity' => 5,
                    'unit' => 'pcs',
                    'status' => 'reserved',
                    'location' => 'Gym Locker',
                    'description' => 'Used for warm-up and strength work.',
                    'icon' => 'resistance_band'
                ],
                [
                    'id' => 6,
                    'name' => 'Water Bottles',
                    'category' => 'Recovery',
                    'quantity' => 18,
                    'unit' => 'pcs',
                    'status' => 'available',
                    'location' => 'Kitchen Area',
                    'description' => 'Bottles for hydration during sessions.',
                    'icon' => 'water_bottle'
                ]
            ];
        }
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
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . ROOT . '/login');
            exit;
        }

        $this->seedInventory();

        // Normalize existing session icon values so legacy data does not render all rows with one icon.
        foreach ($_SESSION['inventory_items'] as &$item) {
            $item['icon'] = $this->normalizeStoredIcon($item['icon'] ?? '', $item['name'] ?? '');
        }
        unset($item);

        $items = $_SESSION['inventory_items'];
        $totalItems = count($items);
        $availableItems = 0;
        $lowStockItems = 0;
        $reservedItems = 0;

        foreach ($items as $item) {
            if (($item['status'] ?? '') === 'available') {
                $availableItems++;
            }

            if (($item['status'] ?? '') === 'low') {
                $lowStockItems++;
            }

            if (($item['status'] ?? '') === 'reserved') {
                $reservedItems++;
            }
        }

        $data = [
            'items' => $items,
            'stats' => [
                'totalItems' => $totalItems,
                'availableItems' => $availableItems,
                'lowStockItems' => $lowStockItems,
                'reservedItems' => $reservedItems
            ],
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
            $this->seedInventory();
            $input = $this->jsonInput();

            if (empty(trim($input['name'] ?? ''))) {
                throw new Exception('Item name is required');
            }

            $items = $_SESSION['inventory_items'];
            $nextId = 1;
            foreach ($items as $item) {
                $nextId = max($nextId, (int) $item['id'] + 1);
            }

            $newItem = [
                'id' => $nextId,
                'name' => trim($input['name'] ?? ''),
                'category' => trim($input['category'] ?? 'General'),
                'quantity' => (int) ($input['quantity'] ?? 0),
                'unit' => trim($input['unit'] ?? 'pcs'),
                'status' => trim($input['status'] ?? 'available'),
                'location' => trim($input['location'] ?? ''),
                'description' => trim($input['description'] ?? ''),
                'icon' => trim($input['icon'] ?? 'inventory_2')
            ];

            $_SESSION['inventory_items'][] = $newItem;

            $this->respond([
                'success' => true,
                'message' => 'Item added successfully',
                'item' => $newItem
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
            $this->seedInventory();
            $input = $this->jsonInput();
            $id = (int) ($input['id'] ?? 0);

            if (!$id) {
                throw new Exception('Item ID is required');
            }

            $updatedItem = null;
            foreach ($_SESSION['inventory_items'] as &$item) {
                if ((int) $item['id'] === $id) {
                    $item['name'] = trim($input['name'] ?? $item['name']);
                    $item['category'] = trim($input['category'] ?? $item['category']);
                    $item['quantity'] = (int) ($input['quantity'] ?? $item['quantity']);
                    $item['unit'] = trim($input['unit'] ?? $item['unit']);
                    $item['status'] = trim($input['status'] ?? $item['status']);
                    $item['location'] = trim($input['location'] ?? $item['location']);
                    $item['description'] = trim($input['description'] ?? $item['description']);
                    $item['icon'] = trim($input['icon'] ?? $item['icon']);
                    $updatedItem = $item;
                    break;
                }
            }
            unset($item);

            if (!$updatedItem) {
                throw new Exception('Item not found');
            }

            $this->respond([
                'success' => true,
                'message' => 'Item updated successfully',
                'item' => $updatedItem
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
            $this->seedInventory();
            $input = $this->jsonInput();
            $id = (int) ($input['id'] ?? 0);

            if (!$id) {
                throw new Exception('Item ID is required');
            }

            $items = $_SESSION['inventory_items'];
            $filtered = [];
            $deleted = false;

            foreach ($items as $item) {
                if ((int) $item['id'] === $id) {
                    $deleted = true;
                    continue;
                }
                $filtered[] = $item;
            }

            if (!$deleted) {
                throw new Exception('Item not found');
            }

            $_SESSION['inventory_items'] = $filtered;

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
            $this->seedInventory();
            $id = (int) ($_GET['id'] ?? 0);

            if (!$id) {
                throw new Exception('Item ID is required');
            }

            foreach ($_SESSION['inventory_items'] as $item) {
                if ((int) $item['id'] === $id) {
                    $this->respond([
                        'success' => true,
                        'data' => $item
                    ]);
                }
            }

            throw new Exception('Item not found');
        } catch (Exception $e) {
            $this->respond([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
}
