
<?php

class NewsModel
{
    use Model;

    protected $table = 'news';

    private function normalizePayload(array $data)
    {
        return [
            'title' => trim((string) ($data['title'] ?? $data['news_heading'] ?? '')),
            'date' => $data['date'] ?? $data['news_date'] ?? date('Y-m-d'),
            'description' => trim((string) ($data['description'] ?? $data['discription'] ?? $data['news_body'] ?? '')),
            'image' => $data['image'] ?? $data['image_name'] ?? null
        ];
    }

    public function create($data)
    {
        $payload = $this->normalizePayload($data);

        if ($payload['title'] === '' || $payload['description'] === '') {
            return false;
        }

        $query = "INSERT INTO {$this->table} (title, `date`, description, image) VALUES (:title, :date, :description, :image)";

        try {
            $this->query($query, $payload);
            return true;
        } catch (Exception $e) {
            error_log('NewsModel::create() error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function update($id, $data)
    {
        $payload = $this->normalizePayload($data);
        $fields = [];
        $params = ['id' => $id];

        foreach ($payload as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            $fields[] = ($key === 'date' ? '`date`' : $key) . ' = :' . $key;
            $params[$key] = $value;
        }

        if (empty($fields)) {
            return false;
        }

        $query = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = :id";

        try {
            $this->query($query, $params);
            return true;
        } catch (Exception $e) {
            error_log('NewsModel::update() error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getAll()
    {
        $query = "SELECT id, title, `date`, description, description AS discription, image, `date` AS publish_date, description AS content FROM {$this->table} ORDER BY `date` DESC, id DESC";
        return $this->query($query);
    }

    public function getById($id)
    {
        $query = "SELECT id, title, `date`, description, description AS discription, image, `date` AS publish_date, description AS content FROM {$this->table} WHERE id = :id LIMIT 1";
        $result = $this->query($query, ['id' => $id]);
        return $result ? $result[0] : null;
    }

    public function delete($id)
    {
        try {
            $this->query("DELETE FROM {$this->table} WHERE id = :id", ['id' => $id]);
            return true;
        } catch (Exception $e) {
            error_log('NewsModel::delete() error: ' . $e->getMessage());
            throw $e;
        }
    }

    public function getLatestNews($limit = 3)
    {
        $limit = (int) $limit;
        $query = "SELECT id, title, `date`, description, description AS discription, image, `date` AS publish_date, description AS content FROM {$this->table} ORDER BY `date` DESC, id DESC LIMIT $limit";

        try {
            return $this->query($query);
        } catch (Exception $e) {
            error_log('NewsModel::getLatestNews() error: ' . $e->getMessage());
            return [];
        }
    }

    public function search($keyword)
    {
        $keyword = '%' . $keyword . '%';
        return $this->query(
            "SELECT id, title, `date`, description, description AS discription, image, `date` AS publish_date, description AS content FROM {$this->table} WHERE title LIKE :keyword OR description LIKE :keyword ORDER BY `date` DESC",
            ['keyword' => $keyword]
        );
    }

    public function insertNews($heading, $body, $date, $imageName)
    {
        return $this->create([
            'title' => $heading,
            'description' => $body,
            'date' => $date,
            'image' => $imageName
        ]);
    }
}
