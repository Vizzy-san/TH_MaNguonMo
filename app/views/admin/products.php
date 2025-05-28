<?php
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">Product Management</h1>
    <a href="/BFYL/Product/add" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Product
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <?php if (empty($products)): ?>
            <p class="text-center">No products found.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?php echo $product->id; ?></td>
                                <td>
                                    <?php if (!empty($product->image)): ?>
                                        <img src="/BFYL/uploads/<?php echo $product->image; ?>" alt="<?php echo htmlspecialchars($product->name); ?>" style="width: 50px; height: 50px; object-fit: cover;">
                                    <?php else: ?>
                                        <span class="text-muted">No image</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($product->name); ?></td>
                                <td><?php echo htmlspecialchars($product->category_name ?? 'Uncategorized'); ?></td>
                                <td><?php echo number_format($product->price, 0, ',', '.'); ?> đ</td>
                                <td>
                                    <a href="/BFYL/Product/show/<?php echo $product->id; ?>" class="btn btn-sm btn-info" target="_blank">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="/BFYL/Product/edit/<?php echo $product->id; ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="/BFYL/Product/delete/<?php echo $product->id; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this product?');">
                                        <i class="fas fa-trash"></i>
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