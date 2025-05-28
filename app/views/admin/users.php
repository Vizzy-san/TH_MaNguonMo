<?php
ob_start();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">User Management</h1>
    <a href="/BFYL/account/register" class="btn btn-primary">
        <i class="fas fa-user-plus"></i> Register New User
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-body">
        <?php if (empty($users)): ?>
            <p class="text-center">No users found.</p>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Created At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo $user->id; ?></td>
                                <td><?php echo htmlspecialchars($user->username); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $user->role_name === 'admin' ? 'danger' : 'primary'; ?>">
                                        <?php echo htmlspecialchars($user->role_name); ?>
                                    </span>
                                </td>
                                <td><?php echo $user->created_at ? date('d/m/Y H:i', strtotime($user->created_at)) : 'N/A'; ?></td>
                                <td>
                                    <button class="btn btn-sm btn-info" disabled>
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-danger" disabled>
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<div class="alert alert-info mt-4">
    <i class="fas fa-info-circle"></i> Note: User editing and deletion features are not implemented in this basic version.
</div>

<?php
$content = ob_get_clean();
include 'app/views/admin/layout.php';
?> 