<?php
class DefaultController {
    public function index() {
        // First check if user is logged in
        require_once('app/helpers/SessionHelper.php');
        SessionHelper::init();
        
        if (!SessionHelper::isLoggedIn()) {
            // User is not logged in, output JavaScript alert and redirect
            echo '<!DOCTYPE html>
                <html>
                <head>
                    <title>Login Required</title>
                    <script>
                        alert("Bạn cần đăng nhập để xem danh sách sản phẩm");
                        window.location.href = "/BFYL/account/login";
                    </script>
                </head>
                <body></body>
                </html>';
            return;
        }
        
        // Redirect to the product index if user is logged in
        header('Location: /BFYL/Product/');
        exit();
    }
}
?>
