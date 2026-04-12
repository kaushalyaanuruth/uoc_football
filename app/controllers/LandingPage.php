<?php
class LandingPage extends Controller {

    public function index() {
        // Load models
        $newsModel = $this->model('NewsModel');
        $eventModel = $this->model('EventModel');
        $playerModel = $this->model('PlayerModel');
        
        // Get all news articles
        $allNews = $newsModel->getAll();
        
        // Get upcoming events
        $upcomingEvents = $eventModel->getUpcoming();
        
        // Get featured players (limit to 3 for the landing page)
        $featuredPlayers = $playerModel->getAll(3, 0);
        
        // Prepare data
        $data = [
            'latestNews' => $allNews,
            'upcomingEvents' => $upcomingEvents,
            'featuredPlayers' => $featuredPlayers ? $featuredPlayers : []
        ];
        
        $this->view('landingPage', $data);
    }
}


