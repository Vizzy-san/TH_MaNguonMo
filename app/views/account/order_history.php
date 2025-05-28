<?php include 'app/share/header.php'; ?>

<div class="container mt-4">
    <h1>Lịch sử mua hàng</h1>
    
    <?php if (empty($orders)): ?>
    <div class="alert alert-info">
        <p>Bạn chưa có đơn hàng nào.</p>
    </div>
    <p><a href="/BFYL/Product" class="btn btn-primary">Mua sắm ngay</a></p>
    <?php else: ?>
    
    <div class="accordion" id="orderAccordion">
        <?php foreach ($orders as $index => $order): ?>
        <div class="card mb-3">
            <div class="card-header" id="heading<?= $order->id ?>">
                <div class="row">
                    <div class="col-md-8">
                        <h5 class="mb-0">
                            <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse<?= $order->id ?>" aria-expanded="<?= ($index === 0) ? 'true' : 'false' ?>" aria-controls="collapse<?= $order->id ?>">
                                Mã Đơn Hàng : <?= isset($order->order_code) ? $order->order_code : '#'.$order->id ?> |  <?= date('d/m/Y H:i', strtotime($order->created_at)) ?>
                            </button>
                        </h5>
                    </div>
                    <div class="col-md-4 text-right">
                        <span class="badge badge-success">
                            <?= number_format($order->total_amount, 0, ',', '.') ?> đ
                        </span>
                    </div>
                </div>
            </div>

            <div id="collapse<?= $order->id ?>" class="collapse <?= ($index === 0) ? 'show' : '' ?>" aria-labelledby="heading<?= $order->id ?>" data-parent="#orderAccordion">
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h5>Thông tin giao hàng</h5>
                            <p><strong>Họ tên:</strong> <?= htmlspecialchars($order->name) ?></p>
                            <p><strong>Số điện thoại:</strong> <?= htmlspecialchars($order->phone) ?></p>
                            <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($order->address) ?></p>
                        </div>
                        <div class="col-md-6">
                            <h5>Thông tin đơn hàng</h5>
                            <p><strong>Ngày đặt hàng:</strong> <?= date('d/m/Y H:i', strtotime($order->created_at)) ?></p>
                            <p><strong>Số lượng mặt hàng:</strong> <?= $order->item_count ?></p>
                            <?php if (isset($order->payment_method)): ?>
                            <p>
                                <strong>Phương thức thanh toán:</strong> 
                                <?= ($order->payment_method == 'cod') ? 'Thanh toán khi nhận hàng' : 'Thanh toán bằng chuyển khoản' ?>
                            </p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <h5>Chi tiết đơn hàng</h5>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="thead-light">
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th>Hình ảnh</th>
                                    <th>Đơn giá</th>
                                    <th>Số lượng</th>
                                    <th>Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if(empty($order->items)): ?>
                                <tr>
                                    <td colspan="5" class="text-center">Không có thông tin chi tiết về sản phẩm trong đơn hàng này</td>
                                </tr>
                                <?php else: ?>
                                <?php foreach ($order->items as $item): ?>
                                <tr>
                                    <td><?= isset($item->product_name) ? htmlspecialchars($item->product_name) : 'Sản phẩm không còn khả dụng' ?></td>
                                    <td>
                                        <?php if (isset($item->image) && $item->image): ?>
                                        <img src="/BFYL/uploads/<?= $item->image ?>" alt="<?= htmlspecialchars($item->product_name ?? 'Sản phẩm') ?>" class="product-image" style="max-width: 50px;">
                                        <?php else: ?>
                                        <span class="text-muted">Không có hình ảnh</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= number_format($item->price, 0, ',', '.') ?> đ</td>
                                    <td><?= $item->quantity ?></td>
                                    <td><?= number_format($item->price * $item->quantity, 0, ',', '.') ?> đ</td>
                                </tr>
                                <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4" class="text-right"><strong>Tổng cộng:</strong></td>
                                    <td><strong><?= number_format($order->total_amount, 0, ',', '.') ?> đ</strong></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    
                    <?php if (isset($order->payment_method) && $order->payment_method == 'bank_transfer'): ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<?php include 'app/share/footer.php'; ?> 