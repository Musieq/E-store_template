<?php
$errors = [];
$orderErrors = [];
$currency = getCurrency($db);

$email = '';
$firstName = '';
$lastName = '';
$phone = '';
$city = '';
$zip = '';
$street = '';
$apartment = '';
$additionalInfo = '';

/** Finalize order **/
if (isset($_POST['btnCheckout'])) {
    $firstName = $_POST['checkoutFirstName'];
    $lastName = $_POST['checkoutLastName'];
    $phone = $_POST['checkoutPhone'];
    $city = $_POST['checkoutCity'];
    $zip = $_POST['checkoutZip'];
    $street = $_POST['checkoutStreet'];
    $apartment = $_POST['checkoutApartment'];
    $shippingOption = $_POST['shippingOption'] ?? '';

    $additionalInfo = $_POST['checkoutAdditionalInfo'];

    // Check if required fields are filled
    if (empty($firstName)) { array_push($orderErrors, "First name is required"); }
    if (empty($lastName)) { array_push($orderErrors, "Last name is required"); }
    if (empty($phone)) { array_push($orderErrors, "Phone number is required"); }
    if (empty($city)) { array_push($orderErrors, "City is required"); }
    if (empty($zip)) { array_push($orderErrors, "Postal code is required"); }
    if (empty($street)) { array_push($orderErrors, "Street is required"); }
    if (empty($apartment)) { array_push($orderErrors, "Apartment is required"); }
    if (empty($shippingOption)) { array_push($orderErrors, "Choose shipping option"); }


    // Check if data is not too long
    if (strlen($firstName) > 50) { array_push($orderErrors, "First name is too long. Max 50 characters."); }
    if (strlen($lastName) > 50) { array_push($orderErrors, "Last name is too long. Max 50 characters."); }
    if (strlen($phone) > 20) { array_push($orderErrors, "Phone number is too long. Max 20 characters."); }
    if (strlen($city) > 100) { array_push($orderErrors, "City is too long. Max 100 characters."); }
    if (strlen($zip) > 10) { array_push($orderErrors, "Postal code is too long. Max 10 characters."); }
    if (strlen($street) > 100) { array_push($orderErrors, "Street is too long. Max 100 characters."); }
    if (strlen($apartment) > 25) { array_push($orderErrors, "Apartment is too long. Max 25 characters."); }


    if (count($orderErrors) == 0) {
        // Init variable with total order cost
        $totalOrderCost = 0;

        // Check if products from cart are available
        foreach ($_SESSION['cart'] as $id => $quantity) {
            $stmt = mysqli_prepare($db, "SELECT name, price, price_sale, stock, stock_status, stock_manage FROM products WHERE id = ?");
            mysqli_stmt_bind_param($stmt, 'i', $id);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            mysqli_stmt_close($stmt);

            if (mysqli_num_rows($res) == 1) {
                while ($resArr = mysqli_fetch_assoc($res)) {
                    $name = $resArr['name'];
                    $price = $resArr['price'];
                    $priceSale = $resArr['price_sale'];
                    $stock = $resArr['stock'];
                    $stockStatus = $resArr['stock_status'];
                    $stockManage = $resArr['stock_manage'];

                    // Add product price to total order cost
                    if ($priceSale != -1) {
                        $totalOrderCost += $priceSale * $quantity;
                    } else {
                        $totalOrderCost += $price * $quantity;
                    }

                    if ($stockManage == 0) {
                        if ($stockStatus == 0) {
                            array_push($orderErrors, "Product $name is no longer available. It was deleted from your cart.");
                        }
                    } else {
                        if ($quantity > $stock) {
                            array_push($orderErrors, "Product $name does not have required quantity available. It's quantity was set to maximum available.");
                            $_SESSION['cart'][$id] = $stock;
                        } elseif ($stock == 0) {
                            array_push($orderErrors, "Product $name is no longer available. It was deleted from your cart.");
                        }
                    }
                }
            } else {
                unset($_SESSION['cart'][$id]);
                array_push($orderErrors, "One of your product is no longer available. It was deleted from your cart.");

            }
        }


        // Proceed if all products are available
        if (count($orderErrors) == 0) {
            // Check if user logged in - true = no registration
            if (isset($_SESSION['userID'])) {
                $userID = $_SESSION['userID'];

            } else {
                $email = $_POST['checkoutEmail'];
                $password = $_POST['checkoutPassword'];
                $agreement = '';
                if(isset($_POST['checkoutAgree'])) { $agreement = $_POST['checkoutAgree']; }

                // Check for required fields
                if (empty($email)) { array_push($orderErrors, 'Email is required'); }
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    array_push($orderErrors, 'Invalid email');
                }
                if (strlen($email) > 60) { array_push($orderErrors, "Email is too long. Max 60 characters."); }

                if (empty($password)) { array_push($orderErrors, 'Password is required'); }
                if (strlen($password) < 7) { array_push($orderErrors, 'Password is too short'); }

                if (!$agreement) { array_push($orderErrors, 'You have to accept our terms of service'); }


                // Check if user with this email already exist
                $stmt = mysqli_prepare($db, "SELECT email FROM users WHERE email = ?");
                mysqli_stmt_bind_param($stmt, 's', $email);
                mysqli_stmt_execute($stmt);
                $res = mysqli_stmt_get_result($stmt);
                mysqli_stmt_close($stmt);

                if (mysqli_num_rows($res) == 0) {
                    // Register user if email is free
                    createAccount($db, $password, $email, $firstName, $lastName, $phone, $city, $zip, $street, $apartment);

                    // Get user id
                    $userID = $_SESSION['userID'];
                } else {
                    array_push($orderErrors, "User with given email address already exists. Choose different email.");
                }
            }


            // If user was logged in or registration was successful - proceed
            if (count($orderErrors) == 0) {
                // Get cost of shipping
                $stmt = mysqli_prepare($db, "SELECT shipping_price FROM shipping_options WHERE id = ?");
                mysqli_stmt_bind_param($stmt, 'i', $shippingOption);
                mysqli_stmt_execute($stmt);
                $res = mysqli_stmt_get_result($stmt);
                mysqli_stmt_close($stmt);

                if (mysqli_num_rows($res) == 1) {
                    $shippingCost = mysqli_fetch_array($res)[0];
                    $totalOrderCost += $shippingCost;
                } else {
                    array_push($orderErrors, "Shipping option was not found. Please refresh page.");
                }

                // Proceed if shipping option was found
                if (count($orderErrors) == 0) {
                    // Add order to "orders" table
                    $stmt = mysqli_prepare($db, "INSERT INTO orders (user_id, order_cost, shipping_id, first_name, last_name, city, street, postal_code, apartment, telephone, additional_info) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    mysqli_stmt_bind_param($stmt, 'idissssssss', $userID, $totalOrderCost, $shippingOption, $firstName, $lastName, $city, $street, $zip, $apartment, $phone, $additionalInfo);
                    mysqli_stmt_execute($stmt);
                    $orderID = mysqli_stmt_insert_id($stmt);
                    mysqli_stmt_close($stmt);

                    // Add products to "orders_products" table
                    foreach ($_SESSION['cart'] as $productID => $quantity) {
                        $stmt = mysqli_prepare($db, "INSERT INTO orders_products (order_id, product_id, quantity) VALUES (?, ?, ?)");
                        mysqli_stmt_bind_param($stmt, 'iii', $orderID, $productID, $quantity);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_close($stmt);

                        // Get current stock
                        $stmt = mysqli_prepare($db, "SELECT stock FROM products WHERE id = ?");
                        mysqli_stmt_bind_param($stmt, 'i', $productID);
                        mysqli_stmt_execute($stmt);
                        $res = mysqli_stmt_get_result($stmt);
                        mysqli_stmt_close($stmt);

                        $currentStock = mysqli_fetch_array($res)[0];


                        // Update stock in "products" table
                        if ($currentStock != -1) {
                            $currentStock = $currentStock - $quantity;
                            $stmt = mysqli_prepare($db, "UPDATE products SET stock = ? WHERE id = ?");
                            mysqli_stmt_bind_param($stmt, 'ii', $currentStock, $productID);
                            mysqli_stmt_execute($stmt);
                            mysqli_stmt_close($stmt);
                        }

                        unset($_SESSION['cart']);

                        echo "
                        <script>
                        window.location.href = 'my-account.php?source=orders&order-successful=$orderID';
                        </script>
                        ";
                    }
                }
            }
        }
    }
}


/** Get user data **/

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
        if (isset($_SESSION['cart'])) :

        displayErrors($orderErrors);
        // Log in info
        if (!isset($_SESSION['userID'])) :
        ?>
        <div class="col-12">
            <div class="callout callout-info alert-info">
                Already have an account? <a href="#" id="checkoutLogin">Click here to login</a>
            </div>
        </div>
        <?php
        endif;
        ?>

        <h4 class="mt-4">Billing details</h4>

        <form method="post" action="index.php?source=checkout">
            <div class="form-width-700">

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
                    <textarea type="text" class="form-control" id="checkoutAdditionalInfo" name="checkoutAdditionalInfo" rows="4"><?=$additionalInfo?></textarea>
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
                                <input class="form-check-input" type="radio" name="shippingOption" id="shippingOption<?=$resArr['id']?>" value="<?=$resArr['id']?>" data-price="<?=$resArr['shipping_price']?>" >
                                <label class="form-check-label" for="shippingOption<?=$resArr['id']?>">
                                    <?=$resArr['shipping_option']?>: <?=$resArr['shipping_price']?> <?=$currency?>
                                </label>
                            </div>
                        <?php
                        endwhile;

                        ?>
                    </div>
                </div>


                <?php
                // Create account if not logged in
                if (!isset($_SESSION['userID'])) :
                    ?>

                    <div class="mb-3">
                        <div class="d-flex flex-row"><label for="checkoutEmail" class="form-label">Email address</label><div class="required">*</div></div>
                        <input type="email" class="form-control" id="checkoutEmail" name="checkoutEmail" maxlength="60" autocomplete="email" value="<?=$email?>" required>
                    </div>

                    <div class="mb-3">
                        <div class="d-flex flex-row"><label for="checkoutPassword" class="form-label">Password</label><div class="required">*</div></div>
                        <input type="password" class="form-control" id="checkoutPassword" name="checkoutPassword" autocomplete="new-password" minlength="7" required>
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="checkoutAgree" name="checkoutAgree" required>
                        <div class="d-flex flex-row"><label class="form-check-label" for="checkoutAgree">I agree to the terms of service</label><div class="required">*</div></div>
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
                                                echo "<div class='priceQty'>".number_format($priceSale * $quantity, 2)." $currency</div>";
                                                echo "<div class='priceEach'>".number_format($priceSale, 2)." $currency each</div>";
                                            } else {
                                                echo "<div class='priceSingleQty'>".number_format($priceSale, 2)." $currency</div>";
                                            }
                                        } else {
                                            if ($allowMultiplePurchases == 1 && $quantity > 1) {
                                                echo "<div class='priceQty'>".number_format($price * $quantity, 2)." $currency</div>";
                                                echo "<div class='priceEach'>".number_format($price, 2)." $currency each</div>";
                                            } else {
                                                echo "<div class='priceSingleQty'>".number_format($price, 2)." $currency</div>";
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
                    const currency = '<?=$currency?>';
                </script>
            </div>


            <div class="checkout-btn">
                <button type="submit" class="btn btn-primary" name="btnCheckout">Place order</button>
            </div>

        </form>

        <?php
        endif;
        ?>

    </div>
</div>
