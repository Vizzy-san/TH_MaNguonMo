<?php include 'app/share/header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-success text-white">
                    <h2 class="mb-0"><i class="fas fa-check-circle mr-2"></i> Thanh toán thành công!</h2>
                </div>
                <div class="card-body">
                    <!-- Success message and icon -->
                    <div class="text-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="currentColor" class="bi bi-check-circle-fill text-success" viewBox="0 0 16 16">
                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                        </svg>
                    </div>
                    
                    <?php if (isset($payment_success)): ?>
                        <div class="alert alert-success text-center mb-4"><?= htmlspecialchars($payment_success) ?></div>
                    <?php endif; ?>
                    
                    <!-- Order and Transaction Information -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h4>Thông tin đơn hàng</h4>
                            <div class="p-3 bg-light rounded">
                                <p class="mb-2"><strong>Mã đơn hàng:</strong> <?= htmlspecialchars($order_code) ?></p>
                                <?php if (!empty($transaction_id)): ?>
                                    <p class="mb-2"><strong>Mã giao dịch:</strong> <?= htmlspecialchars($transaction_id) ?></p>
                                <?php endif; ?>
                                <p class="mb-2"><strong>Phương thức thanh toán:</strong> Thanh toán trực tuyến</p>
                                <p class="mb-2"><strong>Trạng thái:</strong> <span class="badge bg-success text-white">Đã thanh toán</span></p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h4>Thông tin khách hàng</h4>
                            <div class="p-3 bg-light rounded">
                                <p class="mb-2"><strong>Họ tên:</strong> <?= htmlspecialchars($order->name ?? '') ?></p>
                                <p class="mb-2"><strong>Số điện thoại:</strong> <?= htmlspecialchars($order->phone ?? '') ?></p>
                                <p class="mb-2"><strong>Địa chỉ:</strong> <?= htmlspecialchars($order->address ?? '') ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Order Items -->
                    <h4>Chi tiết sản phẩm</h4>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th>Số lượng</th>
                                    <th class="text-right">Đơn giá</th>
                                    <th class="text-right">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (isset($order_items) && !empty($order_items)): ?>
                                    <?php foreach ($order_items as $item): ?>
                                        <?php $itemTotal = $item->price * $item->quantity; ?>
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
                                            <td class="text-right"><?= number_format($item->price, 0, ',', '.') ?> đ</td>
                                            <td class="text-right"><?= number_format($itemTotal, 0, ',', '.') ?> đ</td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center">Không có thông tin sản phẩm</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="3" class="text-right"><strong>Tổng cộng:</strong></td>
                                    <td class="text-right"><strong><?= number_format($totalAmount, 0, ',', '.') ?> đ</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <hr>
                    
                    <!-- Action Buttons -->
                    <div class="text-center mt-4">
                        <a href="/BFYL/Product" class="btn btn-primary btn-lg">Tiếp tục mua sắm</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'app/share/footer.php'; ?>