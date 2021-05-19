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
                <?php
                if (isset($_GET['categoryID'])) {
                    $catID = $_GET['categoryID'];
                    $catNameQuery = mysqli_prepare($db, "SELECT category_name, parent_id FROM categories WHERE category_id = ?");
                    mysqli_stmt_bind_param($catNameQuery, 'i', $catID);
                    mysqli_stmt_execute($catNameQuery);
                    $catNameResult = mysqli_stmt_get_result($catNameQuery);
                    mysqli_stmt_close($catNameQuery);
                    if (mysqli_num_rows($catNameResult) == 1) {
                        while ($row = mysqli_fetch_assoc($catNameResult)) {
                            $catName = $row['category_name'];
                            $catParent = $row['parent_id'];
                        }
                        if ($catParent != 0) {
                            $parentNameQuery = mysqli_query($db, "SELECT category_name FROM categories WHERE category_id = $catParent");
                            $parentName = mysqli_fetch_array($parentNameQuery)[0];
                            echo "Back to <a class='categories-back' href='index.php?categoryID=$catParent'>$parentName</a>";
                        } else {
                            echo "Back to <a class='categories-back' href='index.php'>Categories</a>";
                        }

                        echo "<h4>$catName</h4>";
                    }
                } else {
                    echo "<h4>Categories</h4>";
                }
                ?>

                <ul class="nav flex-column categories">
                <?php
                if (isset($_GET['categoryID'])) {
                    $parentID = $_GET['categoryID'];
                    if (is_numeric($parentID)) {
                        $categoriesQuery = mysqli_prepare($db, "SELECT category_name, category_id FROM categories WHERE parent_id = ?");
                    }
                } else {
                    $parentID = 0;
                    $categoriesQuery = mysqli_prepare($db, "SELECT category_name, category_id FROM categories WHERE parent_id = ?");
                }

                mysqli_stmt_bind_param($categoriesQuery, 'i', $parentID);
                mysqli_stmt_execute($categoriesQuery);
                $categoriesResults = mysqli_stmt_get_result($categoriesQuery);
                mysqli_stmt_close($categoriesQuery);
                while ($row = mysqli_fetch_assoc($categoriesResults)) {
                    ?>
                    <li class="nav-item">
                        <a href="index.php?categoryID=<?=$row['category_id']?>" class="nav-link"><?=$row['category_name']?></a>
                    </li>
                    <?php
                }
                ?>
                </ul>
            </div>

            filters
        </div>
    </div>


    <div class="col-lg-8">
        <div class="products">

            <?php
            // TODO sort by price etc.
            if (isset($_GET['categoryID'])) {
                $categoryID = $_GET['categoryID'];
                if (is_numeric($categoryID)) {
                    // Show products from this category
                    $stmt = mysqli_prepare($db, "SELECT * FROM products, product_category WHERE products.published = 1 AND product_category.category_id LIKE ? AND products.id = product_category.product_id ORDER BY id DESC LIMIT $limit OFFSET $offset");
                    mysqli_stmt_bind_param($stmt, 'i', $categoryID);
                    mysqli_stmt_execute($stmt);
                    $productsQuery = mysqli_stmt_get_result($stmt);
                    mysqli_stmt_close($stmt);
                } else {
                    array_push($errors, 'Category has not been found.');
                }
            } elseif (isset($_GET['query'])) {
                // Show found products
                $query = $_GET['query'];
                $query = '%'.$query.'%';
                $stmt = mysqli_prepare($db, "SELECT * FROM products WHERE published = 1 AND name LIKE ? ORDER BY id DESC LIMIT $limit OFFSET $offset");
                mysqli_stmt_bind_param($stmt, 's', $query);
                mysqli_stmt_execute($stmt);
                $productsQuery = mysqli_stmt_get_result($stmt);
                mysqli_stmt_close($stmt);
            } else {
                // Show all newest products
                echo "<h2 class='mb-3'>Recent products</h2>";
                $productsQuery = mysqli_query($db, "SELECT * FROM products WHERE published = 1 ORDER BY id DESC LIMIT $limit OFFSET $offset");

            }


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

            ?>

        </div>

        <?php
        createPagination("Product pagination", $pages, $currentPage, "index.php");
        ?>
    </div>

</div>
</div>