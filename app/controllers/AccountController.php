<?php
require_once('app/config/database.php');
require_once('app/models/AccountModel.php');
// Removed FavoriteModel require
require_once('app/config/google_auth.php');
require_once('app/helpers/SessionHelper.php');

class AccountController {
    private $accountModel;
    private $db;
    
    public function __construct() {
        $this->db = (new Database())->getConnection();
        $this->accountModel = new AccountModel($this->db);
        SessionHelper::init();
    }
    
    function register(){
        include_once 'app/views/account/register.php';
    }
    
    public function login() {
        include_once 'app/views/account/login.php';
    }
    
    function save(){
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $phone = $_POST['phone'] ?? '';
            $fullName = $_POST['fullname'] ?? '';
            $password = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirmpassword'] ?? '';
            $role = $_POST['role'] ?? 'user'; // Default to 'user' if not provided
            $email = $_POST['email'] ?? '';
            $errors = [];
            $clearFields = [];
            $shouldExitEarly = false;
            
            if(empty($phone)){
                $errors['phone'] = "Vui lòng nhập số điện thoại!";
                $clearFields['clear_all'] = true;
            } else if(!preg_match('/^(0|\+84)(\d{9,10})$/', $phone)) {
                $errors['phone'] = "Số điện thoại Việt Nam không hợp lệ! Định dạng hợp lệ: 0xxxxxxxxx hoặc +84xxxxxxxxx";
                $clearFields['clear_all'] = true;
            }
            
            if(empty($fullName)){
                $errors['fullname'] = "Vui lòng nhập họ tên!";
            }
            if(empty($password)){
                $errors['password'] = "Vui lòng nhập mật khẩu!";
            }
            if($password != $confirmPassword){
                $errors['confirmPass'] = "Mật khẩu và xác nhận mật khẩu không trùng khớp! Vui lòng kiểm tra lại.";
                $clearFields['clear_password'] = true;
            }
            
            // Validate email nếu có nhập
            if (!empty($email)) {
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors['email'] = "Email không hợp lệ!";
                    $clearFields['clear_all_except_phone'] = true;
                }
            }
            
            // Only allow admin role if the current user is an admin
            if ($role === 'admin' && !SessionHelper::isAdmin()) {
                $role = 'user';
            }
            
            //kiểm tra số điện thoại đã được đăng ký chưa?
            if($this->accountModel->phoneExists($phone)){
                // Set a specific session variable for phone already registered
                SessionHelper::set('phone_exists', true);
                SessionHelper::set('phone_exists_value', $phone);
                // Don't add to regular errors since we'll show a special toast
                $clearFields['clear_all'] = true;
                $shouldExitEarly = true;
            }
            
            // Kiểm tra email đã tồn tại và có giá trị
            if(!empty($email) && $this->accountModel->emailExists($email)){
                // Set a specific session variable for email already registered
                SessionHelper::set('email_exists', true);
                SessionHelper::set('email_exists_value', $email);
                // Don't add to regular errors since we'll show a special toast
                $clearFields['clear_all'] = true;
                $shouldExitEarly = true;
            }
            
            // Exit early if phone/email exists or there are errors
            if($shouldExitEarly || count($errors) > 0){
                // Store errors in session
                if(count($errors) > 0) {
                    SessionHelper::set('register_errors', $errors);
                }
                
                // Store field values to repopulate form
                if (!isset($clearFields['clear_all']) && !isset($clearFields['clear_all_except_phone'])) {
                    SessionHelper::set('old_phone', $phone);
                    SessionHelper::set('old_fullname', $fullName);
                    SessionHelper::set('old_email', $email);
                } else if (isset($clearFields['clear_all_except_phone'])) {
                    SessionHelper::set('old_phone', $phone);
                }
                if (!isset($clearFields['clear_password']) && !isset($clearFields['clear_all']) && !isset($clearFields['clear_all_except_phone'])) {
                    SessionHelper::set('old_password', $password);
                    SessionHelper::set('old_confirmpassword', $confirmPassword);
                }
                
                header('Location: /BFYL/account/register');
                exit;
            } else {
                $password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
                $result = $this->accountModel->saveWithPhone($phone, $fullName, $password, $role, $email);
                if($result){
                    header('Location: /BFYL/account/login');
                }
            }
        }
    }
    
    function logout(){
        unset($_SESSION['user_id']);
        unset($_SESSION['user_role']);
        unset($_SESSION['fullname']);
        unset($_SESSION['phone']);
        unset($_SESSION['user_email']);
        header('Location: /BFYL/product');
    }
    
    public function checkLogin(){
        // Kiểm tra xem liệu form đã được submit
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $phone = $_POST['phone'] ?? '';
            $password = $_POST['password'] ?? '';
            $errorMsg = '';
            
            // Validate phone number format
            if(empty($phone)){
                $errorMsg = "Vui lòng nhập số điện thoại!";
            } else if(!preg_match('/^(0|\+84)(\d{9,10})$/', $phone)) {
                $errorMsg = "Số điện thoại Việt Nam không hợp lệ! Định dạng hợp lệ: 0xxxxxxxxx hoặc +84xxxxxxxxx";
            }
            
            if($errorMsg) {
                // Store error in session
                SessionHelper::set('login_error', $errorMsg);
                header('Location: /BFYL/account/login');
                exit;
            }
            
            $account = $this->accountModel->getAccountByPhone($phone);
            if ($account) {
                $pwd_hashed = $account->password;
                //check mat khau
                if (password_verify($password, $pwd_hashed)) {
                    if (session_status() == PHP_SESSION_NONE) {
                        session_start();
                    }
                    $_SESSION['user_id'] = $account->id;
                    $_SESSION['user_role'] = $account->role_name;
                    $_SESSION['fullname'] = $account->fullname;
                    $_SESSION['phone'] = $account->phonenumber;
                    
                    // Check if there is a redirect URL set (coming from checkout)
                    $redirect = SessionHelper::get('redirect_after_login');
                    if ($redirect) {
                        // Clear the redirect URL from session
                        SessionHelper::delete('redirect_after_login');
                        header('Location: ' . $redirect);
                        exit;
                    }
                    
                    header('Location: /BFYL/product');
                    exit;
                }
                else {
                    // Store error in session
                    SessionHelper::set('login_error', "Mật khẩu không đúng.");
                    header('Location: /BFYL/account/login');
                    exit;
                }
            } else {
                // Store error in session
                SessionHelper::set('login_error', "Số điện thoại không đúng hoặc chưa đăng ký.");
                header('Location: /BFYL/account/login');
                exit;
            }
        }
    }
    
    // Phương thức dùng để tạo URL đăng nhập Google
    public function googleLogin() {
        $googleAuthUrl = 'https://accounts.google.com/o/oauth2/auth';
        
        $params = array(
            'client_id' => GOOGLE_CLIENT_ID,
            'redirect_uri' => GOOGLE_REDIRECT_URI,
            'response_type' => 'code',
            'scope' => 'email profile',
            'access_type' => 'online'
        );
        
        $authUrl = $googleAuthUrl . '?' . http_build_query($params);
        
        // Chuyển hướng đến trang đăng nhập Google
        header('Location: ' . $authUrl);
        exit;
    }
    
    // Xử lý callback từ Google sau khi người dùng xác thực
    public function google_callback() {
        if (isset($_GET['code'])) {
            $code = $_GET['code'];
            
            // Đổi code lấy access token
            $tokenUrl = 'https://oauth2.googleapis.com/token';
            $tokenData = [
                'code' => $code,
                'client_id' => GOOGLE_CLIENT_ID,
                'client_secret' => GOOGLE_CLIENT_SECRET,
                'redirect_uri' => GOOGLE_REDIRECT_URI,
                'grant_type' => 'authorization_code'
            ];
            
            $options = [
                'http' => [
                    'header' => "Content-type: application/x-www-form-urlencoded\r\n",
                    'method' => 'POST',
                    'content' => http_build_query($tokenData)
                ]
            ];
            
            $context = stream_context_create($options);
            $response = file_get_contents($tokenUrl, false, $context);
            
            if ($response) {
                $tokenInfo = json_decode($response);
                
                // Lấy thông tin người dùng từ Google
                $userInfoUrl = 'https://www.googleapis.com/oauth2/v3/userinfo';
                $options = [
                    'http' => [
                        'header' => "Authorization: Bearer " . $tokenInfo->access_token . "\r\n"
                    ]
                ];
                
                $context = stream_context_create($options);
                $userInfo = file_get_contents($userInfoUrl, false, $context);
                $userInfo = json_decode($userInfo);
                
                if ($userInfo) {
                    // Kiểm tra xem email đã tồn tại trong hệ thống chưa
                    $account = $this->accountModel->getAccountByEmail($userInfo->email);
                    
                    if (!$account) {
                        // Nếu tài khoản chưa tồn tại, tạo mới
                        $fullName = $userInfo->name;
                        
                        // Lưu thông tin vào DB với google_id, chỉ lưu fullname và email
                        $this->accountModel->saveGoogleUser($fullName, 'user', $userInfo->sub, $userInfo->email);
                        
                        // Cập nhật thông tin tài khoản để dùng ở bước login
                        $account = $this->accountModel->getAccountByEmail($userInfo->email);
                    }
                    
                    // Đăng nhập người dùng
                    SessionHelper::set('user_id', $account->id);
                    SessionHelper::set('user_role', $account->role_name);
                    SessionHelper::set('user_email', $account->email);
                    SessionHelper::set('fullname', $account->fullname);
                    
                    // Lưu số điện thoại nếu có để dùng cho tìm kiếm đơn hàng
                    if (!empty($account->phonenumber)) {
                        SessionHelper::set('phone', $account->phonenumber);
                    }
                    
                    // Chuyển hướng về trang chính
                    header('Location: /BFYL/product');
                    exit;
                }
            }
        }
        
        // Nếu có lỗi, chuyển hướng về trang đăng nhập
        header('Location: /BFYL/account/login');
        exit;
    }
    
    // Hiển thị form quên mật khẩu
    public function forgotPassword() {
        include_once 'app/views/account/forgot_password.php';
    }
    
    // Xử lý yêu cầu quên mật khẩu
    public function processForgotPassword() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $phone = $_POST['phone'] ?? '';
            $errorMsg = '';
            
            // Validate phone number format
            if(empty($phone)){
                $errorMsg = "Vui lòng nhập số điện thoại!";
            } else if(!preg_match('/^(0|\+84)(\d{9,10})$/', $phone)) {
                $errorMsg = "Số điện thoại Việt Nam không hợp lệ! Định dạng hợp lệ: 0xxxxxxxxx hoặc +84xxxxxxxxx";
            }
            
            if($errorMsg) {
                // Store error in session
                SessionHelper::set('forgot_error', $errorMsg);
                header('Location: /BFYL/account/forgotPassword');
                exit;
            }
            
            // Kiểm tra số điện thoại có tồn tại trong hệ thống
            $account = $this->accountModel->getAccountByPhone($phone);
            
            if (!$account) {
                // Số điện thoại không tồn tại
                SessionHelper::set('forgot_error', "Số điện thoại này chưa được đăng ký trong hệ thống.");
                header('Location: /BFYL/account/forgotPassword');
                exit;
            }
            
            // Tạo mã PIN ngẫu nhiên 6 số
            $resetCode = mt_rand(100000, 999999);
            $resetExpiry = date('Y-m-d H:i:s', strtotime('+30 minutes'));
            
            // Lưu mã PIN và thời gian hết hạn vào database
            $updated = $this->accountModel->saveResetCode($account->id, $resetCode, $resetExpiry);
            
            if ($updated) {
                // Trong môi trường thực tế, bạn sẽ gửi SMS với mã PIN đến số điện thoại người dùng
                // Ví dụ: sendSms($phone, "Mã PIN đặt lại mật khẩu của bạn là: " . $resetCode);
                
                // Trong demo này, chúng ta sẽ lưu mã PIN trong session để hiển thị
                SessionHelper::set('reset_phone', $phone);
                SessionHelper::set('reset_code_for_testing', $resetCode);
                
                // Chuyển hướng đến trang nhập mã PIN và mật khẩu mới
                header('Location: /BFYL/account/resetPassword');
                exit;
            } else {
                SessionHelper::set('forgot_error', "Có lỗi xảy ra. Vui lòng thử lại sau.");
                header('Location: /BFYL/account/forgotPassword');
                exit;
            }
        }
    }
    
    // Hiển thị form đặt lại mật khẩu
    public function resetPassword() {
        $phone = SessionHelper::get('reset_phone');
        if (!$phone) {
            header('Location: /BFYL/account/forgotPassword');
            exit;
        }
        
        include_once 'app/views/account/reset_password.php';
    }
    
    // Xử lý đặt lại mật khẩu
    public function processResetPassword() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $phone = SessionHelper::get('reset_phone');
            $verificationCode = $_POST['verification_code'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            
            // Validate inputs
            $errors = [];
            
            if(empty($verificationCode)) {
                $errors[] = "Vui lòng nhập mã PIN!";
            }
            
            if(empty($newPassword)) {
                $errors[] = "Vui lòng nhập mật khẩu mới!";
            }
            
            if($newPassword !== $confirmPassword) {
                $errors[] = "Mật khẩu xác nhận không khớp!";
            }
            
            if(!empty($errors)) {
                SessionHelper::set('reset_errors', $errors);
                header('Location: /BFYL/account/resetPassword');
                exit;
            }
            
            // Kiểm tra mã PIN
            $account = $this->accountModel->getAccountByPhone($phone);
            if (!$account || $account->reset_code != $verificationCode || strtotime($account->reset_expiry) < time()) {
                SessionHelper::set('reset_errors', ["Mã PIN không hợp lệ hoặc đã hết hạn!"]);
                header('Location: /BFYL/account/resetPassword');
                exit;
            }
            
            // Cập nhật mật khẩu mới
            $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
            $updated = $this->accountModel->updatePassword($account->id, $hashedPassword);
            
            if ($updated) {
                // Xóa thông tin reset password khỏi session
                SessionHelper::delete('reset_phone');
                SessionHelper::delete('reset_code_for_testing');
                
                // Thông báo thành công
                SessionHelper::set('login_success', "Mật khẩu đã được cập nhật thành công. Vui lòng đăng nhập với mật khẩu mới.");
                header('Location: /BFYL/account/login');
                exit;
            } else {
                SessionHelper::set('reset_errors', ["Có lỗi xảy ra khi cập nhật mật khẩu. Vui lòng thử lại sau."]);
                header('Location: /BFYL/account/resetPassword');
                exit;
            }
        }
    }
    
    // Hiển thị lịch sử đơn hàng của người dùng
    public function orderHistory() {
        // Kiểm tra đăng nhập
        if (!SessionHelper::isLoggedIn()) {
            header('Location: /BFYL/account/login');
            exit;
        }
        
        $user_id = SessionHelper::get('user_id');
        
        // Lấy đơn hàng dựa trên user_id (chỉ hiển thị đơn hàng của người dùng hiện tại)
        $orders = [];
        
        if ($user_id) {
            // Truy vấn đơn hàng dựa trên user_id (nếu user_id tồn tại trong orders)
            $query = "SELECT o.*, o.order_code,
                      (SELECT COUNT(*) FROM order_details WHERE order_id = o.id) as item_count,
                      (SELECT SUM(price * quantity) FROM order_details WHERE order_id = o.id) as total_amount
                      FROM orders o 
                      WHERE (o.user_id = :user_id) OR
                           (o.user_id IS NULL AND o.phone = :phone) 
                      ORDER BY o.created_at DESC";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':user_id', $user_id);
            
            // Store phone in variable before binding to avoid "pass by reference" error
            $phone = SessionHelper::get('phone');
            $stmt->bindParam(':phone', $phone);
            
            $stmt->execute();
            $orders = $stmt->fetchAll(PDO::FETCH_OBJ);
            
            // Lấy chi tiết đơn hàng
            foreach ($orders as &$order) {
                try {
                    $query = "SELECT od.*, p.name as product_name, p.image 
                              FROM order_details od 
                              JOIN product p ON od.product_id = p.id 
                              WHERE od.order_id = :order_id";
                    $stmt = $this->db->prepare($query);
                    $stmt->bindParam(':order_id', $order->id);
                    $stmt->execute();
                    $order->items = $stmt->fetchAll(PDO::FETCH_OBJ);
                } catch (PDOException $e) {
                    // Xử lý lỗi khi có vấn đề với truy vấn chi tiết
                    $order->items = [];
                }
            }
            // Unset the reference to avoid "Only variables should be passed by reference" error
            unset($order);
        }
        
        include 'app/views/account/order_history.php';
    }
    
    public function profile() {
        if (!SessionHelper::isLoggedIn()) {
            header('Location: /BFYL/account/login');
            exit;
        }
        $userId = $_SESSION['user_id'];
        $account = $this->accountModel->getAccountById($userId);
        
        // Removed favorites functionality as per request
        
        include 'app/views/account/profile.php';
    }
    
    // Handle password change
    public function changePassword() {
        if (!SessionHelper::isLoggedIn()) {
            header('Location: /BFYL/account/login');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $userId = $_SESSION['user_id'];
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';
            
            // Validate inputs
            if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
                SessionHelper::set('profile_message', 'Vui lòng điền đầy đủ thông tin.');
                SessionHelper::set('profile_message_type', 'danger');
                header('Location: /BFYL/account/profile');
                exit;
            }
            
            if ($newPassword !== $confirmPassword) {
                SessionHelper::set('profile_message', 'Mật khẩu mới và xác nhận mật khẩu không khớp.');
                SessionHelper::set('profile_message_type', 'danger');
                header('Location: /BFYL/account/profile');
                exit;
            }
            
            // Get current user data
            $account = $this->accountModel->getAccountById($userId);
            
            // Verify current password
            if (!password_verify($currentPassword, $account->password)) {
                SessionHelper::set('profile_message', 'Mật khẩu hiện tại không chính xác.');
                SessionHelper::set('profile_message_type', 'danger');
                header('Location: /BFYL/account/profile');
                exit;
            }
            
            // Hash the new password
            $hashedNewPassword = password_hash($newPassword, PASSWORD_BCRYPT);
            
            // Update password
            if ($this->accountModel->updatePassword($userId, $hashedNewPassword)) {
                SessionHelper::set('profile_message', 'Đổi mật khẩu thành công.');
                SessionHelper::set('profile_message_type', 'success');
            } else {
                SessionHelper::set('profile_message', 'Có lỗi xảy ra khi cập nhật mật khẩu.');
                SessionHelper::set('profile_message_type', 'danger');
            }
            
            header('Location: /BFYL/account/profile');
            exit;
        }
    }
    
    // Method to display the access denied page
    public function accessDenied() {
        include 'app/views/account/access_denied.php';
    }
}