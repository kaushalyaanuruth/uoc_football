<?php
class LandingPage extends Controller {

    public function index() {
        $newsModel = $this->model('NewsModel');
        $eventModel = $this->model('EventModel');
        
        // Fetch latest news
        $latestNews = $newsModel->getAll();
        
        // Fetch upcoming events
        $upcomingEvents = $eventModel->getUpcoming();
        
        $data = [
            'latestNews' => $latestNews,
            'upcomingEvents' => $upcomingEvents
        ];
        
        $this->view('landingPage', $data);
    }
}
