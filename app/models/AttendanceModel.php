<?php

class AttendanceModel
{
    use Model;

    protected $table = 'attendance';

    /**
     * Mark or update attendance for a player
     */
    public function mark($session_id, $player_id, $status, $marked_by)
    {
        // Check if already exists
        $existing = $this->where([
            'session_id' => $session_id,
            'player_id'  => $player_id
        ]);

        if ($existing) {
            // Update
            return $this->update(
                $existing[0]->id,
                [
                    'status'     => $status,
                    'marked_by'  => $marked_by,
                    'marked_at'  => date('Y-m-d H:i:s')
                ]
            );
        }

        // Insert new
        return $this->insert([
            'session_id' => $session_id,
            'player_id'  => $player_id,
            'status'     => $status,
            'marked_by'  => $marked_by,
            'marked_at'  => date('Y-m-d H:i:s')
        ]);
    }

    /**
     * Get attendance list for a session
     */
    public function getBySession($session_id)
    {
        $query = "
            SELECT 
                p.id AS player_id,
                p.full_name,
                p.position,
                a.status
            FROM players p
            LEFT JOIN attendance a 
                ON p.id = a.player_id 
                AND a.session_id = :session_id
            ORDER BY p.jersey_number ASC
        ";

        return $this->query($query, ['session_id' => $session_id]);
    }

    /**
     * Attendance statistics (Present / Absent / Late)
     */
    public function getStats($session_id)
    {
        $query = "
            SELECT status, COUNT(*) AS total
            FROM {$this->table}
            WHERE session_id = :session_id
            GROUP BY status
        ";

        return $this->query($query, ['session_id' => $session_id]);
    }

    /**
     * Attendance rate for chart (weekly)
     */
    public function getWeeklyRate($team_id)
    {
        $query = "
            SELECT 
                DAYNAME(s.session_date) AS day,
                ROUND(
                    (SUM(a.status = 'present') / COUNT(p.id)) * 100
                ) AS rate
            FROM sessions s
            JOIN players p ON p.team_id = s.team_id
            LEFT JOIN attendance a 
                ON a.session_id = s.id 
                AND a.player_id = p.id
            WHERE s.team_id = :team_id
            GROUP BY s.session_date
            ORDER BY s.session_date
        ";

        return $this->query($query, ['team_id' => $team_id]);
    }
}
