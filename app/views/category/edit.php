<?php include BASE_PATH . '/app/share/header.php'; ?>
<h1>Sửa danh mục</h1>
<form method="POST" action="/BFYL/Category/update">
    <input type="hidden" name="id" value="<?php echo $category->id; ?>">
    <div class="form-group">
        <label for="name">Tên danh mục:</label>
        <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8'); ?>" required>
    </div>
    <div class="form-group">
        <label for="description">Mô tả:</label>
        <textarea id="description" name="description" class="form-control"><?php echo htmlspecialchars($category->description, ENT_QUOTES, 'UTF-8'); ?></textarea>
    </div>
    <button type="submit" class="btn btn-primary">Cập nhật danh mục</button>
</form>
<a href="/BFYL/Category/list" class="btn btn-secondary mt-2">Quay lại danh sách danh mục</a>
<?php include BASE_PATH . '/app/share/footer.php'; ?> 