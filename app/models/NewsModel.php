<?php

class NewsModel
{
    use Model;

    protected $table = "news";

    /**
     * Create a new news article
     */
    public function create($data)
    {
        // Required fields - map from form fields to database columns
        if (empty($data['news_heading'])) {
            return false;
        }
        
        // Map form fields to database columns
        $newsData = [
            'title' => $data['news_heading'],
            'content' => $data['news_body'] ?? '',
            'publish_date' => $data['news_date'] ?? date('Y-m-d'),
            'image' => $data['image_name'] ?? null,
            'author_id' => $data['author'] ?? null,
            'status' => $data['status'] ?? 'published'
        ];
        
        $query = "INSERT INTO {$this->table} 
                  (title, content, publish_date, image, author_id, status) 
                  VALUES 
                  (:title, :content, :publish_date, :image, :author_id, :status)";
        
        try {
            $this->query($query, $newsData);
            return true;
        } catch (Exception $e) {
            error_log("NewsModel::create() error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Update news article
     */
    public function update($id, $data)
    {
        if (empty($data)) {
            return false;
        }
        
        // Map form fields to database columns
        $mappedData = [];
        $mapping = [
            'news_heading' => 'title',
            'news_body' => 'content',
            'news_date' => 'publish_date',
            'image_name' => 'image',
            'author' => 'author_id',
            'category' => 'category',
            'status' => 'status'
        ];
        
        foreach ($data as $key => $value) {
            $dbColumn = $mapping[$key] ?? $key;
            $mappedData[$dbColumn] = $value;
        }
        
        $fields = [];
        $params = ['id' => $id];
        
        foreach ($mappedData as $key => $value) {
            $fields[] = "$key = :$key";
            $params[$key] = $value;
        }
        
        $fieldString = implode(', ', $fields);
        $query = "UPDATE {$this->table} SET $fieldString WHERE id = :id";
        
        try {
            $this->query($query, $params);
            return true;
        } catch (Exception $e) {
            error_log("NewsModel::update() error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get all news articles
     */
    public function getAll()
    {
        return $this->query("SELECT * FROM {$this->table} ORDER BY publish_date DESC, id DESC");
    }

    /**
     * Get news by ID
     */
    public function getById($id)
    {
        $result = $this->query("SELECT * FROM {$this->table} WHERE id = :id LIMIT 1", ['id' => $id]);
        return $result ? $result[0] : null;
    }

    /**
     * Delete news article
     */
    public function delete($id)
    {
        try {
            $this->query("DELETE FROM {$this->table} WHERE id = :id", ['id' => $id]);
            return true;
        } catch (Exception $e) {
            error_log("NewsModel::delete() error: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get latest news articles
     */
    public function getLatestNews($limit = 3)
    {
        $limit = (int)$limit;
        $query = "SELECT * FROM {$this->table} ORDER BY publish_date DESC, id DESC LIMIT $limit";
        
        try {
            return $this->query($query);
        } catch (Exception $e) {
            error_log("NewsModel::getLatestNews() error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get news by status
     */
    public function getByStatus($status)
    {
        return $this->query("SELECT * FROM {$this->table} WHERE status = :status ORDER BY publish_date DESC", [
            'status' => $status
        ]);
    }

    /**
     * Search news
     */
    public function search($keyword)
    {
        $keyword = '%' . $keyword . '%';
        return $this->query(
            "SELECT * FROM {$this->table} 
             WHERE title LIKE :keyword OR content LIKE :keyword
             ORDER BY publish_date DESC",
            ['keyword' => $keyword]
        );
    }

    /**
     * Legacy method for compatibility
     */
    public function insertNews($heading, $body, $date, $imageName)
    {
        return $this->create([
            'news_heading' => $heading,
            'news_body' => $body,
            'news_date' => $date,
            'image_name' => $imageName
        ]);
    }
}
