<?php
$errors = [];


?>

<div class="container-fluid container-xl mt-3">
    <div class="row">
<!--        <div class="col-12">
            <div class="current-location mb-3">
                <?php
/*                $categoryID = $_GET['categoryID'];
                displayCurrentLocation($categoryID, $db);
                */?>
            </div>
        </div>-->

        <?php
        /** Get product **/
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
                if ($published == 1) {
                    // get images for this product
                    $stmt = mysqli_prepare($db, "SELECT * FROM product_image_order WHERE product_id = ? ORDER BY image_order");
                    mysqli_stmt_bind_param($stmt, 'i', $productID);
                    mysqli_stmt_execute($stmt);
                    $res = mysqli_stmt_get_result($stmt);
                    mysqli_stmt_close($stmt);
                    // check if there are any images
                    if (mysqli_num_rows($res) > 0) {
                        $i = 0;
                        echo "<div class='col-lg-8 product-images-container mb-3'>";
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
                                <a href="<?=$imgPath?>">
                                    <img src="<?php echo $i == 0 ? $imgMainScaledPath : $imgThumbnailScaledPath; ?>" alt="<?=$imgAlt?>" title="<?=$imgTitle?>">
                                </a>
                            </div>

                            <?php
                            }
                            $i++;
                        }
                        echo "</div>";
                    }
                    ?>


                    <div class="col-lg-4 product-sidebar sticky-lg-top mb-3">
                        <div class="product-sidebar-content">
                            <h1 class="product-name"><?=$name?></h1>
                            <span class="product-price">
                                <?php
                                if ($priceSale > -1) {
                                    echo "<span class='price-crossed text-muted me-1'>" . $price . "$</span>";
                                    echo "<span class='price'>" . $priceSale . "$</span>";
                                } else {
                                    echo "<span class='price'>" . $price . "$</span>";
                                }
                                ?>
                            </span>

                            <form class="product-add-to-cart" action="#" method="post">
                                <?php
                                if (($stockManage == 1 && $stock > 1 && $allowMultiplePurchases == 1 ) || ($stockManage == 0 && $stockStatus == 1 && $allowMultiplePurchases == 1)) :
                                ?>
                                <div class="d-flex flex-row mb-3">
                                    <input class="btn btn-primary btn-step minus" type="button" value="-">
                                    <input type="number" id="productQuantity" class="product-quantity form-control" step="1" min="1" max="<?=$stock?>" name="quantity" value="1" aria-label="Quantity">
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

