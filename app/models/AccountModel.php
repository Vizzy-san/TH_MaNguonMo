<?php
class AccountModel
{
    private $conn;
    private $table_name = "users";
    
    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function getAccountByUsername($username)
    {
        $query = "SELECT u.*, r.role_name 
                 FROM " . $this->table_name . " u
                 LEFT JOIN user_roles r ON u.role_id = r.id
                 WHERE u.username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result;
    }
    
    // Phương thức lấy tài khoản bằng số điện thoại
    public function getAccountByPhone($phone)
    {
        $query = "SELECT u.*, r.role_name 
                 FROM " . $this->table_name . " u
                 LEFT JOIN user_roles r ON u.role_id = r.id
                 WHERE u.phonenumber = :phone";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':phone', $phone, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result;
    }
    
    // Phương thức lấy tài khoản bằng email
    public function getAccountByEmail($email)
    {
        $query = "SELECT u.*, r.role_name
                 FROM " . $this->table_name . " u
                 LEFT JOIN user_roles r ON u.role_id = r.id
                 WHERE u.email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result;
    }
    
    // Phương thức lấy tài khoản bằng Google ID
    public function getAccountByGoogleId($google_id)
    {
        $query = "SELECT u.*, r.role_name
                 FROM " . $this->table_name . " u
                 LEFT JOIN user_roles r ON u.role_id = r.id
                 WHERE u.google_id = :google_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':google_id', $google_id, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result;
    }
    
    function save($username, $name, $password, $role="user"){
        // First get the role_id for the specified role (default 'user')
        $roleQuery = "SELECT id FROM user_roles WHERE role_name = :role_name";
        $roleStmt = $this->conn->prepare($roleQuery);
        $roleStmt->bindParam(':role_name', $role);
        $roleStmt->execute();
        $roleResult = $roleStmt->fetch(PDO::FETCH_OBJ);
        
        $roleId = $roleResult ? $roleResult->id : null;
        
        // Then insert the user with the role_id
        $query = "INSERT INTO " . $this->table_name . "(username, fullname, password, role_id)
                VALUES (:username, :fullname, :password, :role_id)";
        $stmt = $this->conn->prepare($query);
        
        // Làm sạch dữ liệu
        $name = htmlspecialchars(strip_tags($name));
        $username = htmlspecialchars(strip_tags($username));
        
        // Gán dữ liệu vào câu lệnh
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':fullname', $name);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':role_id', $roleId);
        
        // Thực thi câu lệnh
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    // Phương thức lưu người dùng với số điện thoại
    function saveWithPhone($phone, $name, $password, $role="user", $email=null){
        // First get the role_id for the specified role (default 'user')
        $roleQuery = "SELECT id FROM user_roles WHERE role_name = :role_name";
        $roleStmt = $this->conn->prepare($roleQuery);
        $roleStmt->bindParam(':role_name', $role);
        $roleStmt->execute();
        $roleResult = $roleStmt->fetch(PDO::FETCH_OBJ);
        
        $roleId = $roleResult ? $roleResult->id : null;
        
        // Then insert the user with fullname, phone, email (nếu có) và role_id
        if ($email) {
            $query = "INSERT INTO " . $this->table_name . "(fullname, password, role_id, phonenumber, email)
                    VALUES (:fullname, :password, :role_id, :phone, :email)";
        } else {
            $query = "INSERT INTO " . $this->table_name . "(fullname, password, role_id, phonenumber)
                    VALUES (:fullname, :password, :role_id, :phone)";
        }
        $stmt = $this->conn->prepare($query);
        // Làm sạch dữ liệu
        $name = htmlspecialchars(strip_tags($name));
        $phone = htmlspecialchars(strip_tags($phone));
        if ($email) $email = htmlspecialchars(strip_tags($email));
        // Gán dữ liệu vào câu lệnh
        $stmt->bindParam(':fullname', $name);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':role_id', $roleId);
        $stmt->bindParam(':phone', $phone);
        if ($email) $stmt->bindParam(':email', $email);
        // Thực thi câu lệnh
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    // Phương thức lưu người dùng từ Google
    function saveGoogleUser($name, $role="user", $google_id=null, $email=null){
        // First get the role_id for the specified role (default 'user')
        $roleQuery = "SELECT id FROM user_roles WHERE role_name = :role_name";
        $roleStmt = $this->conn->prepare($roleQuery);
        $roleStmt->bindParam(':role_name', $role);
        $roleStmt->execute();
        $roleResult = $roleStmt->fetch(PDO::FETCH_OBJ);
        
        $roleId = $roleResult ? $roleResult->id : null;
        
        // Then insert the user with fullname, email, google_id and role_id
        $query = "INSERT INTO " . $this->table_name . "(fullname, role_id, google_id, email)
                VALUES (:fullname, :role_id, :google_id, :email)";
        $stmt = $this->conn->prepare($query);
        
        // Làm sạch dữ liệu
        $name = htmlspecialchars(strip_tags($name));
        $email = htmlspecialchars(strip_tags($email));
        
        // Gán dữ liệu vào câu lệnh
        $stmt->bindParam(':fullname', $name);
        $stmt->bindParam(':role_id', $roleId);
        $stmt->bindParam(':google_id', $google_id);
        $stmt->bindParam(':email', $email);
        
        // Thực thi câu lệnh
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    // Kiểm tra xem username đã tồn tại chưa
    private function usernameExists($username) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE username = :username";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->count > 0;
    }
    
    // Kiểm tra xem số điện thoại đã tồn tại chưa
    public function phoneExists($phone) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE phonenumber = :phone";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':phone', $phone);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->count > 0;
    }
    
    // Kiểm tra xem email đã tồn tại chưa
    public function emailExists($email) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE email = :email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->count > 0;
    }
    
    // Save reset code and expiry for password recovery
    public function saveResetCode($user_id, $reset_code, $reset_expiry) {
        $query = "UPDATE " . $this->table_name . " 
                 SET reset_code = :reset_code, reset_expiry = :reset_expiry 
                 WHERE id = :user_id";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':reset_code', $reset_code);
        $stmt->bindParam(':reset_expiry', $reset_expiry);
        $stmt->bindParam(':user_id', $user_id);
        
        return $stmt->execute();
    }
    
    // Update user password
    public function updatePassword($user_id, $new_password) {
        $query = "UPDATE " . $this->table_name . " 
                 SET password = :password, reset_code = NULL, reset_expiry = NULL 
                 WHERE id = :user_id";
        $stmt = $this->conn->prepare($query);
        
        $stmt->bindParam(':password', $new_password);
        $stmt->bindParam(':user_id', $user_id);
        
        return $stmt->execute();
    }
    
    // New method to get all available roles
    public function getRoles() {
        $query = "SELECT * FROM user_roles";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    
    public function getAccountById($id)
    {
        $query = "SELECT u.*, r.role_name 
                 FROM " . $this->table_name . " u
                 LEFT JOIN user_roles r ON u.role_id = r.id
                 WHERE u.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result;
    }
}
