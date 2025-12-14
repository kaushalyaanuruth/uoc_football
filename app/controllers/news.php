<?php

require_once __DIR__ . '/../models/NewsModel.php';

class News extends Controller
{
    public function index()
    {
        $data = [];
        $this->view('news', $data);
    }

    public function addNewNews()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $heading = $_POST['news_heading'];
        $body = $_POST['news_body'];
        $date = date('Y-m-d H:i:s');

        // ✅ Use server path instead of URL
        $uploadDir = __DIR__ . '/../../public/uploads/news_images/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $imageName = time() . '_' . basename($_FILES['image']['name']);
        $targetPath = $uploadDir . $imageName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            // ✅ Save in Database (just store the filename, not full URL)
            $newsModel = new NewsModel();
            $newsModel->insertNews($heading, $body, $date, $imageName);

            header('Location: http://localhost/UOC_Football/public/admin/');
            exit();
        } else {
            echo "Image upload failed!";
        }
    }
}


    public function showLatestNews()
    {
        $newsModel = new NewsModel();
        $latestNews = $newsModel->getLatestNews(3);

        // Pass news to view
        require __DIR__ . '/../views/news_block.php';
    }
}
