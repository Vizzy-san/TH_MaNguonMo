<?php include 'app/share/header.php'; ?>

<div class="container mt-4">
    <div class="jumbotron text-center">
        <h1 class="display-4">Thanh toán thành công!</h1>
        <p class="lead">Cảm ơn bạn đã mua sắm tại cửa hàng của chúng tôi.</p>
        
        <?php if (isset($order_code)): ?>
            <p>Mã đơn hàng: <strong><?= $order_code ?></strong></p>
        <?php endif; ?>
        
        <?php if (isset($payment_success)): ?>
            <div class="alert alert-success"><?= $payment_success ?></div>
        <?php endif; ?>
        
        <hr class="my-4">
        
        <div class="mt-4">
            <a href="/BFYL/Product" class="btn btn-primary btn-lg mr-2">Tiếp tục mua sắm</a>
        </div>
    </div>
</div>

<?php include 'app/share/footer.php'; ?> 