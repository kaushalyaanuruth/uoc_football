<?php

class CaptainMealPlan extends Controller
{
    public function index()
    {
        // Later we will pass real data from DB
        $data = [];

        $this->view('captain/CaptainMealPlan', $data);
    }
}
