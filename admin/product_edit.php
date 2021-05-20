<?php
$errors = [];
$success = false;
$editProductID = $_GET['editProductID'];

// Init variables
$productName = '';
$productDescription = '';
$productTags = '';
$productPrice = '';
$productPriceSale = '';
$stockManage = '';
$stock = '';
$stockStatus = '';
$allowMultiplePurchases = '';
$published = '';
$imagesArr = [];
$categoriesArr = [];


/** Update product **/
if (isset($_POST['productEdit'])) {
    $productName = $_POST['editProductName'];
    if (!empty($productName)) {

        $productImages = $_POST['productImagesInput'];
        $productDescription = $_POST['productDescription'];
        $productTags = $_POST['productTags'];
        $productCategories = $_POST['addProductCategory'] ?? 0;  // array with categories
        $productPrice = $_POST['editProductPrice'];
        $productSalePrice = $_POST['editProductSalePrice'];
        $productSalePrice = $productSalePrice > 0 ? $productSalePrice: -1;
        if (!empty($productPrice) && is_numeric($productPrice) && is_numeric($productSalePrice)) {
            $productManageStock = $_POST['productManageStock'] ?? 0;
            if ($productManageStock) {
                $productManageStock = 1;
                $productStockStatus = -1;
                $productStock = $_POST['editProductStock'];
            } else {
                $productStockStatus = $_POST['editProductStockStatus'];
                $productStock = -1;
            }
            $productStatus = $_POST['editProductStatus'];
            if (isset($_POST['editProductAllowMultiplePurchases'])) {
                $allowMultiplePurchases = 1;
            } else {
                $allowMultiplePurchases = 0;
            }


            // Update products
            $stmt = mysqli_prepare($db, "UPDATE products SET name = ?, description = ?, tags = ?, price = ?, price_sale = ?, stock = ?, stock_status = ?, stock_manage = ?, allow_multiple_purchases = ?, published = ? WHERE id = ?");
            mysqli_stmt_bind_param($stmt, "sssddiiiiii", $productName, $productDescription, $productTags, $productPrice, $productSalePrice, $productStock, $productStockStatus, $productManageStock, $allowMultiplePurchases, $productStatus, $editProductID);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            // Update images table
            if (!empty($productImages)) {
                // Delete existing images for this products
                $stmt = mysqli_prepare($db, "DELETE FROM product_image_order WHERE product_id = ?");
                mysqli_stmt_bind_param($stmt, 'i', $editProductID);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);

                // Insert selected images
                $productImagesArray = explode(',', $productImages);
                foreach ($productImagesArray as $imageOrder => $imageID) {
                    $stmt = mysqli_prepare($db, "INSERT INTO product_image_order (product_id, image_id, image_order) VALUES (?, ?, ?)");
                    mysqli_stmt_bind_param($stmt, "iii", $editProductID, $imageID, $imageOrder);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                }
            }

            // Insert into categories table
            if(!empty($productCategories)) {
                // Delete existing categories for this products
                $stmt = mysqli_prepare($db, "DELETE FROM product_category WHERE product_id = ?");
                mysqli_stmt_bind_param($stmt, 'i', $editProductID);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);

                // Insert selected categories
                foreach ($productCategories as $productCategory) {
                    $stmt = mysqli_prepare($db, "INSERT INTO product_category (product_id, category_id) VALUES (?, ?)");
                    mysqli_stmt_bind_param($stmt, "ii", $editProductID, $productCategory);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                }
            }

            $success = true;

        } else {
            array_push($errors, 'Price cannot be empty');
        }
    } else {
        array_push($errors, 'Product name cannot be empty');
    }
}


/** Get product info **/
if (is_numeric($editProductID)) {
    $stmt = mysqli_prepare($db, "SELECT * FROM products WHERE id = ?");
    mysqli_stmt_bind_param($stmt, 'i', $editProductID);
    mysqli_stmt_execute($stmt);
    $productResults = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);

    while ($row = mysqli_fetch_assoc($productResults)) {
        $productName = $row['name'];
        $productDescription = $row['description'];
        $productTags = $row['tags'];
        $productPrice = $row['price'];
        $productPriceSale = $row['price_sale'];
        $stockManage = $row['stock_manage'];
        $stock = $row['stock'];
        $stockStatus = $row['stock_status'];
        $allowMultiplePurchases = $row['allow_multiple_purchases'];
        $published = $row['published'];

        // Get images
        $stmt = mysqli_prepare($db, "SELECT image_id FROM product_image_order WHERE product_id = ? ORDER BY  image_order");
        mysqli_stmt_bind_param($stmt, 'i', $editProductID);
        mysqli_stmt_execute($stmt);
        $imagesResults = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);

        // Create array with images ID
        if (mysqli_num_rows($imagesResults) > 0) {
            while ($row = mysqli_fetch_assoc($imagesResults)) {
                array_push($imagesArr, $row['image_id']);
            }
        }

        // Get categories
        $stmt = mysqli_prepare($db, "SELECT category_id FROM product_category WHERE product_id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $editProductID);
        mysqli_stmt_execute($stmt);
        $categoriesResults = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);
        // Create array with categories ID
        if (mysqli_num_rows($categoriesResults) > 0) {
            while ($row = mysqli_fetch_assoc($categoriesResults)) {
                array_push($categoriesArr, $row['category_id']);
            }
        }



    }
} else {
    array_push($errors, "Given product ID isn't a numeric value.");
}

?>


<div class="row">

    <?php
    if ($success) :
        ?>
        <div class="col-12">
            <div class="callout callout-success alert-success">
                <p><strong>Product edited successfully.</strong></p>
                <p><a href="index.php?source=products">Go back to products</a></p>
            </div>
        </div>
    <?php
    endif;
    ?>


    <?php
    displayErrors($errors);
    ?>



    <div class="col-12">
        <h2>Edit product</h2>
        <!-- TODO form validation -->
        <form method="post" action="index.php?source=products&editProductID=<?=$_GET['editProductID']?>">

            <div class="mb-3">
                <div class="d-flex flex-row"><label for="editProductName" class="form-label">Product name</label><div class="required">*</div></div>
                <input type="text" class="form-control" id="editProductName" name="editProductName" value="<?=$productName?>">
            </div>

            <div class="mb-3">
                Product images
                <div class="callout callout-info alert-info">
                    First image will be product's main image. You can drag and drop selected images to set their order. Image is dropped before selected one.
                </div>
                <div class="container-images-draggable">
                    <ul id="containerImagesDraggable" class="selected-product-images"><?php
                        /** Display images **/
                        foreach ($imagesArr as $imageID) {
                            $stmt = mysqli_prepare($db, "SELECT title, path FROM images WHERE id = ?");
                            mysqli_stmt_bind_param($stmt, 'i', $imageID);
                            mysqli_stmt_execute($stmt);
                            $imageResults = mysqli_stmt_get_result($stmt);
                            mysqli_stmt_close($stmt);

                            while ($row = mysqli_fetch_assoc($imageResults)) {
                                $path = getScaledImagePath($row['path'], 'thumbnail');
                                ?><li tabindex="0" aria-label="<?=$row['title']?>>" class="imageList list-unstyled" data-id="<?=$imageID?>" draggable="true">
                                    <div class="admin-image-container" style="background-image: url('../<?=$path?>') "></div>
                                    <div class="removeSelectedImg">x</div>
                                </li><?php
                            }
                        }

                        ?></ul>
                    <input type="hidden" id="productImagesInput" name="productImagesInput" value="<?php foreach ($imagesArr as $key => $imageID) {
                        if ($key > 0) {
                            echo ", ".$imageID;
                        } else {
                            echo $imageID;
                        }
                    } ?>">
                </div>

                <a href="#" id="showProductImagesModal">Select product images</a>
            </div>

            <div class="mb-3">
                <label for="productDescription" class="form-label">Product description</label>
                <textarea name="productDescription" id="productDescription"><?=$productDescription?></textarea>
            </div>

            <div class="mb-3">
                <label for="productTags" class="form-label">Product tags</label>
                <input type="text" class="form-control" id="productTags" name="productTags" aria-describedby="productTagsHelp" value="<?=$productTags?>">
                <div id="productTagsHelp" class="form-text">Separate tags with commas. Tags are used for searching products.</div>
            </div>

            <div class="mb-3">
                <label for="editProductCategory" class="form-label">Product categories</label>
                <ul id="editProductCategory" class="select-product-category list-unstyled">
                    <?php
                    categoriesHierarchyInProducts($db, 0);
                    ?>
                    <script>
                        let productCategories = document.getElementsByName('addProductCategory[]');
                        let selectedCategoriesArray = <?php echo json_encode($categoriesArr); ?>;
                        productCategories.forEach(el => {
                            let catID = parseInt(el.value);
                            if (selectedCategoriesArray.includes(catID)) {
                                el.checked = true;
                            }
                        })
                    </script>
                </ul>
            </div>

            <div class="row mb-3">
                <div class="col-lg-6">
                    <div class="d-flex flex-row"><label for="editProductPrice" class="form-label">Price</label><div class="required">*</div></div>
                    <input type="text" class="form-control" id="editProductPrice" name="editProductPrice" value="<?=$productPrice?>">
                </div>

                <div class="col-lg-6">
                    <div class="d-flex flex-row"><label for="editProductSalePrice" class="form-label">Sale price</label></div>
                    <input type="text" class="form-control" id="editProductSalePrice" name="editProductSalePrice" value="<?php if ($productPriceSale != -1) echo $productPriceSale ?>">
                </div>
            </div>


            <div class="mb-3">
                <input type="checkbox" class="form-check-input" id="productManageStock" name="productManageStock" aria-describedby="manageStockHelp">
                <label class="form-check-label" for="productManageStock">Manage stock?</label>
                <div id="manageStockHelp" class="form-text">Check if you want to input exact stock for this products.</div>
            </div>

            <div class="mb-3">
                <div id="noStockManagement" style="display: block">
                    <label for="editProductStockStatus" class="form-label">Stock status</label>
                    <select class="form-select" id="editProductStockStatus" name="editProductStockStatus">
                        <option value="0" <?php if ($stockStatus == 0) echo 'selected' ?>>Out of stock</option>
                        <option value="1" <?php if ($stockStatus == 1) echo 'selected' ?>>In stock</option>
                    </select>
                </div>

                <div id="stockManagement" style="display: none">
                    <div class="d-flex flex-row"><label for="editProductStock" class="form-label">Stock</label><div class="required">*</div></div>
                    <input type="number" class="form-control" id="editProductStock" name="editProductStock" value="<?php if ($stock != -1) echo $stock ?>">
                </div>
            </div>

            <script>
                let noStockManagement = document.getElementById('noStockManagement');
                let stockManagement = document.getElementById('stockManagement');
                let productManageStock = document.getElementById('productManageStock');
                let manageStock = <?php echo $stockManage; ?>;
                if (manageStock == 1) {
                    productManageStock.checked = true;
                    noStockManagement.style.display = 'none';
                    stockManagement.style.display = 'block';
                }
            </script>

            <div class="mb-3">
                <input type="checkbox" class="form-check-input" id="editProductAllowMultiplePurchases" name="editProductAllowMultiplePurchases" aria-describedby="multipleHelp">
                <label class="form-check-label" for="editProductAllowMultiplePurchases">Allow multiple purchases?</label>
                <div id="multipleHelp" class="form-text">Check if you want to allow clients to select quantity when purchasing this product.</div>
                <script>
                    let editProductAllowMultiplePurchases = document.getElementById('editProductAllowMultiplePurchases');
                    let allowMultiplePurchases = <?php echo $allowMultiplePurchases; ?>;
                    if (allowMultiplePurchases == 1) {
                        editProductAllowMultiplePurchases.checked = true;
                    }
                </script>
            </div>

            <div class="mb-3">
                <label for="editProductStatus" class="form-label">Product status</label>
                <select class="form-select" id="editProductStatus" name="editProductStatus">
                    <option value="0" <?php if ($published == 0) echo 'selected' ?>>Draft</option>
                    <option value="1" <?php if ($published == 1) echo 'selected' ?>>Published</option>
                </select>
            </div>



            <button type="submit" class="btn btn-primary" name="productEdit">Submit</button>
        </form>
    </div>





</div>


<!-- Modal for selecting images -->
<div class="modal fade" id="addProductSelectImages" tabindex="-1" aria-labelledby="addProductSelectImagesLabel" aria-hidden="true">
    <div class="modal-dialog modal-select-product-image" id="addProductImagesModal">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addProductSelectImagesLabel">Select product image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">

                <!-- Search images by date and title -->
                <form class="row form-width-700" method="get">
                    <input type="hidden" name="source" value="products">
                    <input type="hidden" name="addProduct" value="1">

                    <div class="col-sm-6 mb-3">
                        <label for="imageFilterTitle" class="form-label">Search for an image by title</label>
                        <input type="text" class="form-control" id="imageFilterTitle" name="imageFilterTitle" oninput="ajaxFilterImages()">
                    </div>

                    <div class="col-sm-6 mb-3">
                        <label for="imageFilterDate" class="form-label">Select the date of upload</label>
                        <select class="form-select" id="imageFilterDate" name="imageFilterDate" onchange="ajaxFilterImages()">
                            <option value="0">Every image</option>
                            <?php
                            /** Create options for select field with dates - image filters **/
                            // Get dates of images uploads
                            $dateQuery = mysqli_query($db, "SELECT upload_date FROM images");
                            $dateArray = [];
                            // Create array with month and year
                            while ($dateQueryResult = mysqli_fetch_assoc($dateQuery)) {
                                $date = strtotime($dateQueryResult['upload_date']);
                                $yearAndMonth = date("F Y", $date);
                                if (!in_array($yearAndMonth, $dateArray)) {
                                    array_push($dateArray, $yearAndMonth);
                                }
                            }
                            $dateFilterSelected = $_GET['imageFilterDate'] ?? 0;
                            // Create options to filter by month/year
                            foreach ($dateArray as $value) {
                                $date = strtotime($value);
                                $dateNumeric = date("Ym", $date);
                                if ($dateFilterSelected == $dateNumeric) {
                                    echo "<option value='$dateNumeric' selected>$value</option>";
                                } else {
                                    echo "<option value='$dateNumeric'>$value</option>";
                                }
                            }
                            ?>
                        </select>
                    </div>
                </form>


                <!-- Display images here - ajaxFilterImages() in adminScripts.js -->
                <ul class="product-images d-flex flex-row flex-wrap" id="productImages">

                </ul>



            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="chooseImagesBtn">Select</button>
            </div>
        </div>
    </div>
</div>
