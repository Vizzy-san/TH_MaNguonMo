<?php include BASE_PATH . '/app/share/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow border-0">
                <div class="card-body p-5 text-center">
                    <div class="mb-4">
                        <div class="mx-auto" style="width: 80px; height: 80px;">
                            <svg class="text-danger w-100 h-100" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm-1-13h2v6h-2zm0 8h2v2h-2z"/>
                            </svg>
                        </div>
                    </div>
                    
                    <h1 class="text-danger mb-3">Access Denied</h1>
                    <p class="lead text-muted mb-4">
                        You don't have permission to access the administrator area. 
                        This area is restricted to admin users only.
                    </p>
                    
                    <div class="mt-4">
                        <a href="/BFYL/product" class="btn btn-primary px-4 py-2">
                            <i class="fas fa-home mr-2"></i> Go to Homepage
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include BASE_PATH . '/app/share/footer.php'; ?>
