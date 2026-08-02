<?php
class ErrorController extends Controller {

    public function index() {
        $this->show404();
    }

    public function notfound() {
        $this->show404();
    }

    public function forbidden() {
        $this->show403();
    }

    private function show404() {
        http_response_code(404);
        require_once VIEWPATH . '/errors/404.php';
    }

    private function show403() {
        http_response_code(403);
        require_once VIEWPATH . '/errors/403.php';
    }
}
