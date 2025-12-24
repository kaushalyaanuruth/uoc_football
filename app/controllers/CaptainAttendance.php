<?php

class CaptainAttendance extends Controller
{
    public function index()
    {
        $data = [
            'totalPlayers' => 24,
            'present' => 18,
            'absent' => 6,
            'players' => [
                ['name' => 'John Smith', 'number' => '001', 'position' => 'Forward', 'status' => 'Present'],
                ['name' => 'Mike Johnson', 'number' => '002', 'position' => 'Midfielder', 'status' => 'Absent'],
                ['name' => 'David Wilson', 'number' => '003', 'position' => 'Defender', 'status' => 'Late'],
            ],
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
}
