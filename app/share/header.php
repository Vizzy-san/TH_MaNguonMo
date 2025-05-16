<?php
// Display cart success message if it exists
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once 'app/helpers/SessionHelper.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Quản lý sản phẩm</title>
<link
href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
<style>
    .toast-container {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1050;
    }
    
    .toast {
        min-width: 300px;
    }
    
    .product-image {
        max-width: 100px;
        height: auto;
    }
</style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
<a class="navbar-brand" href="#">Quản lý sản phẩm</a>

<button class="navbar-toggler" type="button" data-toggle="collapse" data-
target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle

navigation">

<span class="navbar-toggler-icon"></span>
</button>
<div class="collapse navbar-collapse" id="navbarNav">
<ul class="navbar-nav mr-auto">
<li class="nav-item">
<a class="nav-link" href="/project1/Product/">Danh sách sản phẩm</a>
</li>
<li class="nav-item">
<a class="nav-link" href="/project1/Product/add">Thêm sản phẩm</a>
</li>
<li class="nav-item">
<a class="nav-link" href="/project1/Category/add">Thêm danh mục</a>
</li>
</ul>
<div class="navbar-nav">
<a class="nav-link" href="/project1/Product/cart">
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

<!-- User Authentication Links -->
<li class="nav-item">
    <?php
    if(SessionHelper::isLoggedIn()){
        echo "<a class='nav-link'>".$_SESSION['username']."</a>";
    }
    else{
        echo "<a class='nav-link' href='/project1/account/login'>Login</a>";
    }
    ?>
</li>
<?php if(SessionHelper::isAdmin()): ?>
<li class="nav-item">
    <a class="nav-link" href="/project1/admin">
        <i class="fas fa-cog"></i> Admin
    </a>
</li>
<?php endif; ?>
<li class="nav-item">
    <?php
    if(SessionHelper::isLoggedIn()){
        echo "<a class='nav-link' href='/project1/account/logout'>Logout</a>";
    }
    ?>
</li>
</div>
</div>
</nav>

<!-- Toast container -->
<div class="toast-container" aria-live="polite" aria-atomic="true">
    <?php if (isset($_SESSION['cart_success'])): ?>
    <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-delay="3000">
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
    <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-delay="3000">
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

<div class="container mt-4">