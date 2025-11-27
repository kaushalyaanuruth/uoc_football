<?php
class MoreEvent extends Controller {

    public function index() {
        // Load the EventModel
        $eventModel = $this->model('EventModel');
        
        // Get all events from database (including past events)
        $allEvents = $eventModel->getAll();
        
        // Pass data to view
        $data = [
            'allEvents' => $allEvents
        ];
        
        $this->view('moreEvent', $data);
    }
}
