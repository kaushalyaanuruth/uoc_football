<?php

trait Model{
    use Database;

    /**
     * Fetch rows matching given conditions.
     * - $conditions: ['col' => 'value', 'status' => ['active','pending'], 'deleted_at' => null]
     * - $options: ['order' => 'id DESC'|['id'=>'DESC'], 'limit' => 10, 'offset' => 20]
     */
    public function where(array $conditions = [], array $options = [])
    {
        $sql = 'SELECT * FROM ' . $this->table;
        $params = [];

        // Filter by allowed columns if defined
        if (!empty($this->allowedColumns) && !empty($conditions)) {
            $conditions = array_intersect_key($conditions, array_flip($this->allowedColumns));
        }

        if (!empty($conditions)) {
            $parts = [];
            foreach ($conditions as $col => $val) {
                // basic column name safety
                if (!preg_match('/^[a-zA-Z0-9_]+$/', $col)) {
                    continue;
                }

                $paramBase = 'w_' . $col;

                if (is_null($val)) {
                    $parts[] = "$col IS NULL";
                } elseif (is_array($val)) {
                    if (empty($val)) {
                        // empty IN never matches anything
                        $parts[] = '1=0';
                        continue;
                    }
                    $inParams = [];
                    foreach (array_values($val) as $i => $v) {
                        $p = "{$paramBase}_{$i}";
                        $inParams[] = ":$p";
                        $params[$p] = $v;
                    }
                    $parts[] = "$col IN (" . implode(', ', $inParams) . ')';
                } else {
                    $parts[] = "$col = :$paramBase";
                    $params[$paramBase] = $val;
                }
            }

            if (!empty($parts)) {
                $sql .= ' WHERE ' . implode(' AND ', $parts);
            }
        }

        // ORDER BY
        if (!empty($options['order'])) {
            if (is_string($options['order'])) {
                if (preg_match('/^[a-zA-Z0-9_]+(\s+(ASC|DESC))?$/', $options['order'])) {
                    $sql .= ' ORDER BY ' . $options['order'];
                }
            } elseif (is_array($options['order'])) {
                $orderParts = [];
                foreach ($options['order'] as $c => $d) {
                    if (preg_match('/^[a-zA-Z0-9_]+$/', $c)) {
                        $d = strtoupper($d) === 'DESC' ? 'DESC' : 'ASC';
                        $orderParts[] = "$c $d";
                    }
                }
                if ($orderParts) {
                    $sql .= ' ORDER BY ' . implode(', ', $orderParts);
                }
            }
        }

        // LIMIT / OFFSET
        if (isset($options['limit'])) {
            $sql .= ' LIMIT ' . (int) $options['limit'];
        }
        if (isset($options['offset'])) {
            $sql .= ' OFFSET ' . (int) $options['offset'];
        }

        return $this->query($sql, $params);
    }
    

    /**
     * Insert a row into the model's table.
     * $data is an associative array: ['col' => 'value', ...]
     */
    public function insert(array $data)
    {
        if (empty($data)) {
            return false;
        }

        if (!empty($this->allowedColumns)) {
            $data = array_intersect_key($data, array_flip($this->allowedColumns));
            if (empty($data)) {
                return false;
            }
        }

        $columns = array_keys($data);
        $placeholders = array_map(fn ($c) => ':' . $c, $columns);

        $sql = 'INSERT INTO ' . $this->table .
               ' (' . implode(', ', $columns) . ') VALUES (' . implode(', ', $placeholders) . ')';

        return $this->query($sql, $data);
    }

    /**
     * Update a row by id.
     * $data is an associative array: ['col' => 'value', ...]
     * $idColumn is the primary key column name (defaults to 'id')
     */
    public function update($id, array $data, string $idColumn = 'id')
    {
        if (empty($data)) {
            return false;
        }

        if (!empty($this->allowedColumns)) {
            $data = array_intersect_key($data, array_flip($this->allowedColumns));
            if (empty($data)) {
                return false;
            }
        }

        $setParts = [];
        foreach ($data as $col => $val) {
            $setParts[] = "$col = :$col";
        }

        $sql = 'UPDATE ' . $this->table .
               ' SET ' . implode(', ', $setParts) .
               " WHERE $idColumn = :_id";

        $params = $data;
        $params['_id'] = $id;

        return $this->query($sql, $params);
    }

    /**
     * Delete a row by id.
     * $idColumn is the primary key column name (defaults to 'id')
     */
    public function delete($id, string $idColumn = 'id')
    {
        $sql = 'DELETE FROM ' . $this->table . " WHERE $idColumn = :_id";
        return $this->query($sql, ['_id' => $id]);
    }
}