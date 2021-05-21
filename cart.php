<?php
$errors = [];
$totalCost = 0;
//unset($_SESSION['cart']);
//print_r($_SESSION['cart']);

if (isset($_GET['removeProduct'])) {
    $removeProductID = $_GET['removeProduct'];
    if (is_numeric($removeProductID)) {
        unset($_SESSION['cart'][$removeProductID]);
    }
    header("Location: index.php?source=cart");
}

if (isset($_POST['updateCartBtn'])) {
    $quantity = $_POST['quantity'];
    $productID = $_POST['productID'];

    if (count($quantity) == count($productID)) {
        $arr = array_combine($productID, $quantity);

        foreach ($arr as $productID => $quantity) {
            if (isset($_SESSION['cart'])) {
                $_SESSION['cart'][$productID] = $quantity;
            } else {
                $_SESSION['cart'] = [$productID => $quantity];
            }
        }
    }
}

?>

<div class="container-fluid container-xl mt-3">
    <div class="row">
        <h2>Cart</h2>
        <?php

        if (!isset($_SESSION['cart']) && empty($_SESSION['cart'])){
            ?>
            <div class="callout callout-info alert-info">Your cart is empty. Go back to <a href="index.php">shop</a>.</div>
            <?php
        } else {
            ?>
            <form action="index.php?source=cart" method="post">
            <div class="cart-wrapper d-flex flex-wrap shadow-sm mb-3">


                <?php
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

                                    <div class="cart-quantity-wrapper">
                                        <input type="hidden" name="productID[]" value="<?=$productID?>">
                                        <?php
                                        // check if buying multiple products is allowed
                                        if ($allowMultiplePurchases == 1) {
                                            ?>
                                            <div class="d-flex flex-row">
                                                <input class="btn btn-primary btn-step minus" type="button" value="-">
                                                <input type="number" id="productQuantity-<?=$productID?>" class="product-quantity form-control" step="1" min="1" max="<?=$stock?>" name="quantity[]" value="<?=$quantity?>" aria-label="Quantity of <?=$name?>">
                                                <input class="btn btn-primary btn-step plus" type="button" value="+">
                                            </div>

                                            <?php
                                        } else {
                                            ?>
                                            <input type="hidden" value="1" name="quantity[]">
                                            <?php
                                        }
                                        ?>
                                    </div>

                                    <div class="cart-subtotal">
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

                                    <div class="cart-remove-product">
                                        <button class="btn" name="cartRemoveProduct" value="<?=$productID?>"></button>
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
                <div class="cart-button-wrapper d-flex">
                    <input type="submit" name="updateCartBtn" class="btn btn-primary ms-auto" value="Update cart">
                </div>

            </form>
            <?php
        }
        ?>

        <div class="col-12">
            <div class="cart-summary-wrapper">
                <div class="cart-summary-total">
                    <div class="price-total">
                        Total cost: <?=number_format($totalCost,2)?> $
                    </div>
                </div>

                <div class="cart-summary-checkout">
                    <a href="index.php?source=checkout" type="button" class="btn btn-primary">Proceed to checkout</a>
                </div>
            </div>
        </div>
    </div>
</div>


