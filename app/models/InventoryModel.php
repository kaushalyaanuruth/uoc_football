<?php

class InventoryModel
{
    use Model;

    protected $table = 'inventory_items';
    private $columnCache = null;

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
}