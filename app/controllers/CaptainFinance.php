<?php

class CaptainFinance extends Controller
{
    public function index()
    {
        $data = [

            "stats" => [
                "income" => 45250,
                "expense" => 32180,
                "balance" => 13070
            ],

            "transactions" => [
                [
                    "type" => "Income",
                    "category" => "Sponsorship",
                    "amount" => 5000,
                    "date" => "2024-10-15",
                    "description" => "Nike Partnership"
                ],
                [
                    "type" => "Expense",
                    "category" => "Equipment",
                    "amount" => 1250,
                    "date" => "2024-10-12",
                    "description" => "Training gear"
                ]
            ]
        ];

        $this->view('captain/CaptainFinance', $data);
    }
}
