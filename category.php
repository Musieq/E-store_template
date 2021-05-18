<?php
$errors = [];

$limit = 20;
$currentPage = $_GET['page'] ?? 1;
$offset = ($currentPage - 1) * $limit;
$productCountQuery = mysqli_query($db, "SELECT COUNT(id) FROM products WHERE published = 1");
$productCount = mysqli_fetch_array($productCountQuery)[0];
$pages =  ceil($productCount / $limit);

?>

<div class="container-fluid container-xl mt-3">
<div class="row">

    <?php
    displayErrors($errors);
    ?>

    <div class="col-lg-4">
        <div class="sidebar-wrapper shadow-sm">
            <div class="categories-wrapper p-3">
                <h4>Categories</h4>
            </div>


        </div>
    </div>


    <div class="col-lg-8">
        <div class="products">

            <?php

            if (isset($_GET['categoryID'])) {
                $categoryID = $_GET['categoryID'];
                if (is_numeric($categoryID)) {
                    // Show products from this category
                } else {
                    array_push($errors, 'Category has not been found.');
                }
            } elseif (isset($_GET['query'])) {
                // Show found products

            } else {
                // Show all newest products
                echo "<h2 class='mb-3'>Recent products</h2>";
                $productsQuery = mysqli_query($db, "SELECT * FROM products WHERE published = 1 ORDER BY id DESC LIMIT $limit OFFSET $offset");
                if (mysqli_num_rows($productsQuery) > 0) {
                    while ($row = mysqli_fetch_assoc($productsQuery)) {
                        $currentProductID = $row['id'];
                        $imageQuery = mysqli_query($db, "SELECT image_id FROM product_image_order WHERE product_id = $currentProductID ORDER BY image_order LIMIT 1");
                        if (mysqli_num_rows($imageQuery) > 0) {
                            $imageThumbnailID = mysqli_fetch_array($imageQuery)[0];
                            $imagePathQuery = mysqli_query($db, "SELECT path, alt, title FROM images WHERE id = $imageThumbnailID");
                            $imageResult = mysqli_fetch_assoc($imagePathQuery);
                            $path = getScaledImagePath($imageResult['path'], 'thumbnail');
                        }

                        ?>

                        <div class="product-wrapper mb-4 shadow-sm">
                            <a href="index.php?productID=<?= $currentProductID ?>" class="d-flex">
                                <div class="image-wrapper d-flex">
                                    <?php
                                    if (mysqli_num_rows($imageQuery) > 0) {
                                        ?>
                                        <img src="<?=$path?>" alt="<?=$imageResult['alt']?>" title="<?=$imageResult['title']?>" class="m-auto">
                                        <?php
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

                                <div class="product-name">
                                    <h3><?=$row['name']?></h3>
                                </div>

                                <div class="product-price">

                                    <?php
                                        if ($row['price_sale'] > -1) {
                                            echo "<span class='price-crossed text-muted me-1'>" . $row['price'] . "$</span>";
                                            echo "<span class='price'>" . $row['price_sale'] . "$</span>";
                                        } else {
                                            echo "<span class='price'>" . $row['price'] . "$</span>";
                                        }
                                    ?>

                                </div>
                            </a>
                        </div>

                        <?php
                    }
                } else {
                    array_push($errors, "No products found.");
                }
            }

            ?>

        </div>

        <?php
        createPagination("Product pagination", $pages, $currentPage, "index.php");
        ?>
    </div>

</div>
</div>