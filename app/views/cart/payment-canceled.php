<?php require_once 'app/views/includes/header.php'; ?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0 rounded-lg">
                <div class="card-body text-center p-5">
                    <!-- Red Circle with X icon -->
                    <div class="mb-4">
                        <div class="mx-auto" style="width: 64px; height: 64px;">
                            <svg class="text-danger w-100 h-100" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm5 13.59L15.59 17 12 13.41 8.41 17 7 15.59 10.59 12 7 8.41 8.41 7 12 10.59 15.59 7 17 8.41 13.41 12 17 15.59z"></path>
                            </svg>
                        </div>
                    </div>
                    
                    <!-- Main heading - using exact Vietnamese text from image -->
                    <h2 class="text-danger fw-bold mb-3">Thanh Toán Đã Bị Hủy</h2>
                    
                    <!-- Message - using exact text from image -->
                    <p class="text-muted mb-4">
                        Đặt vé của bạn đã được hủy. Bạn có thể thử quay lại danh sách phim để đặt lại.
                    </p>
                    
                    <!-- Order code if available -->
                    <?php if (isset($orderCode) && !empty($orderCode)): ?>
                    <div class="text-muted small mb-4">
                        Mã đơn hàng: <?= htmlspecialchars($orderCode) ?>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Return button - using blue button style as in image -->
                    <div class="mt-3">
                        <a href="/BFYL/Product" class="btn btn-primary px-4 py-2 rounded-pill">
                            Quay Lại Danh Sách Phim
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once 'app/views/includes/footer.php'; ?>
