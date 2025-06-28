<?php include 'app/share/header.php'; ?>

<div class="container mt-4">
    <h1>Thanh toán đơn hàng</h1>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5>Thanh toán đơn hàng trực tuyến</h5>
                </div>
                <div class="card-body text-center">
                    <?php if (isset($payos_data) && isset($payos_data['checkoutUrl'])): ?>
                        <div class="mt-3">
                            <p><strong>Mã đơn hàng:</strong> <?= $order_code ?></p>
                            <p><strong>Số tiền:</strong> <?= number_format($payos_data['amount'], 0, ',', '.') ?> đ</p>
                        </div>
                        
                        <!-- Link trực tiếp đến trang thanh toán -->
                        <div class="mt-3">
                            <a href="<?= $payos_data['checkoutUrl'] ?>" target="_blank" class="btn btn-primary btn-lg">
                                Mở trang thanh toán
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <p>Không thể tạo liên kết thanh toán. Vui lòng thử lại hoặc chọn phương thức thanh toán khác.</p>
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
                        
                        <hr>
                        <h6 class="font-weight-bold">Sản phẩm đã đặt</h6>
                        
                        <!-- Show products from order_details if available -->
                        <?php if (!empty($order_details)): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Sản phẩm</th>
                                            <th>Số lượng</th>
                                            <th>Đơn giá</th>
                                            <th>Thành tiền</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $totalAmount = 0; ?>
                                        <?php foreach ($order_details as $item): ?>
                                            <?php $itemTotal = $item->price * $item->quantity; ?>
                                            <?php $totalAmount += $itemTotal; ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <?php if (!empty($item->image)): ?>
                                                            <img src="/BFYL/uploads/<?= htmlspecialchars($item->image) ?>" 
                                                                 alt="<?= htmlspecialchars($item->name) ?>" 
                                                                 style="width: 50px; height: auto; margin-right: 10px;">
                                                        <?php endif; ?>
                                                        <?= htmlspecialchars($item->name) ?>
                                                    </div>
                                                </td>
                                                <td><?= $item->quantity ?></td>
                                                <td><?= number_format($item->price, 0, ',', '.') ?> đ</td>
                                                <td><?= number_format($itemTotal, 0, ',', '.') ?> đ</td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-right"><strong>Tổng cộng:</strong></td>
                                            <td><strong><?= number_format($totalAmount, 0, ',', '.') ?> đ</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        <!-- Show products from cart if order_details is not available -->
                        <?php elseif (!empty($cart_items)): ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th>Sản phẩm</th>
                                            <th>Số lượng</th>
                                            <th>Đơn giá</th>
                                            <th>Thành tiền</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $totalAmount = 0; ?>
                                        <?php foreach ($cart_items as $id => $item): ?>
                                            <?php $itemTotal = $item['price'] * $item['quantity']; ?>
                                            <?php $totalAmount += $itemTotal; ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <?php if (!empty($item['image'])): ?>
                                                            <img src="/BFYL/uploads/<?= htmlspecialchars($item['image']) ?>" 
                                                                 alt="<?= htmlspecialchars($item['name']) ?>" 
                                                                 style="width: 50px; height: auto; margin-right: 10px;">
                                                        <?php endif; ?>
                                                        <?= htmlspecialchars($item['name']) ?>
                                                    </div>
                                                </td>
                                                <td><?= $item['quantity'] ?></td>
                                                <td><?= number_format($item['price'], 0, ',', '.') ?> đ</td>
                                                <td><?= number_format($itemTotal, 0, ',', '.') ?> đ</td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <td colspan="3" class="text-right"><strong>Tổng cộng:</strong></td>
                                            <td><strong><?= number_format($totalAmount, 0, ',', '.') ?> đ</strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="alert alert-info">Không có thông tin sản phẩm để hiển thị.</div>
                        <?php endif; ?>
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
<?php if (!isset($payos_data) || !isset($payos_data['checkoutUrl'])): ?>
<div class="container mt-3">
    <div class="row">
        <div class="col-12 text-center">
            <a href="/BFYL/Product/testPayment" class="btn btn-success">Tạo liên kết thanh toán mới</a>
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
                    
                    // Redirect to payment success page instead of Products page
                    window.location.href = '/BFYL/Product/paymentSuccess?orderCode=<?= $order_code ?>';
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