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
    
    // User is logged in, show products as normal
    $products = $this->productModel->getProducts();
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
    
    // Removed favorites check as per request
    
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
        
        // Bắt đầu giao dịch
        $this->db->beginTransaction();
        
        try {
            // Generate custom order ID for database (alphanumeric)
            $order_code = $this->generateOrderId();
            
            // Calculate total amount
            $totalAmount = 0;
            foreach ($cart as $item) {
                $totalAmount += $item['price'] * $item['quantity'];
            }
            
            // Lưu thông tin đơn hàng vào bảng orders
            $query = "INSERT INTO orders (name, phone, address, payment_method, order_code, user_id, created_at) 
                      VALUES (:name, :phone, :address, :payment_method, :order_code, :user_id, NOW())";
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
            
            // PayOS QR processing
            $payos_data = null;
            if ($payment_method == 'payos_qr' || $payment_method == 'bank_transfer') {
                // Initialize PayOS
                $payos = new PayOS(PAYOS_CLIENT_ID, PAYOS_API_KEY, PAYOS_CHECKSUM_KEY);
                
                // Generate PayOS compatible numeric order code
                $payosOrderCode = $this->generatePayOSOrderCode();
                
                // Set up payment data
                $paymentData = [
                    'orderCode' => $payosOrderCode, // Use numeric order code for PayOS
                    'amount' => intval($totalAmount), // Ensure we use the actual cart total amount, cast to integer
                    'description' => "Thanh toán đơn hàng " . $order_code,
                    'returnUrl' => "http://" . $_SERVER['HTTP_HOST'] . "/BFYL/Product/paymentCallback",
                    'cancelUrl' => "http://" . $_SERVER['HTTP_HOST'] . "/BFYL/Product"
                ];
                
                // Create payment
                try {
                    // Create a new payment link for each transaction
                    $payos_data = $payos->createPaymentLink($paymentData);
                    
                    // Store PayOS data in session for the confirmation page
                    SessionHelper::set('payos_data', $payos_data);
                    
                    // Log success
                    error_log("PayOS Success: Created payment link for order " . $order_code);
                    
                    // Commit giao dịch
                    $this->db->commit();
                    
                    // Store order info in session
                    $_SESSION['current_order_id'] = $order_id;
                    
                    // Xóa giỏ hàng sau khi đặt hàng thành công
                    SessionHelper::delete('cart');
                    
                    // Always redirect directly to PayOS checkout URL for bank_transfer
                    if (isset($payos_data['checkoutUrl'])) {
                        header('Location: ' . $payos_data['checkoutUrl']);
                        exit;
                    } else {
                        // Fallback to home page if checkoutUrl is not available
                        header('Location: /BFYL/Product');
                        exit;
                    }
                } catch (Exception $e) {
                    // Log the error
                    error_log("PayOS Error: " . $e->getMessage());
                    
                    // Still commit the transaction as we have the order
                    $this->db->commit();
                    
                    // Redirect to home page
                    header('Location: /BFYL/Product');
                    exit;
                }
            }
            
            // Xóa giỏ hàng sau khi đặt hàng thành công
            SessionHelper::delete('cart');
            
            // Commit giao dịch if not PayOS (because PayOS already committed)
            if ($payment_method != 'payos_qr' && $payment_method != 'bank_transfer') {
                $this->db->commit();
            }
            
            // Chuyển hướng đến trang chủ thay vì trang xác nhận đơn hàng
            header('Location: /BFYL/Product');
        } catch (Exception $e) {
            // Rollback giao dịch nếu có lỗi
            $this->db->rollBack();
            echo "Đã xảy ra lỗi khi xử lý đơn hàng: " . $e->getMessage();
        }
    }
}

// Callback handler for PayOS payment confirmation
public function paymentCallback()
{
    if (isset($_GET['orderCode'])) {
        $orderCode = $_GET['orderCode'];
        $isAjax = isset($_GET['ajax']) && $_GET['ajax'] == '1';
        
        // Check if the payment was canceled
        if (isset($_GET['cancel']) && $_GET['cancel'] == 'true') {
            // Redirect to the payment canceled page
            header('Location: /BFYL/Product/paymentCanceled?' . http_build_query($_GET));
            exit;
        }
        
        // If this is a callback from PayOS with status parameter
        if (isset($_GET['status'])) {
            $status = $_GET['status'];
            
            // If payment was explicitly marked as CANCELLED
            if ($status == 'CANCELLED') {
                // Redirect to the payment canceled page
                header('Location: /BFYL/Product/paymentCanceled?' . http_build_query($_GET));
                exit;
            }
            
            // Update the order status based on PayOS callback
            if ($status == 'PAID') {
                // Find the order by orderCode
                $query = "SELECT id FROM orders WHERE order_code = :order_code";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':order_code', $orderCode);
                $stmt->execute();
                $order = $stmt->fetch(PDO::FETCH_OBJ);
                
                if ($order) {
                    // Set success message
                    SessionHelper::set('payment_success', 'Thanh toán thành công! Cảm ơn bạn đã đặt hàng.');
                    
                    // Update payment status in the database
                    $query = "UPDATE orders SET payment_status = 'paid' WHERE order_code = :order_code";
                    $stmt = $this->db->prepare($query);
                    $stmt->bindParam(':order_code', $orderCode);
                    $stmt->execute();
                    
                    // Redirect to home page instead of confirmation page
                    header('Location: /BFYL/Product');
                    exit;
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
                
                // Get order ID for this payment
                $query = "SELECT id FROM orders WHERE order_code = :order_code";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':order_code', $orderCode);
                $stmt->execute();
                $order = $stmt->fetch(PDO::FETCH_OBJ);
                
                // If payment status is PAID, update the database
                if ($paymentInfo['status'] === 'PAID' && $order) {
                    $query = "UPDATE orders SET payment_status = 'paid' WHERE order_code = :order_code";
                    $stmt = $this->db->prepare($query);
                    $stmt->bindParam(':order_code', $orderCode);
                    $stmt->execute();
                }
                
                // Return payment status as JSON
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => $paymentInfo['status'] ?? 'PENDING',
                    'message' => $paymentInfo['status'] == 'PAID' ? 'Payment successful' : 'Payment pending',
                    'orderId' => $order ? $order->id : null
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
                // Update the order in the database
                $query = "UPDATE orders SET payment_status = 'paid' WHERE order_code = :order_code";
                $stmt = $this->db->prepare($query);
                $stmt->bindParam(':order_code', $orderCode);
                $result = $stmt->execute();
                
                if ($result) {
                    http_response_code(200);
                    echo json_encode(['status' => 'success', 'message' => 'Payment processed successfully']);
                    return;
                }
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
        
        // Example test payment
        $orderId = isset($_SESSION['order_id']) ? $_SESSION['order_id'] : 1;
        $amount = 10000; // Example amount (100.00 VND)
        $description = "Test payment with PayOS";
        $customerId = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
        
        $paymentId = $paymentModel->createPayment($orderId, $amount, $description, $customerId);
        
        if ($paymentId) {
            // Initialize PayOS
            $payos = new PayOS(PAYOS_CLIENT_ID, PAYOS_API_KEY, PAYOS_CHECKSUM_KEY);
            
            // Generate compatible numeric order code
            $orderCode = $this->generatePayOSOrderCode();
            
            $paymentData = [
                'orderCode' => $orderCode, // Already an integer, no need to cast
                'amount' => $amount,
                'description' => $description,
                'returnUrl' => "http://" . $_SERVER['HTTP_HOST'] . "/BFYL/Product/paymentCallback",
                'cancelUrl' => "http://" . $_SERVER['HTTP_HOST'] . "/BFYL/Product"
            ];
            
            // Create actual PayOS payment link
            try {
                $payos_data = $payos->createPaymentLink($paymentData);
                
                // Store the data for the view and update our payment record
                $transactionId = $payos_data['paymentLinkId'] ?? 'unknown';
                $qrCode = $payos_data['qrCode'] ?? '';
                $checkoutUrl = $payos_data['checkoutUrl'] ?? '';
                $deeplink = $payos_data['deeplink'] ?? '';
                
                $paymentModel->savePayOSResponse($paymentId, $transactionId, $qrCode, $checkoutUrl, $deeplink);
                
                // Make variables available for the view
                $paymentId = $paymentId;
                $transactionId = $transactionId;
                $qr_code = $qrCode;
                $checkout_url = $checkoutUrl;
                
                // Include the view directly instead of using view() method
                include 'app/views/cart/payosPayment.php';
            } catch (Exception $e) {
                echo "PayOS Error: " . $e->getMessage();
            }
        } else {
            // Handle error
            echo "Error creating payment record";
        }
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
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