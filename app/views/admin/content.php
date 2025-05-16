<?php ob_start(); ?>
<div class="container-fluid">
    <h1 class="h3 mb-2 text-gray-800">Content Management</h1>
    
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Manage Content</h6>
            <div>
                <select class="custom-select custom-select-sm mr-2" style="width: auto;" id="contentTypeFilter">
                    <option value="all">All Types</option>
                    <option value="page">Pages</option>
                    <option value="post">Blog Posts</option>
                    <option value="banner">Banners</option>
                    <option value="slider">Sliders</option>
                </select>
                <button class="btn btn-primary btn-sm" id="addContentBtn">
                    <i class="fas fa-plus"></i> Add New Content
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Author</th>
                            <th>Created Date</th>
                            <th>Updated Date</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($contents)): ?>
                            <tr>
                                <td colspan="8" class="text-center">No content items found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($contents as $content): ?>
                                <tr>
                                    <td><?= $content->id ?></td>
                                    <td><?= $content->title ?></td>
                                    <td><?= ucfirst($content->type) ?></td>
                                    <td><?= $content->author ?></td>
                                    <td><?= $content->created_at ?></td>
                                    <td><?= $content->updated_at ?></td>
                                    <td>
                                        <span class="badge badge-<?= strtolower($content->status) === 'published' ? 'success' : (strtolower($content->status) === 'draft' ? 'warning' : 'secondary') ?>">
                                            <?= $content->status ?>
                                        </span>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-info">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> Preview
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