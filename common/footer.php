
<div class="footer-margin">&nbsp</div>

<div class="footer">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                Footer
            </div>
        </div>
    </div>
</div>











<!-- Bootstrap Bundle with Popper -->
<script src="js/bootstrap.bundle.min.js"></script>

<!-- Ybox JS -->
<script src="common/Ybox-master/dist/js/directive.js"></script>
<script src="common/Ybox-master/dist/js/yBox.min.js"></script>

<!-- Website JS -->
<script>
    const updateCartCount = function () {
        <?php
        $cartQty = 0;
        if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $qty) {
                $cartQty += $qty;
            }
        }
        ?>
        let cartQty = <?php echo $cartQty ?>;
        const cartCount = document.getElementById('cartCount');
        cartCount.innerHTML = cartQty;
        if (cartQty > 0) {
            cartCount.style.display='block';
        } else {
            cartCount.style.display='none';
        }

    }
    updateCartCount();
</script>

<script src="js/script.js"></script>

<!-- Login/register window JS -->
<?php
if (!isset($_SESSION['userID'])) {
    ?>
    <script src="js/loginWindow.js"></script>
    <?php
}
?>


</body>
</html>