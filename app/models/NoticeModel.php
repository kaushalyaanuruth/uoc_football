<?php

class NoticeModel
{
    use Model;

    protected $table = 'notices';
    private $tableEnsured = false;

    private function ensureTable()
    {
        if ($this->tableEnsured) {
            return;
        }

        $this->query(
            "CREATE TABLE IF NOT EXISTS notices (
                notice_id INT AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255) NOT NULL,
                content TEXT NOT NULL,
                location VARCHAR(255) NULL,
                target_group VARCHAR(50) NOT NULL DEFAULT 'present_team',
                created_by VARCHAR(100) NOT NULL DEFAULT 'Admin',
                created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                is_active TINYINT(1) NOT NULL DEFAULT 1
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
        );

        $this->tableEnsured = true;
    }

    public function getRecent($limit = 10, $targetGroup = 'present_team')
    {
        $this->ensureTable();

        $limit = max(1, (int)$limit);

        return $this->query(
                        "SELECT notice_id, title, content, location, target_group, created_by, created_at
             FROM {$this->table}
             WHERE is_active = 1
               AND (target_group = 'all' OR target_group = :target_group OR target_group = 'present_team')
             ORDER BY created_at DESC
             LIMIT {$limit}",
            ['target_group' => $targetGroup]
        );
    }

    public function getForAdmin($limit = 5)
    {
        $this->ensureTable();

        $limit = max(1, (int)$limit);

        return $this->query(
            "SELECT notice_id, title, content, location, target_group, created_by, created_at
             FROM {$this->table}
             WHERE is_active = 1
             ORDER BY created_at DESC
             LIMIT {$limit}"
        );
    }

    public function createNotice($title, $content, $createdBy = 'Admin', $targetGroup = 'present_team', $location = null)
    {
        $this->ensureTable();

        return $this->query(
            "INSERT INTO {$this->table} (title, content, location, created_by, target_group)
             VALUES (:title, :content, :location, :created_by, :target_group)",
            [
                'title' => $title,
                'content' => $content,
                'location' => $location,
                'created_by' => $createdBy,
                'target_group' => $targetGroup
            ]
        );
    }

    public function getById($noticeId)
    {
        $this->ensureTable();

        $rows = $this->query(
                        "SELECT notice_id, title, content, location, target_group, created_by, created_at
             FROM {$this->table}
             WHERE notice_id = :notice_id
               AND is_active = 1
             LIMIT 1",
            ['notice_id' => (int) $noticeId]
        );

        return $rows[0] ?? null;
    }

    public function updateNotice($noticeId, $title, $content)
    {
        $this->ensureTable();

        return $this->query(
            "UPDATE {$this->table}
             SET title = :title,
                 content = :content
             WHERE notice_id = :notice_id
               AND is_active = 1",
            [
                'notice_id' => (int) $noticeId,
                'title' => $title,
                'content' => $content,
            ]
        );
    }

    public function deleteNotice($noticeId)
    {
        $this->ensureTable();

        return $this->query(
            "UPDATE {$this->table}
             SET is_active = 0
             WHERE notice_id = :notice_id
               AND is_active = 1",
            ['notice_id' => (int) $noticeId]
        );
    }
}
