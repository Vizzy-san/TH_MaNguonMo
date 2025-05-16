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
    
    function save($username, $name, $password, $role="user"){
        // First get the role_id for the specified role (default 'user')
        $roleQuery = "SELECT id FROM user_roles WHERE role_name = :role_name";
        $roleStmt = $this->conn->prepare($roleQuery);
        $roleStmt->bindParam(':role_name', $role);
        $roleStmt->execute();
        $roleResult = $roleStmt->fetch(PDO::FETCH_OBJ);
        
        $roleId = $roleResult ? $roleResult->id : null;
        
        // Then insert the user with the role_id
        $query = "INSERT INTO " . $this->table_name . "(username, password, role_id)
                VALUES (:username, :password, :role_id)";
        $stmt = $this->conn->prepare($query);
        
        // Làm sạch dữ liệu
        $name = htmlspecialchars(strip_tags($name));
        $username = htmlspecialchars(strip_tags($username));
        
        // Gán dữ liệu vào câu lệnh
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':role_id', $roleId);
        
        // Thực thi câu lệnh
        if ($stmt->execute()) {
            return true;
        }
        return false;
    }
    
    // New method to get all available roles
    public function getRoles() {
        $query = "SELECT * FROM user_roles";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
}
