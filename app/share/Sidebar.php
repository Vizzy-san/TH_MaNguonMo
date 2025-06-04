<?php
// Require database and category model if not already included
if (!class_exists('CategoryModel')) {
    require_once('app/config/database.php');
    require_once('app/models/CategoryModel.php');
}

class Sidebar {
    private $categories;
    
    public function __construct() {
        // Connect to database and get categories
        $db = (new Database())->getConnection();
        $categoryModel = new CategoryModel($db);
        $this->categories = $categoryModel->getCategories();
    }
    
    /**
     * Render the sidebar with categories
     */
    public function render() {
        // Icons mapping for categories (you can add more or customize)
        $icons = [
            'Thời trang' => 'fas fa-tshirt',
            'Làm đẹp' => 'fas fa-magic',
            'Điện gia dụng' => 'fas fa-plug',
            'Thiết bị điện tử' => 'fas fa-laptop',
            'Đồ điện gia dụng' => 'fas fa-blender',
            'Mẹ và Bé' => 'fas fa-baby',
            'Sức khỏe' => 'fas fa-heartbeat',
            'Văn phòng phẩm và Sách' => 'fas fa-book',
            'Thực phẩm và Đồ uống' => 'fas fa-utensils',
            'Thể thao và Du lịch' => 'fas fa-volleyball-ball',
            'Ô tô và Xe máy' => 'fas fa-car',
            // Default icon for categories without a specific mapping
            'default' => 'fas fa-folder'
        ];
        
        // Start sidebar HTML
        echo '<div class="sidebar-categories">';
        echo '<div class="sidebar-header"><h5>Danh mục</h5></div>';
        echo '<ul class="category-list">';
        
        // Loop through categories and display them
        foreach ($this->categories as $category) {
            // Determine which icon to use
            $iconClass = $icons['default']; // Default icon
            foreach ($icons as $key => $icon) {
                if (stripos($category->name, $key) !== false) {
                    $iconClass = $icon;
                    break;
                }
            }
            
            echo '<li>';
            echo '<a href="/BFYL/Product?category=' . $category->id . '">';
            echo '<i class="' . $iconClass . '"></i> ';
            echo htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8');
            echo '</a>';
            echo '</li>';
        }
        
        echo '</ul>';
        echo '</div>';
    }
}
?>
