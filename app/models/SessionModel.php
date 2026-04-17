<?php

class SessionModel
{
    use Model;

    protected $table = 'sessions';

    protected $allowedColumns = [
        'team_id',
        'session_type',
        'session_date',
        'created_by',
        'created_at'
    ];

    public function getOrCreate($team_id, $type, $date, $created_by)
    {
        $type = strtolower($type);

        $existing = $this->where([
            'team_id' => $team_id,
            'session_date' => $date,
            'session_type' => $type
        ]);

        if (!empty($existing)) {
            return $existing[0];
        }

        $this->insert([
            'team_id' => $team_id,
            'session_type' => $type,
            'session_date' => $date,
            'created_by' => $created_by
        ]);

        $new = $this->where([
            'team_id' => $team_id,
            'session_date' => $date,
            'session_type' => $type
        ]);

        return $new[0] ?? null;
    }
}
