<?php include 'app/share/header.php'; ?>

<div class="container mt-4">
    <h1>Giỏ hàng</h1>
    
    <?php if (empty($cart)): ?>
    <div class="alert alert-info">
        Giỏ hàng của bạn đang trống. <a href="/project1/Product">Tiếp tục mua sắm</a>
    </div>
    <?php else: ?>
    <form action="/project1/Product/updateCart" method="post">
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
                    $itemTotal = $item['price'] * $item['quantity'];
                    $totalAmount += $itemTotal;
                ?>
                <tr>
                    <td>
                        <?php if (!empty($item['image'])): ?>
                            <img src="/project1/uploads/<?= $item['image'] ?>" alt="<?= $item['name'] ?>" class="img-thumbnail" width="80">
                        <?php else: ?>
                            <img src="/project1/uploads/no-image.jpg" alt="No Image" class="img-thumbnail" width="80">
                        <?php endif; ?>
                    </td>
                    <td><?= $item['name'] ?></td>
                    <td><?= number_format($item['price'], 0, ',', '.') ?> đ</td>
                    <td>
                        <input type="number" name="quantity[<?= $id ?>]" value="<?= $item['quantity'] ?>" min="0" class="form-control">
                    </td>
                    <td><?= number_format($itemTotal, 0, ',', '.') ?> đ</td>
                    <td>
                        <button type="button" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#deleteModal" data-id="<?= $id ?>" data-name="<?= $item['name'] ?>">
                            Xóa
                        </button>
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
            <a href="/project1/Product" class="btn btn-secondary">Tiếp tục mua sắm</a>
            <div>
                <button type="submit" class="btn btn-primary mr-2">Cập nhật giỏ hàng</button>
                <a href="/project1/Product/checkout" class="btn btn-success">Thanh toán</a>
            </div>
        </div>
    </form>
    <?php endif; ?>
</div>

<!-- Modal xác nhận xóa sản phẩm -->
<div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">Xác nhận xóa</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                <p>Bạn có muốn xóa sản phẩm này không?</p>
                <h5 id="productName" class="text-danger"></h5>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Không</button>
                <button type="button" id="confirmDelete" class="btn btn-danger">Có</button>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        var deleteUrl = '';
        
        $('#deleteModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var name = button.data('name');
            var modal = $(this);
            
            deleteUrl = '/project1/Product/removeFromCart/' + id;
            modal.find('#productName').text(name);
        });
        
        $('#confirmDelete').on('click', function() {
            window.location.href = deleteUrl;
        });
    });
</script>

<?php include 'app/share/footer.php'; ?> 