<?php
// Require database
require_once('app/config/database.php');
require_once('app/models/CategoryModel.php');

class CategoryController
{
    private $categoryModel;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->categoryModel = new CategoryModel($this->db);
    }

    public function index()
    {
        $categories = $this->categoryModel->getCategories();
        include 'app/views/category/list.php';
    }

    public function list()
    {
        // Just call the index method which already handles list functionality
        $this->index();
    }

    public function add()
    {
        include 'app/views/category/add.php';
    }

    public function save()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            
            // Simple validation
            $errors = [];
            if (empty($name)) {
                $errors[] = 'Tên danh mục là bắt buộc.';
            }
            
            if (!empty($errors)) {
                // If there are errors, redisplay the form
                include 'app/views/category/add.php';
            } else {
                // No errors, save the category
                $result = $this->categoryModel->addCategory($name, $description);
                
                if ($result) {
                    // Redirect to category list
                    header('Location: /project1/Category');
                    exit;
                } else {
                    $errors[] = 'Đã xảy ra lỗi khi lưu danh mục.';
                    include 'app/views/category/add.php';
                }
            }
        }
    }

    public function edit($id)
    {
        $category = $this->categoryModel->getCategoryById($id);
        if ($category) {
            include 'app/views/category/edit.php';
        } else {
            echo "Không tìm thấy danh mục.";
        }
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $name = $_POST['name'];
            $description = $_POST['description'];
            
            $edit = $this->categoryModel->updateCategory($id, $name, $description);
            if ($edit) {
                header('Location: /project1/Category');
            } else {
                echo "Đã xảy ra lỗi khi lưu danh mục.";
            }
        }
    }

    public function delete($id)
    {
        if ($this->categoryModel->deleteCategory($id)) {
            header('Location: /project1/Category');
        } else {
            echo "Đã xảy ra lỗi khi xóa danh mục.";
        }
    }
}
?> 