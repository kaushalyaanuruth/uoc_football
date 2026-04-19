<?php

require_once __DIR__ . '/CoachBaseController.php';

class coachPerformance extends CoachBaseController {

    public function index() {
        $this->ensureCoachAccess();
        $this->view('coachPerformance', $this->buildCoachViewData());
    }
}