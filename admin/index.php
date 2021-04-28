<?php
require_once('common/header.php');
?>
<div class="admin-wrapper">

    <div class="container-fluid">
        <h1>Hello, admin!</h1>




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
                include 'images.php';
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

<!-- Admin JS -->
<script src="js/adminScripts.js"></script>

</body>
</html>


