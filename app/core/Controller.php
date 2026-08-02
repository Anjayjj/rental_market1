<?php
class Controller {
    public function view($view, $data = []) {
        $viewFile = __DIR__ . '/../views/' . ltrim($view, '/') . '.php';
        if (!is_file($viewFile)) {
            http_response_code(500);
            exit('View tidak ditemukan: ' . htmlspecialchars($view));
        }
        extract($data, EXTR_SKIP);
        require $viewFile;
    }

    public function model($model) {
        $modelFile = __DIR__ . '/../models/' . $model . '.php';
        if (!is_file($modelFile)) {
            throw new RuntimeException('Model tidak ditemukan: ' . $model);
        }
        require_once $modelFile;
        return new $model;
    }

    public function requireAuth($role = null, $pesan = 'Silakan login terlebih dahulu.') {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['flash_error'] = $pesan;
            header('Location: ' . BASEURL . '/auth/login');
            exit;
        }
        if ($role && ($_SESSION['user_role'] ?? null) != $role) {
            header('Location: ' . BASEURL . '/error/forbidden');
            exit;
        }
    }
}
