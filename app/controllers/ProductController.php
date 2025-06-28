<?php
// Require SessionHelper and other necessary files
require_once('app/config/database.php');
require_once('app/models/ProductModel.php');
require_once('app/models/CategoryModel.php');
require_once('app/helpers/SessionHelper.php');
require_once('app/config/payos.php'); // Add PayOS configuration
require_once('vendor/autoload.php'); // Add composer autoload for PayOS

use PayOS\PayOS;

class ProductController
{
private $productModel;
private $db;
private $upload_dir = "uploads/";

public function __construct()
{
$this->db = (new Database())->getConnection();
$this->productModel = new ProductModel($this->db);
SessionHelper::init();

// Create uploads directory if it doesn't exist
if (!file_exists($this->upload_dir)) {
    mkdir($this->upload_dir, 0777, true);
}
}

// Generate a custom order ID with 2 letters + 6 random numbers
private function generateOrderId() {
    // Use the first 2 letters from a set
    $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $prefix = substr(str_shuffle($letters), 0, 2);
    
    // Generate 6 random numbers
    $numbers = mt_rand(100000, 999999);
    
    return $prefix . $numbers;
}

// Create a new method for generating PayOS-compatible numeric order codes
private function generatePayOSOrderCode() {
    // Create a unique numeric ID that's small enough to fit within PayOS constraints
    // Use current timestamp (10 digits) + random 6 digits for uniqueness, but keep it under MAX_SAFE_INTEGER
    $timestamp = time(); // 10 digits
    $random = mt_rand(100000, 999999); // 6 digits
    
    // Combine to create a max 16-digit number, well under 9007199254740991 (which is 16 digits)
    return (int)($timestamp . $random);
}

// Handle file upload and return filename if successful
private function handleFileUpload() {
    // Check if a file was uploaded
    if(!isset($_FILES['image']) || $_FILES['image']['error'] == UPLOAD_ERR_NO_FILE) {
        return null; // No file was uploaded
    }
    
    // Check for errors
    if($_FILES['image']['error'] != UPLOAD_ERR_OK) {
        return false; // Error in upload
    }
    
    // Validate file type
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg', 'image/webp'];
    if(!in_array($_FILES['image']['type'], $allowed_types)) {
        return false; // Invalid file type
    }
    
    // Generate unique filename
    $filename = uniqid() . '_' . basename($_FILES['image']['name']);
    $target_file = $this->upload_dir . $filename;
    
    // Move uploaded file to target directory
    if(move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
        return $filename;
    }
    
    return false; // Failed to move file
}

public function index()
{
    // Check if user is logged in
    if (!SessionHelper::isLoggedIn()) {
        // User is not logged in, output JavaScript alert and redirect
        echo '<!DOCTYPE html>
            <html>
            <head>
                <title>Login Required</title>
                <script>
                    alert("Bạn cần đăng nhập để xem danh sách sản phẩm");
                    window.location.href = "/BFYL/account/login";
                </script>
            </head>
            <body></body>
            </html>';
        return;
    }
    
    // User is logged in, check if category filter is provided
    $categoryId = isset($_GET['category']) ? $_GET['category'] : null;
    
    if ($categoryId) {
        // Get products filtered by category
        $products = $this->productModel->getProductsByCategory($categoryId);
        
        // Get the category name for display
        $categoryModel = new CategoryModel($this->db);
        $category = $categoryModel->getCategoryById($categoryId);
    } else {
        // Show all products as normal
        $products = $this->productModel->getProducts();
    }
    
    include 'app/views/product/list.php';
}

public function list()
{
    // Just call the index method which already handles list functionality
    $this->index();
}

public function show($id)
{
    $product = $this->productModel->getProductById($id);
    
    // Check if the product is in favorites if logged in
    $isFavorite = false;
    if (SessionHelper::isLoggedIn()) {
        $favoriteModel = new FavoriteModel($this->db);
        $userId = SessionHelper::get('user_id');
        $isFavorite = $favoriteModel->isFavorite($userId, $id);
    }
    
    if ($product) {
        include 'app/views/product/show.php';
    } else {
        echo "Không thấy sản phẩm.";
    }
}

public function add()
{
$categories = (new CategoryModel($this->db))->getCategories();
include_once 'app/views/product/add.php';
}

public function save()
{
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    $price = $_POST['price'] ?? '';
    $category_id = $_POST['category_id'] ?? null;
    
    // Handle file upload
    $image = $this->handleFileUpload();
    if ($image === false) {
        $errors = ['image' => 'Có lỗi khi tải lên hình ảnh. Vui lòng thử lại.'];
        $categories = (new CategoryModel($this->db))->getCategories();
        include 'app/views/product/add.php';
        return;
    }
    
    $result = $this->productModel->addProduct($name, $description, $price, $category_id, $image);

    if (is_array($result)) {
        $errors = $result;
        $categories = (new CategoryModel($this->db))->getCategories();
        include 'app/views/product/add.php';
    } else {
        header('Location: /BFYL/Product/');
    }
}
}

public function edit($id)
{
$product = $this->productModel->getProductById($id);
$categories = (new CategoryModel($this->db))->getCategories();
if ($product) {
include 'app/views/product/edit.php';
} else {
echo "Không thấy sản phẩm.";
}
}

public function update()
{
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $category_id = $_POST['category_id'];
    $current_image = $_POST['current_image'] ?? '';
    
    // Handle file upload if a new image was uploaded
    $image = $this->handleFileUpload();
    if ($image === false) {
        echo "Có lỗi khi tải lên hình ảnh. Vui lòng thử lại.";
        return;
    }
    
    // If no new image was uploaded, keep the current one
    if ($image === null) {
        $image = $current_image;
    } else if (!empty($current_image)) {
        // Remove old image if a new one was uploaded
        if (file_exists($this->upload_dir . $current_image)) {
            unlink($this->upload_dir . $current_image);
        }
    }
    
    $edit = $this->productModel->updateProduct($id, $name, $description, $price, $category_id, $image);
    if ($edit) {
        header('Location: /BFYL/Product/');
    } else {
        echo "Đã xảy ra lỗi khi lưu sản phẩm.";
    }
}
}

public function delete($id)
{
    // Get product to find image before deleting
    $product = $this->productModel->getProductById($id);
    
    if ($this->productModel->deleteProduct($id)) {
        // Delete associated image file if it exists
        if ($product && !empty($product->image)) {
            if (file_exists($this->upload_dir . $product->image)) {
                unlink($this->upload_dir . $product->image);
            }
        }
        header('Location: /BFYL/Product/');
    } else {
        echo "Đã xảy ra lỗi khi xóa sản phẩm.";
    }
}

public function cart()
{
    $cart = SessionHelper::get('cart') ?: [];
    include 'app/views/cart/cart.php';
}

public function addToCart($id)
{
    $product = $this->productModel->getProductById($id);
    if (!$product) {
        echo "Không tìm thấy sản phẩm.";
        return;
    }
    
    $cart = SessionHelper::get('cart') ?: [];
    
    if (isset($cart[$id])) {
        $cart[$id]['quantity']++;
    } else {
        $cart[$id] = [
            'name' => $product->name,
            'price' => $product->price,
            'quantity' => 1,
            'image' => $product->image
        ];
    }
    
    SessionHelper::set('cart', $cart);
    SessionHelper::set('cart_success', "Sản phẩm \"" . $product->name . "\" đã được thêm vào giỏ hàng thành công!");
    
    // Redirect back to the referring page instead of cart page
    $referer = $_SERVER['HTTP_REFERER'] ?? '/BFYL/Product';
    header('Location: ' . $referer);
}

public function removeFromCart($id)
{
    $cart = SessionHelper::get('cart') ?: [];
    
    if (isset($cart[$id])) {
        $productName = $cart[$id]['name'];
        unset($cart[$id]);
        SessionHelper::set('cart', $cart);
        SessionHelper::set('cart_error', "Sản phẩm \"" . $productName . "\" đã được xóa khỏi giỏ hàng!");
    }
    
    header('Location: /BFYL/Product/cart');
}

public function updateCart()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $cart = SessionHelper::get('cart') ?: [];
        $quantities = $_POST['quantity'] ?? [];
        
        foreach ($quantities as $id => $quantity) {
            if (isset($cart[$id])) {
                $quantity = (int)$quantity;
                if ($quantity > 0) {
                    $cart[$id]['quantity'] = $quantity;
                } else {
                    unset($cart[$id]);
                }
            }
        }
        
        SessionHelper::set('cart', $cart);
        SessionHelper::set('cart_success', "Giỏ hàng đã được cập nhật thành công!");
        header('Location: /BFYL/Product/cart');
    }
}

public function checkout()
{
    // Check if user is logged in
    if (!SessionHelper::isLoggedIn()) {
        // Store a message indicating login is required
        SessionHelper::set('login_required', 'Vui lòng đăng nhập để tiếp tục thanh toán.');
        
        // Store the intended destination for post-login redirect
        SessionHelper::set('redirect_after_login', '/BFYL/Product/checkout');
        
        // Redirect to login page
        header('Location: /BFYL/account/login');
        exit;
    }
    
    $cart = SessionHelper::get('cart') ?: [];
    if (empty($cart)) {
        echo "Giỏ hàng trống.";
        return;
    }
    
    include 'app/views/cart/checkout.php';
}

public function processCheckout()
{
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $name = $_POST['name'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];
        $payment_method = $_POST['payment_method'] ?? 'cod';  // Default to COD if not specified
        $user_id = SessionHelper::get('user_id'); // Get logged-in user ID
        
        // Kiểm tra giỏ hàng
        $cart = SessionHelper::get('cart');
        if (!$cart || empty($cart)) {
            echo "Giỏ hàng trống.";
            return;
        }
        
        // Generate custom order ID for database (alphanumeric)
        $order_code = $this->generateOrderId();
        
        // Calculate total amount
        $totalAmount = 0;
        foreach ($cart as $item) {
            $totalAmount += $item['price'] * $item['quantity'];
        }
        
        // For bank transfers, don't save to database yet, just store in session
        if ($payment_method == 'payos_qr' || $payment_method == 'bank_transfer') {
            // Store all order data in session for later database insertion
            $_SESSION['pending_order'] = [
                'name' => $name,
                'phone' => $phone,
                'address' => $address,
                'payment_method' => $payment_method,
                'order_code' => $order_code,
                'user_id' => $user_id,
                'cart' => $cart,
                'total_amount' => $totalAmount,
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            // Store customer details in session for PayOS payment
            $_SESSION['checkout_details'] = [
                'name' => $name,
                'phone' => $phone,
                'address' => $address,
                'payment_method' => $payment_method,
                'order_code' => $order_code,
                'amount' => $totalAmount
            ];
            
            // Redirect to testPayment without an order_id (it hasn't been saved yet)
            header('Location: /BFYL/Product/testPayment');
            exit;
        }
        
        // For COD payments, save to database immediately
        else {
            // Bắt đầu giao dịch
            $this->db->beginTransaction();
            
            try {
                // Lưu thông tin đơn hàng vào bảng orders
                $query = "INSERT INTO orders (name, phone, address, payment_method, order_code, user_id, created_at, payment_status) 
                          VALUES (:name, :phone, :address, :payment_method, :order_code, :user_id, NOW(), 'pending')";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':phone', $phone);
                $stmt->bindParam(':address', $address);
                $stmt->bindParam(':payment_method', $payment_method);
                $stmt->bindParam(':order_code', $order_code);
                $stmt->bindParam(':user_id', $user_id);
                $stmt->execute();
                $order_id = $this->db->lastInsertId();
                
                // Lưu chi tiết đơn hàng vào bảng order_details
                foreach ($cart as $product_id => $item) {
                    $query = "INSERT INTO order_details (order_id, product_id, quantity, price) VALUES (:order_id, :product_id, :quantity, :price)";
                    $stmt = $this->db->prepare($query);
                    $stmt->bindParam(':order_id', $order_id);
                    $stmt->bindParam(':product_id', $product_id);
                    $stmt->bindParam(':quantity', $item['quantity']);
                    $stmt->bindParam(':price', $item['price']);
                    $stmt->execute();
                }
                
                // Store order info in session
                $_SESSION['current_order_id'] = $order_id;
                $_SESSION['order_code'] = $order_code;
                
                $this->db->commit();
                
                // Xóa giỏ hàng sau khi đặt hàng thành công
                SessionHelper::delete('cart');
                
                // Redirect to order success page for COD
                include 'app/views/cart/order-success.php';
                exit;
            } catch (Exception $e) {
                // Rollback giao dịch nếu có lỗi
                $this->db->rollBack();
                echo "Đã xảy ra lỗi khi xử lý đơn hàng: " . $e->getMessage();
            }
        }
    }
}

public function paymentCallback()
{
    if (isset($_GET['orderCode'])) {
        $orderCode = $_GET['orderCode'];
        $isAjax = isset($_GET['ajax']) && $_GET['ajax'] == '1';
        $payOSOrderCode = $_SESSION['payos_order_code'] ?? null;
        
        // Check if the payment was canceled
        if (isset($_GET['cancel']) && $_GET['cancel'] == 'true') {
            // Clear pending order data
            unset($_SESSION['pending_order']);
            unset($_SESSION['checkout_details']);
            unset($_SESSION['pending_payment_id']);
            unset($_SESSION['payos_order_code']);
            
            // Redirect to the payment canceled page
            header('Location: /BFYL/Product/paymentCanceled?' . http_build_query($_GET));
            exit;
        }
        
        // If this is a callback from PayOS with status parameter
        if (isset($_GET['status'])) {
            $status = $_GET['status'];
            
            // If payment was explicitly marked as CANCELLED
            if ($status == 'CANCELLED') {
                // Clear pending order data
                unset($_SESSION['pending_order']);
                unset($_SESSION['checkout_details']);
                unset($_SESSION['pending_payment_id']);
                unset($_SESSION['payos_order_code']);
                
                // Redirect to the payment canceled page
                header('Location: /BFYL/Product/paymentCanceled?' . http_build_query($_GET));
                exit;
            }
            
            // Update the order status based on PayOS callback
            if ($status == 'PAID') {
                // Get pending order data from session
                $pendingOrder = $_SESSION['pending_order'] ?? null;
                $pendingPaymentId = $_SESSION['pending_payment_id'] ?? null;
                
                if ($pendingOrder && $payOSOrderCode == $orderCode) {
                    // Now save the order to database since payment was successful
                    $this->db->beginTransaction();
                    
                    try {
                        // Insert into orders table
                        $query = "INSERT INTO orders (name, phone, address, payment_method, order_code, user_id, created_at, payment_status) 
                                VALUES (:name, :phone, :address, :payment_method, :order_code, :user_id, :created_at, 'paid')";
                        $stmt = $this->db->prepare($query);
                        $stmt->bindParam(':name', $pendingOrder['name']);
                        $stmt->bindParam(':phone', $pendingOrder['phone']);
                        $stmt->bindParam(':address', $pendingOrder['address']);
                        $stmt->bindParam(':payment_method', $pendingOrder['payment_method']);
                        $stmt->bindParam(':order_code', $pendingOrder['order_code']);
                        $stmt->bindParam(':user_id', $pendingOrder['user_id']);
                        $stmt->bindParam(':created_at', $pendingOrder['created_at']);
                        $stmt->execute();
                        $orderId = $this->db->lastInsertId();
                        
                        // Save order details
                        foreach ($pendingOrder['cart'] as $productId => $item) {
                            $query = "INSERT INTO order_details (order_id, product_id, quantity, price) 
                                    VALUES (:order_id, :product_id, :quantity, :price)";
                            $stmt = $this->db->prepare($query);
                            $stmt->bindParam(':order_id', $orderId);
                            $stmt->bindParam(':product_id', $productId);
                            $stmt->bindParam(':quantity', $item['quantity']);
                            $stmt->bindParam(':price', $item['price']);
                            $stmt->execute();
                        }
                        
                        // Update the payment record with the real order ID
                        if ($pendingPaymentId) {
                            $query = "UPDATE payments SET order_id = :order_id WHERE id = :payment_id";
                            $stmt = $this->db->prepare($query);
                            $stmt->bindParam(':order_id', $orderId);
                            $stmt->bindParam(':payment_id', $pendingPaymentId);
                            $stmt->execute();
                        }
                        
                        $this->db->commit();
                        
                        // Set success message
                        SessionHelper::set('payment_success', 'Thanh toán thành công! Cảm ơn bạn đã đặt hàng.');
                        
                        // Clear cart and pending order data
                        SessionHelper::delete('cart');
                        unset($_SESSION['pending_order']);
                        unset($_SESSION['checkout_details']);
                        unset($_SESSION['pending_payment_id']);
                        unset($_SESSION['payos_order_code']);
                        
                        // Redirect to payment success page
                        header('Location: /BFYL/Product/paymentSuccess?orderCode=' . $pendingOrder['order_code']);
                        exit;
                    } catch (Exception $e) {
                        $this->db->rollBack();
                        error_log("Error saving order after payment: " . $e->getMessage());
                        
                        // Redirect with error
                        SessionHelper::set('payment_error', 'Thanh toán thành công nhưng có lỗi lưu đơn hàng. Vui lòng liên hệ admin.');
                        header('Location: /BFYL/Product');
                        exit;
                    }
                }
            }
        } 
        // For AJAX requests checking payment status
        else if ($isAjax) {
            // Initialize PayOS
            try {
                $payos = new PayOS(PAYOS_CLIENT_ID, PAYOS_API_KEY, PAYOS_CHECKSUM_KEY);
                
                // Get payment information
                $paymentInfo = $payos->getPaymentLinkInformation($orderCode);
                
                // If payment status is PAID, process the pending order
                if ($paymentInfo['status'] === 'PAID') {
                    // Get pending order data from session
                    $pendingOrder = $_SESSION['pending_order'] ?? null;
                    $pendingPaymentId = $_SESSION['pending_payment_id'] ?? null;
                    
                    if ($pendingOrder && $payOSOrderCode == $orderCode) {
                        // Now save the order to database since payment was successful
                        $this->db->beginTransaction();
                        
                        try {
                            // Insert into orders table
                            $query = "INSERT INTO orders (name, phone, address, payment_method, order_code, user_id, created_at, payment_status) 
                                    VALUES (:name, :phone, :address, :payment_method, :order_code, :user_id, :created_at, 'paid')";
                            $stmt = $this->db->prepare($query);
                            $stmt->bindParam(':name', $pendingOrder['name']);
                            $stmt->bindParam(':phone', $pendingOrder['phone']);
                            $stmt->bindParam(':address', $pendingOrder['address']);
                            $stmt->bindParam(':payment_method', $pendingOrder['payment_method']);
                            $stmt->bindParam(':order_code', $pendingOrder['order_code']);
                            $stmt->bindParam(':user_id', $pendingOrder['user_id']);
                            $stmt->bindParam(':created_at', $pendingOrder['created_at']);
                            $stmt->execute();
                            $orderId = $this->db->lastInsertId();
                            
                            // Save order details
                            foreach ($pendingOrder['cart'] as $productId => $item) {
                                $query = "INSERT INTO order_details (order_id, product_id, quantity, price) 
                                        VALUES (:order_id, :product_id, :quantity, :price)";
                                $stmt = $this->db->prepare($query);
                                $stmt->bindParam(':order_id', $orderId);
                                $stmt->bindParam(':product_id', $productId);
                                $stmt->bindParam(':quantity', $item['quantity']);
                                $stmt->bindParam(':price', $item['price']);
                                $stmt->execute();
                            }
                            
                            // Update the payment record with the real order ID
                            if ($pendingPaymentId) {
                                $query = "UPDATE payments SET order_id = :order_id WHERE id = :payment_id";
                                $stmt = $this->db->prepare($query);
                                $stmt->bindParam(':order_id', $orderId);
                                $stmt->bindParam(':payment_id', $pendingPaymentId);
                                $stmt->execute();
                            }
                            
                            $this->db->commit();
                            
                            // Clear cart but keep order data for redirect after AJAX
                            SessionHelper::delete('cart');
                        } catch (Exception $e) {
                            $this->db->rollBack();
                            error_log("AJAX - Error saving order after payment: " . $e->getMessage());
                        }
                    }
                }
                
                // Return payment status as JSON
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => $paymentInfo['status'] ?? 'PENDING',
                    'message' => $paymentInfo['status'] == 'PAID' ? 'Payment successful' : 'Payment pending'
                ]);
                exit;
            } catch (Exception $e) {
                // Return error
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 'ERROR',
                    'message' => $e->getMessage()
                ]);
                exit;
            }
        }
    }
    
    // Redirect to home if something went wrong (only for non-AJAX requests)
    if (!isset($isAjax) || !$isAjax) {
        SessionHelper::set('payment_error', 'Có lỗi xảy ra trong quá trình thanh toán. Vui lòng thử lại.');
        header('Location: /BFYL/Product');
    }
}

// Add new method to handle payment success page
public function paymentSuccess()
{
    // Get order code from query parameters
    $orderCode = $_GET['orderCode'] ?? '';
    
    if (empty($orderCode)) {
        header('Location: /BFYL/Product');
        exit;
    }
    
    // Get order details from database
    $query = "SELECT o.*, p.transaction_id 
              FROM orders o 
              LEFT JOIN payments p ON o.id = p.order_id 
              WHERE o.order_code = :order_code 
              LIMIT 1";
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(':order_code', $orderCode);
    $stmt->execute();
    $order = $stmt->fetch(PDO::FETCH_OBJ);
    
    if (!$order) {
        header('Location: /BFYL/Product');
        exit;
    }
    
    // Get order items
    $query = "SELECT od.*, p.name, p.image 
              FROM order_details od 
              LEFT JOIN product p ON od.product_id = p.id 
              WHERE od.order_id = :order_id";
    $stmt = $this->db->prepare($query);
    $stmt->bindParam(':order_id', $order->id);
    $stmt->execute();
    $order_items = $stmt->fetchAll(PDO::FETCH_OBJ);
    
    // Calculate total amount
    $totalAmount = 0;
    foreach ($order_items as $item) {
        $totalAmount += $item->price * $item->quantity;
    }
    
    // Set variables for the view
    $order_code = $orderCode;
    $transaction_id = $order->transaction_id ?? '';
    $payment_success = SessionHelper::get('payment_success');
    
    // Clear cart after successful payment - moved before including the view
    SessionHelper::delete('cart');
    
    include 'app/views/cart/paymentSuccess.php';
}

// Handle PayOS webhook callbacks
public function payosWebhook()
{
    try {
        // Get the raw POST data
        $inputJSON = file_get_contents('php://input');
        $webhookData = json_decode($inputJSON, true);
        
        if (!$webhookData) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => 'Invalid JSON data']);
            return;
        }
        
        // Initialize PayOS
        $payos = new PayOS(PAYOS_CLIENT_ID, PAYOS_API_KEY, PAYOS_CHECKSUM_KEY);
        
        // Verify the webhook data
        $verifiedData = $payos->verifyPaymentWebhookData($webhookData);
        
        // Process the verified data
        if ($verifiedData && isset($verifiedData['orderCode']) && isset($verifiedData['status'])) {
            $orderCode = $verifiedData['orderCode'];
            $status = $verifiedData['status'];
            
            if ($status == 'PAID') {
                // Use a queue system or log for asynchronous processing
                // In a real system, you should implement a robust queue system
                // For this example, we'll log the payment for later processing
                $logFile = 'payos_successful_payments.log';
                file_put_contents($logFile, date('Y-m-d H:i:s') . " - Order Code: $orderCode - Status: $status\n", FILE_APPEND);
                
                http_response_code(200);
                echo json_encode(['status' => 'success', 'message' => 'Payment logged for processing']);
                return;
            }
        }
        
        // Something went wrong
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Failed to process payment']);
        
    } catch (Exception $e) {
        // Log error
        error_log('PayOS Webhook Error: ' . $e->getMessage());
        
        // Return error response
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Internal server error']);
    }
}

// Handle PayOS test payment - tạo đơn hàng test và chuyển thẳng đến trang PayOS
public function testPayment() {
    try {
        require_once 'app/models/PaymentModel.php';
        $paymentModel = new PaymentModel();
        
        // Get pending order from session
        $pendingOrder = $_SESSION['pending_order'] ?? null;
        
        // Get checkout details from session
        $checkoutDetails = $_SESSION['checkout_details'] ?? null;
        $amount = $checkoutDetails['amount'] ?? 10000; // Use order amount or default to 10000
        
        // Truncate description to max 25 characters to comply with PayOS requirements
        $orderCodeText = $checkoutDetails['order_code'] ?? 'Test';
        $description = "Đơn #" . $orderCodeText;
        $description = mb_substr($description, 0, 25); // Ensure limit of 25 characters
        
        $customerId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        
        // Create temporary payment record without actual order_id
        // We'll use 0 as a placeholder since the order doesn't exist yet
        $tempOrderId = 0;
        $paymentId = $paymentModel->createPayment($tempOrderId, $amount, $description, $customerId);
        
        if ($paymentId) {
            // Save payment ID in session to link with order later
            $_SESSION['pending_payment_id'] = $paymentId;
            
            // Initialize PayOS
            $payos = new PayOS(PAYOS_CLIENT_ID, PAYOS_API_KEY, PAYOS_CHECKSUM_KEY);
            
            // Generate compatible numeric order code
            $orderCode = $this->generatePayOSOrderCode();
            // Make sure to assign to $order_code for the view
            $order_code = $checkoutDetails['order_code'] ?? $this->generateOrderId();
            
            // Store the orderCode in session for callback reference
            $_SESSION['payos_order_code'] = $orderCode;
            
            $paymentData = [
                'orderCode' => $orderCode,
                'amount' => $amount,
                'description' => $description,
                'returnUrl' => "http://" . $_SERVER['HTTP_HOST'] . "/BFYL/Product/paymentCallback",
                'cancelUrl' => "http://" . $_SERVER['HTTP_HOST'] . "/BFYL/Product/paymentCallback?cancel=true&orderCode=" . $orderCode
            ];
            
            // Create actual PayOS payment link
            try {
                $payos_data = $payos->createPaymentLink($paymentData);
                
                // Store the data for the view and update our payment record
                // Use PayOS orderCode as transaction_id instead of paymentLinkId
                $transactionId = (string)$orderCode; // Convert to string for compatibility
                $qrCode = $payos_data['qrCode'] ?? '';
                $checkoutUrl = $payos_data['checkoutUrl'] ?? '';
                $deeplink = $payos_data['deeplink'] ?? '';
                
                $paymentModel->savePayOSResponse($paymentId, $transactionId, $qrCode, $checkoutUrl, $deeplink);
                
                // Make variables available for the view
                $paymentId = $paymentId;
                $transactionId = $transactionId;
                $qr_code = $qrCode;
                $checkout_url = $checkoutUrl;
                
                // Setup order for the view
                $order = null;
                if ($checkoutDetails) {
                    $order = (object) [
                        'name' => $checkoutDetails['name'],
                        'phone' => $checkoutDetails['phone'],
                        'address' => $checkoutDetails['address']
                    ];
                    $payment_text = 'Thanh toán bằng chuyển khoản ngân hàng';
                }
                
                // Get cart items from session for product details
                $cart_items = SessionHelper::get('cart') ?? [];
                
                // Include the view directly instead of using view() method
                include 'app/views/cart/payosPayment.php';
            } catch (Exception $e) {
                error_log("PayOS Create Payment Link Error: " . $e->getMessage());
                echo "Có lỗi khi tạo liên kết thanh toán. Vui lòng thử lại.";
            }
        } else {
            echo "Có lỗi khi tạo đơn hàng. Vui lòng thử lại.";
        }
    } catch (Exception $e) {
        error_log("Error in testPayment: " . $e->getMessage());
        echo "Có lỗi xảy ra. Vui lòng thử lại.";
    }
}

// Handle PayOS payment cancellation
public function paymentCanceled()
{
    // Get any parameters passed from PayOS
    $orderCode = $_GET['orderCode'] ?? '';
    $status = $_GET['status'] ?? '';
    
    // Include the payment canceled view
    include 'app/views/cart/payment-canceled.php';
}
}
?>