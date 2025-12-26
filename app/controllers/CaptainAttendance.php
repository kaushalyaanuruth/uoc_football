<?php

class CaptainAttendance extends Controller
{
    /**
     * Load Attendance Dashboard
     */
    public function index()
    {
        // TEMP: later this comes from logged-in captain
        $team_id = 1;
        $captain_id = 1;

        $sessionType = $_GET['type'] ?? 'Practice';
        $date        = $_GET['date'] ?? date('Y-m-d');

        $sessionModel    = new SessionModel();
        $playerModel     = new PlayerModel();
        $attendanceModel = new AttendanceModel();

        // Get or create session
        $session_id = $sessionModel->getOrCreate(
            $team_id,
            $sessionType,
            $date,
            $captain_id
        );

        // Get players + attendance
        $players = $attendanceModel->getBySession($session_id);

        // Get stats
        $statsRaw = $attendanceModel->getStats($session_id);
        $stats = [
            'present' => 0,
            'absent'  => 0,
            'late'    => 0
        ];

        foreach ($statsRaw as $row) {
            $stats[$row->status] = $row->total;
        }

        // Load view
        $this->view('captain/attendance', [
            'players' => $players,
            'stats'   => $stats,
            'date'    => $date,
            'type'    => $sessionType
        ]);
    }

    /**
     * AJAX: Mark attendance
     */
    public function mark()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['error' => 'Invalid request']);
            return;
        }

        $attendanceModel = new AttendanceModel();

        $attendanceModel->mark(
            $_POST['session_id'],
            $_POST['player_id'],
            $_POST['status'],
            1 // captain_id (later from session)
        );

        echo json_encode(['success' => true]);
    }

    /**
     * AJAX: Get chart data
     */
    public function chart()
    {
        $attendanceModel = new AttendanceModel();
        $team_id = 1;

        $data = $attendanceModel->getWeeklyRate($team_id);

        echo json_encode($data);
    }
}


// <?php
// class CaptainAttendance extends Controller {
//     public function index() {
//         echo "Captain Attendance Controller OK";
//     }
// }
