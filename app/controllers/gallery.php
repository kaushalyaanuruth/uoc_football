<?php
class gallery extends Controller {

    public function index() {
        // Load the GalleryModel
        $galleryModel = $this->model('GalleryModel');
        
        // Get current page from URL parameter (default to 1)
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $currentPage = max(1, $currentPage); // Ensure page is at least 1
        
        // Get category filter if provided
        $category = isset($_GET['category']) ? $_GET['category'] : null;
        
        // Items per page
        $perPage = 40;
        
        // Get total count and images for current page
        $totalImages = $galleryModel->getTotalCount($category);
        $images = $galleryModel->getPaginated($currentPage, $perPage, $category);
        
        // Calculate total pages
        $totalPages = ceil($totalImages / $perPage);
        
        // Pass data to view
        $data = [
            'images' => $images,
            'currentPage' => $currentPage,
            'totalPages' => $totalPages,
            'totalImages' => $totalImages,
            'category' => $category
        ];
        
        $this->view('gallery', $data);
    }
}
