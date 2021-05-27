<?php

?>

<div class="container-fluid container-xl mt-3">
    <div class="row">
        <h2>Checkout</h2>

        <?php
        if (!isset($_SESSION['userID'])) :
        ?>
        <div class="callout callout-info alert-info">
            Already have an account? <a href="#" id="checkoutLogin">Click here to login</a>
        </div>
        <?php

        endif;
        ?>

    </div>
</div>
