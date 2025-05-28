<?php include 'app/share/header.php'; ?>

<div class="container mt-4">
    <h1>Thanh toán đơn hàng</h1>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Quét mã QR để thanh toán</h5>
                </div>
                <div class="card-body text-center">
                    <?php if (isset($payos_data) && isset($payos_data['qrCode'])): ?>
                        <img src="<?= $payos_data['qrCode'] ?>" alt="QR Code" class="img-fluid" style="max-width: 300px;">
                        
                        <div class="mt-3">
                            <p><strong>Mã đơn hàng:</strong> <?= $order_code ?></p>
                            <p><strong>Số tiền:</strong> <?= number_format($payos_data['amount'], 0, ',', '.') ?> đ</p>
                            <p><strong>Trạng thái:</strong> <span id="payment-status">Đang chờ thanh toán</span></p>
                        </div>
                        
                        <div class="mt-3">
                            <p>Vui lòng sử dụng ứng dụng ngân hàng hoặc ví điện tử để quét mã QR và hoàn tất thanh toán.</p>
                            <p>Trang sẽ tự động chuyển hướng sau khi thanh toán thành công.</p>
                        </div>
                        
                        <!-- Link trực tiếp đến trang thanh toán -->
                        <?php if (isset($payos_data['checkoutUrl'])): ?>
                            <div class="mt-3">
                                <a href="<?= $payos_data['checkoutUrl'] ?>" target="_blank" class="btn btn-primary">
                                    Mở trang thanh toán
                                </a>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <p>Không thể tạo mã QR thanh toán. Vui lòng thử lại hoặc chọn phương thức thanh toán khác.</p>
                            <div class="mt-3">
                                <a href="/BFYL/Product/checkout" class="btn btn-outline-primary">Quay lại trang thanh toán</a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="mt-3">
                <a href="/BFYL/Product" class="btn btn-secondary">Quay lại trang sản phẩm</a>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Chi tiết đơn hàng</h5>
                </div>
                <div class="card-body">
                    <?php if (isset($order)): ?>
                        <p><strong>Họ tên:</strong> <?= htmlspecialchars($order->name ?? '') ?></p>
                        <p><strong>Số điện thoại:</strong> <?= htmlspecialchars($order->phone ?? '') ?></p>
                        <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($order->address ?? '') ?></p>
                        <p><strong>Phương thức thanh toán:</strong> <?= $payment_text ?></p>
                    <?php else: ?>
                        <div class="alert alert-info">
                            Không có thông tin đơn hàng để hiển thị.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Button to regenerate payment link -->
<?php if (!isset($payos_data) || !isset($payos_data['qrCode'])): ?>
<div class="container mt-3">
    <div class="row">
        <div class="col-12 text-center">
            <a href="/BFYL/Product/testPayment" class="btn btn-success">Tạo QR thanh toán mới</a>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- JavaScript để kiểm tra trạng thái thanh toán -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Kiểm tra trạng thái thanh toán mỗi 5 giây
    const checkPaymentStatus = function() {
        <?php if (isset($order_code)): ?>
        fetch('/BFYL/Product/paymentCallback?orderCode=<?= $order_code ?>&ajax=1')
            .then(response => response.json())
            .then(data => {
                console.log('Payment status:', data);
                if (data.status === 'PAID') {
                    document.getElementById('payment-status').textContent = 'Đã thanh toán';
                    document.getElementById('payment-status').classList.add('text-success');
                    
                    // Chuyển hướng đến trang xác nhận đơn hàng
                    <?php if (isset($order_id)): ?>
                    window.location.href = '/BFYL/Product/orderConfirmation/<?= $order_id ?>';
                    <?php else: ?>
                    // Use the order ID from the response if available
                    if (data.orderId) {
                        window.location.href = '/BFYL/Product/orderConfirmation/' + data.orderId;
                    } else {
                        // Reload the page to get the latest status
                        window.location.reload();
                    }
                    <?php endif; ?>
                }
            })
            .catch(error => {
                console.error('Error checking payment status:', error);
                // Don't keep trying if we have persistent errors
                if (window.paymentCheckErrors === undefined) {
                    window.paymentCheckErrors = 1;
                } else {
                    window.paymentCheckErrors++;
                }
                
                if (window.paymentCheckErrors > 5) {
                    clearInterval(window.paymentCheckInterval);
                    console.log('Stopped payment status checking due to errors');
                }
            });
        <?php endif; ?>
    };
    
    // Kiểm tra ngay lập tức và sau đó mỗi 5 giây
    <?php if (isset($order_code)): ?>
    checkPaymentStatus();
    window.paymentCheckInterval = setInterval(checkPaymentStatus, 5000);
    <?php endif; ?>
});
</script>

<?php include 'app/share/footer.php'; ?> 