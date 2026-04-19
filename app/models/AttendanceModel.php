<?php

class AttendanceModel
{
    use Model;

    protected $table = 'attendance';
    private $hasEventTypeColumn = null;

    private function eventTypeColumnExists()
    {
        if ($this->hasEventTypeColumn !== null) {
            return $this->hasEventTypeColumn;
        }

        $rows = $this->query("SHOW COLUMNS FROM events LIKE 'event_type'");
        $this->hasEventTypeColumn = !empty($rows);
        return $this->hasEventTypeColumn;
    }

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

    public function getOrCreateEvent($date, $type)
    {
        if ($this->eventTypeColumnExists()) {
            $event = $this->query(
                "SELECT * FROM events WHERE date = :date AND event_type = :type LIMIT 1",
                [
                    'date' => $date,
                    'type' => $type
                ]
            );

            if (!empty($event)) {
                return $event[0];
            }

            $this->query(
                "INSERT INTO events (location, date, event_type) 
                 VALUES ('Ground', :date, :type)",
                [
                    'date' => $date,
                    'type' => $type
                ]
            );

            return $this->query(
                "SELECT * FROM events WHERE date = :date AND event_type = :type LIMIT 1",
                [
                    'date' => $date,
                    'type' => $type
                ]
            )[0];
        }

        $event = $this->query(
            "SELECT * FROM events WHERE date = :date LIMIT 1",
            ['date' => $date]
        );

        if (!empty($event)) {
            return $event[0];
        }

        $this->query(
            "INSERT INTO events (location, date) VALUES ('Ground', :date)",
            ['date' => $date]
        );

        return $this->query(
            "SELECT * FROM events WHERE date = :date LIMIT 1",
            ['date' => $date]
        )[0];
    }

    public function getPlayersWithAttendanceByTeam($event_id, $team_id)
    {
        $query = "
        SELECT 
            p.player_id,
            CONCAT(u.first_name, ' ', u.last_name) AS name,
            p.position,
            COALESCE(a.status, 'Absent') AS status,
            u.image
        FROM players p
        JOIN users u ON p.nic = u.nic
        JOIN team_players tp ON p.player_id = tp.player_id
        LEFT JOIN attendance a 
            ON p.player_id = a.player_id 
            AND a.event_id = :event_id
        WHERE tp.team_id = :team_id
        ORDER BY p.player_id ASC
        ";

        return $this->query($query, ['event_id' => $event_id, 'team_id' => $team_id]);
    }

    public function getStatsByTeam($event_id, $team_id)
    {
        $query = "
        SELECT 
            COUNT(p.player_id) AS total,
            SUM(COALESCE(a.status, 'Absent') = 'Present') AS present,
            SUM(COALESCE(a.status, 'Absent') = 'Absent') AS absent,
            SUM(COALESCE(a.status, 'Absent') = 'Late') AS late
        FROM players p
        JOIN team_players tp ON p.player_id = tp.player_id
        LEFT JOIN attendance a 
            ON p.player_id = a.player_id 
            AND a.event_id = :event_id
        WHERE tp.team_id = :team_id
        ";

        $rows = $this->query($query, ['event_id' => $event_id, 'team_id' => $team_id]);
        return !empty($rows) ? $rows[0] : (object) ['total' => 0, 'present' => 0, 'absent' => 0, 'late' => 0];
    }

    public function getTeamPlayerIds($team_id)
    {
        $rows = $this->query(
            "SELECT player_id FROM team_players WHERE team_id = :team_id",
            ['team_id' => $team_id]
        );

        $ids = [];
        foreach ($rows as $row) {
            $ids[] = (int) ($row->player_id ?? 0);
        }

        return array_values(array_filter($ids));
    }

    public function getDistributionByTeam($event_id, $team_id)
    {
        $stats = $this->getStatsByTeam($event_id, $team_id);

        return [
            'Present' => (int) ($stats->present ?? 0),
            'Absent' => (int) ($stats->absent ?? 0),
            'Late' => (int) ($stats->late ?? 0),
            'Excused' => 0,
        ];
    }

    public function countSessionsByTeam($team_id)
    {
        $rows = $this->query(
            "SELECT COUNT(DISTINCT a.event_id) AS total_sessions
             FROM attendance a
             JOIN team_players tp ON tp.player_id = a.player_id
             WHERE tp.team_id = :team_id",
            ['team_id' => $team_id]
        );

        return (int) ($rows[0]->total_sessions ?? 0);
    }

    public function getAttendanceTrendByTeam($team_id, $selectedDate, $eventType = 'Practice', $limit = 8)
    {
        $safeLimit = max(1, (int) $limit);

        $params = [
            'team_id' => $team_id,
            'selected_date' => $selectedDate,
        ];

        if ($this->eventTypeColumnExists()) {
            $params['event_type'] = $eventType;
            $query = "
            SELECT 
                e.date,
                ROUND(
                    100 * SUM(COALESCE(a.status, 'Absent') = 'Present') / NULLIF(COUNT(tp.player_id), 0),
                    1
                ) AS attendance_rate
            FROM events e
            JOIN team_players tp ON tp.team_id = :team_id
            LEFT JOIN attendance a 
                ON a.event_id = e.event_id 
                AND a.player_id = tp.player_id
            WHERE e.date <= :selected_date
              AND e.event_type = :event_type
            GROUP BY e.event_id, e.date
            ORDER BY e.date DESC, e.event_id DESC
            LIMIT {$safeLimit}
            ";
        } else {
            $query = "
            SELECT 
                e.date,
                ROUND(
                    100 * SUM(COALESCE(a.status, 'Absent') = 'Present') / NULLIF(COUNT(tp.player_id), 0),
                    1
                ) AS attendance_rate
            FROM events e
            JOIN team_players tp ON tp.team_id = :team_id
            LEFT JOIN attendance a 
                ON a.event_id = e.event_id 
                AND a.player_id = tp.player_id
            WHERE e.date <= :selected_date
            GROUP BY e.event_id, e.date
            ORDER BY e.date DESC, e.event_id DESC
            LIMIT {$safeLimit}
            ";
        }

        $rows = $this->query($query, $params);
        $rows = array_reverse($rows);

        $labels = [];
        $values = [];

        foreach ($rows as $row) {
            $labels[] = !empty($row->date) ? date('M d', strtotime($row->date)) : 'N/A';
            $values[] = (float) ($row->attendance_rate ?? 0);
        }

        if (empty($labels)) {
            $labels = ['No Data'];
            $values = [0];
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }
}