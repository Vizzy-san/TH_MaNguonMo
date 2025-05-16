<?php ob_start(); ?>
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Promotion Management</h1>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Manage Promotions</h6>
            <button class="btn btn-primary btn-sm" id="addPromoBtn">
                <i class="fas fa-plus"></i> Add New Promotion
            </button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Code</th>
                            <th>Discount</th>
                            <th>Type</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($promotions)): ?>
                            <tr>
                                <td colspan="9" class="text-center">No promotions found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($promotions as $promo): ?>
                                <tr>
                                    <td><?= $promo->id ?></td>
                                    <td><?= $promo->name ?></td>
                                    <td><?= $promo->code ?></td>
                                    <td><?= $promo->discount_value ?></td>
                                    <td><?= $promo->discount_type ?></td>
                                    <td><?= $promo->start_date ?></td>
                                    <td><?= $promo->end_date ?></td>
                                    <td>
                                        <span class="badge badge-<?= strtolower($promo->status) === 'active' ? 'success' : (strtolower($promo->status) === 'inactive' ? 'secondary' : 'warning') ?>">
                                            <?= $promo->status ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info">
                                            <i class="fas fa-edit"></i> Edit
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