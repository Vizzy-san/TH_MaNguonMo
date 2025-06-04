<?php include BASE_PATH . '/app/share/header.php'; ?>

<div class="container py-5">
    <div class="row">
        <div class="col-md-3">
            <!-- Profile sidebar -->
            <div class="card mb-4">
                <div class="card-body text-center">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($account->fullname); ?>&background=random" 
                         alt="<?php echo htmlspecialchars($account->fullname); ?>" 
                         class="rounded-circle img-fluid" style="width: 150px;">
                    <h5 class="my-3"><?php echo htmlspecialchars($account->fullname); ?></h5>
                    <p class="text-muted mb-1"><?php echo htmlspecialchars($account->role_name); ?></p>
                </div>
            </div>
            
            <!-- Navigation tabs -->
            <div class="card mb-4">
                <div class="card-body">
                    <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                        <a class="nav-link active" id="v-pills-info-tab" data-toggle="pill" href="#v-pills-info" role="tab" 
                           aria-controls="v-pills-info" aria-selected="true">
                            <i class="fas fa-user mr-2"></i> Thông tin tài khoản
                        </a>
                        <a class="nav-link" id="v-pills-password-tab" data-toggle="pill" href="#v-pills-password" role="tab" 
                           aria-controls="v-pills-password" aria-selected="false">
                            <i class="fas fa-key mr-2"></i> Đổi mật khẩu
                        </a>
                        <a class="nav-link" id="v-pills-favorites-tab" data-toggle="pill" href="#v-pills-favorites" role="tab" 
                           aria-controls="v-pills-favorites" aria-selected="false">
                            <i class="fas fa-heart mr-2"></i> Sản phẩm yêu thích
                        </a>
                        <a class="nav-link" href="/BFYL/account/orderHistory">
                            <i class="fas fa-history mr-2"></i> Lịch sử đơn hàng
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-9">
            <!-- Tab content -->
            <div class="tab-content" id="v-pills-tabContent">
                <!-- Account Information Tab -->
                <div class="tab-pane fade show active" id="v-pills-info" role="tabpanel" aria-labelledby="v-pills-info-tab">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Thông tin tài khoản</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-3">
                                    <p class="mb-0 font-weight-bold">Họ tên:</p>
                                </div>
                                <div class="col-sm-9">
                                    <p class="text-muted mb-0"><?php echo htmlspecialchars($account->fullname); ?></p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <p class="mb-0 font-weight-bold">Số điện thoại:</p>
                                </div>
                                <div class="col-sm-9">
                                    <p class="text-muted mb-0"><?php echo $account->phonenumber ?? 'Chưa cập nhật'; ?></p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <p class="mb-0 font-weight-bold">Email:</p>
                                </div>
                                <div class="col-sm-9">
                                    <p class="text-muted mb-0"><?php echo $account->email ?? 'Chưa cập nhật'; ?></p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <p class="mb-0 font-weight-bold">Vai trò:</p>
                                </div>
                                <div class="col-sm-9">
                                    <p class="text-muted mb-0"><?php echo htmlspecialchars($account->role_name); ?></p>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <p class="mb-0 font-weight-bold">Ngày tạo:</p>
                                </div>
                                <div class="col-sm-9">
                                    <p class="text-muted mb-0"><?php echo $account->created_at ? date('d/m/Y H:i', strtotime($account->created_at)) : 'N/A'; ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Change Password Tab -->
                <div class="tab-pane fade" id="v-pills-password" role="tabpanel" aria-labelledby="v-pills-password-tab">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Đổi mật khẩu</h5>
                        </div>
                        <div class="card-body">
                            <form action="/BFYL/account/changePassword" method="post">
                                <?php if ($account->google_id): ?>
                                <div class="alert alert-info">
                                    <i class="fab fa-google"></i> Bạn đăng nhập bằng Google, không cần đặt mật khẩu.
                                </div>
                                <?php else: ?>
                                <div class="form-group">
                                    <label for="current_password">Mật khẩu hiện tại</label>
                                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                                </div>
                                <div class="form-group">
                                    <label for="new_password">Mật khẩu mới</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                                </div>
                                <div class="form-group">
                                    <label for="confirm_password">Xác nhận mật khẩu mới</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Cập nhật mật khẩu</button>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Favorites Tab -->
                <div class="tab-pane fade" id="v-pills-favorites" role="tabpanel" aria-labelledby="v-pills-favorites-tab">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Sản phẩm yêu thích</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($favorites)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-heart-broken fa-3x text-muted mb-3"></i>
                                <p class="lead">Bạn chưa có sản phẩm yêu thích nào.</p>
                                <a href="/BFYL/product" class="btn btn-outline-primary mt-3">Khám phá sản phẩm ngay</a>
                            </div>
                            <?php else: ?>
                            <div class="row">
                                <?php foreach ($favorites as $favorite): ?>
                                <div class="col-md-4 mb-4">
                                    <div class="card h-100">
                                        <img src="<?php echo !empty($favorite->image) ? '/BFYL/uploads/' . $favorite->image : '/BFYL/uploads/no-image.jpg'; ?>" 
                                             class="card-img-top" alt="<?php echo htmlspecialchars($favorite->name); ?>" style="height: 180px; object-fit: cover;">
                                        <div class="card-body">
                                            <h5 class="card-title"><?php echo htmlspecialchars($favorite->name); ?></h5>
                                            <p class="card-text text-danger font-weight-bold"><?php echo number_format($favorite->price, 0, ',', '.'); ?> ₫</p>
                                        </div>
                                        <div class="card-footer bg-white d-flex justify-content-between">
                                            <a href="/BFYL/product/show/<?php echo $favorite->id; ?>" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i> Xem
                                            </a>
                                            <a href="/BFYL/account/removeFavorite/<?php echo $favorite->id; ?>" class="btn btn-sm btn-outline-danger">
                                                <i class="fas fa-trash"></i> Xóa
                                            </a>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast for success/error messages -->
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
