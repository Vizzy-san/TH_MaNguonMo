<?php include 'app/share/header.php'; ?>

<div class="container mt-4">
    <h1>Giỏ hàng</h1>
    
    <?php if (empty($cart)): ?>
    <div class="alert alert-info">
        Giỏ hàng của bạn đang trống. <a href="/BFYL/Product">Tiếp tục mua sắm</a>
    </div>
    <?php else: ?>
    <form action="/BFYL/Product/updateCart" method="post">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th width="100">Hình ảnh</th>
                    <th>Sản phẩm</th>
                    <th width="150">Giá</th>
                    <th width="150">Số lượng</th>
                    <th width="150">Tổng tiền</th>
                    <th width="100">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $totalAmount = 0;
                foreach ($cart as $id => $item): 
                    // Kiểm tra sản phẩm đã được thanh toán chưa
                    if ($item['paid'] ?? false) {
                        continue; // Bỏ qua sản phẩm đã thanh toán
                    }
                    $itemTotal = $item['price'] * $item['quantity'];
                    $totalAmount += $itemTotal;
                ?>
                <tr>
                    <td>
                        <?php if (!empty($item['image'])): ?>
                            <img src="/BFYL/uploads/<?= $item['image'] ?>" alt="<?= $item['name'] ?>" class="img-thumbnail" width="80">
                        <?php else: ?>
                            <img src="/BFYL/uploads/no-image.jpg" alt="No Image" class="img-thumbnail" width="80">
                        <?php endif; ?>
                    </td>
                    <td><?= $item['name'] ?></td>
                    <td><?= number_format($item['price'], 0, ',', '.') ?> đ</td>
                    <td>
                        <input type="number" name="quantity[<?= $id ?>]" value="<?= $item['quantity'] ?>" min="0" class="form-control">
                    </td>
                    <td><?= number_format($itemTotal, 0, ',', '.') ?> đ</td>
                    <td>
                        <a href="/BFYL/Product/removeFromCart/<?= $id ?>" class="btn btn-danger btn-sm">
                            Xóa
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="4" class="text-right"><strong>Tổng cộng:</strong></td>
                    <td><strong><?= number_format($totalAmount, 0, ',', '.') ?> đ</strong></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        
        <div class="d-flex justify-content-between mt-3">
            <a href="/BFYL/Product" class="btn btn-secondary">Tiếp tục mua sắm</a>
            <div>
                <button type="submit" class="btn btn-primary mr-2">Cập nhật giỏ hàng</button>
                <a href="/BFYL/Product/checkout" class="btn btn-success">Thanh toán</a>
            </div>
        </div>
    </form>
    <?php endif; ?>
</div>

<?php include 'app/share/footer.php'; ?>