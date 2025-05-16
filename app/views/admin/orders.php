<?php
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">Order Management</h1>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <?php if (empty($orders)): ?>
            <p class="text-center">No orders found.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Contact</th>
                            <th>Date</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>#<?php echo $order->id; ?></td>
                                <td><?php echo htmlspecialchars($order->name); ?></td>
                                <td><?php echo htmlspecialchars($order->phone); ?></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($order->created_at)); ?></td>
                                <td><?php echo $order->item_count; ?></td>
                                <td><?php echo number_format($order->total_amount, 0, ',', '.'); ?> đ</td>
                                <td>
                                    <a href="/project1/admin/viewOrder/<?php echo $order->id; ?>" class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'app/views/admin/layout.php';
?> 