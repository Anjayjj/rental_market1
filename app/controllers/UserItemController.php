<?php
class UserItemController extends Controller {

    public function __construct() {
        // Wajib login untuk semua fungsi di controller ini
        $this->requireAuth();
    }

    public function index() {
        $itemModel = $this->model('ItemModel');
        $data['items'] = $itemModel->getItemsByOwner($_SESSION['user_id']);
        
        // Memanggil View dengan Layout Dashboard
        $this->view('user/barang_saya', $data);
    }

    public function create() {
        $itemModel = $this->model('ItemModel');
        $data['categories'] = $itemModel->getCategories();
        
        $this->view('user/tambah_barang', $data);
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die("CSRF Token Invalid");
            }

            // Sanitasi Input
            $name = htmlspecialchars($_POST['name']);
            // Generate Slug dari nama (contoh: "Kamera Sony" -> "kamera-sony-1698765432")
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name))) . '-' . time();
            
            $data = [
                'owner_id' => $_SESSION['user_id'],
                'category_id' => (int)$_POST['category_id'],
                'name' => $name,
                'slug' => $slug,
                'description' => htmlspecialchars($_POST['description']),
                'price_daily' => (float)$_POST['price_daily']
            ];

            // Validasi & Upload Gambar Utama
            $file = $_FILES['primary_image'];
            if ($file['error'] === UPLOAD_ERR_OK) {
                // Verifikasi MIME-Type (seperti di modul pembayaran)
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $file['tmp_name']);
                finfo_close($finfo);

                if (in_array($mime, ['image/jpeg', 'image/png', 'image/webp'])) {
                    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $filename = 'item_' . time() . '_' . uniqid() . '.' . $ext;
                    $upload_path = __DIR__ . '/../assets/uploads/items/' . $filename;

                    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
                        
                        $itemModel = $this->model('ItemModel');
                        if ($itemModel->storeItem($data, $filename)) {
                            $_SESSION['flash_success'] = "Barang berhasil ditambahkan!";
                            header('Location: ' . BASEURL . '/useritem/index');
                            exit;
                        }
                    }
                } else {
                    $_SESSION['flash_error'] = "Format file harus JPG, PNG, atau WEBP.";
                }
            } else {
                $_SESSION['flash_error'] = "Gambar utama wajib diunggah.";
            }
            
            // Redirect kembali jika gagal
            header('Location: ' . BASEURL . '/useritem/create');
            exit;
        }
    }
    public function edit($id) {
        $itemModel = $this->model('ItemModel');
        $user_id = $_SESSION['user_id'];

        // Ambil data barang berdasarkan ID
        $item = $itemModel->getItemById($id); 

        // Proteksi Keamanan: Pastikan barang ada dan benar-benar milik user yang sedang login
        if (!$item || $item['owner_id'] != $user_id) {
            $_SESSION['flash_error'] = "Barang tidak ditemukan atau Anda tidak berhak mengaksesnya.";
            header('Location: ' . BASEURL . '/useritem/index');
            exit;
        }

        $data['item'] = $item;
        $data['categories'] = $itemModel->getCategories();
        
        // Memanggil View untuk form edit (pastikan Anda punya file app/views/user/edit_barang.php)
        $this->view('user/edit_barang', $data);
    }

    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Cek CSRF Token
            if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                die("CSRF Token Invalid");
            }

            $itemModel = $this->model('ItemModel');
            $user_id = $_SESSION['user_id'];

            // Proteksi Keamanan: Pastikan ID barang ini milik user yang sedang login
            if (!$itemModel->checkOwnership($id, $user_id)) {
                $_SESSION['flash_error'] = "Anda tidak berhak mengubah barang ini.";
                header('Location: ' . BASEURL . '/useritem/index');
                exit;
            }

            // Ambil dan bersihkan data teks
            $data = [
                'category_id' => (int)$_POST['category_id'],
                'name' => htmlspecialchars($_POST['name']),
                'description' => htmlspecialchars($_POST['description']),
                'price_daily' => (float)$_POST['price_daily']
            ];

            // Proses Update Gambar (HANYA jika user mengunggah gambar baru)
            $filename = null; // Default null jika tidak ganti gambar
            if (isset($_FILES['primary_image']) && $_FILES['primary_image']['error'] === UPLOAD_ERR_OK) {
                $file = $_FILES['primary_image'];
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mime = finfo_file($finfo, $file['tmp_name']);
                finfo_close($finfo);

                if (in_array($mime, ['image/jpeg', 'image/png', 'image/webp'])) {
                    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
                    $filename = 'item_' . time() . '_' . uniqid() . '.' . $ext;
                    $upload_path = __DIR__ . '/../assets/uploads/items/' . $filename;

                    move_uploaded_file($file['tmp_name'], $upload_path);
                } else {
                    $_SESSION['flash_error'] = "Gagal memperbarui: Format gambar harus JPG, PNG, atau WEBP.";
                    header('Location: ' . BASEURL . '/useritem/edit/' . $id);
                    exit;
                }
            }

            // Eksekusi update ke database
            if ($itemModel->updateItem($id, $data, $filename)) {
                $_SESSION['flash_success'] = "Data barang berhasil diperbarui!";
            } else {
                $_SESSION['flash_error'] = "Terjadi kesalahan saat memperbarui barang.";
            }

            header('Location: ' . BASEURL . '/useritem/index');
            exit;
        }
    }

    public function delete($id) {
        $itemModel = $this->model('ItemModel');
        $user_id = $_SESSION['user_id'];

        // Proteksi IDOR: Pastikan barang ini milik user yang sedang login!
        if ($itemModel->checkOwnership($id, $user_id)) {
            // (Opsional) Hapus file gambar dari server sebelum delete record
            $itemModel->deleteItem($id);
            $_SESSION['flash_success'] = "Barang berhasil dihapus.";
        } else {
            $_SESSION['flash_error'] = "Anda tidak berhak menghapus barang ini.";
        }
        
        header('Location: ' . BASEURL . '/useritem/index');
        exit;
    }
}
?>