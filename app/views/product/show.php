<?php include BASE_PATH . '/app/share/header.php'; ?>

<!-- Add toast container -->
<div class="toast-container"></div>

<div class="container mt-4">
    <!-- Product breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb bg-white px-0">
            <li class="breadcrumb-item"><a href="/BFYL/Product">Trang chủ</a></li>
            <?php if (isset($product->category_id) && $product->category_id): ?>
            <?php
                $categoryModel = new CategoryModel((new Database())->getConnection());
                $category = $categoryModel->getCategoryById($product->category_id);
                if ($category):
            ?>
            <li class="breadcrumb-item"><a href="/BFYL/Product?category=<?php echo $product->category_id; ?>"><?php echo htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8'); ?></a></li>
            <?php endif; ?>
            <?php endif; ?>
            <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?></li>
        </ol>
    </nav>

    <!-- Product detail section with Tiki-like layout -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="row product-detail">
                <!-- Left column - Product images -->
                <div class="col-md-5 product-gallery">
                    <div class="main-image-container mb-3 text-center">
                        <?php if (!empty($product->image)): ?>
                        <img src="/BFYL/uploads/<?php echo htmlspecialchars($product->image, ENT_QUOTES, 'UTF-8'); ?>" 
                             alt="<?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>" 
                             class="img-fluid product-main-image">
                        <?php else: ?>
                        <div class="no-image-placeholder">
                            <i class="fas fa-image fa-5x text-muted"></i>
                            <p class="text-muted mt-3">Không có hình ảnh sản phẩm</p>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="product-badges d-flex justify-content-center flex-wrap mb-3">
                        <span class="badge bg-light border text-dark m-1 p-2">
                            <i class="fas fa-check-circle text-success"></i> Hàng chính hãng
                        </span>
                        <span class="badge bg-light border text-dark m-1 p-2">
                            <i class="fas fa-shipping-fast text-primary"></i> Freeship đơn từ 45k
                        </span>
                        <span class="badge bg-light border text-dark m-1 p-2">
                            <i class="fas fa-shield-alt text-primary"></i> Bảo hành 12 tháng
                        </span>
                    </div>
                </div>
                
                <!-- Right column - Product info -->
                <div class="col-md-7 product-info">
                    <h1 class="product-title"><?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?></h1>
                    
                    <div class="product-meta d-flex align-items-center mb-3">
                        <div class="ratings mr-2">
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star text-warning"></i>
                            <i class="fas fa-star-half-alt text-warning"></i>
                            <span class="rating-text ml-1">4.5</span>
                        </div>
                        <div class="separator mx-2">|</div>
                        <div class="sold-count">
                            <span>Đã bán: 100+</span>
                        </div>
                    </div>
                    
                    <div class="product-price mb-3">
                        <span class="current-price"><?php echo number_format($product->price, 0, ',', '.'); ?> đ</span>
                        <span class="original-price text-muted"><del><?php echo number_format($product->price * 1.2, 0, ',', '.'); ?> đ</del></span>
                        <span class="discount-badge">-20%</span>
                    </div>
                    
                    <!-- Shipping info panel -->
                    <div class="shipping-info mb-4">
                        <h6>Thông tin vận chuyển</h6>
                        <div class="d-flex align-items-center mb-2">
                            <i class="fas fa-map-marker-alt mr-2"></i>
                            <span>Giao đến: <a href="#" class="font-weight-bold">Quận 1, P. Bến Nghé, Hồ Chí Minh</a> - <a href="#">Đổi địa chỉ</a></span>
                        </div>
                        <div class="d-flex align-items-center">
                            <i class="fas fa-truck mr-2"></i>
                            <span>Phí vận chuyển: <strong class="text-success">Miễn phí</strong></span>
                        </div>
                    </div>
                    
                    <?php if (isset($product->category_id) && $product->category_id): ?>
                    <div class="product-meta-info mb-3">
                        <div class="row">
                            <div class="col-4 col-md-3 font-weight-bold">Danh mục:</div>
                            <div class="col-8 col-md-9">
                                <?php
                                    if ($category) {
                                        echo '<a href="/BFYL/Product?category=' . $category->id . '">' . htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8') . '</a>';
                                    } else {
                                        echo 'Không có';
                                    }
                                ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="product-actions d-flex flex-wrap align-items-center mt-4">
                        <a href="/BFYL/Product/addToCart/<?php echo $product->id; ?>" class="btn btn-primary btn-lg mr-3 mb-3">
                            <i class="fas fa-cart-plus mr-2"></i> Thêm vào giỏ hàng
                        </a>
                        
                        <?php if(SessionHelper::isLoggedIn()): ?>
                            <?php if(isset($isFavorite) && $isFavorite): ?>
                                <a href="/BFYL/account/removeFavorite/<?php echo $product->id; ?>" class="btn btn-outline-danger btn-lg mb-3">
                                    <i class="fas fa-heart"></i> Đã yêu thích
                                </a>
                            <?php else: ?>
                                <a href="/BFYL/account/addFavorite/<?php echo $product->id; ?>" class="btn btn-outline-secondary btn-lg mb-3">
                                    <i class="far fa-heart"></i> Thêm vào yêu thích
                                </a>
                            <?php endif; ?>
                        <?php else: ?>
                            <a href="/BFYL/account/login" class="btn btn-outline-secondary btn-lg mb-3">
                                <i class="far fa-heart"></i> Đăng nhập để yêu thích
                            </a>
                        <?php endif; ?>
                    </div>
                    
                    <?php if(SessionHelper::isAdmin()): ?>
                    <div class="admin-actions mt-3 pt-3 border-top">
                        <h6>Quản trị sản phẩm:</h6>
                        <div class="btn-group">
                            <a href="/BFYL/Product/edit/<?php echo $product->id; ?>" class="btn btn-warning btn-sm">
                                <i class="fas fa-edit"></i> Sửa
                            </a>
                            <a href="/BFYL/Product/delete/<?php echo $product->id; ?>" class="btn btn-danger btn-sm" 
                               onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');">
                               <i class="fas fa-trash"></i> Xóa
                            </a>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Product description section -->
    <div class="card border-0 shadow-sm mt-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">Mô tả sản phẩm</h5>
        </div>
        <div class="card-body product-description">
            <?php echo nl2br(htmlspecialchars($product->description, ENT_QUOTES, 'UTF-8')); ?>
        </div>
    </div>
    
    <div class="mt-4 mb-5">
        <a href="/BFYL/Product" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left mr-2"></i> Quay lại danh sách
        </a>
    </div>
</div>

<!-- Toast for favorite actions -->
<?php if (SessionHelper::has('profile_message')): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Create toast
    const toast = document.createElement('div');
    toast.className = 'toast show';
    toast.role = 'alert';
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    toast.setAttribute('data-delay', '5000');
    
    toast.innerHTML = `
        <div class="toast-header bg-<?php echo SessionHelper::get('profile_message_type') ?? 'success'; ?> text-white">
            <strong class="mr-auto">
                <i class="fas fa-<?php echo SessionHelper::get('profile_message_type') == 'danger' ? 'exclamation-circle' : 'check-circle'; ?>"></i> 
                <?php echo SessionHelper::get('profile_message_type') == 'danger' ? 'Lỗi' : 'Thành công'; ?>
            </strong>
            <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="toast-body">
            <?php echo SessionHelper::get('profile_message'); ?>
        </div>
    `;
    
    // Add toast to container
    document.querySelector('.toast-container').appendChild(toast);
    
    // Show toast
    $('.toast').toast('show');
    
    // Auto remove after 5 seconds
    setTimeout(function() {
        toast.remove();
    }, 5000);
});
</script>
<?php 
    SessionHelper::delete('profile_message');
    SessionHelper::delete('profile_message_type');
endif; 
?>

<?php include BASE_PATH . '/app/share/footer.php'; ?>
