<?php

class InventoryModel
{
    use Model;

    protected $table = 'inventory_items';
    private $columnCache = null;
    private $inventoryLogReady = false;

    private function getColumns()
    {
        if ($this->columnCache !== null) {
            return $this->columnCache;
        }

        $this->columnCache = [];
        $columns = $this->query("SHOW COLUMNS FROM inventory_items");

        foreach ($columns as $column) {
            if (isset($column->Field)) {
                $this->columnCache[$column->Field] = true;
            }
        }

        return $this->columnCache;
    }

    private function hasColumn($name)
    {
        $columns = $this->getColumns();
        return isset($columns[$name]);
    }

    private function ensureStatusColumn()
    {
        if ($this->hasColumn('status')) {
            return;
        }

        try {
            $this->query("ALTER TABLE inventory_items ADD COLUMN status VARCHAR(20) NOT NULL DEFAULT 'Available'");
            $this->columnCache = null;
        } catch (Exception $e) {
            // Keep legacy behavior if ALTER is not permitted.
        }
    }

    private function statusExpression()
    {
        if ($this->hasColumn('status')) {
            return "status";
        }

        return "CASE
            WHEN (total_count - available_count) > 0 THEN 'In Use'
            ELSE 'Available'
        END";
    }

    private function categoryExpression()
    {
        if ($this->hasColumn('category')) {
            return "category";
        }

        return "'General'";
    }

    private function locationExpression()
    {
        if ($this->hasColumn('location')) {
            return "location";
        }

        return "''";
    }

    private function descriptionExpression()
    {
        if ($this->hasColumn('description')) {
            return "description";
        }

        return "''";
    }

    private function unitExpression()
    {
        if ($this->hasColumn('unit')) {
            return "unit";
        }

        return "'pcs'";
    }

    private function iconExpression()
    {
        if ($this->hasColumn('icon')) {
            return "icon";
        }

        return "'inventory_2'";
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

    private function deriveAvailableCountForStatus($totalCount, $availableCount, $status)
    {
        $total = max(0, (int) $totalCount);
        $currentAvailable = max(0, min($total, (int) $availableCount));
        $label = $this->normalizeStatusLabel($status);

        if ($label === 'Available') {
            return $total;
        }

        // Without a dedicated status column, non-available states are represented as not available.
        return 0;
    }

    private function teamWhereClause($alias = '')
    {
        if (!$this->hasColumn('team_id')) {
            return '';
        }

        $prefix = $alias !== '' ? ($alias . '.') : '';
        return " WHERE {$prefix}team_id = :team_id";
    }

    private function withTeamParam($params, $teamId)
    {
        if ($this->hasColumn('team_id')) {
            $params['team_id'] = (int) $teamId;
        }

        return $params;
    }

    private function resolveUpdatedByNic($candidateNic)
    {
        $nic = trim((string) $candidateNic);
        if ($nic !== '') {
            $rows = $this->query("SELECT nic FROM users WHERE nic = :nic LIMIT 1", ['nic' => $nic]);
            if (!empty($rows)) {
                return $nic;
            }
        }

        $rows = $this->query("SELECT nic FROM users ORDER BY nic ASC LIMIT 1");
        return (string) ($rows[0]->nic ?? '');
    }

    private function ensureInventoryLogTable()
    {
        if ($this->inventoryLogReady) {
            return;
        }

        try {
            $this->query(
                "CREATE TABLE IF NOT EXISTS inventory_log (
                    log_id INT(32) PRIMARY KEY AUTO_INCREMENT,
                    item_id INT(32) NOT NULL,
                    taken_by INT(32) NOT NULL,
                    quantity INT(32) NOT NULL,
                    taken_date DATE NOT NULL,
                    return_date DATE DEFAULT NULL,
                    FOREIGN KEY (item_id) REFERENCES inventory_items(item_id) ON DELETE CASCADE ON UPDATE CASCADE,
                    FOREIGN KEY (taken_by) REFERENCES players(player_id) ON DELETE RESTRICT ON UPDATE CASCADE
                )"
            );
        } catch (Exception $e) {
            // If table creation is not allowed, queries below will throw with a clear message.
        }

        $this->inventoryLogReady = true;
    }

    private function statusFromCounts($totalCount, $availableCount, $existingStatus = 'Available')
    {
        $existing = $this->normalizeStatusLabel($existingStatus);
        if ($existing === 'Damaged') {
            return 'Damaged';
        }

        $total = max(0, (int) $totalCount);
        $available = max(0, min($total, (int) $availableCount));

        if ($total > 0 && $available === $total) {
            return 'Available';
        }

        return 'In Use';
    }

    public function addItem($data, $teamId = null)
    {
        $this->ensureStatusColumn();

        $columns = ['item_name', 'total_count', 'available_count', 'updated_by'];
        $values = [':item_name', ':total_count', ':available_count', ':updated_by'];
        $normalizedStatus = $this->normalizeStatusLabel($data['status'] ?? 'Available');
        $derivedAvailable = $this->deriveAvailableCountForStatus(
            $data['total_count'],
            $data['available_count'],
            $normalizedStatus
        );

        $params = [
            'item_name' => $data['item_name'],
            'total_count' => $data['total_count'],
            'available_count' => $derivedAvailable,
            'updated_by' => $data['updated_by']
        ];

        if ($this->hasColumn('team_id')) {
            $columns[] = 'team_id';
            $values[] = ':team_id';
            $params['team_id'] = (int) ($teamId ?? ($data['team_id'] ?? 0));
        }

        if ($this->hasColumn('category')) {
            $columns[] = 'category';
            $values[] = ':category';
            $params['category'] = $data['category'] ?? 'General';
        }

        if ($this->hasColumn('status')) {
            $columns[] = 'status';
            $values[] = ':status';
            $params['status'] = $normalizedStatus;
        }

        if ($this->hasColumn('location')) {
            $columns[] = 'location';
            $values[] = ':location';
            $params['location'] = $data['location'] ?? '';
        }

        if ($this->hasColumn('description')) {
            $columns[] = 'description';
            $values[] = ':description';
            $params['description'] = $data['description'] ?? '';
        }

        if ($this->hasColumn('unit')) {
            $columns[] = 'unit';
            $values[] = ':unit';
            $params['unit'] = $data['unit'] ?? 'pcs';
        }

        if ($this->hasColumn('icon')) {
            $columns[] = 'icon';
            $values[] = ':icon';
            $params['icon'] = $data['icon'] ?? 'inventory_2';
        }

        $query = "INSERT INTO inventory_items (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $values) . ")";

        return $this->query($query, $params);
    }

    public function getAllItems($teamId)
    {
        $this->ensureStatusColumn();

        $categoryExpr = $this->categoryExpression();
        $statusExpr = $this->statusExpression();
        $locationExpr = $this->locationExpression();
        $descriptionExpr = $this->descriptionExpression();
        $unitExpr = $this->unitExpression();
        $iconExpr = $this->iconExpression();
        $where = $this->teamWhereClause();
        $params = $this->withTeamParam([], $teamId);

        $query = "SELECT
            item_id,
            item_name,
            {$categoryExpr} AS category,
            total_count,
            available_count,
            {$statusExpr} AS status,
            {$locationExpr} AS location,
            {$descriptionExpr} AS description,
            {$unitExpr} AS unit,
            {$iconExpr} AS icon,
            last_updated,
            updated_by
            FROM inventory_items
            {$where}
            ORDER BY last_updated DESC";

        return $this->query($query, $params);
    }

    public function getStats($teamId)
    {
        $this->ensureStatusColumn();

        $where = $this->teamWhereClause();
        $params = $this->withTeamParam([], $teamId);

        if ($this->hasColumn('status')) {
            $query = "SELECT
                COALESCE(SUM(total_count), 0) as total,
                COALESCE(SUM(CASE WHEN status='In Use' THEN total_count ELSE 0 END), 0) as in_use,
                COALESCE(SUM(CASE WHEN status='Available' THEN total_count ELSE 0 END), 0) as available,
                COALESCE(SUM(CASE WHEN status='Damaged' THEN total_count ELSE 0 END), 0) as damaged
                FROM inventory_items
                {$where}";
        } else {
            $query = "SELECT
                COALESCE(SUM(total_count), 0) as total,
                COALESCE(SUM(total_count - available_count), 0) as in_use,
                COALESCE(SUM(available_count), 0) as available,
                0 as damaged
                FROM inventory_items
                {$where}";
        }

        $rows = $this->query($query, $params);
        return $rows[0] ?? (object) ['total' => 0, 'in_use' => 0, 'available' => 0, 'damaged' => 0];
    }

    public function getItemById($id, $teamId)
    {
        $this->ensureStatusColumn();

        $categoryExpr = $this->categoryExpression();
        $statusExpr = $this->statusExpression();
        $locationExpr = $this->locationExpression();
        $descriptionExpr = $this->descriptionExpression();
        $unitExpr = $this->unitExpression();
        $iconExpr = $this->iconExpression();
        $where = $this->hasColumn('team_id') ? ' AND team_id = :team_id' : '';

        $params = ['item_id' => (int) $id];
        if ($this->hasColumn('team_id')) {
            $params['team_id'] = (int) $teamId;
        }

        $rows = $this->query(
            "SELECT
                item_id,
                item_name,
                {$categoryExpr} AS category,
                total_count,
                available_count,
                {$statusExpr} AS status,
                {$locationExpr} AS location,
                {$descriptionExpr} AS description,
                {$unitExpr} AS unit,
                {$iconExpr} AS icon,
                last_updated,
                updated_by
             FROM inventory_items
             WHERE item_id = :item_id{$where}",
            $params
        );

        return $rows[0] ?? null;
    }

    public function updateItem($data, $teamId)
    {
        $this->ensureStatusColumn();

        $set = [
            'item_name = :item_name',
            'total_count = :total_count',
            'available_count = :available_count',
            'updated_by = :updated_by'
        ];

        $normalizedStatus = $this->normalizeStatusLabel($data['status'] ?? 'Available');
        $derivedAvailable = $this->deriveAvailableCountForStatus(
            $data['total_count'],
            $data['available_count'],
            $normalizedStatus
        );

        $params = [
            'item_id' => $data['item_id'],
            'item_name' => $data['item_name'],
            'total_count' => $data['total_count'],
            'available_count' => $derivedAvailable,
            'updated_by' => $data['updated_by'] ?? null
        ];

        if ($this->hasColumn('category')) {
            $set[] = 'category = :category';
            $params['category'] = $data['category'] ?? 'General';
        }

        if ($this->hasColumn('status')) {
            $set[] = 'status = :status';
            $params['status'] = $normalizedStatus;
        }

        if ($this->hasColumn('location')) {
            $set[] = 'location = :location';
            $params['location'] = $data['location'] ?? '';
        }

        if ($this->hasColumn('description')) {
            $set[] = 'description = :description';
            $params['description'] = $data['description'] ?? '';
        }

        if ($this->hasColumn('unit')) {
            $set[] = 'unit = :unit';
            $params['unit'] = $data['unit'] ?? 'pcs';
        }

        if ($this->hasColumn('icon')) {
            $set[] = 'icon = :icon';
            $params['icon'] = $data['icon'] ?? 'inventory_2';
        }

        $where = 'item_id = :item_id';
        if ($this->hasColumn('team_id')) {
            $where .= ' AND team_id = :team_id';
            $params['team_id'] = (int) $teamId;
        }

        $query = "UPDATE inventory_items SET " . implode(', ', $set) . " WHERE {$where}";

        return $this->query($query, $params);
    }

    public function deleteItem($id, $teamId)
    {
        $params = ['id' => (int) $id];
        $where = 'item_id = :id';

        if ($this->hasColumn('team_id')) {
            $where .= ' AND team_id = :team_id';
            $params['team_id'] = (int) $teamId;
        }

        $query = "DELETE FROM inventory_items WHERE {$where}";
        return $this->query($query, $params);
    }

    public function getCategoryStats($teamId)
    {
        $this->ensureStatusColumn();

        $categoryExpr = $this->categoryExpression();
        $where = $this->teamWhereClause();
        $params = $this->withTeamParam([], $teamId);

        if ($this->hasColumn('status')) {
            $query = "SELECT
                {$categoryExpr} AS category,
                COALESCE(SUM(CASE WHEN status='In Use' THEN total_count ELSE 0 END), 0) as in_use,
                COALESCE(SUM(CASE WHEN status='Available' THEN total_count ELSE 0 END), 0) as available,
                COALESCE(SUM(CASE WHEN status='Damaged' THEN total_count ELSE 0 END), 0) as damaged
            FROM inventory_items
            {$where}
            GROUP BY {$categoryExpr}";
        } else {
            $query = "SELECT
                {$categoryExpr} AS category,
                COALESCE(SUM(total_count - available_count), 0) as in_use,
                COALESCE(SUM(available_count), 0) as available,
                0 as damaged
            FROM inventory_items
            {$where}
            GROUP BY {$categoryExpr}";
        }

        return $this->query($query, $params);
    }

    public function getSnapshot($teamId)
    {
        return [
            'items' => $this->getAllItems($teamId),
            'stats' => $this->getStats($teamId),
            'category_stats' => $this->getCategoryStats($teamId),
        ];
    }

    public function seedDummyItemsIfEmpty($teamId, $updatedBy)
    {
        $teamId = (int) $teamId;
        if ($teamId <= 0 && $this->hasColumn('team_id')) {
            return;
        }

        $params = $this->withTeamParam([], $teamId);
        $where = $this->teamWhereClause();
        $rows = $this->query("SELECT COUNT(*) AS count FROM inventory_items{$where}", $params);
        $count = (int) ($rows[0]->count ?? 0);
        if ($count > 0) {
            return;
        }

        $validUpdatedBy = $this->resolveUpdatedByNic($updatedBy);
        if ($validUpdatedBy === '') {
            return;
        }

        $dummyItems = [
            ['name' => 'Training Bibs', 'category' => 'Training', 'qty' => 30, 'status' => 'Available', 'location' => 'Equipment Room', 'desc' => 'Color bibs for training drills.', 'unit' => 'pcs', 'icon' => 'checkroom'],
            ['name' => 'Match Footballs', 'category' => 'Match', 'qty' => 12, 'status' => 'Available', 'location' => 'Storage Rack A', 'desc' => 'Official-size footballs for matches.', 'unit' => 'pcs', 'icon' => 'sports_soccer'],
            ['name' => 'Training Cones', 'category' => 'Training', 'qty' => 40, 'status' => 'In Use', 'location' => 'Training Shed', 'desc' => 'Cones used for agility patterns.', 'unit' => 'pcs', 'icon' => 'sports_bar'],
            ['name' => 'Resistance Bands', 'category' => 'Fitness', 'qty' => 18, 'status' => 'Available', 'location' => 'Gym Cabinet', 'desc' => 'Bands for warm-up and strength work.', 'unit' => 'pcs', 'icon' => 'fitness_center'],
            ['name' => 'Ice Packs', 'category' => 'Recovery', 'qty' => 10, 'status' => 'Damaged', 'location' => 'Medical Box', 'desc' => 'Reusable cold packs for recovery.', 'unit' => 'pcs', 'icon' => 'inventory_2'],
            ['name' => 'Water Bottles', 'category' => 'Recovery', 'qty' => 25, 'status' => 'Available', 'location' => 'Hydration Station', 'desc' => 'Reusable team hydration bottles.', 'unit' => 'pcs', 'icon' => 'sports_bar'],
        ];

        foreach ($dummyItems as $item) {
            $this->addItem([
                'item_name' => $item['name'],
                'category' => $item['category'],
                'total_count' => $item['qty'],
                'available_count' => $item['qty'],
                'status' => $item['status'],
                'location' => $item['location'],
                'description' => $item['desc'],
                'unit' => $item['unit'],
                'icon' => $item['icon'],
                'updated_by' => $validUpdatedBy,
            ], $teamId);
        }
    }

    public function getPlayerOpenLogs($playerId, $teamId)
    {
        $this->ensureInventoryLogTable();

        $params = ['player_id' => (int) $playerId];
        $teamWhere = '';
        if ($this->hasColumn('team_id')) {
            $teamWhere = ' AND i.team_id = :team_id';
            $params['team_id'] = (int) $teamId;
        }

        return $this->query(
            "SELECT
                l.log_id,
                l.item_id,
                l.quantity,
                l.taken_date,
                l.return_date,
                i.item_name,
                " . $this->categoryExpression() . " AS category,
                " . $this->unitExpression() . " AS unit
             FROM inventory_log l
             JOIN inventory_items i ON i.item_id = l.item_id
             WHERE l.taken_by = :player_id
               AND l.return_date IS NULL{$teamWhere}
             ORDER BY l.taken_date DESC, l.log_id DESC",
            $params
        );
    }

    public function getPlayerRecentLogs($playerId, $teamId, $limit = 15)
    {
        $this->ensureInventoryLogTable();

        $params = ['player_id' => (int) $playerId];
        $teamWhere = '';
        if ($this->hasColumn('team_id')) {
            $teamWhere = ' AND i.team_id = :team_id';
            $params['team_id'] = (int) $teamId;
        }

        $limitVal = max(1, (int) $limit);

        return $this->query(
            "SELECT
                l.log_id,
                l.item_id,
                l.quantity,
                l.taken_date,
                l.return_date,
                i.item_name,
                " . $this->categoryExpression() . " AS category,
                " . $this->unitExpression() . " AS unit
             FROM inventory_log l
             JOIN inventory_items i ON i.item_id = l.item_id
             WHERE l.taken_by = :player_id
               AND l.return_date IS NOT NULL{$teamWhere}
             ORDER BY l.return_date DESC, l.log_id DESC
             LIMIT {$limitVal}",
            $params
        );
    }

    public function getOpenTakenSummaryByItem($teamId)
    {
        $this->ensureInventoryLogTable();

        $params = [];
        $teamWhere = '';
        if ($this->hasColumn('team_id')) {
            $teamWhere = ' AND i.team_id = :team_id';
            $params['team_id'] = (int) $teamId;
        }

        return $this->query(
            "SELECT
                l.item_id,
                COALESCE(SUM(l.quantity), 0) AS borrowed_qty,
                GROUP_CONCAT(
                    CONCAT(
                        COALESCE(NULLIF(TRIM(CONCAT(COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, ''))), ''), p.nic),
                        ' (',
                        l.quantity,
                        ')'
                    )
                    ORDER BY l.log_id ASC
                    SEPARATOR ', '
                ) AS taken_by
             FROM inventory_log l
             JOIN inventory_items i ON i.item_id = l.item_id
             JOIN players p ON p.player_id = l.taken_by
             JOIN users u ON u.nic = p.nic
             WHERE l.return_date IS NULL{$teamWhere}
             GROUP BY l.item_id",
            $params
        );
    }

    public function takeItemForPlayer($itemId, $playerId, $teamId, $quantity, $updatedByNic)
    {
        $this->ensureStatusColumn();
        $this->ensureInventoryLogTable();

        $itemId = (int) $itemId;
        $playerId = (int) $playerId;
        $teamId = (int) $teamId;
        $quantity = max(1, (int) $quantity);

        $item = $this->getItemById($itemId, $teamId);
        if ($item === null) {
            throw new Exception('Item not found');
        }

        $itemStatus = $this->normalizeStatusLabel($item->status ?? 'Available');
        if ($itemStatus === 'Damaged') {
            throw new Exception('Damaged items cannot be taken');
        }

        $available = max(0, (int) ($item->available_count ?? 0));
        if ($available < $quantity) {
            throw new Exception('Requested quantity is not available');
        }

        $total = max(0, (int) ($item->total_count ?? 0));
        $newAvailable = max(0, $available - $quantity);
        $newStatus = $this->statusFromCounts($total, $newAvailable, $item->status ?? 'Available');
        $updatedBy = $this->resolveUpdatedByNic($updatedByNic);
        if ($updatedBy === '') {
            throw new Exception('Unable to resolve update user');
        }

        $params = [
            'item_id' => $itemId,
            'available_count' => $newAvailable,
            'updated_by' => $updatedBy,
        ];

        $where = 'item_id = :item_id';
        if ($this->hasColumn('team_id')) {
            $where .= ' AND team_id = :team_id';
            $params['team_id'] = $teamId;
        }

        if ($this->hasColumn('status')) {
            $params['status'] = $newStatus;
            $this->query(
                "UPDATE inventory_items
                 SET available_count = :available_count,
                     status = :status,
                     updated_by = :updated_by
                 WHERE {$where}",
                $params
            );
        } else {
            $this->query(
                "UPDATE inventory_items
                 SET available_count = :available_count,
                     updated_by = :updated_by
                 WHERE {$where}",
                $params
            );
        }

        $this->query(
            "INSERT INTO inventory_log (item_id, taken_by, quantity, taken_date, return_date)
             VALUES (:item_id, :player_id, :quantity, CURDATE(), NULL)",
            [
                'item_id' => $itemId,
                'player_id' => $playerId,
                'quantity' => $quantity,
            ]
        );

        return [
            'item_id' => $itemId,
            'taken_quantity' => $quantity,
            'available_count' => $newAvailable,
            'status' => $newStatus,
        ];
    }

    public function returnItemForPlayer($logId, $playerId, $teamId, $updatedByNic)
    {
        $this->ensureStatusColumn();
        $this->ensureInventoryLogTable();

        $logId = (int) $logId;
        $playerId = (int) $playerId;
        $teamId = (int) $teamId;

        if ($logId <= 0) {
            throw new Exception('Log ID is required');
        }

        $params = [
            'log_id' => $logId,
            'player_id' => $playerId,
        ];

        $teamWhere = '';
        if ($this->hasColumn('team_id')) {
            $teamWhere = ' AND i.team_id = :team_id';
            $params['team_id'] = $teamId;
        }

        $rows = $this->query(
            "SELECT
                l.log_id,
                l.item_id,
                l.quantity,
                i.total_count,
                i.available_count,
                " . $this->statusExpression() . " AS status
             FROM inventory_log l
             JOIN inventory_items i ON i.item_id = l.item_id
             WHERE l.log_id = :log_id
               AND l.taken_by = :player_id
               AND l.return_date IS NULL{$teamWhere}",
            $params
        );

        $log = $rows[0] ?? null;
        if ($log === null) {
            throw new Exception('Borrow record not found');
        }

        $total = max(0, (int) ($log->total_count ?? 0));
        $available = max(0, (int) ($log->available_count ?? 0));
        $returnQty = max(0, (int) ($log->quantity ?? 0));
        $newAvailable = min($total, $available + $returnQty);
        $newStatus = $this->statusFromCounts($total, $newAvailable, $log->status ?? 'In Use');
        $updatedBy = $this->resolveUpdatedByNic($updatedByNic);
        if ($updatedBy === '') {
            throw new Exception('Unable to resolve update user');
        }

        $itemParams = [
            'item_id' => (int) $log->item_id,
            'available_count' => $newAvailable,
            'updated_by' => $updatedBy,
        ];

        $where = 'item_id = :item_id';
        if ($this->hasColumn('team_id')) {
            $where .= ' AND team_id = :team_id';
            $itemParams['team_id'] = $teamId;
        }

        if ($this->hasColumn('status')) {
            $itemParams['status'] = $newStatus;
            $this->query(
                "UPDATE inventory_items
                 SET available_count = :available_count,
                     status = :status,
                     updated_by = :updated_by
                 WHERE {$where}",
                $itemParams
            );
        } else {
            $this->query(
                "UPDATE inventory_items
                 SET available_count = :available_count,
                     updated_by = :updated_by
                 WHERE {$where}",
                $itemParams
            );
        }

        $this->query(
            "UPDATE inventory_log
             SET return_date = CURDATE()
             WHERE log_id = :log_id
               AND taken_by = :player_id
               AND return_date IS NULL",
            [
                'log_id' => $logId,
                'player_id' => $playerId,
            ]
        );

        return [
            'log_id' => $logId,
            'item_id' => (int) $log->item_id,
            'returned_quantity' => $returnQty,
            'available_count' => $newAvailable,
            'status' => $newStatus,
        ];
    }
}