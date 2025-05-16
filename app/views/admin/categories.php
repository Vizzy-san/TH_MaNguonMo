<?php
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">Category Management</h1>
    <a href="/project1/Category/add" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Category
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <?php if (empty($categories)): ?>
            <p class="text-center">No categories found.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $category): ?>
                            <tr>
                                <td><?php echo $category->id; ?></td>
                                <td><?php echo htmlspecialchars($category->name); ?></td>
                                <td><?php echo htmlspecialchars($category->description ?? ''); ?></td>
                                <td>
                                    <a href="/project1/Category/edit/<?php echo $category->id; ?>" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="/project1/Category/delete/<?php echo $category->id; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this category? This will affect all associated products.');">
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