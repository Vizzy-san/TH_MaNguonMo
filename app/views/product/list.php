<?php include BASE_PATH . '/app/share/header.php'; ?>

<?php 
// Include sidebar component
require_once 'app/share/Sidebar.php';
$sidebar = new Sidebar();

// Include banner image component
require_once 'app/share/bannerImage.php';
$banner = new BannerImage();
?>



<!-- Main container is already closed in header -->
<div class="sidebar-wrapper">
    <?php $sidebar->render(); ?>
</div>

<!-- Main content -->
<div class="main-content">
    <!-- Banner Image -->
    <?php $banner->render(); ?>
    
    <h1>Danh sách sản phẩm</h1>
    <div class="row product-grid"> <!-- Added product-grid class for styling -->
    <?php foreach ($products as $product): ?>
        <div class="col-md-4 col-lg-3 mb-3"> <!-- Changed for more responsive sizing -->
            <div class="card h-100 product-card"> <!-- Added product-card class for consistent styling -->
                <?php if (!empty($product->image)): ?>
                    <img src="/BFYL/uploads/<?php echo htmlspecialchars($product->image, ENT_QUOTES, 'UTF-8'); ?>" 
                         class="card-img-top" alt="<?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>"
                         style="height: 160px; object-fit: contain; padding: 8px;"> <!-- Reduced height and padding -->
                <?php else: ?>
                    <div class="text-center p-3 bg-light"> <!-- Reduced padding -->
                        <span class="text-muted">Không có hình ảnh</span>
                    </div>
                <?php endif; ?>
                <div class="card-body py-2"> <!-- Reduced padding -->
                    <h5 class="card-title" style="font-size: 0.95rem; margin-bottom: 0.5rem;"> <!-- Reduced font size -->
                        <a href="/BFYL/Product/show/<?php echo $product->id; ?>">
                            <?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>
                        </a>
                    </h5>
                    <p class="card-text" style="font-size: 0.85rem; margin-bottom: 0.5rem;"><?php echo mb_substr(htmlspecialchars($product->description, ENT_QUOTES, 'UTF-8'), 0, 70) . (mb_strlen($product->description) > 70 ? '...' : ''); ?></p> <!-- Reduced character limit and font size -->
                    <p class="card-text" style="font-size: 0.9rem;"><strong>Giá:</strong> <?php echo number_format($product->price, 0, ',', '.'); ?> VND</p>
                </div>
                <div class="card-footer d-flex justify-content-center py-2"> <!-- Changed to center alignment -->
                    <a href="/BFYL/Product/addToCart/<?php echo $product->id; ?>" class="btn btn-success btn-sm" style="padding: 0.2rem 0.4rem; font-size: 0.75rem;">
                        <i class="fas fa-cart-plus"></i> Thêm vào giỏ
                    </a> <!-- Smaller button -->
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    </div>
</div>
<!-- Close main-container -->
</div>

<!-- Re-open container for footer -->
<div class="container">
<?php include BASE_PATH . '/app/share/footer.php'; ?>