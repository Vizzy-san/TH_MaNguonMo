<?php
require_once('app/config/database.php');
require_once('app/models/ProductModel.php');
require_once('app/models/CategoryModel.php');
require_once('app/models/AccountModel.php');
require_once('app/helpers/SessionHelper.php');

class AdminController {
    private $db;
    private $productModel;
    private $categoryModel;
    private $accountModel;
    
    public function __construct() {
        // First check if user is logged in
        if (!SessionHelper::isLoggedIn()) {
            // Not logged in, redirect to login page
            header('Location: /BFYL/account/login');
            exit;
        }
        
        // Then check if user is admin
        if (!SessionHelper::isAdmin()) {
            // User is logged in but not an admin, show access denied
            header('Location: /BFYL/account/accessDenied');
            exit;
        }
        
        $this->db = (new Database())->getConnection();
        $this->productModel = new ProductModel($this->db);
        $this->categoryModel = new CategoryModel($this->db);
        $this->accountModel = new AccountModel($this->db);
    }
    
    // Dashboard - Overview page
    public function index() {
        // Get counts for dashboard
        $productCount = $this->productModel->getProductCount();
        $categoryCount = $this->categoryModel->getCategoryCount();
        
        // Get recent orders (assuming there's an OrderModel, we'll add stub data for now)
        $recentOrders = $this->getRecentOrders();
        
        include 'app/views/admin/dashboard.php';
    }
    
    // Product management
    public function products() {
        $products = $this->productModel->getProducts();
        include 'app/views/admin/products.php';
    }
    
    // Category management
    public function categories() {
        $categories = $this->categoryModel->getCategories();
        include 'app/views/admin/categories.php';
    }
    
    // User management
    public function users() {
        $users = $this->getUsers();
        include 'app/views/admin/users.php';
    }
    
    // Order management
    public function orders() {
        $orders = $this->getOrders();
        include 'app/views/admin/orders.php';
    }
    
    // Promotions management
    public function promotions() {
        $promotions = $this->getPromotions();
        include 'app/views/admin/promotions.php';
    }
    
    // Customer support management
    public function customer_support() {
        $tickets = $this->getSupportTickets();
        include 'app/views/admin/customer_support.php';
    }
    
    // This method handle customer-support URL format
    public function customersupport() {
        return $this->customer_support();
    }
    
    // Alternative method to handle hyphenated URL
    public function customer() {
        // This method handles /BFYL/admin/customer-support URL
        // The rest of the URL (after the hyphen) is passed as an argument to this method
        $action = isset($_GET['url']) ? explode('/', $_GET['url']) : [];
        
        if (isset($action[2]) && $action[2] == 'support') {
            // Call the customer_support method
            $this->customer_support();
        } else {
            // Default customer action or 404
            echo "Customer action not found";
        }
    }
    
    // Shipping management
    public function shipping() {
        $shippingMethods = $this->getShippingMethods();
        include 'app/views/admin/shipping.php';
    }
    
    // Content management
    public function content() {
        $contents = $this->getContents();
        include 'app/views/admin/content.php';
    }
    
    // Helper method to get recent orders
    private function getRecentOrders($limit = 5) {
        $query = "SELECT o.*, 
                    (SELECT COUNT(*) FROM order_details WHERE order_id = o.id) as item_count,
                    (SELECT SUM(price * quantity) FROM order_details WHERE order_id = o.id) as total_amount
                 FROM orders o 
                 ORDER BY o.created_at DESC 
                 LIMIT :limit";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    
    // Get all orders
    private function getOrders() {
        $query = "SELECT o.*, 
                    (SELECT COUNT(*) FROM order_details WHERE order_id = o.id) as item_count,
                    (SELECT SUM(price * quantity) FROM order_details WHERE order_id = o.id) as total_amount
                 FROM orders o 
                 ORDER BY o.created_at DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    
    // Get all users
    private function getUsers() {
        $query = "SELECT u.*, r.role_name 
                 FROM users u
                 LEFT JOIN user_roles r ON u.role_id = r.id
                 ORDER BY u.id DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    
    // View order details
    public function viewOrder($orderId) {
        $order = $this->getOrderById($orderId);
        $orderItems = $this->getOrderItems($orderId);
        
        include 'app/views/admin/order_details.php';
    }
    
    // Get order by ID
    private function getOrderById($orderId) {
        $query = "SELECT o.* FROM orders o WHERE o.id = :order_id";
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':order_id', $orderId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_OBJ);
    }
    
    // Get order items
    private function getOrderItems($orderId) {
        $query = "SELECT od.*, p.name, p.image 
                 FROM order_details od
                 LEFT JOIN product p ON od.product_id = p.id
                 WHERE od.order_id = :order_id";
        
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':order_id', $orderId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    
    // Get all promotions
    private function getPromotions() {
        $query = "SELECT * FROM promotions ORDER BY id DESC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    
    // Get all support tickets
    private function getSupportTickets() {
        try {
            // First, check if support_tickets table exists
            $checkQuery = "SHOW TABLES LIKE 'support_tickets'";
            $checkStmt = $this->db->prepare($checkQuery);
            $checkStmt->execute();
            
            if ($checkStmt->rowCount() == 0) {
                // Table doesn't exist yet, return empty array
                return [];
            }
            
            // Let's use a simpler query without joins
            $query = "SELECT t.* FROM support_tickets t ORDER BY t.status ASC, t.created_at DESC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            
            $tickets = $stmt->fetchAll(PDO::FETCH_OBJ);
            
            // Add placeholder customer info
            foreach ($tickets as $ticket) {
                // Get customer info from users table if possible
                if (isset($ticket->user_id)) {
                    try {
                        $userQuery = "SELECT * FROM users WHERE id = :user_id";
                        $userStmt = $this->db->prepare($userQuery);
                        $userStmt->bindParam(':user_id', $ticket->user_id, PDO::PARAM_INT);
                        $userStmt->execute();
                        $user = $userStmt->fetch(PDO::FETCH_OBJ);
                        
                        // Use available fields or defaults
                        $ticket->customer_name = $user && isset($user->username) ? $user->username : 'Customer #' . $ticket->user_id;
                        $ticket->customer_email = $user && isset($user->email) ? $user->email : 'customer' . $ticket->user_id . '@example.com';
                    } catch (Exception $e) {
                        // Fallback to default values
                        $ticket->customer_name = 'Customer #' . $ticket->user_id;
                        $ticket->customer_email = 'customer' . $ticket->user_id . '@example.com';
                    }
                } else {
                    $ticket->customer_name = 'Anonymous';
                    $ticket->customer_email = 'anonymous@example.com';
                }
            }
            
            return $tickets;
            
        } catch (PDOException $e) {
            // Log the error but don't crash
            error_log('Error in getSupportTickets: ' . $e->getMessage());
            return [];
        }
    }
    
    // Get all shipping methods
    private function getShippingMethods() {
        $query = "SELECT * FROM shipping_methods ORDER BY id ASC";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    
    // Get all content items
    private function getContents() {
        try {
            $query = "SELECT * FROM content ORDER BY position ASC";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_OBJ);
        } catch (PDOException $e) {
            // Table might not exist yet
            return [];
        }
    }
    
    // Default list action - redirects to index
    public function list() {
        // Redirect to index (dashboard) when list action is called
        $this->index();
    }
}