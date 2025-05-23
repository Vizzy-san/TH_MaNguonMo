<?php include BASE_PATH . '/app/share/header.php'; ?>
<h1>Sửa sản phẩm</h1>
<?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
<ul>
<?php foreach ($errors as $error): ?>
<li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
<?php endforeach; ?>
</ul>
</div>
<?php endif; ?>
<form method="POST" action="/BFYL/Product/update" enctype="multipart/form-data" onsubmit="return validateForm();">
<input type="hidden" name="id" value="<?php echo $product->id; ?>">
<div class="form-group">
<label for="name">Tên sản phẩm:</label>
<input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>" required>
</div>
<div class="form-group">
<label for="description">Mô tả:</label>
<textarea id="description" name="description" class="form-control" required><?php echo htmlspecialchars($product->description, ENT_QUOTES, 'UTF-8'); ?></textarea>
</div>
<div class="form-group">
<label for="price">Giá:</label>
<input type="number" id="price" name="price" class="form-control" step="0.01" value="<?php echo htmlspecialchars($product->price, ENT_QUOTES, 'UTF-8'); ?>" required>
</div>
<div class="form-group">
<label for="category_id">Danh mục:</label>
<select id="category_id" name="category_id" class="form-control" required>
<?php foreach ($categories as $category): ?>
<option value="<?php echo $category->id; ?>" <?php echo $category->id == $product->category_id ? 'selected' : ''; ?>>
<?php echo htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8'); ?>
</option>
<?php endforeach; ?>
</select>
</div>
<div class="form-group">
<label for="image">Hình ảnh sản phẩm:</label>

<div id="existingImage" class="mb-2 position-relative">
    <?php if (!empty($product->image)): ?>
        <div class="image-container position-relative d-inline-block">
            <img src="/BFYL/uploads/<?php echo htmlspecialchars($product->image, ENT_QUOTES, 'UTF-8'); ?>" 
                 alt="<?php echo htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8'); ?>" 
                 class="img-thumbnail" style="max-height: 200px;">
            <button type="button" class="btn btn-sm btn-danger position-absolute" 
                    style="top: 5px; right: 5px; border-radius: 50%; padding: 0.2rem 0.5rem;" 
                    onclick="clearCurrentImage()">X</button>
        </div>
        <input type="hidden" id="current_image" name="current_image" value="<?php echo htmlspecialchars($product->image, ENT_QUOTES, 'UTF-8'); ?>">
    <?php endif; ?>
</div>

<div class="input-group">
    <input type="file" id="image" name="image" class="form-control-file" accept="image/*" onchange="previewImage(this);">
</div>
<small class="form-text text-muted">Chọn file hình ảnh mới (JPG, PNG, GIF, WebP) hoặc để trống để giữ hình ảnh hiện tại</small>
<div id="imagePreview" class="mt-2 position-relative" style="display: none;">
    <p><strong>Hình ảnh mới đã chọn:</strong></p>
    <div class="image-container position-relative d-inline-block">
        <img id="preview" src="#" alt="Xem trước hình ảnh" class="img-thumbnail" style="max-height: 200px;">
        <button type="button" class="btn btn-sm btn-danger position-absolute" 
                style="top: 5px; right: 5px; border-radius: 50%; padding: 0.2rem 0.5rem;" 
                onclick="clearNewImage()">X</button>
    </div>
</div>

</div>
<button type="submit" class="btn btn-primary">Lưu thay đổi</button>
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

function clearNewImage() {
    // Clear the file input
    document.getElementById('image').value = '';
    
    // Hide the preview
    document.getElementById('imagePreview').style.display = 'none';
}

function clearCurrentImage() {
    // Hide the existing image
    document.getElementById('existingImage').innerHTML = '';
    
    // Clear the hidden input value
    document.getElementById('current_image').value = '';
}
</script>

<?php include BASE_PATH . '/app/share/footer.php'; ?>