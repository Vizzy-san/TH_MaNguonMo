<?php include 'app/share/header.php'; ?>

<div class="container mt-4">
    <h1>Thanh toán</h1>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Thông tin đặt hàng
                </div>
                <div class="card-body">
                    <form action="/project1/Product/processCheckout" method="post">
                        <div class="form-group">
                            <label for="name">Họ tên</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Số điện thoại</label>
                            <input type="text" class="form-control" id="phone" name="phone" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="address">Địa chỉ</label>
                            <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Đặt hàng</button>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    Giỏ hàng của bạn
                </div>
                <div class="card-body">
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
                            foreach ($cart as $id => $item): 
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
                    
                    <a href="/project1/Product/cart" class="btn btn-secondary">Quay lại giỏ hàng</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'app/share/footer.php'; ?> 