<?php include BASE_PATH . '/app/share/header.php'; ?>
<h1>Thêm sản phẩm mới</h1>
<?php if (!empty($errors)): ?>
<div class="alert alert-danger">
<ul>
<?php foreach ($errors as $error): ?>
<li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
<?php endforeach; ?>
</ul>
</div>
<?php endif; ?>
<form method="POST" action="/BFYL/Product/save" enctype="multipart/form-data" onsubmit="return validateForm();">
<div class="form-group">
<label for="name">Tên sản phẩm:</label>
<input type="text" id="name" name="name" class="form-control" required>
</div>
<div class="form-group">
<label for="description">Mô tả:</label>
<textarea id="description" name="description" class="form-control" required></textarea>
</div>
<div class="form-group">
<label for="price">Giá:</label>
<input type="number" id="price" name="price" class="form-control" step="0.01" required>
</div>
<div class="form-group">
<label for="category_id">Danh mục:</label>
<select id="category_id" name="category_id" class="form-control" required>
<?php foreach ($categories as $category): ?>
<option value="<?php echo $category->id; ?>"><?php echo htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8'); ?></option>
<?php endforeach; ?>
</select>
</div>
<div class="form-group">
<label for="image">Hình ảnh sản phẩm:</label>
<input type="file" id="image" name="image" class="form-control-file" accept="image/*" onchange="previewImage(this);">
<small class="form-text text-muted">Chọn file hình ảnh (JPG, PNG, GIF, WebP) hoặc bỏ qua</small>
<div id="imagePreview" class="mt-2" style="display: none;">
    <img id="preview" src="#" alt="Xem trước hình ảnh" class="img-thumbnail" style="max-height: 200px;">
</div>
</div>
<button type="submit" class="btn btn-primary">Thêm sản phẩm</button>
</form>
<a href="/BFYL/Product/list" class="btn btn-secondary mt-2">Quay lại danh sách sản phẩm</a>

<script>
function previewImage(input) {
    var preview = document.getElementById('preview');
    var previewDiv = document.getElementById('imagePreview');
    
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            previewDiv.style.display = 'block';
        }
        
        reader.readAsDataURL(input.files[0]);
    } else {
        previewDiv.style.display = 'none';
    }
}

function clearImageSelection() {
    // Clear the file input
    document.getElementById('image').value = '';
    
    // Hide the preview
    document.getElementById('imagePreview').style.display = 'none';
}
</script>

<?php include BASE_PATH . '/app/share/footer.php'; ?>