<?php
require_once('app/config/database.php');
require_once('app/models/ProductModel.php');
require_once('app/models/CategoryModel.php');
require_once('app/utils/JWTHandler.php');

class ProductApiController
{
    private $productModel;
    private $db;
    private $jwtHandler;
    
    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->productModel = new ProductModel($this->db);
        $this->jwtHandler = new JWTHandler();
    }
    
    /**
     * Authenticate user with JWT token
     * 
     * @return bool True if authenticated, false otherwise
     */
    private function authenticate()
    {
        $headers = apache_request_headers();

        // Debug log to check received headers
        error_log("Auth headers received: " . json_encode($headers));

        if (isset($headers['Authorization']) || isset($headers['authorization'])) {
            // Account for different server environments which might normalize header keys
            $authHeader = $headers['Authorization'] ?? $headers['authorization'];
            $arr = explode(" ", $authHeader);
            
            // Ensure Bearer token format
            if (count($arr) != 2 || $arr[0] != 'Bearer') {
                error_log("Invalid Authorization header format");
                return false;
            }
            
            $jwt = $arr[1];
            if ($jwt) {
                $decoded = $this->jwtHandler->decode($jwt);
                if ($decoded) {
                    return true;
                } else {
                    error_log("JWT token validation failed");
                }
            }
        } else {
            error_log("No Authorization header present");
        }
        return false;
    }
    
    // Lấy danh sách sản phẩm
    public function index()
    {
        if (!$this->authenticate()) {
            ApiRouterHelper::sendJsonResponse(['message' => 'Unauthorized'], 401);
            return;
        }
        
        $products = $this->productModel->getProducts();
        ApiRouterHelper::sendJsonResponse($products);
    }
    
    // Lấy thông tin sản phẩm theo ID
    public function show($id)
    {
        if (!$this->authenticate()) {
            ApiRouterHelper::sendJsonResponse(['message' => 'Unauthorized'], 401);
            return;
        }
        
        $product = $this->productModel->getProductById($id);
        if ($product) {
            ApiRouterHelper::sendJsonResponse($product);
        } else {
            ApiRouterHelper::sendJsonResponse(['message' => 'Product not found'], 404);
        }
    }
    
    // Thêm sản phẩm mới
    public function store()
    {
        if (!$this->authenticate()) {
            ApiRouterHelper::sendJsonResponse(['message' => 'Unauthorized'], 401);
            return;
        }
        
        $data = json_decode(file_get_contents("php://input"), true);
        $name = $data['name'] ?? '';
        $description = $data['description'] ?? '';
        $price = $data['price'] ?? '';
        $category_id = $data['category_id'] ?? null;
        $result = $this->productModel->addProduct($name, $description, $price,
        $category_id, null);
        if (is_array($result)) {
            ApiRouterHelper::sendJsonResponse(['errors' => $result], 400);
        } else {
            ApiRouterHelper::sendJsonResponse(['message' => 'Product created successfully'], 201);
        }
    }
    
    // Cập nhật sản phẩm theo ID
    public function update($id)
    {
        if (!$this->authenticate()) {
            ApiRouterHelper::sendJsonResponse(['message' => 'Unauthorized'], 401);
            return;
        }
        
        $data = json_decode(file_get_contents("php://input"), true);
        $name = $data['name'] ?? '';
        $description = $data['description'] ?? '';
        $price = $data['price'] ?? '';
        $category_id = $data['category_id'] ?? null;
        $result = $this->productModel->updateProduct($id, $name, $description, $price,
        $category_id, null);
        if ($result) {
            ApiRouterHelper::sendJsonResponse(['message' => 'Product updated successfully']);
        } else {
            ApiRouterHelper::sendJsonResponse(['message' => 'Product update failed'], 400);
        }
    }
    
    // Xóa sản phẩm theo ID
    public function destroy($id)
    {
        if (!$this->authenticate()) {
            ApiRouterHelper::sendJsonResponse(['message' => 'Unauthorized'], 401);
            return;
        }
        
        $result = $this->productModel->deleteProduct($id);
        if ($result) {
            ApiRouterHelper::sendJsonResponse(['message' => 'Product deleted successfully']);
        } else {
            ApiRouterHelper::sendJsonResponse(['message' => 'Product deletion failed'], 400);
        }
    }
}
?>
