<?php

class CaptainAttendance extends Controller
{
    // public function index()
    // {
    //     $attendanceModel = new AttendanceModel();

    //     $event_id = 1;

    //     $players = $attendanceModel->getPlayersWithAttendance($event_id);
    //     $stats = $attendanceModel->getStats($event_id);

    //     $data = [
    //          'event_id' => $event_id,
    //         'totalPlayers' => $stats->total ?? 0,
    //         'present' => $stats->present ?? 0,
    //         'absent' => $stats->absent ?? 0,
    //         'players' => $players,
    //         'weekly' => [
    //             'Mon' => 80,
    //             'Tue' => 70,
    //             'Wed' => 85,
    //             'Thu' => 75,
    //             'Fri' => 90,
    //             'Sat' => 60,
    //             'Sun' => 50,

    //         ] 
    //     ];

    //     $this->view('captain/attendance', $data);
    // }
    public function index()
    {
        $attendanceModel = new AttendanceModel();

        // ✅ Get selected date (from URL)
        $date = $_GET['date'] ?? date('Y-m-d');
        $type = $_GET['type'] ?? 'Practice';

        // ✅ Get or create event
        $event = $attendanceModel->getOrCreateEvent($date, $type);

        $event_id = $event->event_id;
        $players = $attendanceModel->getPlayersWithAttendance($event_id);
        $stats = $attendanceModel->getStats($event_id);

        $data = [
            'event_id' => $event_id,
            'selected_date' => $date,
            'totalPlayers' => $stats->total ?? 0,
            'present' => $stats->present ?? 0,
            'absent' => $stats->absent ?? 0,
            'players' => $players,
            'selected_type' => $type,
        ];

        $this->view('captain/attendance', $data);
    }
    public function update()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        $attendanceModel = new AttendanceModel();


        foreach ($data as $row) {
            if (!in_array($row['status'], ['Present', 'Absent', 'Late']))
                continue;

            $attendanceModel->query("
            INSERT INTO attendance (player_id, event_id, status)
            VALUES (:player_id, :event_id, :status)
            ON DUPLICATE KEY UPDATE status = :status2
        ", [
                'player_id' => $row['player_id'],
                'event_id' => $row['event_id'],
                'status' => $row['status'],
                'status2' => $row['status']
            ]);
        }

        echo json_encode(["success" => true]);
        exit;
    }
    public function export()
    {
        $attendanceModel = new AttendanceModel();

        $date = $_GET['date'] ?? date('Y-m-d');
        $type = $_GET['type'] ?? 'Practice';
        $event = $attendanceModel->getOrCreateEvent($date, $type);

        $players = $attendanceModel->getPlayersWithAttendance($event->event_id);

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="attendance-' . $date . '.csv"');

        $output = fopen("php://output", "w");

        fputcsv($output, ['Player', 'Position', 'Status']);

        foreach ($players as $p) {
            fputcsv($output, [
                $p->name,
                $p->position,
                $p->status
            ]);
        }

        fclose($output);
        exit;
    }
}

