<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css">
    <style>
        .sidebar {
            min-height: calc(100vh - 56px);
            background-color: #343a40;
            color: white;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.75);
        }
        .sidebar .nav-link:hover {
            color: #fff;
        }
        .sidebar .nav-link.active {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.1);
        }
        .content {
            padding: 20px;
        }
        .stats-card {
            border-left: 4px solid;
            border-radius: 4px;
        }
        .stats-card.primary {
            border-left-color: #4e73df;
        }
        .stats-card.success {
            border-left-color: #1cc88a;
        }
        .stats-card.info {
            border-left-color: #36b9cc;
        }
        .stats-card.warning {
            border-left-color: #f6c23e;
        }
        .table-responsive {
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="/BFYL/admin">Admin Dashboard</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/BFYL/product" target="_blank">
                        <i class="fas fa-store"></i> View Store
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/BFYL/account/logout">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </nav>
    
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2 sidebar pt-3">
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['REQUEST_URI']) == 'admin' ? 'active' : ''; ?>" href="/BFYL/admin">
                            <i class="fas fa-tachometer-alt mr-2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], 'products') !== false ? 'active' : ''; ?>" href="/BFYL/admin/products">
                            <i class="fas fa-box mr-2"></i> Products
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], 'categories') !== false ? 'active' : ''; ?>" href="/BFYL/admin/categories">
                            <i class="fas fa-folder mr-2"></i> Categories
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], 'orders') !== false ? 'active' : ''; ?>" href="/BFYL/admin/orders">
                            <i class="fas fa-shopping-cart mr-2"></i> Orders
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], 'promotions') !== false ? 'active' : ''; ?>" href="/BFYL/admin/promotions">
                            <i class="fas fa-percent mr-2"></i> Promotions
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], 'shipping') !== false ? 'active' : ''; ?>" href="/BFYL/admin/shipping">
                            <i class="fas fa-truck mr-2"></i> Shipping
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], 'customersupport') !== false ? 'active' : ''; ?>" href="/BFYL/admin/customersupport">
                            <i class="fas fa-headset mr-2"></i> Customer Support
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo strpos($_SERVER['REQUEST_URI'], 'content') !== false ? 'active' : ''; ?>" href="/BFYL/admin/content">
                            <i class="fas fa-file-alt mr-2"></i> Content
                        </a>
                    </li>
                 
                </ul>
            </div>
            <div class="col-md-10 content">
                <!-- Content will be injected here -->
                <?php if (isset($content)) { echo $content; } ?>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html> 