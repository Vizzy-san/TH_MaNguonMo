<?php include BASE_PATH . '/app/share/header.php'; ?>
<h1>Danh sách danh mục</h1>
<a href="/BFYL/Category/add" class="btn btn-success mb-2">Thêm danh mục mới</a>
<div class="table-responsive">
    <table class="table table-striped table-bordered">
        <thead class="thead-dark">
            <tr>
                <th>ID</th>
                <th>Tên danh mục</th>
                <th>Mô tả</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if(empty($categories)): ?>
                <tr>
                    <td colspan="4" class="text-center">Không có danh mục nào</td>
                </tr>
            <?php else: ?>
                <?php foreach($categories as $category): ?>
                    <tr>
                        <td><?php echo $category->id; ?></td>
                        <td><?php echo htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($category->description, ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <a href="/BFYL/Category/edit/<?php echo $category->id; ?>" class="btn btn-warning btn-sm">Sửa</a>
                            <a href="/BFYL/Category/delete/<?php echo $category->id; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa danh mục này?');">Xóa</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<a href="/BFYL/Product" class="btn btn-primary">Quay lại trang sản phẩm</a>
<?php include BASE_PATH . '/app/share/footer.php'; ?> 