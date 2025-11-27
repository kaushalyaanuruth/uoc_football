<?php

class Home extends Controller {

    public function index() {
        echo "this is home controller";
        $model = new Player();
        $data = $model->where(['id' => 1], []);
        $this->view('home');
    }
}
