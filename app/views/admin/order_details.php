<?php
ob_start();
?>

<div class="mb-4">
    <a href="/BFYL/admin/orders" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left"></i> Back to Orders
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Order #<?php echo $order->id; ?> Details</h6>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <h5>Customer Information</h5>
                <p><strong>Name:</strong> <?php echo htmlspecialchars($order->name); ?></p>
                <p><strong>Phone:</strong> <?php echo htmlspecialchars($order->phone); ?></p>
                <p><strong>Address:</strong> <?php echo htmlspecialchars($order->address); ?></p>
            </div>
            <div class="col-md-6">
                <h5>Order Information</h5>
                <p><strong>Order Date:</strong> <?php echo date('d/m/Y H:i', strtotime($order->created_at)); ?></p>
                <p><strong>Order Status:</strong> <span class="badge badge-success">Completed</span></p>
            </div>
        </div>
        
        <h5>Order Items</h5>
        <?php if (empty($orderItems)): ?>
            <p class="text-center">No items found for this order.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Image</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $grandTotal = 0;
                        foreach ($orderItems as $item): 
                            $itemTotal = $item->price * $item->quantity;
                            $grandTotal += $itemTotal;
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item->name ?? 'Unknown Product'); ?></td>
                                <td>
                                    <?php if (!empty($item->image)): ?>
                                        <img src="/BFYL/uploads/<?php echo $item->image; ?>" alt="<?php echo htmlspecialchars($item->name ?? ''); ?>" style="width: 50px; height: 50px; object-fit: cover;">
                                    <?php else: ?>
                                        <span class="text-muted">No image</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo number_format($item->price, 0, ',', '.'); ?> đ</td>
                                <td><?php echo $item->quantity; ?></td>
                                <td><?php echo number_format($itemTotal, 0, ',', '.'); ?> đ</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-right">Grand Total:</th>
                            <th><?php echo number_format($grandTotal, 0, ',', '.'); ?> đ</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'app/views/admin/layout.php';
?> 