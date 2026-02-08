<?php

class Analyze extends Controller
{

    public function index()
    {
        $data = [
            'performance' => [
                ['label' => 'Stamina', 'value' => 85, 'status' => 'Excellent', 'color' => '#a29bfe'],
                ['label' => 'Speed', 'value' => 78, 'status' => 'Good', 'color' => '#0984e3'],
                ['label' => 'Accuracy', 'value' => 92, 'status' => 'Excellent', 'color' => '#00b894'],
                ['label' => 'Overall', 'value' => 81, 'status' => 'Good', 'color' => '#e17055']
            ],
            'trends' => [
                'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                'datasets' => [
                    ['label' => 'Endurance', 'data' => [75, 78, 83, 85, 88, 90], 'color' => '#a29bfe'],
                    ['label' => 'Sprint Speed', 'data' => [70, 72, 75, 78, 82, 85], 'color' => '#00b894'],
                    ['label' => 'Agility', 'data' => [68, 70, 73, 76, 80, 83], 'color' => '#fdcb6e']
                ]
            ],
            'comparison' => [
                ['label' => 'Goals Scored', 'value' => '+25%', 'sub' => '8 vs 6', 'type' => 'up'],
                ['label' => 'Minutes Played', 'value' => '+12%', 'sub' => '450 vs 402', 'type' => 'up'],
                ['label' => 'Sprint Speed', 'value' => '+8%', 'sub' => '28.5 vs 26.4 km/h', 'type' => 'up']
            ],
            'stats' => [
                ['label' => 'Goals', 'value' => 12, 'icon' => '⚽', 'color' => '#a29bfe'],
                ['label' => 'Assists', 'value' => 8, 'icon' => '🔄', 'color' => '#0984e3'],
                ['label' => 'Passes', 'value' => 456, 'icon' => '↗️', 'color' => '#00b894'],
                ['label' => 'Minutes', 'value' => 890, 'icon' => '🕒', 'color' => '#e17055']
            ]
        ];

        $this->view('analyze', $data);
    }
}
