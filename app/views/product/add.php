<?php include BASE_PATH . '/app/share/header.php'; ?>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <!-- Page title with icon -->
            <div class="d-flex align-items-center mb-4">
                <i class="fas fa-plus-circle text-primary fa-2x mr-3"></i>
                <h1 class="mb-0">Thêm sản phẩm mới</h1>
            </div>

            <!-- Error messages display -->
            <?php if (!empty($errors)): ?>
            <div class="alert alert-danger shadow-sm">
                <ul class="mb-0">
                <?php foreach ($errors as $error): ?>
                <li><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></li>
                <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <!-- Main card for form -->
            <div class="card shadow border-0 rounded-lg mb-4">
                <div class="card-header bg-gradient-primary text-white py-3">
                    <h5 class="m-0 font-weight-bold">
                        <i class="fas fa-clipboard-list mr-2"></i>
                        Thông tin sản phẩm
                    </h5>
                </div>
                
                <div class="card-body">
                    <form method="POST" action="/BFYL/Product/save" enctype="multipart/form-data" onsubmit="return validateForm();">
                        <div class="row">
                            <!-- Left side - Product details -->
                            <div class="col-md-8 pr-md-4">
                                <div class="form-group">
                                    <label for="name" class="font-weight-bold">
                                        <i class="fas fa-tag text-primary mr-1"></i>
                                        Tên sản phẩm:
                                    </label>
                                    <input type="text" id="name" name="name" class="form-control" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="category_id" class="font-weight-bold">
                                        <i class="fas fa-folder text-primary mr-1"></i>
                                        Danh mục:
                                    </label>
                                    <select id="category_id" name="category_id" class="form-control" required>
                                        <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category->id; ?>"><?php echo htmlspecialchars($category->name, ENT_QUOTES, 'UTF-8'); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                
                                <div class="form-group">
                                    <label for="price" class="font-weight-bold">
                                        <i class="fas fa-money-bill-wave text-primary mr-1"></i>
                                        Giá:
                                    </label>
                                    <div class="input-group">
                                        <input type="number" id="price" name="price" class="form-control" step="0.01" required>
                                        <div class="input-group-append">
                                            <span class="input-group-text">VNĐ</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="description" class="font-weight-bold">
                                        <i class="fas fa-align-left text-primary mr-1"></i>
                                        Mô tả:
                                    </label>
                                    <textarea id="description" name="description" class="form-control" rows="5" required></textarea>
                                </div>
                            </div>
                            
                            <!-- Right side - Image upload -->
                            <div class="col-md-4 mt-3 mt-md-0">
                                <div class="card bg-light border">
                                    <div class="card-header bg-light">
                                        <h6 class="m-0 font-weight-bold text-secondary">
                                            <i class="fas fa-image mr-1"></i>
                                            Hình ảnh sản phẩm
                                        </h6>
                                    </div>
                                    <div class="card-body text-center">
                                        <div class="image-upload-container mb-3">
                                            <div id="imagePreview" class="mb-3 d-flex justify-content-center align-items-center" 
                                                style="height: 200px; border: 2px dashed #ccc; cursor: pointer; position: relative; background-color: #f8f9fa; overflow: hidden;">
                                                <div id="uploadPrompt" class="text-center p-4">
                                                    <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-2"></i>
                                                    <p class="mb-0 text-muted">Nhấp để tải lên hình ảnh</p>
                                                </div>
                                                <img id="preview" src="#" alt="Preview" style="display: none; max-height: 100%; max-width: 100%; object-fit: contain;">
                                                <div id="removeImage" class="btn btn-sm btn-danger" 
                                                     style="position: absolute; top: 5px; right: 5px; border-radius: 50%; display: none;"
                                                     onclick="clearImageSelection(event)">
                                                    <i class="fas fa-times"></i>
                                                </div>
                                            </div>
                                            <input type="file" id="image" name="image" class="form-control-file d-none" accept="image/*">
                                        </div>
                                        <small class="text-muted">Hỗ trợ định dạng: JPG, PNG, GIF, WebP</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        
                        <!-- Form actions -->
                        <div class="row">
                            <div class="col-md-12 text-right">
                                <a href="/BFYL/Product/list" class="btn btn-outline-secondary mr-2">
                                    <i class="fas fa-arrow-left mr-1"></i> Quay lại
                                </a>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="fas fa-save mr-1"></i> Thêm sản phẩm
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Make the preview div clickable to trigger file input
    document.getElementById('imagePreview').addEventListener('click', function() {
        document.getElementById('image').click();
    });

    // Set up the file input change handler
    document.getElementById('image').addEventListener('change', function(e) {
        previewImage(this);
    });
});

function previewImage(input) {
    var preview = document.getElementById('preview');
    var uploadPrompt = document.getElementById('uploadPrompt');
    var removeBtn = document.getElementById('removeImage');
    
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
            uploadPrompt.style.display = 'none';
            removeBtn.style.display = 'block';
        }
        
        reader.readAsDataURL(input.files[0]);
    } else {
        clearImageSelection();
    }
}

function clearImageSelection(e) {
    if (e) {
        e.stopPropagation(); // Prevent click from bubbling to parent
    }
    
    // Clear the file input
    document.getElementById('image').value = '';
    
    // Reset the preview area
    document.getElementById('preview').style.display = 'none';
    document.getElementById('uploadPrompt').style.display = 'block';
    document.getElementById('removeImage').style.display = 'none';
}

function validateForm() {
    // Add any additional validation if needed
    return true;
}
</script>

<style>
.bg-gradient-primary {
    background: linear-gradient(to right, #4e73df, #224abe);
}
</style>

<?php include BASE_PATH . '/app/share/footer.php'; ?>