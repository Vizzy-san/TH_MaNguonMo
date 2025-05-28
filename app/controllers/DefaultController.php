<?php
class DefaultController {
    public function index() {
        // Redirect to the product list
        header('Location: /BFYL/Product/list');
        exit();
    }
}
?>
