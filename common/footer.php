
<div class="footer">

</div>











<!-- Bootstrap Bundle with Popper -->
<script src="js/bootstrap.bundle.min.js"></script>

<!-- Website JS -->
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