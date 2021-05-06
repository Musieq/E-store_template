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
                if(isset($_GET['addProduct'])) {
                    include 'product_add.php';
                } else {
                    include 'products.php';
                }
                break;


            default:
                ?>

                <p>Dashboard</p>

                <?php
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
<script src="js/adminScripts.js"></script>

</body>
</html>


