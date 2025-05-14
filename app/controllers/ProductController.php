<?php
// Require SessionHelper and other necessary files
require_once('app/config/database.php');
require_once('app/models/ProductModel.php');
require_once('app/models/CategoryModel.php');
class ProductController
{
private $productModel;
private $db;
private $upload_dir = "uploads/";

public function __construct()
{
$this->db = (new Database())->getConnection();
$this->productModel = new ProductModel($this->db);

// Create uploads directory if it doesn't exist
if (!file_exists($this->upload_dir)) {
    mkdir($this->upload_dir, 0777, true);
}
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
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg'];
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
        header('Location: /project1/Product');
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
        header('Location: /project1/Product');
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
        header('Location: /project1/Product');
    } else {
        echo "Đã xảy ra lỗi khi xóa sản phẩm.";
    }
}

}
?>