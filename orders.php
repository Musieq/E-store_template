<?php
$errors = [];
$userID = $_SESSION['userID'];
$currency = getCurrency($db);

/** Get bank info **/
$stmt = mysqli_prepare($db, "SELECT * FROM settings");
mysqli_stmt_execute($stmt);
$resBank = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);

$settingsArr = [];

while ($resBankArr = mysqli_fetch_assoc($resBank)) {
    $settingsArr[$resBankArr['setting_name']] = $resBankArr['value'];
}

?>

<h2 class="mb-3">Orders</h2>


<div class="row">
    <div class="col-12">

        <div class="orders-wrapper shadow-sm">


        <?php
        /** Get orders **/
        $orderedProductsCost = 0;
        $stmt = mysqli_prepare($db, "SELECT order_id, order_cost, first_name, last_name, city, street, postal_code, apartment, telephone, additional_info, order_status, order_date FROM orders WHERE user_id = ? ORDER BY order_id DESC");
        mysqli_stmt_bind_param($stmt, 'i', $userID);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);

        // Get order if exists
        if (mysqli_num_rows($res) > 0) {
            ?>
            <div class="my-account-orders-wrapper">
            <?php
            while ($resArr = mysqli_fetch_assoc($res)) {
                $orderCostNoShipping = 0;

                $orderID = $resArr['order_id'];
                $orderCost = $resArr['order_cost'];
                $shipmentFirstName = $resArr['first_name'];
                $shipmentLastName = $resArr['last_name'];
                $shipmentCity = $resArr['city'];
                $shipmentStreet = $resArr['street'];
                $shipmentZip = $resArr['postal_code'];
                $shipmentApartment = $resArr['apartment'];
                $shipmentPhone = $resArr['telephone'];
                $additionalInfo = $resArr['additional_info'];
                $orderStatus = $resArr['order_status'];
                $orderDate = $resArr['order_date'];

                ?>
                <div class="single-order">
                    <div class="order-id">
                        Order ID: <?=$orderID?>
                    </div>
                    <div class="order-date">
                        <?=$orderDate?>
                    </div>
                <?php

                // Get ordered products
                $stmt = mysqli_prepare($db, "SELECT product_id, quantity, current_price, products.name FROM orders_products INNER JOIN products ON orders_products.product_id = products.id WHERE order_id = ?");
                mysqli_stmt_bind_param($stmt, 'i', $orderID);
                mysqli_stmt_execute($stmt);
                $res2 = mysqli_stmt_get_result($stmt);
                mysqli_stmt_close($stmt);
                while ($resArr2 = mysqli_fetch_assoc($res2)) {
                    $productID = $resArr2['product_id'];
                    $quantity = $resArr2['quantity'];
                    $currentPrice = $resArr2['current_price'];
                    $productName = $resArr2['name'];

                    $orderCostNoShipping += $quantity * $currentPrice;

                    // Get product thumbnail
                    $stmt = mysqli_prepare($db, "SELECT title, alt, path FROM product_image_order INNER JOIN images ON product_image_order.image_id = images.id WHERE product_id = ? ORDER BY image_order LIMIT 1");
                    mysqli_stmt_bind_param($stmt, 'i', $productID);
                    mysqli_stmt_execute($stmt);
                    $res3 = mysqli_stmt_get_result($stmt);
                    ?>
                    <div class="single-product">
                    <?php
                    if (mysqli_num_rows($res3) > 0) {
                        while ($resArr3 = mysqli_fetch_assoc($res3)) {

                            $imgTitle = $resArr3['title'];
                            $imgAlt = $resArr3['alt'];
                            $imgPath = $resArr3['path'];

                            $thumbnailPath = getScaledImagePath($imgPath, 'thumbnail');
                        ?>
                            <div class="order-product-image">
                                <img src="<?=$thumbnailPath?>" alt="<?=$imgAlt?>" title="<?=$imgTitle?>">
                            </div>
                            <?php
                        }
                    } else {
                        ?>
                        <div class="order-product-image">
                            <div class="placeholder m-auto d-flex">
                                <span class="m-auto">Placeholder</span>
                            </div>
                        </div>
                        <?php
                    }
                    
                    ?>
                    <div class="order-product-name">
                        <a href="index.php?productID=<?=$productID?>"><?=$productName?></a>
                    </div>
                    <div class="order-product-quantity">
                        <?=$quantity?>x
                    </div>
                    <div class="order-product-price">
                        <?php
                        if ($quantity > 1) {
                            echo "<div class='priceQty'>".number_format($currentPrice * $quantity, 2)." $currency</div>";
                            echo "<div class='priceEach'>".number_format($currentPrice, 2)." $currency each</div>";
                        } else {
                            echo "<div class='priceSingleQty'>".number_format($currentPrice, 2)." $currency</div>";
                        }
                        ?>
                    </div>
                    </div>
                    <?php


                }
                ?>
                    <div class="order-shipping-cost">
                        Shipping cost: <?=$orderCost - $orderCostNoShipping.' '.$currency?>
                    </div>

                    <div class="order-total-cost">
                        Total cost: <?=$orderCost.' '.$currency?>
                    </div>

                    <div class="order-additional-info">
                        <h6>Your additional order information</h6>
                        <?=!empty($additionalInfo) ? $additionalInfo : 'None'?>
                    </div>

                    <div class="order-payment-info">
                        <h6>Payment information</h6>
                        <span class="payment-title"><strong>Payment title: Order ID <?=$orderID?></strong></span>
                        <span class="payment-name"><?=isset($settingsArr['payment_name']) && !empty($settingsArr['payment_name']) ? '<strong>Payment name:</strong> '.$settingsArr['payment_name'] : ''?></span>
                        <span class="bank-name"><?=isset($settingsArr['bank_name']) && !empty($settingsArr['bank_name']) ? '<strong>Bank name:</strong> '.$settingsArr['bank_name'] : ''?></span>
                        <span class="payment-account"><?=isset($settingsArr['account_number']) && !empty($settingsArr['account_number']) ? '<strong>Account number:</strong> '.$settingsArr['account_number'] : ''?></span>
                        <span class="sort-code"><?=isset($settingsArr['sort_code']) && !empty($settingsArr['sort_code']) ? '<strong>Sort code:</strong> '.$settingsArr['sort_code'] : ''?></span>
                        <span class="iban"><?=isset($settingsArr['iban']) && !empty($settingsArr['iban']) ? '<strong>IBAN:</strong> '.$settingsArr['iban'] : ''?></span>
                        <span class="bic-swift"><?=isset($settingsArr['bic_swift']) && !empty($settingsArr['bic_swift']) ? '<strong>BIC / Swift:</strong> '.$settingsArr['bic_swift'] : ''?></span>
                    </div>

                    <div class="order-shipping-info">
                        <h6>Shipping address</h6>
                        <span class="shipping-first-last-name"><?=$shipmentFirstName?> <?=$shipmentLastName?></span>
                        <span class="shipping-street"><?=$shipmentStreet?> <?=$shipmentApartment?></span>
                        <span class="shipping-city"><?=$shipmentCity?> <?=$shipmentZip?></span>
                        <span class="shipping-phone"><?=$shipmentPhone?></span>
                    </div>

                    <div class="order-status">
                        <h6>Order status</h6>
                        <?=$orderStatus?>
                    </div>

                </div>

                <hr class="break">
                <?php
            }
            ?>
            </div>
            <?php
        } else {
            ?>
            <div class="callout callout-info alert-info">
                You don't have any orders yet. Go to <a href="index.php">shop</a>.
            </div>
            <?php
        }
        ?>

        </div>
    </div>
</div>
