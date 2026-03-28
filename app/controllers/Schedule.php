<?php

class Schedule extends Controller
{

    public function index()
    {
        // Mock data for the schedule page
        $data = [
            'events' => [
                [
                    'type' => 'training',
                    'title' => 'Fitness Training',
                    'subtitle' => 'Training Session',
                    'date' => 'September 01 2025',
                    'time' => '8.00 am - 12.00 pm'
                ],
                [
                    'type' => 'training',
                    'title' => 'Fitness Training',
                    'subtitle' => 'Training Session',
                    'date' => 'September 01 2025',
                    'time' => '8.00 am - 12.00 pm'
                ],
                [
                    'type' => 'match',
                    'title' => 'UOC vs MORA',
                    'subtitle' => 'Match',
                    'date' => 'September 01 2025',
                    'time' => '8.00 am - 12.00 pm'
                ],
                [
                    'type' => 'training',
                    'title' => 'Fitness Training',
                    'subtitle' => 'Training Session',
                    'date' => 'September 01 2025',
                    'time' => '8.00 am - 12.00 pm'
                ],
                [
                    'type' => 'training',
                    'title' => 'Fitness Training',
                    'subtitle' => 'Training Session',
                    'date' => 'September 01 2025',
                    'time' => '8.00 am - 12.00 pm'
                ],
                [
                    'type' => 'match',
                    'title' => 'UOC vs MORA',
                    'subtitle' => 'Match',
                    'date' => 'September 01 2025',
                    'time' => '8.00 am - 12.00 pm'
                ],
                [
                    'type' => 'training',
                    'title' => 'Fitness Training',
                    'subtitle' => 'Training Session',
                    'date' => 'September 01 2025',
                    'time' => '8.00 am - 12.00 pm'
                ],
                [
                    'type' => 'training',
                    'title' => 'Fitness Training',
                    'subtitle' => 'Training Session',
                    'date' => 'September 01 2025',
                    'time' => '8.00 am - 12.00 pm'
                ],
                [
                    'type' => 'match',
                    'title' => 'UOC vs MORA',
                    'subtitle' => 'Match',
                    'date' => 'September 01 2025',
                    'time' => '8.00 am - 12.00 pm'
                ]
            ],
            'pagination' => [
                'current' => 1,
                'total' => 3
            ]
        ];

        $this->view('schedule', $data);
    }
}
