<?php

class CaptainAttendance extends Controller
{
    public function index()
    {
        $attendanceModel = new AttendanceModel();

        $event_id = 1;

        $players = $attendanceModel->getPlayersWithAttendance($event_id);
        $stats = $attendanceModel->getStats($event_id);

        $data = [
             'event_id' => $event_id,
            'totalPlayers' => $stats->total ?? 0,
            'present' => $stats->present ?? 0,
            'absent' => $stats->absent ?? 0,
            'players' => $players,
            'weekly' => [
                'Mon' => 80,
                'Tue' => 70,
                'Wed' => 85,
                'Thu' => 75,
                'Fri' => 90,
                'Sat' => 60,
                'Sun' => 50,
                           
            ] 
        ];

        $this->view('captain/attendance', $data);
    }
    public function update()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        $attendanceModel = new AttendanceModel();

       
        foreach ($data as $row) {
                if (!in_array($row['status'], ['Present','Absent','Late'])) continue;

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
    $event_id = 1; 
    $players = $attendanceModel->getPlayersWithAttendance($event_id);

    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="attendance.csv"');

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

