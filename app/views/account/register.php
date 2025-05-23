<?php include BASE_PATH . '/app/share/header.php'; ?>

<!-- Container for toasts -->
<div class="toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>

<section class="vh-100 gradient-custom">
    <div class="container py-5 h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                <div class="card bg-dark text-white" style="border-radius: 1rem;">
                    <div class="card-body p-5 text-center">
                        <h2 class="fw-bold mb-4 text-uppercase">Đăng ký tài khoản</h2>
                        
                        <form class="user" action="/BFYL/account/save" method="post">
                            <div class="form-outline form-white mb-4">
                                <input type="text" class="form-control form-control-lg" id="phone" name="phone" placeholder="Số điện thoại" value="<?php echo SessionHelper::get('old_phone') ?? ''; ?>">
                            </div>
                            
                            <div class="form-outline form-white mb-4">
                                <input type="text" class="form-control form-control-lg" id="fullname" name="fullname" placeholder="Họ và tên" value="<?php echo SessionHelper::get('old_fullname') ?? ''; ?>">
                            </div>
                            
                            <div class="form-outline form-white mb-4">
                                <input type="email" class="form-control form-control-lg" id="email" name="email" placeholder="Email (không bắt buộc)" value="<?php echo SessionHelper::get('old_email') ?? ''; ?>">
                            </div>
                            
                            <div class="form-outline form-white mb-4">
                                <input type="password" class="form-control form-control-lg" id="password" name="password" placeholder="Mật khẩu" value="<?php echo SessionHelper::get('old_password') ?? ''; ?>">
                            </div>
                            
                            <div class="form-outline form-white mb-4">
                                <input type="password" class="form-control form-control-lg" id="confirmpassword" name="confirmpassword" placeholder="Xác nhận mật khẩu" value="<?php echo SessionHelper::get('old_confirmpassword') ?? ''; ?>">
                            </div>
                            
                            <?php if (SessionHelper::isAdmin()): ?>
                            <div class="form-outline form-white mb-4">
                                <select class="form-control form-control-lg" name="role">
                                    <option value="user">User</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                            <?php endif; ?>
                            
                            <button class="btn btn-outline-light btn-lg px-5 mb-3" type="submit">
                                Đăng ký
                            </button>
                            
                            <div class="mt-4">
                                <p class="mb-0">Đã có tài khoản? <a href="/BFYL/account/login" class="text-white-50 fw-bold">Đăng nhập</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Clear session values after using them -->
<?php
// Clear old form values
SessionHelper::delete('old_phone');
SessionHelper::delete('old_fullname');
SessionHelper::delete('old_password');
SessionHelper::delete('old_confirmpassword');
SessionHelper::delete('old_email');
?>

<!-- Phone already exists toast -->
<?php if (SessionHelper::get('phone_exists')): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Create toast for phone already exists error
    const toast = document.createElement('div');
    toast.className = 'toast show';
    toast.role = 'alert';
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    toast.setAttribute('data-delay', '10000');
    
    toast.innerHTML = `
        <div class="toast-header bg-warning text-dark">
            <strong class="mr-auto"><i class="fas fa-exclamation-triangle"></i> Thông báo</strong>
            <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="toast-body">
            Số điện thoại <strong><?php echo SessionHelper::get('phone_exists_value'); ?></strong> đã được đăng ký trong hệ thống. Vui lòng sử dụng số điện thoại khác hoặc đăng nhập.
        </div>
    `;
    
    // Add toast to container
    document.querySelector('.toast-container').appendChild(toast);
    
    // Initialize Bootstrap toast
    $('.toast').toast({delay: 10000});
    $('.toast').toast('show');
    
    // Auto remove after 10 seconds
    setTimeout(function() {
        toast.remove();
    }, 10000);
});
</script>
<?php 
    // Clear phone exists flag from session
    SessionHelper::delete('phone_exists');
    SessionHelper::delete('phone_exists_value');
endif; 
?>

<!-- Email already exists toast -->
<?php if (SessionHelper::get('email_exists')): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Create toast for email already exists error
    const toast = document.createElement('div');
    toast.className = 'toast show';
    toast.role = 'alert';
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    toast.setAttribute('data-delay', '10000');
    
    toast.innerHTML = `
        <div class="toast-header bg-warning text-dark">
            <strong class="mr-auto"><i class="fas fa-exclamation-triangle"></i> Thông báo</strong>
            <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="toast-body">
            Email <strong><?php echo SessionHelper::get('email_exists_value'); ?></strong> đã được đăng ký trong hệ thống. Vui lòng sử dụng email khác hoặc đăng nhập.
        </div>
    `;
    
    // Add toast to container
    document.querySelector('.toast-container').appendChild(toast);
    
    // Initialize Bootstrap toast
    $('.toast').toast({delay: 10000});
    $('.toast').toast('show');
    
    // Auto remove after 10 seconds
    setTimeout(function() {
        toast.remove();
    }, 10000);
});
</script>
<?php 
    // Clear email exists flag from session
    SessionHelper::delete('email_exists');
    SessionHelper::delete('email_exists_value');
endif; 
?>

<!-- Register Toasts -->
<?php 
$errors = SessionHelper::get('register_errors');
if (isset($errors) && is_array($errors) && count($errors) > 0): 
?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php foreach ($errors as $key => $err): ?>
        // Create toast for each error
        const toast = document.createElement('div');
        toast.className = 'toast show';
        toast.role = 'alert';
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        toast.setAttribute('data-delay', '10000');
        
        toast.innerHTML = `
            <div class="toast-header bg-danger text-white">
                <strong class="mr-auto"><i class="fas fa-exclamation-circle"></i> Lỗi</strong>
                <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="toast-body">
                <?php echo $err; ?>
            </div>
        `;
        
        // Add toast to container
        document.querySelector('.toast-container').appendChild(toast);
        
        // Initialize Bootstrap toast
        $('.toast').toast({delay: 10000});
        $('.toast').toast('show');
        
        // Auto remove after 10 seconds
        setTimeout(function() {
            toast.remove();
        }, 10000);
    <?php endforeach; ?>
});
</script>
<?php 
    // Clear errors from session
    SessionHelper::delete('register_errors');
endif; 
?>

<?php include BASE_PATH . '/app/share/footer.php'; ?> 