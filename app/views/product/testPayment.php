<?php include BASE_PATH . '/app/share/header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4>Thanh toán đơn hàng</h4>
                </div>
                <div class="card-body text-center">
                    <?php if (isset($qr_code) && $qr_code): ?>
                        <div class="mb-4">
                            <h5>Quét mã QR để thanh toán</h5>
                            <img src="<?php echo $qr_code; ?>" alt="QR Code" class="img-fluid" style="max-width: 300px;">
                        </div>
                        
                        <div class="mb-4">
                            <p><strong>Mã đơn hàng:</strong> <?php echo isset($order_code) ? $order_code : $transactionId; ?></p>
                            <p><strong>Số tiền:</strong> <?php echo number_format($amount, 0, ',', '.'); ?> VND</p>
                        </div>
                        
                        <!-- Hiển thị thông tin đơn hàng -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h5>Chi tiết đơn hàng</h5>
                            </div>
                            <div class="card-body">
                                <?php if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0): ?>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Sản phẩm</th>
                                            <th>Số lượng</th>
                                            <th>Đơn giá</th>
                                            <th>Thành tiền</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $totalAmount = 0;
                                        foreach ($_SESSION['cart'] as $id => $item): 
                                            $itemTotal = $item['price'] * $item['quantity'];
                                            $totalAmount += $itemTotal;
                                        ?>
                                        <tr>
                                            <td><?= $item['name'] ?></td>
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
                                <?php else: ?>
                                <div class="alert alert-info">
                                    Không có thông tin đơn hàng để hiển thị.
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <?php if (isset($checkout_url) && $checkout_url): ?>
                            <div class="mb-4">
                                <a href="<?php echo $checkout_url; ?>" target="_blank" class="btn btn-success btn-lg">
                                    <i class="fas fa-credit-card"></i> Mở trang thanh toán
                                </a>
                            </div>
                        <?php endif; ?>
                    <?php else: ?>
                        <div class="alert alert-danger">
                            <p>Không thể tạo mã QR thanh toán. Vui lòng thử lại sau.</p>
                        </div>
                    <?php endif; ?>
                    
                    <a href="/BFYL/Product" class="btn btn-secondary">Quay lại trang sản phẩm</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include BASE_PATH . '/app/share/footer.php'; ?>
