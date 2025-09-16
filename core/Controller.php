<?php
namespace core;

class Controller
{
    protected $params = [];

    public function __construct($params = [])
    {
        $this->params = $params;
    }

    protected function render($view, $data = [])
    {
        extract($data);
        
        $controller = get_class($this);
        $controller = str_replace('controllers\\', '', $controller);
        $controller = str_replace('Controller', '', $controller);
        $controller = strtolower($controller);
        
        $viewFile = APP_PATH . "/views/$controller/$view.php";
        
        if (file_exists($viewFile)) {
            require_once $viewFile;
        } else {
            echo "View not found: $viewFile";
        }
    }

    protected function redirect($url)
    {
        header("Location: $url");
        exit();
    }
}