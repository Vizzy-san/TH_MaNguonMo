<?php ob_start(); ?>
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Customer Support Management</h1>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Support Tickets</h6>
            <div>
                <select class="custom-select custom-select-sm mr-2" style="width: auto;" id="ticketStatusFilter">
                    <option value="all">All Tickets</option>
                    <option value="open">Open</option>
                    <option value="in-progress">In Progress</option>
                    <option value="closed">Closed</option>
                </select>
                <input type="text" class="form-control form-control-sm d-inline-block mr-2" style="width: 200px;" id="searchTicket" placeholder="Search tickets...">
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Subject</th>
                            <th>Customer</th>
                            <th>Status</th>
                            <th>Created</th>
                            <th>Last Updated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($tickets)): ?>
                            <tr>
                                <td colspan="7" class="text-center">No support tickets found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($tickets as $ticket): ?>
                                <tr>
                                    <td><?= $ticket->id ?></td>
                                    <td><?= $ticket->subject ?></td>
                                    <td>
                                        <?= $ticket->customer_name ?><br>
                                        <small class="text-muted"><?= $ticket->customer_email ?></small>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?= strtolower($ticket->status) === 'open' ? 'danger' : 
                                            (strtolower($ticket->status) === 'in-progress' ? 'warning' : 'success') ?>">
                                            <?= $ticket->status ?>
                                        </span>
                                    </td>
                                    <td><?= $ticket->created_at ?></td>
                                    <td><?= $ticket->updated_at ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> View
                                        </button>
                                        <button class="btn btn-sm btn-info">
                                            <i class="fas fa-reply"></i> Reply
                                        </button>
                                        <button class="btn btn-sm btn-success" <?= $ticket->status === 'Closed' ? 'disabled' : '' ?>>
                                            <i class="fas fa-check"></i> Close
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