<?php

class PlayerDashboard extends Controller
{

    public function index()
    {
        // Real-time data for the dashboard
        $data = [
            'player_name' => 'Priyajan',
            'date' => date('j, F Y'),
            'time' => date('H:i A'),
            'next_practice' => [
                'date' => date('j, F', strtotime('tomorrow')),
                'time_of_day' => 'morning',
                'time' => '6.00 A.M.'
            ],
            'next_event' => [
                'type' => 'Practice Match',
                'title' => 'UOC vs Old Bends',
                'date' => date('l, F j', strtotime('next Monday')) . ' at 6 a.m.',
                'location' => 'Uni Ground'
            ],
            'slug_countdown' => 28,
            'notices' => [
                [
                    'text' => 'Tomorrow (25th august 2025) practice has been canceled.',
                    'date' => '25th august 2025'
                ],
                [
                    'text' => 'Fill the form attached here. ( Link )',
                    'link' => '#'
                ]
            ],
            'meal_plan' => [
                'Breakfast' => [
                    'Oatmeal with fruits',
                    'Boiled eggs',
                    'Green tea',
                    'Banana'
                ],
                'Lunch' => [
                    'Basmati or Red rice',
                    'Chicken, Egg, Fish',
                    'Vegetable(minimam 3)',
                    'Pala',
                    'Yogurt',
                    'Fruits'
                ],
                'Dinner' => [
                    'Grilled chicken breast',
                    'Steamed vegetables',
                    'Boiled sweet potato',
                    'Glass of warm milk'
                ]
            ]
        ];

        $this->view('playerDashboard', $data);
    }
}
