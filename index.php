<?php
require_once('common/header.php');
?>






<?php

if (isset($_GET['productID']) && is_numeric($_GET['productID'])) {
    include 'single_product.php';
} else {
    include 'category.php';
}
?>











<?php
require_once('common/footer.php');
?>


