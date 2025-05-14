</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<?php if (isset($_SESSION['cart_success_display']) && $_SESSION['cart_success_display']): ?>
<script>
    $(document).ready(function(){
        $('.toast').toast('show');
        <?php unset($_SESSION['cart_success_display']); ?>
    });
</script>
<?php endif; ?>

</body>
</html>
