<?php
class App {
    protected $controller = 'HomeController';
    protected $method = 'index';
    protected $params = [];

    public function __construct() {
        $url = $this->parseURL();

        if (isset($url[0]) && $url[0] !== '') {
            $controllerName = ucfirst(strtolower($url[0])) . 'Controller';
            $controllerFile = __DIR__ . '/../controllers/' . $controllerName . '.php';
            if (is_file($controllerFile)) {
                $this->controller = $controllerName;
                unset($url[0]);
            }
        }

        $controllerFile = __DIR__ . '/../controllers/' . $this->controller . '.php';
        if (!is_file($controllerFile)) {
            http_response_code(500);
            exit('Controller tidak ditemukan.');
        }
        require_once $controllerFile;
        $this->controller = new $this->controller;

        if (isset($url[1]) && $url[1] !== '') {
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            } else {
                $this->method = 'index';
            }
        }

        $this->params = !empty($url) ? array_values($url) : [];
        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    public function parseURL() {
        if (!isset($_GET['url'])) return [];
        $url = trim((string) $_GET['url'], '/');
        if ($url === '') return [];
        $url = filter_var($url, FILTER_SANITIZE_URL);
        return explode('/', $url);
    }
}
