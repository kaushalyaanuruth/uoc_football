<?php

class PlayerDashboard extends Controller {

    public function index() {
        // Mock data for the dashboard
        $data = [
            'player_name' => 'Priyajan',
            'date' => '15, August 2025',
            'time' => '19:25 P.M.',
            'next_practice' => [
                'date' => '15, August',
                'time_of_day' => 'morning',
                'time' => '6.00 A.M.'
            ],
            'next_event' => [
                'type' => 'Practice Match',
                'title' => 'UOC vs Old Bends',
                'date' => 'Monday, August 8 at 6 a.m.',
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
                'selected' => 'Lunch',
                'items' => [
                    'Basmati or Red rice',
                    'Chicken, Egg, Fish',
                    'Vegetable(minimam 3)',
                    'Pala',
                    'Yogurt',
                    'Fruits'
                ]
            ]
        ];

        $this->view('playerDashboard', $data);
    }
}
