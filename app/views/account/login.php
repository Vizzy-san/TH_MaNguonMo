<?php include BASE_PATH . '/app/share/header.php'; ?>
<section class="vh-100 gradient-custom">
    <div class="container py-5 h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                <div class="card bg-dark text-white" style="border-radius: 1rem;">
                    <div class="card-body p-5 text-center">
                        <form action="/BFYL/account/checklogin" method="post">
                            <div class="mb-md-5 mt-md-4 pb-5">
                                <h2 class="fw-bold mb-2 text-uppercase">Đăng nhập</h2>
                                <p class="text-white-50 mb-5">Vui lòng nhập số điện thoại và mật khẩu!</p>
                                <div class="form-outline form-white mb-4">
                                    <input type="text" name="phone" class="form-control form-control-lg" />
                                    <label class="form-label" for="typePhoneX">Số điện thoại</label>
                                </div>
                                <div class="form-outline form-white mb-4">
                                    <input type="password" name="password" class="form-control form-control-lg" />
                                    <label class="form-label" for="typePasswordX">Mật khẩu</label>
                                </div>
                                <p class="small mb-5 pb-lg-2"><a class="text-white-50" href="/BFYL/account/forgotPassword">Quên mật khẩu?</a></p>
                                <button class="btn btn-outline-light btn-lg px-5" type="submit">Đăng nhập</button>
                                
                                <div class="d-flex flex-column justify-content-center text-center mt-4 pt-1">
                                    <p class="text-white-50 mb-3">- HOẶC -</p>
                                    <a href="/BFYL/account/googleLogin" class="btn btn-danger btn-lg px-5">
                                        <i class="fab fa-google fa-lg me-2"></i> Đăng nhập với Google
                                    </a>
                                </div>
                            </div>
                            <div>
                                <p class="mb-0">Chưa có tài khoản? <a href="/BFYL/account/register" class="text-white-50 fw-bold">Đăng ký</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Login Error Toast -->
<?php if (SessionHelper::get('login_error')): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Create toast for login error
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
            <?php echo SessionHelper::get('login_error'); ?>
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
    // Clear error from session
    SessionHelper::delete('login_error');
endif; 
?>

<!-- Login Success Toast -->
<?php if (SessionHelper::get('login_success')): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Create toast for success message
    const toast = document.createElement('div');
    toast.className = 'toast show';
    toast.role = 'alert';
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    toast.setAttribute('data-delay', '10000');
    
    toast.innerHTML = `
        <div class="toast-header bg-success text-white">
            <strong class="mr-auto"><i class="fas fa-check-circle"></i> Thành công</strong>
            <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="toast-body">
            <?php echo SessionHelper::get('login_success'); ?>
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
    // Clear success from session
    SessionHelper::delete('login_success');
endif; 
?>

<?php include BASE_PATH . '/app/share/footer.php'; ?> 