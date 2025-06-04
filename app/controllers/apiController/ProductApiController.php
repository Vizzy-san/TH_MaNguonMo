<?php
require_once('app/config/database.php');
require_once('app/models/ProductModel.php');
require_once('app/models/CategoryModel.php');

class ProductApiController
{
    private $productModel;
    private $db;
    
    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->productModel = new ProductModel($this->db);
    }
    
    // Lấy danh sách sản phẩm
    public function index()
    {
        $products = $this->productModel->getProducts();
        ApiRouterHelper::sendJsonResponse($products);
    }
    
    // Lấy thông tin sản phẩm theo ID
    public function show($id)
    {
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
        $result = $this->productModel->deleteProduct($id);
        if ($result) {
            ApiRouterHelper::sendJsonResponse(['message' => 'Product deleted successfully']);
        } else {
            ApiRouterHelper::sendJsonResponse(['message' => 'Product deletion failed'], 400);
        }
    }
}
?>
