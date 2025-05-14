<?php
class DefaultController {
    public function index() {
        // Redirect to the product list
        header('Location: /project1/Product/list');
        exit();
    }
}
?>
