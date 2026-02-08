<?php

class Notices extends Controller
{

    public function index()
    {
        $data = [
            'notices' => [
                [
                    'title' => 'Training Cancellation',
                    'author' => 'Team Captain',
                    'date' => 'Jan 25, 2026',
                    'category' => 'Training',
                    'content' => 'Tomorrow\'s training session has been cancelled due to field maintenance.',
                    'is_new' => true
                ],
                [
                    'title' => 'Upcoming Match',
                    'author' => 'Coach Martinez',
                    'date' => 'Jan 24, 2026',
                    'category' => 'Matches',
                    'content' => 'Please arrive at the stadium 2 hours before kickoff for our local derby.',
                    'is_new' => true
                ],
                [
                    'title' => 'New Equipment',
                    'author' => 'Kit Manager',
                    'date' => 'Jan 23, 2026',
                    'category' => 'General',
                    'content' => 'New training kits are available for pickup at the gym office.',
                    'is_new' => false
                ],
                [
                    'title' => 'Fitness Assessment',
                    'author' => 'Dr. Smith',
                    'date' => 'Jan 22, 2026',
                    'category' => 'Training',
                    'content' => 'Mandatory fitness tests scheduled for this Friday. Be ready.',
                    'is_new' => false
                ],
                [
                    'title' => 'Team Meeting',
                    'author' => 'Coach Martinez',
                    'date' => 'Jan 21, 2026',
                    'category' => 'General',
                    'content' => 'Strategy session in the meeting room this evening at 6:00 PM.',
                    'is_new' => false
                ],
                [
                    'title' => 'Match Postponed',
                    'author' => 'Team Captain',
                    'date' => 'Jan 20, 2026',
                    'category' => 'Matches',
                    'content' => 'The match against Red Lions has been postponed to next week.',
                    'is_new' => false
                ]
            ]
        ];

        $this->view('notices', $data);
    }
}
