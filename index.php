<?php
require_once('common/header.php');
?>






<?php

if (isset($_GET['productID']) && is_numeric($_GET['productID'])) {
    include 'single_product.php';
} elseif (isset($_GET['source']) && $_GET['source'] == 'cart') {
    include 'cart.php';
} elseif (isset($_GET['source']) && $_GET['source'] == 'checkout' && isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    include 'checkout.php';
} else {
    include 'category.php';
}
?>











<?php
require_once('common/footer.php');
?>


