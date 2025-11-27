<?php
class admin extends Controller {

    public function index() {
        $this->view('admin');
    }
        public function addNews() {
        $this->view('add-news');
    }
}
