<?php include 'app/share/header.php'; ?>

<div class="container mt-4">
    <div class="jumbotron text-center">
        <h1 class="display-4">Đặt hàng thành công!</h1>
        <p class="lead">Cảm ơn bạn đã đặt hàng. Chúng tôi sẽ liên hệ với bạn trong thời gian sớm nhất.</p>
        <?php if (isset($order_id) && $order_id): ?>
        <p>Mã đơn hàng của bạn là: <strong><?= $order_id ?></strong></p>
        <?php endif; ?>
        <hr class="my-4">
        <p>Bạn có thể tiếp tục mua sắm hoặc kiểm tra các sản phẩm khác.</p>
        <a class="btn btn-primary btn-lg" href="/project1/Product" role="button">Tiếp tục mua sắm</a>
    </div>
</div>

<?php include 'app/share/footer.php'; ?> 