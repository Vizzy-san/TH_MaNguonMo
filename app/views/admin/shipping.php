<?php ob_start(); ?>
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Shipping Management</h1>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Manage Shipping Methods</h6>
            <button class="btn btn-primary btn-sm" id="addShippingBtn">
                <i class="fas fa-plus"></i> Add Shipping Method
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Method Name</th>
                            <th>Description</th>
                            <th>Cost</th>
                            <th>Estimated Delivery</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($shippingMethods)): ?>
                            <tr>
                                <td colspan="7" class="text-center">No shipping methods found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($shippingMethods as $method): ?>
                                <tr>
                                    <td><?= $method->id ?></td>
                                    <td><?= $method->name ?></td>
                                    <td><?= $method->description ?></td>
                                    <td><?= number_format($method->cost, 0, '.', ',') ?> đ</td>
                                    <td><?= $method->delivery_time ?></td>
                                    <td>
                                        <span class="badge badge-<?= $method->active ? 'success' : 'secondary' ?>">
                                            <?= $method->active ? 'Active' : 'Inactive' ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button class="btn btn-sm btn-<?= $method->active ? 'warning' : 'success' ?>">
                                            <i class="fas fa-power-off"></i> <?= $method->active ? 'Deactivate' : 'Activate' ?>
                                        </button>
                                        <button class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
include 'app/views/admin/layout.php';
?> 