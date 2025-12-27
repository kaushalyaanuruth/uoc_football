<?php

class CaptainFinance extends Controller
{
    public function index()
    {
        // Later we will pass real data from DB
        $data = [];

        $this->view('captain/CaptainFinance', $data);
    }
}
