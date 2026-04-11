<?php

class AttendanceModel
{
    use Model;

    protected $table = 'attendance';

    public function getPlayersWithAttendance($event_id)
    {
        $query = "
        SELECT 
            p.player_id,
            CONCAT(u.first_name, ' ', u.last_name) AS name,
            p.position,
            COALESCE(a.status, 'Absent') AS status
        FROM players p
        JOIN users u ON p.nic = u.nic
        LEFT JOIN attendance a 
            ON p.player_id = a.player_id 
            AND a.event_id = :event_id
        ORDER BY p.player_id ASC
        ";

        return $this->query($query, ['event_id' => $event_id]);
    }

    public function getStats($event_id)
{
    $query = "
    SELECT 
        COUNT(p.player_id) AS total,
        SUM(COALESCE(a.status, 'Absent') = 'Present') AS present,
        SUM(COALESCE(a.status, 'Absent') = 'Absent') AS absent
    FROM players p
    LEFT JOIN attendance a 
        ON p.player_id = a.player_id 
        AND a.event_id = :event_id
    ";

    return $this->query($query, ['event_id' => $event_id])[0];
}
}