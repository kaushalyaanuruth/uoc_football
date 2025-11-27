<?php
class LandingPage extends Controller {

    public function index() {
        $data = [];
        $this->view('landingPage', $data);
    }
}
