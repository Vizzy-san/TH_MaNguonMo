<?php
// Display cart success message if it exists
require_once BASE_PATH . '/app/helpers/SessionHelper.php';
SessionHelper::init();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Buy For Your Life - Trang Mua Sắm Trực Tuyến Hàng Đầu Việt Nam</title>
<link
href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
<link rel="stylesheet" href="/BFYL/public/css/share/header.css">
<script>
function logout() {
    // Remove JWT token if exists
    localStorage.removeItem('jwtToken');
    // Redirect to logout endpoint for session-based logout
    location.href = '/BFYL/account/logout';
}

document.addEventListener("DOMContentLoaded", function() {
    // Check for JWT token
    const token = localStorage.getItem('jwtToken');
    // JWT token check is used alongside session based authentication
    // Server-side session auth takes precedence
});
</script>
</head>
<body>
<!-- Top bar with scrolling text -->
<div class="top-bar">
    <div class="scrolling-text">
        <strong>Freeship đơn từ 45k, giảm nhiều hơn cùng FREESHIP XTRA</strong> - Mua sắm ngay hôm nay để nhận ưu đãi đặc biệt!
    </div>
</div>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
<a class="navbar-brand" href="/BFYL/Product/">BFYL</a>

<button class="navbar-toggler" type="button" data-toggle="collapse" data-
target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle

navigation">

<span class="navbar-toggler-icon"></span>
</button>

<!-- Add Search Bar -->
<div class="search-container d-none d-md-block">
    <form class="search-form" action="/BFYL/Product" method="get">
        <input type="text" class="form-control search-input" name="search" placeholder="Freeship đơn từ 45k" aria-label="Search">
        <button class="search-btn" type="submit">
            <i class="fas fa-search"></i>
        </button>
    </form>
</div>

<div class="collapse navbar-collapse" id="navbarNav">
<ul class="navbar-nav mr-auto">
<li class="nav-item">

</li>
</ul>

<!-- Search bar for mobile (appears when navbar is collapsed) -->
<div class="search-container d-md-none w-100">
    <form class="search-form" action="/BFYL/Product" method="get">
        <input type="text" class="form-control search-input" name="search" placeholder="Freeship đơn từ 45k" aria-label="Search">
        <button class="search-btn" type="submit">
            <i class="fas fa-search"></i>
        </button>
    </form>
</div>

<div class="navbar-nav">
<a class="nav-link" href="/BFYL/Product/cart">
    <i class="fas fa-shopping-cart"></i> Giỏ hàng
    <?php 
        $cartCount = 0;
        if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $item) {
                $cartCount += $item['quantity'];
            }
        }
        if ($cartCount > 0): 
    ?>
    <span class="badge badge-pill badge-primary"><?= $cartCount ?></span>
    <?php endif; ?>
</a>

<!-- Add PayOS Test Payment Link -->
<a class="nav-link" href="/BFYL/Product/testPayment">
    <i class="fas fa-credit-card"></i> Test PayOS
</a>

<!-- User Authentication Links -->
<?php if(SessionHelper::isLoggedIn() || isset($_COOKIE['jwtToken'])): ?>
    <!-- User account dropdown -->
    <div class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fas fa-user-circle mr-1"></i> <?= $_SESSION['fullname'] ?? 'Tài khoản' ?>
        </a>
        <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in" aria-labelledby="userDropdown">
            <a class="dropdown-item" href="/BFYL/account/profile">
                <i class="fas fa-user fa-sm fa-fw mr-2 text-gray-400"></i>
                Thông tin tài khoản
            </a>
            <a class="dropdown-item" href="/BFYL/account/orderHistory">
                <i class="fas fa-shopping-bag fa-sm fa-fw mr-2 text-gray-400"></i>
                Đơn hàng của tôi
            </a>
            <div class="dropdown-divider"></div>
            <a class="dropdown-item" href="#" data-toggle="modal" data-target="#supportModal">
                <i class="fas fa-question-circle fa-sm fa-fw mr-2 text-gray-400"></i>
                Trung tâm hỗ trợ
            </a>
            <a class="dropdown-item" href="#" onclick="logout()">
                <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                Đăng xuất
            </a>
        </div>
    </div>
    
    <?php if(SessionHelper::isAdmin()): ?>
    <li class="nav-item">
        <a class="nav-link" href="/BFYL/admin">
            <i class="fas fa-cog"></i> Admin
        </a>
    </li>
    <?php endif; ?>
<?php else: ?>
    <li class="nav-item" id="nav-login">
        <a class="nav-link" href="/BFYL/account/login">
            <i class="fas fa-sign-in-alt mr-1"></i> Đăng nhập
        </a>
    </li>
<?php endif; ?>
</div>
</div>
</nav>

<!-- Support Modal -->
<div class="modal fade" id="supportModal" tabindex="-1" role="dialog" aria-labelledby="supportModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="supportModalLabel">Trung tâm hỗ trợ</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Bạn cần hỗ trợ? Vui lòng liên hệ với chúng tôi qua:</p>
        <ul class="list-unstyled">
          <li class="mb-2"><i class="fas fa-phone mr-2 text-primary"></i>Hotline: <strong>1900 1900</strong></li>
          <li class="mb-2"><i class="fas fa-envelope mr-2 text-primary"></i>Email: <strong>support@bfyl.com</strong></li>
          <li class="mb-2"><i class="fas fa-comment-alt mr-2 text-primary"></i>Chat trực tuyến: <strong>8:00 - 22:00</strong></li>
        </ul>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
        <button type="button" class="btn btn-primary">Gửi yêu cầu hỗ trợ</button>
      </div>
    </div>
  </div>
</div>

<!-- Toast container -->
<div class="toast-container" aria-live="polite" aria-atomic="true">
    <?php if (isset($_SESSION['cart_success'])): ?>
    <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-delay="10000">
        <div class="toast-header bg-success text-white">
            <strong class="mr-auto"><i class="fas fa-check-circle"></i> Thành công</strong>
            <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="toast-body">
            <?= $_SESSION['cart_success'] ?>
        </div>
    </div>
    <?php 
        // Set a flag to show the toast via JavaScript in footer
        $_SESSION['cart_success_display'] = true;
        // Remove the message so it doesn't appear again on refresh
        unset($_SESSION['cart_success']);
    ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['cart_error'])): ?>
    <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-delay="10000">
        <div class="toast-header bg-danger text-white">
            <strong class="mr-auto"><i class="fas fa-exclamation-circle"></i> Thông báo</strong>
            <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="toast-body">
            <?= $_SESSION['cart_error'] ?>
        </div>
    </div>
    <?php 
        // Set a flag to show the toast via JavaScript in footer
        $_SESSION['cart_success_display'] = true;
        // Remove the message so it doesn't appear again on refresh
        unset($_SESSION['cart_error']);
    ?>
    <?php endif; ?>
</div>

<!-- Commitment badges section -->
<div class="container-fluid bg-light py-2 commitment-badges border-top border-bottom">
    <div class="container">
        <div class="row">
            <div class="col-6 col-md-2 text-center mb-2 mb-md-0">
                <div class="badge-item">
                    <i class="fas fa-check-circle text-primary"></i>
                    <span class="ml-1">Cam kết</span>
                </div>
            </div>
            <div class="col-6 col-md-2 text-center mb-2 mb-md-0">
                <div class="badge-item">
                    <i class="fas fa-certificate text-primary"></i>
                    <span class="ml-1">100% hàng thật</span>
                </div>
            </div>
            <div class="col-6 col-md-2 text-center mb-2 mb-md-0">
                <div class="badge-item">
                    <i class="fas fa-shipping-fast text-primary"></i>
                    <span class="ml-1">Freeship mọi đơn</span>
                </div>
            </div>
            <div class="col-6 col-md-2 text-center mb-2 mb-md-0">
                <div class="badge-item">
                    <i class="fas fa-undo-alt text-primary"></i>
                    <span class="ml-1">Hoàn 200% nếu hàng giả</span>
                </div>
            </div>
            <div class="col-6 col-md-2 text-center mb-2 mb-md-0">
                <div class="badge-item">
                    <i class="fas fa-calendar-alt text-primary"></i>
                    <span class="ml-1">30 ngày đổi trả</span>
                </div>
            </div>
            <div class="col-6 col-md-2 text-center">
                <div class="badge-item">
                    <i class="fas fa-tags text-primary"></i>
                    <span class="ml-1">Giá siêu rẻ</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Close container before main-container -->
</div>

<!-- Main container outside the default container for full-width sidebar -->
<div class="main-container mt-4">