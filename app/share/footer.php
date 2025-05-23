</div>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    $(document).ready(function(){
        // Initialize all toasts with 10 second delay
        $('.toast').toast({delay: 10000});
        $('.toast').toast('show');
        
        <?php if (isset($_SESSION['cart_success_display'])): ?>
            <?php unset($_SESSION['cart_success_display']); ?>
        <?php endif; ?>
    });
</script>

</body>
</html>
