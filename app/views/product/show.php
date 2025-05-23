<?php include BASE_PATH . '/app/share/header.php'; ?>
<div class="card">
    <div class="card-header">
        <h1><?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?></h1>
    </div>
    <div class="card-body">
        <?php if (!empty($product->image)): ?>
        <div class="text-center mb-4">
            <img src="/BFYL/uploads/<?php echo htmlspecialchars($product->image, ENT_QUOTES, 'UTF-8'); ?>" 
                 alt="<?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>" 
                 class="img-fluid" style="max-height: 300px;">
        </div>
        <?php endif; ?>
        <h5 class="card-title">Chi tiết sản phẩm</h5>
        <p class="card-text">
            <strong>Mô tả:</strong> <?php echo htmlspecialchars($product->description, ENT_QUOTES, 'UTF-8'); ?>
        </p>
        <p class="card-text">
            <strong>Giá:</strong> <?php echo number_format($product->price, 0, ',', '.'); ?> VND
        </p>
        <?php if (isset($product->category_id) && $product->category_id): ?>
        <p class="card-text">
            <strong>Danh mục:</strong> 
            <?php
                $categoryModel = new CategoryModel((new Database())->getConnection());
                $category = $categoryModel->getCategoryById($product->category_id);
                echo $category ? htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8') : 'Không có';
            ?>
        </p>
        <?php endif; ?>
        
        <div class="mt-3">
            <a href="/BFYL/Product/addToCart/<?php echo $product->id; ?>" class="btn btn-success">Thêm vào giỏ hàng</a>
            <a href="/BFYL/Product/edit/<?php echo $product->id; ?>" class="btn btn-warning">Sửa</a>
            <a href="/BFYL/Product/delete/<?php echo $product->id; ?>" class="btn btn-danger" 
               onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');">Xóa</a>
            <a href="/BFYL/Product" class="btn btn-secondary">Quay lại danh sách</a>
        </div>
    </div>
</div>
<?php include BASE_PATH . '/app/share/footer.php'; ?>
