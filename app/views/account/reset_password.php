<?php include BASE_PATH . '/app/share/header.php'; ?>
<section class="vh-100 gradient-custom">
    <div class="container py-5 h-100">
        <div class="row d-flex justify-content-center align-items-center h-100">
            <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                <div class="card bg-dark text-white" style="border-radius: 1rem;">
                    <div class="card-body p-5 text-center">
                        <form action="/BFYL/account/processResetPassword" method="post">
                            <div class="mb-md-5 mt-md-4 pb-5">
                                <h2 class="fw-bold mb-2 text-uppercase">Đặt lại mật khẩu</h2>
                                <p class="text-white-50 mb-5">Nhập mã PIN 6 số đã được gửi đến số điện thoại và mật khẩu mới</p>
                                
                                <?php if(SessionHelper::get('reset_code_for_testing')): ?>
                                <div class="alert alert-info">
                                    <p>Mã PIN của bạn là (chỉ dùng trong demo): <strong><?php echo SessionHelper::get('reset_code_for_testing'); ?></strong></p>
                                    <small>Trong môi trường thực tế, mã PIN sẽ được gửi qua tin nhắn SMS đến số điện thoại của bạn.</small>
                                </div>
                                <?php endif; ?>
                                
                                <div class="form-outline form-white mb-4">
                                    <input type="text" name="verification_code" class="form-control form-control-lg text-center" maxlength="6" placeholder="Nhập mã PIN 6 số" />
                                    <label class="form-label text-center d-block mt-2">Mã xác thực</label>
                                </div>
                                
                                <div class="form-outline form-white mb-4">
                                    <input type="password" name="new_password" class="form-control form-control-lg" placeholder="Nhập mật khẩu mới" />
                                    <label class="form-label text-center d-block mt-2">Mật khẩu mới</label>
                                </div>
                                
                                <div class="form-outline form-white mb-4">
                                    <input type="password" name="confirm_password" class="form-control form-control-lg" placeholder="Xác nhận mật khẩu mới" />
                                    <label class="form-label text-center d-block mt-2">Xác nhận mật khẩu mới</label>
                                </div>
                                
                                <button class="btn btn-outline-light btn-lg px-5 mt-4" type="submit">Đặt lại mật khẩu</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Reset Password Errors Toast -->
<?php if (SessionHelper::has('reset_errors')): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Create toast for errors
    const toast = document.createElement('div');
    toast.className = 'toast show';
    toast.role = 'alert';
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');
    toast.setAttribute('data-delay', '10000');
    
    let errorMessages = '';
    <?php foreach(SessionHelper::get('reset_errors') as $error): ?>
    errorMessages += '<?php echo $error; ?><br>';
    <?php endforeach; ?>
    
    toast.innerHTML = `
        <div class="toast-header bg-danger text-white">
            <strong class="mr-auto"><i class="fas fa-exclamation-circle"></i> Lỗi</strong>
            <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="toast-body">
            ${errorMessages}
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
    // Clear errors from session
    SessionHelper::delete('reset_errors');
endif; 
?>

<?php include BASE_PATH . '/app/share/footer.php'; ?> 