<?php
$errors = [];
$success = false;

$float = 12.5;

/** Add shipping option **/
if (isset($_POST['shippingAdd'])) {
    $shippingName = $_POST['shippingOptionName'];
    $shippingPrice = $_POST['shippingPrice'];
    $shippingPrice = str_replace(',', '.', $shippingPrice);

    if (strlen($shippingName) > 100) { array_push($errors, "Shipping option name is too long. Max 100 characters."); }

    if (count($errors) == 0) {
        $stmt = mysqli_prepare($db, "INSERT INTO shipping_options(shipping_option, shipping_price) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, 'sd', $shippingName, $shippingPrice);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

if (isset($_GET['deleteShippingID'])) {
    $shippingID = $_GET['deleteShippingID'];

    if (is_numeric($shippingID)) {
        $stmt = mysqli_prepare($db, "DELETE FROM shipping_options WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $shippingID);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

    } else {
        array_push($errors, "Given shipping ID isn't a numeric value");
    }
}

?>

<div class="row">


    <?php
    displayErrors($errors);
    ?>



    <div class="col-xl-5 mb-3 mb-xl-0">
        <h2>Add new shipping option</h2>
        <form method="post" action="index.php?source=shippingOptions">

            <div class="mb-3">
                <div class="d-flex flex-row"><label for="shippingOptionName" class="form-label">Shipping option</label><div class="required">*</div></div>
                <input type="text" class="form-control" id="shippingOptionName" name="shippingOptionName" maxlength="100" required>
            </div>

            <div class="mb-3">
                <div class="d-flex flex-row"><label for="shippingPrice" class="form-label">Shipping price</label><div class="required">*</div></div>
                <input type="number" step="0.01" class="form-control" id="shippingPrice" name="shippingPrice" required>
            </div>

            <button type="submit" class="btn btn-primary " name="shippingAdd">Submit</button>

        </form>
    </div>




    <div class="col-xl-7">
        <h2>Shipping options</h2>

        <div class="table-responsive">
            <table class="table">
                <thead>
                <tr>
                    <th scope="col">Name</th>
                    <th scope="col">Price</th>
                    <th scope="col">Edit</th>
                    <th scope="col">Delete</th>
                </tr>
                </thead>
                <tbody>

                <?php

                $stmt = mysqli_prepare($db, "SELECT * FROM shipping_options");
                mysqli_stmt_execute($stmt);
                $res = mysqli_stmt_get_result($stmt);
                mysqli_stmt_close($stmt);

                while($resArr = mysqli_fetch_assoc($res)) :

                    ?>
                    <tr>
                        <td><?=$resArr['shipping_option']?></td>
                        <td><?=$resArr['shipping_price']?> <?=getCurrency($db)?></td>
                        <td><a href="index.php?source=shippingOptions&editShippingID=<?=$resArr['id']?>">Edit</a></td>
                        <td><a href="index.php?source=shippingOptions&deleteShippingID=<?=$resArr['id']?>" class="link-danger delete-shipping-link" data-bs-toggle="modal" data-bs-target="#modalShippingDeleteWarning">Delete</a></td>
                    </tr>
                    <?php

                endwhile;
                ?>

                </tbody>
            </table>
        </div>
    </div>

</div>




<!-- Modal - delete shipping option -->
<div class="modal fade" id="modalShippingDeleteWarning" tabindex="-1" aria-labelledby="modalShippingDeleteWarningLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalShippingDeleteWarningLabel">Delete category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this shipping option? This operation cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" id="deleteShippingOptionConfirm">Delete shipping option</button>
            </div>
        </div>
    </div>
</div>

<script>
    window.onload = function() { deleteAndShowModal('delete-shipping-link', 'deleteShippingOptionConfirm') };
</script>
