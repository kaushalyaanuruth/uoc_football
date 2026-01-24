<?php

class Notices extends Controller
{

    public function index()
    {
        $data = [
            'notices' => [
                [
                    'title' => 'Notice',
                    'author' => 'Team Captain',
                    'date' => 'Jan 13, 2024',
                    'content' => 'Tomorrow\'s (25th August 2025) training session has been cancelled due to heavy rain and unsafe field conditions.',
                    'is_new' => true
                ],
                [
                    'title' => 'Notice',
                    'author' => 'Team Captain',
                    'date' => 'Jan 13, 2024',
                    'content' => 'Tomorrow\'s (25th August 2025) training session has been cancelled due to heavy rain and unsafe field conditions.',
                    'is_new' => false
                ],
                [
                    'title' => 'Notice',
                    'author' => 'Coach Martinez',
                    'date' => 'Jan 14, 2024',
                    'content' => 'Tomorrow\'s (25th August 2025) training session has been cancelled due to heavy rain and unsafe field conditions.',
                    'is_new' => true
                ],
                [
                    'title' => 'Notice',
                    'author' => 'Coach Martinez',
                    'date' => 'Jan 14, 2024',
                    'content' => 'Tomorrow\'s (25th August 2025) training session has been cancelled due to heavy rain and unsafe field conditions.',
                    'is_new' => false
                ],
                [
                    'title' => 'Notice',
                    'author' => 'Coach Martinez',
                    'date' => 'Jan 14, 2024',
                    'content' => 'Tomorrow\'s (25th August 2025) training session has been cancelled due to heavy rain and unsafe field conditions.',
                    'is_new' => false
                ],
                [
                    'title' => 'Notice',
                    'author' => 'Team Captain',
                    'date' => 'Jan 13, 2024',
                    'content' => 'Tomorrow\'s (25th August 2025) training session has been cancelled due to heavy rain and unsafe field conditions.',
                    'is_new' => false
                ],
                [
                    'title' => 'Notice',
                    'author' => 'Coach Martinez',
                    'date' => 'Jan 14, 2024',
                    'content' => 'Tomorrow\'s (25th August 2025) training session has been cancelled due to heavy rain and unsafe field conditions.',
                    'is_new' => false
                ],
                [
                    'title' => 'Notice',
                    'author' => 'Coach Martinez',
                    'date' => 'Jan 14, 2024',
                    'content' => 'Tomorrow\'s (25th August 2025) training session has been cancelled due to heavy rain and unsafe field conditions.',
                    'is_new' => false
                ]
            ]
        ];

        $this->view('notices', $data);
    }
}
