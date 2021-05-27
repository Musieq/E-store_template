<?php
$errors = [];
$success = false;

$shippingID = $_GET['editShippingID'];
$shippingName = '';
$shippingPrice = '';

if (is_numeric($shippingID)) {
    /** Update shipping option **/
    if (isset($_POST['shippingEdit'])) {
        $shippingName = $_POST['shippingOptionName'];
        $shippingPrice = $_POST['shippingPrice'];
        $shippingPrice = str_replace(',', '.', $shippingPrice);

        if (strlen($shippingName) > 100) { array_push($errors, "Shipping option name is too long. Max 100 characters."); }

        if (count($errors) == 0) {
            $stmt = mysqli_prepare($db, "UPDATE shipping_options SET shipping_option = ?, shipping_price = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, 'sdi', $shippingName, $shippingPrice, $shippingID);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
        }
    }


    /** Get shipping option **/
    $stmt = mysqli_prepare($db, "SELECT * FROM shipping_options WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $shippingID);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);

    if (mysqli_num_rows($res) > 0) {
        while ($resArr = mysqli_fetch_assoc($res)) {
            $shippingName = $resArr['shipping_option'];
            $shippingPrice = $resArr['shipping_price'];
        }
    } else {
        array_push($errors, 'Shipping option with given ID was not found.');
    }
} else {
    array_push($errors, 'Given shipping ID is not a number.');
}


?>

<div class="row">

    <?php
    if ($success) :
        ?>
        <div class="col-12">
            <div class="alert-success">
                <p><strong>Shipping option updated.</strong></p>
                <p><a href="index.php?source=shippingOptions">Go back to shipping options</a></p>
            </div>
        </div>
    <?php
    endif;
    ?>

    <?php
    displayErrors($errors);
    ?>

    <div class="col-12">
        <h2>Edit shipping option</h2>

        <form class="form-width-700" method="post" action="index.php?source=shippingOptions&editShippingID=<?=$shippingID?>">
            <div class="mb-3">
                <div class="d-flex flex-row"><label for="shippingOptionName" class="form-label">Shipping option</label><div class="required">*</div></div>
                <input type="text" class="form-control" id="shippingOptionName" name="shippingOptionName" maxlength="100" value="<?=$shippingName?>" required>
            </div>

            <div class="mb-3">
                <div class="d-flex flex-row"><label for="shippingPrice" class="form-label">Shipping price</label><div class="required">*</div></div>
                <input type="number" step="0.01" class="form-control" id="shippingPrice" name="shippingPrice" value="<?=$shippingPrice?>" required>
            </div>

            <button type="submit" class="btn btn-primary " name="shippingEdit">Submit</button>
        </form>
    </div>
</div>
