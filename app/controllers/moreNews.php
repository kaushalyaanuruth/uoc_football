<?php
class MoreNews extends Controller {

    public function index() {
        // Load the NewsModel
        $newsModel = $this->model('NewsModel');
        
        // Get all published news (no limit)
        $allNews = $newsModel->getAll();
        
        // Pass data to view
        $data = [
            'allNews' => $allNews
        ];
        
        $this->view('moreNews', $data);
    }
}
