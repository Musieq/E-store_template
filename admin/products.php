<?php
?>


<!-- Search products by name, category and status -->
<form class="row form-width-700 g-3 mb-3" method="get">
    <input type="hidden" name="source" value="products">

    <div class="col-md-4">
        <label for="productFilterName" class="form-label">Search for a product by name</label>
        <input type="text" class="form-control" id="productFilterName" name="productFilterName" value="<?=$_GET['productFilterName'] ?? ''?>">
    </div>

    <div class="col-md-4">
        <label for="productFilterCategory" class="form-label">Category</label>
        <select class="form-select" id="productFilterCategory" name="productFilterCategory">
            <?php
            $productFilterCatID = $_GET['productFilterCategory'] ?? 0;
            ?>
            <option value="-1" <?php if ($productFilterCatID == 0) echo 'selected' ?>>All products</option>
            <?php
            categoriesHierarchyInSelectField($db, $productFilterCatID);
            ?>
        </select>
    </div>

    <div class="col-md-4">
        <label for="productFilterPublished" class="form-label">Published?</label>
        <select class="form-select" id="productFilterPublished" name="productFilterPublished">
            <?php
            $productFilterPublishedID = $_GET['productFilterPublished'] ?? -1;
            ?>
            <option value="-1" <?php if ($productFilterPublishedID == -1) echo 'selected' ?>>All products</option>
            <option value="0" <?php if ($productFilterPublishedID == 0) echo 'selected' ?>>Not published</option>
            <option value="1" <?php if ($productFilterPublishedID == 1) echo 'selected' ?>>Published</option>
        </select>
    </div>

    <div class="col-12">
        <button type="submit" class="btn btn-primary" name="productsFilterSubmit" value="filter">Filter</button>
    </div>
</form>





<!-- Bulk delete form -->
<form class="row row-cols-lg-auto g-3" id="productBulkDeleteForm" name="productBulkDeleteForm" method="post" action="index.php?<?=http_build_query(array_merge($_GET))?>">

    <div class="col-12">
        <label for="productBulkOption" class="form-label visually-hidden">Bulk action</label>
        <select class="form-select" id="productBulkOption" name="productBulkOption">
            <option value="0" selected>Bulk action</option>
            <option value="1">Delete all</option>
        </select>
    </div>

    <div class="col-12">
        <button type="button" class="btn btn-primary" name="productBulkDelete" onclick="bulkDeleteModal('productBulkDeleteForm', 'productBulkOption', 'modalProductDeleteWarning', 'deleteProductConfirm')">Submit</button>
    </div>



    <div class="table-responsive" style="width: 100%">
        <table class="table table-products">
            <thead>
            <tr>
                <th scope="col"><input type="checkbox" class="form-check-input" aria-label="Check to delete every product shown below" id="productDeleteSelectAll" onclick="selectCheckboxes(this.id, 'productDeleteCheckbox[]')"></th>
                <th scope="col">Image</th>
                <th scope="col">Name</th>
                <th scope="col">Stock</th>
                <th scope="col">Price</th>
                <th scope="col">Edit</th>
                <th scope="col">Delete</th>
            </tr>
            </thead>
            <tbody>

            <?php
            // Limit for products per page
            $limit = 50;
            $currentPage = $_GET['page'] ?? 1;
            $offset = ($currentPage - 1) * $limit;

            // Check if filters are applied
            $productFilterName = '';
            $productFilterCategory = -1;
            $productFilterPublished = -1;
            if (isset($_GET['productsFilterSubmit'])) {
                $productFilterName = $_GET['productFilterName'];
                $productFilterCategory = $_GET['productFilterCategory'];
                $productFilterPublished = $_GET['productFilterPublished'];
            }

            // Create query depending on filters
            // Filter by category first
            if ($productFilterCategory != -1) {
                if ($productFilterPublished != -1 && $productFilterName == '') {
                    $stmt = mysqli_prepare($db, "SELECT * FROM products, product_category WHERE products.published = ? AND product_category.category_id LIKE ? AND products.id = product_category.product_id ORDER BY id DESC LIMIT $limit OFFSET $offset");
                    mysqli_stmt_bind_param($stmt, "ii", $productFilterPublished, $productFilterCategory);

                    $stmtCount = mysqli_prepare($db, "SELECT products.id FROM products, product_category WHERE products.published = ? AND product_category.category_id LIKE ? AND products.id = product_category.product_id");
                    mysqli_stmt_bind_param($stmtCount, "ii", $productFilterPublished, $productFilterCategory);
                } elseif ($productFilterPublished == -1 && $productFilterName != '') {
                    $stmt = mysqli_prepare($db, "SELECT * FROM products, product_category WHERE products.name LIKE ? AND product_category.category_id LIKE ? AND products.id = product_category.product_id ORDER BY id DESC LIMIT $limit OFFSET $offset");
                    mysqli_stmt_bind_param($stmt, "si", $productFilterName, $productFilterCategory);

                    $stmtCount = mysqli_prepare($db, "SELECT products.id FROM products, product_category WHERE products.name LIKE ? AND product_category.category_id LIKE ? AND products.id = product_category.product_id");
                    mysqli_stmt_bind_param($stmtCount, "si", $productFilterName, $productFilterCategory);
                } elseif ($productFilterPublished != -1 && $productFilterName != '') {
                    $stmt = mysqli_prepare($db, "SELECT * FROM products, product_category WHERE products.name LIKE ? AND products.published = ? AND product_category.category_id LIKE ? AND products.id = product_category.product_id ORDER BY id DESC LIMIT $limit OFFSET $offset");
                    mysqli_stmt_bind_param($stmt, "sii", $productFilterName, $productFilterPublished, $productFilterCategory);

                    $stmtCount = mysqli_prepare($db, "SELECT products.id FROM products, product_category WHERE products.name LIKE ? AND products.published = ? AND product_category.category_id LIKE ? AND products.id = product_category.product_id");
                    mysqli_stmt_bind_param($stmtCount, "sii", $productFilterName, $productFilterPublished, $productFilterCategory);
                } else {
                    $stmt = mysqli_prepare($db, "SELECT * FROM products, product_category WHERE product_category.category_id LIKE ? AND products.id = product_category.product_id ORDER BY id DESC LIMIT $limit OFFSET $offset");
                    mysqli_stmt_bind_param($stmt, "i",$productFilterCategory);

                    $stmtCount = mysqli_prepare($db, "SELECT products.id FROM products, product_category WHERE product_category.category_id LIKE ? AND products.id = product_category.product_id");
                    mysqli_stmt_bind_param($stmtCount, "i",$productFilterCategory);
                }
            } else {
                if ($productFilterPublished != -1 && $productFilterName == '') {
                    $stmt = mysqli_prepare($db, "SELECT * FROM products WHERE published = ? ORDER BY id DESC LIMIT $limit OFFSET $offset");
                    mysqli_stmt_bind_param($stmt, "i", $productFilterPublished);

                    $stmtCount = mysqli_prepare($db, "SELECT id FROM products WHERE published = ?");
                    mysqli_stmt_bind_param($stmtCount, "i", $productFilterPublished);
                } elseif ($productFilterPublished == -1 && $productFilterName != '') {
                    $stmt = mysqli_prepare($db, "SELECT * FROM products WHERE products.name LIKE ? ORDER BY id DESC LIMIT $limit OFFSET $offset");
                    mysqli_stmt_bind_param($stmt, "s", $productFilterName);

                    $stmtCount = mysqli_prepare($db, "SELECT id FROM products WHERE name LIKE ?");
                    mysqli_stmt_bind_param($stmtCount, "s", $productFilterName);
                } elseif ($productFilterPublished != -1 && $productFilterName != '') {
                    $stmt = mysqli_prepare($db, "SELECT * FROM products WHERE name LIKE ? AND published = ? ORDER BY id DESC LIMIT $limit OFFSET $offset");
                    mysqli_stmt_bind_param($stmt, "si", $productFilterName, $productFilterPublished);

                    $stmtCount = mysqli_prepare($db, "SELECT id FROM products WHERE name LIKE ? AND published = ?");
                    mysqli_stmt_bind_param($stmtCount, "si", $productFilterName, $productFilterPublished);
                } else {
                    $stmt = mysqli_prepare($db, "SELECT * FROM products ORDER BY id DESC LIMIT $limit OFFSET $offset");

                    $stmtCount = mysqli_prepare($db, "SELECT id FROM products");
                }
            }

            // Count pages
            mysqli_stmt_execute($stmtCount);
            $productCountResults = mysqli_stmt_get_result($stmtCount);
            mysqli_stmt_close($stmtCount);
            $productCount = mysqli_num_rows($productCountResults);

            // Calculate how many pages are there
            $pages = ceil($productCount / $limit);

            // Get products to display
            mysqli_stmt_execute($stmt);
            $productsResults = mysqli_stmt_get_result($stmt);
            mysqli_stmt_close($stmt);
            while ($row = mysqli_fetch_assoc($productsResults)) {
                $productID = $row['id'];
                // Get image thumbnail
                $getMainImageQuery = mysqli_query($db, "SELECT path FROM images INNER JOIN product_image_order ON product_image_order.image_id = images.id WHERE product_image_order.product_id = $productID");
                if (mysqli_num_rows($getMainImageQuery) == 1) {
                    $mainImagePath = '../'.mysqli_fetch_array($getMainImageQuery)[0];
                    $thumbnailPath = getScaledImagePath($mainImagePath, 'thumbnail');
                } else {
                    $mainImagePath = '#';
                    $thumbnailPath = '#';
                }
                // Create URL when deleting images after applying filters
                $deleteURL = http_build_query(array_merge($_GET,['deleteProductID' => $row['id']]));
                ?>
                <tr>
                    <td><input type="checkbox" name="productDeleteCheckbox[]" class="form-check-input" value="<?=$row['id']?>" aria-label="Check for bulk deleting product <?=$row['name']?>"></td>
                    <td><a href="<?=$mainImagePath?>">
                            <div class="admin-image-container" style="background-image: url('<?=$thumbnailPath?>') "></div>
                        </a>
                    </td>
                    <td><a href="../index.php?productID=<?=$row['id']?>"><?=$row['name']?></a></td>
                    <td>
                        <?php
                        if ($row['stock_manage'] == 0) {
                            if ($row['stock_status'] == 0) {
                                echo "<div class=\"text-danger mb-3 fw-bold\">
                                Out of stock
                                </div>";
                            } else {
                                echo "<div class=\"text-success mb-3 fw-bold\">
                                    In stock
                                </div>";
                            }
                        } else {
                            if ($row['stock'] == 0) {
                                echo "<div class=\"text-danger mb-3 fw-bold\">
                                Out of stock ({$row['stock']})
                                </div>";
                            } else {
                                echo "<div class=\"text-success mb-3 fw-bold\">
                                    In stock ({$row['stock']})
                                </div>";
                            }
                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        if ($row['price_sale'] == -1) {
                            echo "<span>{$row['price']} $</span>";
                        } else {
                            echo "<span class='text-decoration-line-through wrap'>{$row['price']} $</span><br>";
                            echo "<span>{$row['price_sale']} $</span>";
                        }
                        ?>
                    </td>
                    <td><a href="index.php?source=products&editProductID=<?=$row['id']?>">Edit</td>
                    <td><a href="index.php?<?=$deleteURL?>" class="link-danger delete-product-link" data-bs-toggle="modal" data-bs-target="#modalProductDeleteWarning">Delete</td>
                </tr>
                <?php
            }

            ?>
            </tbody>
        </table>
    </div>



</form>

<?php
// Create pagination if there is more than 1 page
createPagination('Pagination for images in product add', $pages, $currentPage, 'index.php');
?>



<!-- Modal - delete product -->
<div class="modal fade" id="modalProductDeleteWarning" tabindex="-1" aria-labelledby="modalProductDeleteWarningLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalProductDeleteWarningLabel">Delete category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this product? This operation cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" id="deleteProductConfirm">Delete product</button>
            </div>
        </div>
    </div>
</div>

<script>
    window.onload = function() { deleteAndShowModal('delete-product-link', 'deleteProductConfirm') };
</script>
