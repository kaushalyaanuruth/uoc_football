<?php

class Store extends Controller
{
    private $storeModel;

    public function __construct()
    {
        $this->storeModel = $this->model('StoreManagementModel');
    }

    public function index()
    {
        try {
            $items = $this->storeModel->getAll();
        } catch (Exception $e) {
            $items = [];
        }

        $this->view('store', [
            'items' => $items,
        ]);
    }
}
