<?php
class UserModel {
    private $db;

    public function __construct() {
        $this->db = new Database;
    }

    // Mencari user berdasarkan email (untuk Login & validasi Register)
    public function getUserByEmail($email) {
        $this->db->query("SELECT * FROM users WHERE email = :email");
        $this->db->bind('email', $email);
        return $this->db->single();
    }

    // Menyimpan user baru (Register)
    public function registerUser($data) {
        $query = "INSERT INTO users (name, email, password, phone, role) 
                  VALUES (:name, :email, :password, :phone, 'user')";
        
        $this->db->query($query);
        $this->db->bind('name', $data['name']);
        $this->db->bind('email', $data['email']);
        $this->db->bind('password', $data['password']); // Sudah di-hash dari controller
        $this->db->bind('phone', $data['phone']);

        return $this->db->execute();
    }
   // Fungsi untuk mengupdate profil pengguna beserta foto
    public function updateProfile($id, $name, $address, $avatar) {
        $query = "UPDATE users SET name = :name, address = :address, avatar = :avatar WHERE id = :id";
        
        $this->db->query($query);
        $this->db->bind('name', $name);
        $this->db->bind('address', $address);
        $this->db->bind('avatar', $avatar);
        $this->db->bind('id', $id);

        return $this->db->execute();
    }
    public function getUserById($id) {
        $this->db->query("SELECT * FROM users WHERE id = :id");
        $this->db->bind('id', $id);
        
        return $this->db->single();
    }
}
?>