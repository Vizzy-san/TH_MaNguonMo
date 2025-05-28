<?php include BASE_PATH . '/app/share/header.php'; ?>
<section class="vh-100 gradient-custom">
    <div class="container py-5 h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                <div class="card bg-dark text-white" style="border-radius: 1rem;">
                    <div class="card-body p-5 text-center">
                        <form action="/BFYL/account/processForgotPassword" method="post">
                            <div class="mb-md-5 mt-md-4 pb-5">
                                <h2 class="fw-bold mb-2 text-uppercase">QUÊN MẬT KHẨU</h2>
                                <p class="text-white-50 mb-5">Vui lòng nhập số điện thoại để khôi phục mật khẩu!</p>
                                
                                <div class="form-outline form-white mb-4">
                                    <input type="text" name="phone" class="form-control form-control-lg" placeholder="0975234550" />
                                    <label class="form-label text-center d-block mt-2">Số điện thoại</label>
                                </div>
                                
                                <button class="btn btn-outline-light btn-lg px-5 mt-4" type="submit">Tiếp tục</button>
                                
                                <div class="d-flex justify-content-center text-center mt-4 pt-1">
                                    <a href="/BFYL/account/login" class="text-white-50">Quay lại đăng nhập</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Forgot Password Error Toast -->
<?php if (SessionHelper::get('forgot_error')): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Create toast for error
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
            <?php echo SessionHelper::get('forgot_error'); ?>
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
    SessionHelper::delete('forgot_error');
endif; 
?>

<?php include BASE_PATH . '/app/share/footer.php'; ?> 