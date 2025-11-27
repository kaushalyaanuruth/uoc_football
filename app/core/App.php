<?php
class App{
    private $controller = 'Home';
    private $method = 'index';
    private $params = [];

    private function splitURL(){
        $URL = $_GET['url'] ?? 'landingPage';
        $parts = explode('/', trim($URL, '/'));
        return $parts;
    }

    public function loadController(){
        $URL = $this->splitURL();

        // controller from URL[0], default to 'Home'
        $controllerName = !empty($URL[0]) ? ucfirst(preg_replace('/[^a-z0-9_]/i', '', $URL[0])) : $this->controller;
        $file = __DIR__ . "/../controllers/{$controllerName}.php";

        if(file_exists($file)){
            require $file;
            $this->controller = $controllerName;
        } else {
            require __DIR__ . "/../controllers/_404.php";
            $this->controller = "_404";
        }

        // instantiate controller
        $controller = new $this->controller;

        // set method from URL[1] if present and valid
        if(!empty($URL[1])){
            $methodName = preg_replace('/[^a-z0-9_]/i', '', $URL[1]);
            if(method_exists($controller, $methodName)){
                $this->method = $methodName;
            } else {
                // fall back to 404 controller/method or keep default
                if($this->controller !== '_404'){
                    require __DIR__ . "/../controllers/_404.php";
                    $controller = new _404;
                    $this->method = 'index';
                }
            }
        }

        // remaining parts are params
        $this->params = array_slice($URL, 2);

        // call with params
        call_user_func_array([$controller, $this->method], $this->params);
    }
}
