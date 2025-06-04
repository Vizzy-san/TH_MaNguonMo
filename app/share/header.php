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
</head>
<body>
<!-- Top bar with scrolling text -->
<div class="top-bar">
    <div class="scrolling-text">
        <strong>Freeship đơn từ 45k, giảm nhiều hơn cùng FREESHIP XTRA</strong> - Mua sắm ngay hôm nay để nhận ưu đãi đặc biệt!
    </div>
</div>

<nav class="navbar navbar-expand-lg navbar-light bg-light">
<a class="navbar-brand" href="/BFYL/">BFYL</a>

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

<?php if(SessionHelper::isLoggedIn()): ?>
<a class="nav-link" href="/BFYL/account/orderHistory">
    <i class="fas fa-history"></i> Lịch sử mua hàng
</a>
<?php endif; ?>

<!-- User Authentication Links -->
<li class="nav-item">
    <?php
    if(SessionHelper::isLoggedIn()){
        // Hiển thị fullname và link tới trang profile
        echo "<a class='nav-link' href='/BFYL/account/profile'>".$_SESSION['fullname']."</a>";
    }
    else{
        echo "<a class='nav-link' href='/BFYL/account/login'>Login</a>";
    }
    ?>
</li>
<?php if(SessionHelper::isAdmin()): ?>
<li class="nav-item">
    <a class="nav-link" href="/BFYL/admin">
        <i class="fas fa-cog"></i> Admin
    </a>
</li>
<?php endif; ?>
<li class="nav-item">
    <?php
    if(SessionHelper::isLoggedIn()){
        echo "<a class='nav-link' href='/BFYL/account/logout'>Logout</a>";
    }
    ?>
</li>
</div>
</div>
</nav>

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

<!-- Close container before main-container -->
</div>

<!-- Main container outside the default container for full-width sidebar -->
<div class="main-container mt-4">