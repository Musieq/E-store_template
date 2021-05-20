<?php
$errors = [];

$limit = 20;
$currentPage = $_GET['page'] ?? 1;
$offset = ($currentPage - 1) * $limit;
$productCountQuery = mysqli_query($db, "SELECT COUNT(id) FROM products WHERE published = 1");
$productCount = mysqli_fetch_array($productCountQuery)[0];
$pages =  ceil($productCount / $limit);


function sortBy(): string {
    $order = '';
    if (isset($_GET['order'])) {
        $order = $_GET['order'];
    }
    switch ($order) {
        case 'price':
            $prepQuery = " ORDER BY curPrice";
            break;

        case 'priceDesc':
            $prepQuery = " ORDER BY curPrice DESC";
            break;

        default:
            $prepQuery = " ORDER BY id DESC";
            break;
    }

    return $prepQuery;
}
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

            <div class="products-top-bar">
            <?php

            // TODO sort by price etc.
            if (isset($_GET['categoryID'])) {
                $categoryID = $_GET['categoryID'];
                if (is_numeric($categoryID)) {
                    // Show products from this category
                    $prepQuery = "SELECT *, IF(price_sale > -1, price_sale, price) as curPrice FROM products, product_category WHERE products.published = 1 AND product_category.category_id LIKE ? AND products.id = product_category.product_id";
                    $prepQuery .= sortBy();
                    $prepQuery .= " LIMIT $limit OFFSET $offset";
                    $stmt = mysqli_prepare($db, $prepQuery);
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
                echo "<h2 class='mb-3'>Search results for: $query</h2>";
                $query = '%'.$query.'%';
                $prepQuery = "SELECT *, IF(price_sale > -1, price_sale, price) as curPrice FROM products WHERE published = 1 AND (name LIKE ? OR tags LIKE ?)";
                $prepQuery .= sortBy();
                $prepQuery .= " LIMIT $limit OFFSET $offset";
                $stmt = mysqli_prepare($db, $prepQuery);
                mysqli_stmt_bind_param($stmt, 'ss', $query, $query);
                mysqli_stmt_execute($stmt);
                $productsQuery = mysqli_stmt_get_result($stmt);
                mysqli_stmt_close($stmt);
            } else {
                // Show all newest products
                echo "<h2 class='mb-3'>Recent products</h2>";
                $prepQuery = "SELECT *, IF(price_sale > -1, price_sale, price) as curPrice FROM products WHERE published = 1";
                $prepQuery .= sortBy();
                $prepQuery .= " LIMIT $limit OFFSET $offset";
                $productsQuery = mysqli_query($db, $prepQuery);
            }

            ?>
            <form action="index.php" method="get">
                <?php
                foreach ($_GET as $key => $value) {
                    if ($key != 'page' || $key != 'order') {
                        echo "<input type='hidden' name='$key' value='$value'>";
                    }
                }
                ?>
                <div class="mb-3">
                    <label for="order" class="form-label">Sort by</label>
                    <select class="form-select" id="order" name="order" onchange="this.form.submit()">
                        <option value="id" <?php if (isset($_GET['order']) && $_GET['order'] == 'id') echo 'selected'; ?>>Newest products</option>
                        <option value="price" <?php if (isset($_GET['order']) && $_GET['order'] == 'price') echo 'selected'; ?>>Price low to high</option>
                        <option value="priceDesc" <?php if (isset($_GET['order']) && $_GET['order'] == 'priceDesc') echo 'selected'; ?>>Price high to low</option>
                    </select>
                </div>
            </form>
            </div>
            <?php


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