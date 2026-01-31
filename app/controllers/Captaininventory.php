<?php

class CaptainInventory extends Controller
{
    public function index()
    {
        $data = [
            'total' => 248,
            'in_use' => 142,
            'available' => 89,
            'damaged' => 17,
            'inventory' => [
                [
                    'item' => 'Training Jerseys',
                    'category' => 'Kits',
                    'quantity' => 25,
                    'status' => 'Available',
                    'updated' => '2 hours ago'
                ],
                [
                    'item' => 'Match Footballs',
                    'category' => 'Balls',
                    'quantity' => 12,
                    'status' => 'In Use',
                    'updated' => '1 day ago'
                ],
                [
                    'item' => 'Training Cones',
                    'category' => 'Equipment',
                    'quantity' => 50,
                    'status' => 'Available',
                    'updated' => '3 days ago'
                ],
                [
                    'item' => 'Shin Guards',
                    'category' => 'Accessories',
                    'quantity' => 8,
                    'status' => 'Damaged',
                    'updated' => '1 week ago'
                ]
            ]


        ];

        $this->view('captain/CaptainInventory', $data);
    }
}
