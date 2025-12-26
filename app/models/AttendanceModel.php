<?php

class AttendanceModel
{
    use Model;

    protected $table = 'attendance';

    public function getBySession($session)
    {
        // session may be an object → extract ID safely
        $session_id = is_object($session) ? $session->session_id : $session;

        $query = "
        SELECT 
            p.player_id,
            p.full_name,
            p.position,
            a.status
        FROM players p
        LEFT JOIN attendance a 
            ON p.player_id = a.player_id 
            AND a.session_id = :session_id
        ORDER BY p.jersey_number ASC
    ";

        return $this->query($query, ['session_id' => $session_id]);
    }

}
