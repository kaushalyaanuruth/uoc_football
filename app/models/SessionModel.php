<?php

class SessionModel
{
    use Model;

    protected $table = 'sessions';

    /**
     * Get session OR create if not exists
     */
    public function getOrCreate($team_id, $type, $date, $created_by)
    {
        $existing = $this->where([
            'team_id'      => $team_id,
            'session_type' => $type,
            'session_date' => $date
        ]);

        if ($existing) {
            return $existing[0]->id;
        }

        $this->insert([
            'team_id'      => $team_id,
            'session_type' => $type,
            'session_date' => $date,
            'created_by'   => $created_by
        ]);

        return $this->db->lastInsertId();
    }

    /**
     * Get sessions by team
     */
    public function getByTeam($team_id)
    {
        return $this->where(
            ['team_id' => $team_id],
            ['order' => ['session_date' => 'DESC']]
        );
    }
}
