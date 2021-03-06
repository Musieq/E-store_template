<?php
$errors = [];

/** Add to cart **/
if(isset($_POST['productAddToCart'])) {
    // Zainicjowanie zmiennej przechowującej ilość produktów
    $productQuantity = 1;
    // Sprawdzenie czy użytkownik zmienił ilość produktów i przypisanie jej do zmiennej
    if (isset($_POST['quantity'])) {
        $productQuantity = $_POST['quantity'];
    }
    // Pobranie ID produktu z adresu URL
    $productID = $_GET['productID'];
    // Stworzenie zmiennej sesyjnej lub przypisanie do niej nowych danych
    if (isset($_SESSION['cart'])) {
        $_SESSION['cart'][$productID] = $productQuantity;
    } else {
        $_SESSION['cart'] = [$productID => $productQuantity];
    }

}
?>

<div class="container-fluid container-xl mt-3">
    <div class="row">
        <div class="col-12">
            <div class="current-location mb-3">
                <?php
                $categoryID = $_GET['categoryID'] ?? 0;
                displayCurrentLocation($categoryID, $db);
                ?>
            </div>
        </div>

        <?php
        /** Get product **/
        $currency = getCurrency($db);
        $productID = $_GET['productID'];
        if (is_numeric($_GET['productID'])) {
            $stmt = mysqli_prepare($db, "SELECT * FROM products WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "i", $productID);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            mysqli_stmt_close($stmt);
            // check if product exists
            if (mysqli_num_rows($res) == 1) {
                $resArr = mysqli_fetch_assoc($res);
                // get product info from DB
                $name = $resArr['name'];
                $description = $resArr['description'];
                $price = $resArr['price'];
                $priceSale = $resArr['price_sale'];
                $stock = $resArr['stock']; // if -1 - ignore, if 0 or more - that's the stock left
                $stockStatus = $resArr['stock_status']; // if -1 - ignore, if 0 - out of stock, if 1 - in stock
                $stockManage = $resArr['stock_manage']; // if 0 - check stockStatus, if 1 - check if in stock
                $allowMultiplePurchases = $resArr['allow_multiple_purchases'];
                $published = $resArr['published'];

                // check if product is published
                if ($published == 1 || $_SESSION['userRole'] == 'admin') {
                    // get images for this product
                    $stmt = mysqli_prepare($db, "SELECT * FROM product_image_order WHERE product_id = ? ORDER BY image_order");
                    mysqli_stmt_bind_param($stmt, 'i', $productID);
                    mysqli_stmt_execute($stmt);
                    $res = mysqli_stmt_get_result($stmt);
                    mysqli_stmt_close($stmt);
                    // check if there are any images
                    echo "<div class='col-lg-8 product-images-container mb-3'>";
                    if (mysqli_num_rows($res) > 0) {
                        $i = 0;
                        while ($resArr = mysqli_fetch_assoc($res)) {
                            $imageID = $resArr['image_id'];

                            $stmt = mysqli_prepare($db, "SELECT title, alt, path FROM images WHERE id = ?");
                            mysqli_stmt_bind_param($stmt, 'i', $imageID);
                            mysqli_stmt_execute($stmt);
                            $resImg = mysqli_stmt_get_result($stmt);
                            mysqli_stmt_close($stmt);

                            // display images
                            while ($resImgArr = mysqli_fetch_assoc($resImg)) {
                                $imgTitle = $resImgArr['title'];
                                $imgAlt =$resImgArr['alt'];
                                $imgPath = $resImgArr['path'];
                                $imgMainScaledPath = getScaledImagePath($imgPath, 'main-image');
                                $imgThumbnailScaledPath = getScaledImagePath($imgPath, 'thumbnail');
                            ?>

                            <div class="product-single-image">
                                <a href="<?=$imgPath?>" class="yBox" data-ybox-group="group1">
                                    <img src="<?php echo $i == 0 ? $imgMainScaledPath : $imgThumbnailScaledPath; ?>" alt="<?=$imgAlt?>" title="<?=$imgTitle?>">
                                </a>
                            </div>

                            <?php
                            }
                            $i++;
                        }
                    } else {
                        ?>
                            <div class="product-single-image-placeholder">
                                <span>Placeholder</span>
                            </div>
                        <?php
                    }
                    echo "</div>";
                    ?>


                    <div class="col-lg-4 product-sidebar sticky-lg-top mb-3">
                        <div class="product-sidebar-content">
                            <h1 class="product-name"><?=$name?></h1>
                            <span class="product-price">
                                <?php
                                if ($priceSale > -1) {
                                    echo "<span class='price-crossed text-muted me-1'>" . $price . "$currency</span>";
                                    echo "<span class='price'>" . $priceSale . "$currency</span>";
                                } else {
                                    echo "<span class='price'>" . $price . "$currency</span>";
                                }
                                ?>
                            </span>

                            <form id="addToCartForm" class="product-add-to-cart" action="index.php?<?php echo http_build_query($_GET) ?>" method="post">
                                <?php
                                if (($stockManage == 1 && $stock > 1 && $allowMultiplePurchases == 1 ) || ($stockManage == 0 && $stockStatus == 1 && $allowMultiplePurchases == 1)) :
                                    $max = $stock == -1 ? 999 : $stock;
                                ?>
                                <div class="d-flex flex-row mb-3">
                                    <input class="btn btn-primary btn-step minus" type="button" value="-">
                                    <input type="number" id="productQuantity" class="product-quantity form-control" step="1" min="1" max="<?=$max?>" name="quantity" value="1" aria-label="Quantity">
                                    <input class="btn btn-primary btn-step plus" type="button" value="+">
                                </div>
                                <?php
                                elseif (($stockManage == 1 && $stock == 1) || ($stockManage == 0 && $stockStatus == 1)) :
                                ?>
                                <div class="text-success mb-3 fw-bold">
                                    In stock
                                </div>
                                <?php
                                elseif (($stockManage == 1 && $stock == 0) || ($stockManage == 0 && $stockStatus == 0)) :
                                ?>
                                <div class="text-danger mb-3 fw-bold">
                                Out of stock
                                </div>
                                <?php
                                endif;
                                ?>

                                <button type="submit" name="productAddToCart" class="btn btn-primary w-100 mb-3" <?php if (($stockManage == 1 && $stock == 0) || ($stockManage == 0 && $stockStatus == 0)) { echo 'disabled'; } ?>>Add to cart</button>
                            </form>
                        </div>
                    </div>


                    <div class="col-lg-8">
                        <?=$description?>
                    </div>


                    <div class="col-lg-8 mt-3">
                        <div class="shipping-options">
                            <h4>Shipping options</h4>
                            <?php
                            // Shipping options

                            $stmt = mysqli_prepare($db, "SELECT * FROM shipping_options");
                            mysqli_stmt_execute($stmt);
                            $res = mysqli_stmt_get_result($stmt);
                            mysqli_stmt_close($stmt);

                            while ($resArr = mysqli_fetch_assoc($res)) :
                                ?>
                                <div class="shipping-option">
                                    <?=$resArr['shipping_option']?>: <?=$resArr['shipping_price']?> <?=$currency?>
                                </div>
                            <?php
                            endwhile;

                            ?>
                        </div>
                    </div>


                    <?php
                } else {
                    array_push($errors, "Product has not been found.");
                }

            } else {
                array_push($errors, "Product has not been found.");
            }

        } else {
            array_push($errors, "Product has not been found.");
        }


        displayErrors($errors);
        ?>



    </div>
</div>

<div class="modal fade" tabindex="-1" id="modalAddedToCart" aria-labelledby="modalAddedToCartTitle" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAddedToCartTitle">Product added</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>You have added this product to a cart.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <a href="index.php?source=cart" class="btn btn-primary">Go to cart</a>
            </div>
        </div>
    </div>
</div>