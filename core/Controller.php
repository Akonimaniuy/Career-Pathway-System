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
        
        if (strpos($view, '../') === 0) {
            // Handle relative paths from the /views/ directory
            $viewFile = APP_PATH . '/views/' . substr($view, 3) . '.php';
        } else {
            // Default behavior: look in controller-specific folder
            $controller = get_class($this);
            $controller = str_replace('controllers\\', '', $controller);
            $controller = str_replace('Controller', '', $controller);
            $controller = strtolower($controller);
            $viewFile = APP_PATH . "/views/$controller/$view.php";
        }
        
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