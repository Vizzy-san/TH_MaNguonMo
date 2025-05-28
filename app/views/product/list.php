<?php include BASE_PATH . '/app/share/header.php'; ?>
<h1>Danh sách sản phẩm</h1>
<div class="row">
<?php foreach ($products as $product): ?>
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <?php if (!empty($product->image)): ?>
                <img src="/BFYL/uploads/<?php echo htmlspecialchars($product->image, ENT_QUOTES, 'UTF-8'); ?>" 
                     class="card-img-top" alt="<?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>"
                     style="height: 200px; object-fit: contain; padding: 10px;">
            <?php else: ?>
                <div class="text-center p-4 bg-light">
                    <span class="text-muted">Không có hình ảnh</span>
                </div>
            <?php endif; ?>
            <div class="card-body">
                <h5 class="card-title">
                    <a href="/BFYL/Product/show/<?php echo $product->id; ?>">
                        <?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>
                    </a>
                </h5>
                <p class="card-text"><?php echo mb_substr(htmlspecialchars($product->description, ENT_QUOTES, 'UTF-8'), 0, 100) . (mb_strlen($product->description) > 100 ? '...' : ''); ?></p>
                <p class="card-text"><strong>Giá:</strong> <?php echo number_format($product->price, 0, ',', '.'); ?> VND</p>
            </div>
            <div class="card-footer d-flex justify-content-between">
                <div>
                    <a href="/BFYL/Product/edit/<?php echo $product->id; ?>" class="btn btn-warning btn-sm">Sửa</a>
                    <a href="/BFYL/Product/delete/<?php echo $product->id; ?>" class="btn btn-danger btn-sm" 
                       onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');">Xóa</a>
                </div>
                <a href="/BFYL/Product/addToCart/<?php echo $product->id; ?>" class="btn btn-success btn-sm">
                    <i class="fas fa-cart-plus"></i> Thêm vào giỏ
                </a>
            </div>
        </div>
    </div>
<?php endforeach; ?>
</div>
<?php include BASE_PATH . '/app/share/footer.php'; ?>