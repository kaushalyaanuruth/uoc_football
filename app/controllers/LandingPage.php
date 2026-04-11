<?php
class LandingPage extends Controller {

    public function index() {
        // Load News Model
        $newsModel = $this->model('NewsModel');
        
        // Get all news articles
        $allNews = $newsModel->getAll();
        
        // Prepare data
        $data = [
            'latestNews' => $allNews,
            'upcomingEvents' => []
        ];
        
        $this->view('landingPage', $data);
    }
}
