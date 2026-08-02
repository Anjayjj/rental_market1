<?php
class AuthController extends Controller {

    public function login() {
        // Jika sudah login, lempar ke dashboard masing-masing
        if (isset($_SESSION['user_id'])) {
            $this->redirectBasedOnRole($_SESSION['user_role']);
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Validasi CSRF
            if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die("CSRF Token Invalid.");
            }

            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'];

            $userModel = $this->model('UserModel');
            $user = $userModel->getUserByEmail($email);

            // Verifikasi User dan Password
            if ($user && password_verify($password, $user['password'])) {
                // Set Session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                $_SESSION['user_role'] = $user['role'];
                $_SESSION['user_avatar'] = $user['avatar'];

                $this->redirectBasedOnRole($user['role']);
            } else {
                $_SESSION['flash_error'] = "Email atau password salah.";
                header('Location: ' . BASEURL . '/auth/login');
                exit;
            }
        } else {
            // Tampilkan Halaman Login
            $itemModel = $this->model('ItemModel');
            $data['categories'] = $itemModel->getCategories();
            $this->view('auth/login', $data);
        }
    } // <-- KURUNG KURAWAL PENUTUP FUNGSI LOGIN (Sebelumnya Hilang)

    public function register() {
        if (isset($_SESSION['user_id'])) {
            $this->redirectBasedOnRole($_SESSION['user_role']);
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die("CSRF Token Invalid.");
            }

            // Sanitasi Input
            $name = htmlspecialchars($_POST['name']);
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $phone = htmlspecialchars($_POST['phone']);
            $password = $_POST['password'];
            $password_confirm = $_POST['password_confirm'];

            // Validasi Password Match
            if ($password !== $password_confirm) {
                $_SESSION['flash_error'] = "Konfirmasi password tidak cocok.";
                header('Location: ' . BASEURL . '/auth/register');
                exit;
            }

            $userModel = $this->model('UserModel');

            // Validasi Email Unik
            if ($userModel->getUserByEmail($email)) {
                $_SESSION['flash_error'] = "Email sudah terdaftar. Silakan gunakan email lain.";
                header('Location: ' . BASEURL . '/auth/register');
                exit;
            }

            // Hashing Password (BCRYPT)
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            $data = [
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'password' => $hashed_password
            ];

            if ($userModel->registerUser($data)) {
                $_SESSION['flash_success'] = "Registrasi berhasil! Silakan login.";
                header('Location: ' . BASEURL . '/auth/login');
                exit;
            } else {
                $_SESSION['flash_error'] = "Terjadi kesalahan sistem.";
                header('Location: ' . BASEURL . '/auth/register');
                exit;
            }
        } else {
            // Tampilkan Halaman Register
            $itemModel = $this->model('ItemModel');
            $data['categories'] = $itemModel->getCategories();
            $this->view('auth/register', $data);
        }
    }

    // Method untuk memproses Logout
    public function logout() {
        // Hapus variabel session
        $_SESSION = array();

        // Hancurkan Cookie Session di Browser
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Hancurkan sesi di server
        session_destroy();
        
        // Mulai sesi baru HANYA untuk mengirim pesan notifikasi
        session_start();
        $_SESSION['flash_success'] = "Anda telah berhasil keluar dari sistem.";
        
        header('Location: ' . BASEURL);
        exit;
    }

    // <-- FUNGSI INI SEBELUMNYA TIDAK SENGAJA TERHAPUS
    // Helper untuk redirect berdasarkan Role
    private function redirectBasedOnRole($role) {
        if ($role === 'admin') {
            header('Location: ' . BASEURL . '/admin/dashboard');
        } else {
            // Jika User biasa, masuk ke Halaman Utama (Katalog)
            header('Location: ' . BASEURL); 
        }
        exit;
    }
}
?>