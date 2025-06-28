<?php include 'app/share/header.php'; ?>

<div class="container mt-5">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="fas fa-check-circle mr-2"></i> Đặt hàng thành công!</h4>
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 64px;"></i>
                    </div>
                    
                    <h5>Cảm ơn bạn đã đặt hàng!</h5>
                    <p>Mã đơn hàng của bạn: <strong><?= $order_code ?></strong></p>
                    <p>Chúng tôi sẽ liên hệ với bạn qua số điện thoại để xác nhận đơn hàng.</p>
                    <p>Phương thức thanh toán: <strong>Thanh toán khi nhận hàng</strong></p>
                    
                    <h5 class="mt-4">Thông tin đơn hàng</h5>
                    <p>Họ tên: <?= $name ?></p>
                    <p>Số điện thoại: <?= $phone ?></p>
                    <p>Địa chỉ giao hàng: <?= $address ?></p>
                    
                    <!-- Hiển thị tổng giá tiền -->
                    <div class="alert alert-info mt-3">
                        <h5>Tổng giá trị đơn hàng: <strong><?= number_format($totalAmount, 0, ',', '.') ?> đ</strong></h5>
                    </div>
                    
                    <div class="text-center mt-4">
                        <a href="/BFYL/Product" class="btn btn-primary">Tiếp tục mua sắm</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'app/share/footer.php'; ?>
