<?php
require_once('app/config/database.php');
require_once('app/models/AccountModel.php');

class AccountController {
    private $accountModel;
    private $db;
    
    public function __construct() {
        $this->db = (new Database())->getConnection();
        $this->accountModel = new AccountModel($this->db);
    }
    
    function register(){
        include_once 'app/views/account/register.php';
    }
    
    public function login() {
        include_once 'app/views/account/login.php';
    }
    
    function save(){
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = $_POST['username'] ?? '';
            $fullName = $_POST['fullname'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirmpassword'] ?? '';
            $role = $_POST['role'] ?? 'user'; // Default to 'user' if not provided
            $errors = [];
            
            if(empty($username)){
                $errors['username'] = "Vui long nhap userName!";
            }
            if(empty($fullName)){
                $errors['fullname'] = "Vui long nhap fullName!";
            }
            if(empty($password)){
                $errors['password'] = "Vui long nhap password!";
            }
            if($password != $confirmPassword){
                $errors['confirmPass'] = "Mat khau va xac nhan chua dung";
            }
            
            // Only allow admin role if the current user is an admin
            if ($role === 'admin' && !SessionHelper::isAdmin()) {
                $role = 'user';
            }
            
            //kiểm tra username đã được đăng ký chưa?
            $account = $this->accountModel->getAccountByUsername($username);
            if($account){
                $errors['account'] = "Tai khoan nay da co nguoi dang ky!";
            }
            
            if(count($errors) > 0){
                include_once 'app/views/account/register.php';
            } else {
                $password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
                $result = $this->accountModel->save($username, $fullName, $password, $role);
                if($result){
                    header('Location: /project1/account/login');
                }
            }
        }
    }
    
    function logout(){
        unset($_SESSION['username']);
        unset($_SESSION['user_role']);
        header('Location: /project1/product');
    }
    
    public function checkLogin(){
        // Kiểm tra xem liệu form đã được submit
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            $account = $this->accountModel->getAccountByUsername($username);
            if ($account) {
                $pwd_hashed = $account->password;
                //check mat khau
                if (password_verify($password, $pwd_hashed)) {
                    if (session_status() == PHP_SESSION_NONE) {
                        session_start();
                    }
                    $_SESSION['username'] = $account->username;
                    $_SESSION['user_role'] = $account->role_name;
                    header('Location: /project1/product');
                    exit;
                }
                else {
                    echo "Password incorrect.";
                }
            } else {
                echo "Bao loi khong tim thay tai khoan";
            }
        }
    }
}
