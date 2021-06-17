<?php
require_once('common/header.php');
?>
<div class="admin-wrapper">

    <div class="container-fluid">





        <?php

        if (isset($_GET['source'])) {
            $source = $_GET['source'];
        } else {
            $source = '';
        }

        switch ($source) {
            case 'categories':
                if (isset($_GET['editCatID'])) {
                    include 'categories_edit.php';
                } else {
                    include 'categories.php';
                }
                break;


            case 'images':
                if (isset($_GET['editImageID'])) {
                    include 'images_edit.php';
                } elseif (isset($_GET['uploadImage'])) {
                    include 'images_upload.php';
                } else {
                    include 'images.php';
                }
                break;


            case 'products':
                if (isset($_GET['editProductID'])) {
                    include 'product_edit.php';
                } elseif (isset($_GET['addProduct'])) {
                    include 'product_add.php';
                } else {
                    include 'products.php';
                }
                break;

            case 'shippingOptions':
                if (isset($_GET['editShippingID'])) {
                    include 'shipping_options_edit.php';
                } else {
                    include 'shipping_options.php';
                }
                break;

            case 'settings':
                include 'settings.php';
                break;

            case 'orders':
                if (isset($_GET['order-info'])) {
                    include "order_info.php";
                } else {
                    include "orders.php";
                }
                break;

            default:
                include "orders.php";
                break;
        }

        ?>
    </div>

</div>









</div>
<!-- Bootstrap Bundle with Popper -->
<script src="../js/bootstrap.bundle.min.js"></script>

<!-- CKEditor -->
<script src="../common/ckeditor5-build-classic/ckeditor.js"></script>
<script src="https://ckeditor.com/apps/ckfinder/3.5.0/ckfinder.js"></script>

<!-- Admin JS -->
<script src="js/adminScripts.min.js"></script>

</body>
</html>


