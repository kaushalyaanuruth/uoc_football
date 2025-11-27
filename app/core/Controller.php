<?php

class Controller {
    protected $view;

    public function view($name, $data = []){

        $filename = "../app/views/" .$name.".view.php";

        if(file_exists($filename)){
            require $filename;
        } else {
            $filename = "../app/views/404.view.php";
            echo $filename;
            require $filename;
        }
    }
    
    /**
     * Load a model
     * @param string $model - Model name
     * @return object - Model instance
     */
    public function model($model) {
        $filename = "../app/models/" . $model . ".php";
        
        if(file_exists($filename)){
            require_once $filename;
            return new $model();
        }
        
        return false;
    }
}