<?php
class LandingPage extends Controller {

    public function index() {
        $newsModel = $this->model('NewsModel');
        $eventModel = $this->model('EventModel');
        $galleryModel = $this->model('GalleryModel');
        
        // Fetch latest news
        $latestNews = $newsModel->getAll();
        
        // Fetch upcoming events
        $upcomingEvents = $eventModel->getUpcoming();
        
        // Fetch latest gallery images (limit to 6)
        $allGalleryImages = $galleryModel->getPublished();
        $latestGalleryImages = array_slice($allGalleryImages, 0, 6);
        
        $data = [
            'latestNews' => $latestNews,
            'upcomingEvents' => $upcomingEvents,
            'latestGalleryImages' => $latestGalleryImages
        ];
        
        $this->view('landingPage', $data);
    }
}
