<?php
$errors = [];

/** Get user data **/
$email = '';
$firstName = '';
$lastName = '';
$phone = '';
$city = '';
$zip = '';
$street = '';
$apartment = '';

if (isset($_SESSION['userID'])) {
    $email = $_SESSION['userEmail'];

    $stmt = mysqli_prepare($db, "SELECT * FROM user_informations WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $_SESSION['userID']);
    mysqli_stmt_execute($stmt);
    $res = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);

    while ($resArr = mysqli_fetch_assoc($res)) {
        $firstName = $resArr['first_name'];
        $lastName = $resArr['last_name'];
        $phone = $resArr['telephone'];
        $city = $resArr['city'];
        $zip = $resArr['postal_code'];
        $street = $resArr['street'];
        $apartment = $resArr['apartment'];
    }
}

?>

<div class="container-fluid container-xl mt-3">
    <div class="row">
        <h2>Checkout</h2>

        <?php
        displayErrors($errors);
        // Log in info
        if (!isset($_SESSION['userID'])) :
        ?>
        <div class="callout callout-info alert-info">
            Already have an account? <a href="#" id="checkoutLogin">Click here to login</a>
        </div>
        <?php
        endif;
        ?>

        <h4 class="mt-4">Billing details</h4>

        <form method="post" action="index.php?source=checkout">
            <div class="form-width-700">
                <div class="mb-3">
                    <div class="d-flex flex-row"><label for="checkoutEmail" class="form-label">Email address</label><div class="required">*</div></div>
                    <input type="email" class="form-control" id="checkoutEmail" name="checkoutEmail" maxlength="60" autocomplete="email" value="<?=$email?>" required>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-sm">
                        <div class="d-flex flex-row"><label for="checkoutFirstName" class="form-label">First name</label><div class="required">*</div></div>
                        <input type="text" class="form-control" id="checkoutFirstName" name="checkoutFirstName" maxlength="50" autocomplete="given-name" value="<?=$firstName?>" required>
                    </div>
                    <div class="col-sm">
                        <div class="d-flex flex-row"><label for="checkoutLastName" class="form-label">Last name</label><div class="required">*</div></div>
                        <input type="text" class="form-control" id="checkoutLastName" name="checkoutLastName" maxlength="50" autocomplete="family-name" value="<?=$lastName?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex flex-row"><label for="checkoutPhone" class="form-label">Phone number</label><div class="required">*</div></div>
                    <input type="text" class="form-control" id="checkoutPhone" name="checkoutPhone" maxlength="20" autocomplete="tel" value="<?=$phone?>" required>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-sm-8">
                        <div class="d-flex flex-row"><label for="checkoutCity" class="form-label">City</label><div class="required">*</div></div>
                        <input type="text" class="form-control" id="checkoutCity" name="checkoutCity" maxlength="100" autocomplete="address-level2" value="<?=$city?>" required>
                    </div>
                    <div class="col-sm-4">
                        <div class="d-flex flex-row"><label for="checkoutZip" class="form-label">Zip</label><div class="required">*</div></div>
                        <input type="text" class="form-control" id="checkoutZip" name="checkoutZip" maxlength="10" autocomplete="postal-code" value="<?=$zip?>" required>
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-sm-8">
                        <div class="d-flex flex-row"><label for="checkoutStreet" class="form-label">Street</label><div class="required">*</div></div>
                        <input type="text" class="form-control" id="checkoutStreet" name="checkoutStreet" maxlength="100" autocomplete="address-line1" value="<?=$street?>" required>
                    </div>
                    <div class="col-sm-4">
                        <div class="d-flex flex-row"><label for="checkoutApartment" class="form-label">Apartment</label><div class="required">*</div></div>
                        <input type="text" class="form-control" id="checkoutApartment" name="checkoutApartment" maxlength="25" autocomplete="address-line2" value="<?=$apartment?>" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="checkoutAdditionalInfo" class="form-label">Additional information</label>
                    <textarea type="text" class="form-control" id="checkoutAdditionalInfo" name="checkoutAdditionalInfo" rows="4"></textarea>
                </div>

                <div class="mb-3">
                    <div class="d-flex flex-row"><label for="shippingOptions" class="form-label">Shipping options</label><div class="required">*</div></div>
                    <div class="checkout-shipping-options" id="shippingOptions">
                        <?php
                        // Shipping options

                        $stmt = mysqli_prepare($db, "SELECT * FROM shipping_options");
                        mysqli_stmt_execute($stmt);
                        $res = mysqli_stmt_get_result($stmt);
                        mysqli_stmt_close($stmt);

                        while ($resArr = mysqli_fetch_assoc($res)) :
                            ?>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="shippingOption" id="shippingOption<?=$resArr['id']?>" value="<?=$resArr['id']?>" data-price="<?=$resArr['shipping_price']?>">
                                <label class="form-check-label" for="shippingOption<?=$resArr['id']?>">
                                    <?=$resArr['shipping_option']?>: <?=$resArr['shipping_price']?>$
                                </label>
                            </div>
                        <?php
                        endwhile;

                        ?>
                    </div>
                </div>


                <?php
                // Create account if not logged in and user wants it
                if (!isset($_SESSION['userID'])) :
                    ?>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="checkoutCreateAcc" name="checkoutCreateAcc">
                        <label class="form-check-label" for="checkoutCreateAcc">Create account?</label>
                    </div>

                    <div class="checkout-create-account" id="checkoutCreateAccountContainer">
                        <div class="mb-3">
                            <div class="d-flex flex-row"><label for="checkoutPassword" class="form-label">Password</label><div class="required">*</div></div>
                            <input type="password" class="form-control" id="checkoutPassword" name="checkoutPassword" autocomplete="new-password" minlength="7" required>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="checkoutAgree" name="checkoutAgree" required>
                            <div class="d-flex flex-row"><label class="form-check-label" for="checkoutAgree">I agree to the terms of service</label><div class="required">*</div></div>
                        </div>
                    </div>

                <?php
                endif;
                ?>
            </div>



            <h4 class="mt-4">Order summary</h4>

            <div class="cart-wrapper d-flex flex-wrap shadow-sm mb-3">


                <?php
                $totalCost = 0;
                foreach ($_SESSION['cart'] as $productID => $quantity) {
                    $stmt = mysqli_prepare($db, "SELECT name, price, price_sale, stock, stock_status, stock_manage, allow_multiple_purchases, published FROM products WHERE id = ?");
                    mysqli_stmt_bind_param($stmt, 'i', $productID);
                    mysqli_stmt_execute($stmt);
                    $productResults = mysqli_stmt_get_result($stmt);
                    mysqli_stmt_close($stmt);

                    // Get product data
                    if (mysqli_num_rows($productResults) == 1) {
                        while ($row = mysqli_fetch_assoc($productResults)) {
                            $name = $row['name'];
                            $price = $row['price'];
                            $priceSale = $row['price_sale'];
                            $stock = $row['stock'];
                            $stockStatus = $row['stock_status'];
                            $stockManage = $row['stock_manage'];
                            $allowMultiplePurchases = $row['allow_multiple_purchases'];
                            $published = $row['published'];

                            // Calculate total cost
                            if ($priceSale != -1) {
                                $totalCost += $priceSale * $quantity;
                            } else {
                                $totalCost += $price * $quantity;
                            }

                            if ($published == 1 && ($stock > 0 || $stockStatus == 1)) {
                                if ($stockStatus == 1) {
                                    $stock = 999;
                                }
                                // Get image
                                $stmt = mysqli_prepare($db, "SELECT title, alt, path FROM images INNER JOIN product_image_order ON product_image_order.image_id = images.id WHERE product_image_order.product_id = ? ORDER BY product_image_order.image_order LIMIT 1");
                                mysqli_stmt_bind_param($stmt, 'i', $productID);
                                mysqli_stmt_execute($stmt);
                                $imageResults = mysqli_stmt_get_result($stmt);
                                mysqli_stmt_close($stmt);

                                ?>
                                <div class="cart-product">

                                    <div class="cart-image-wrapper">
                                        <?php
                                        // display image thumbnail
                                        if (mysqli_num_rows($imageResults) == 1) {
                                            while ($row = mysqli_fetch_assoc($imageResults)) {
                                                $path = getScaledImagePath($row['path'], 'thumbnail');
                                                $alt = $row['alt'];
                                                $title = $row['title'];
                                                ?>
                                                <img src="<?=$path?>" alt="<?=$alt?>" title="<?=$title?>">
                                                <?php
                                            }
                                        } else {
                                            ?>
                                            <div class="placeholder m-auto d-flex">
                                                <span class="m-auto">
                                                    Placeholder
                                                </span>
                                            </div>
                                            <?php
                                        }
                                        ?>
                                    </div>

                                    <div class="cart-name-wrapper">
                                        <a href="index.php?productID=<?= $productID ?>"><?=$name?></a>
                                    </div>

                                    <div class="cart-quantity-no-edit-wrapper">
                                        <span class="my-auto">x <?=$quantity?></span>
                                    </div>

                                    <div class="cart-subtotal-no-edit">
                                        <?php
                                        if ($priceSale != -1) {
                                            if ($allowMultiplePurchases == 1 && $quantity > 1) {
                                                echo "<div class='priceQty'>".number_format($priceSale * $quantity, 2)." $</div>";
                                                echo "<div class='priceEach'>".number_format($priceSale, 2)." $ each</div>";
                                            } else {
                                                echo "<div class='priceSingleQty'>".number_format($priceSale, 2)." $</div>";
                                            }
                                        } else {
                                            if ($allowMultiplePurchases == 1 && $quantity > 1) {
                                                echo "<div class='priceQty'>".number_format($price * $quantity, 2)." $</div>";
                                                echo "<div class='priceEach'>".number_format($price, 2)." $ each</div>";
                                            } else {
                                                echo "<div class='priceSingleQty'>".number_format($price, 2)." $</div>";
                                            }
                                        }
                                        ?>
                                    </div>

                                </div>
                                <hr class="break">

                                <?php
                            } else {
                                unset($_SESSION['cart'][$productID]);
                                array_push($errors, "Product ".$name." is no longer available. It was deleted from your cart.");
                            }
                        }
                    } else {
                        unset($_SESSION['cart'][$productID]);
                        array_push($errors, "One of your product is no longer available. It was deleted from your cart.");
                    }
                }
                ?>

                <div class="cart-errors-wrapper">
                    <?php
                    displayErrors($errors);
                    ?>
                </div>


            </div>


            <div class="checkout-total" id="checkoutTotalElement">
                <script>
                    let checkoutTotal = <?=$totalCost?>;
                    // TODO currency here
                    const currency = '$';
                </script>
            </div>


            <div class="checkout-btn">
                <button type="submit" class="btn btn-primary" name="btnCheckout">Place order</button>
            </div>

        </form>

    </div>
</div>
