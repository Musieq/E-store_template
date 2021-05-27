<?php
$errors = [];
$success = false;

if (isset($_POST['productAdd'])) {
    $productName = $_POST['addProductName'];
    if (!empty($productName)) {

        $productImages = $_POST['productImagesInput'];
        $productDescription = $_POST['productDescription'];
        $productTags = $_POST['productTags'];
        $productCategories = $_POST['addProductCategory'] ?? 0;  // array with categories
        $productPrice = $_POST['addProductPrice'];
        $productSalePrice = $_POST['addProductSalePrice'];
        $productSalePrice = $productSalePrice > 0 ? $productSalePrice: -1;
        if (!empty($productPrice) && is_numeric($productPrice) && is_numeric($productSalePrice)) {

            if (strlen($productName) <= 255) {

                $productManageStock = $_POST['productManageStock'] ?? 0;
                if ($productManageStock) {
                    $productManageStock = 1;
                    $addProductStockStatus = -1;
                    $addProductStock = $_POST['addProductStock'];
                } else {
                    $addProductStockStatus = $_POST['addProductStockStatus'];
                    $addProductStock = -1;
                }
                $addProductStatus = $_POST['addProductStatus'];
                if (isset($_POST['addProductAllowMultiplePurchases'])) {
                    $allowMultiplePurchases = 1;
                } else {
                    $allowMultiplePurchases = 0;
                }

                if ($addProductStock != -1 && $addProductStock < 99999) {
                    // Insert into product table
                    $stmt = mysqli_prepare($db, "INSERT INTO products (name, description, tags, price, price_sale, stock, stock_status, stock_manage, allow_multiple_purchases, published) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                    mysqli_stmt_bind_param($stmt, "sssddiiiis", $productName, $productDescription, $productTags, $productPrice, $productSalePrice, $addProductStock, $addProductStockStatus, $productManageStock, $allowMultiplePurchases, $addProductStatus);
                    mysqli_stmt_execute($stmt);
                    $productID = mysqli_stmt_insert_id($stmt);
                    mysqli_stmt_close($stmt);

                    // Insert into images table
                    if (!empty($productImages)) {
                        $productImagesArray = explode(',', $productImages);
                        foreach ($productImagesArray as $imageOrder => $imageID) {
                            $stmt = mysqli_prepare($db, "INSERT INTO product_image_order (product_id, image_id, image_order) VALUES (?, ?, ?)");
                            mysqli_stmt_bind_param($stmt, "iii", $productID, $imageID, $imageOrder);
                            mysqli_stmt_execute($stmt);
                            mysqli_stmt_close($stmt);
                        }
                    }

                    // Insert into categories table
                    if(!empty($productCategories)) {
                        foreach ($productCategories as $productCategory) {
                            $stmt = mysqli_prepare($db, "INSERT INTO product_category (product_id, category_id) VALUES (?, ?)");
                            mysqli_stmt_bind_param($stmt, "ii", $productID, $productCategory);
                            mysqli_stmt_execute($stmt);
                            mysqli_stmt_close($stmt);
                        }
                    }

                    $success = true;
                } else {
                    array_push($errors, 'Max stock is 99999.');
                }
            } else {
                array_push($errors, 'Product name is too long. Max 255 characters.');
            }
        } else {
            array_push($errors, 'Price cannot be empty');
        }
    } else {
        array_push($errors, 'Product name cannot be empty');
    }
}

?>



<div class="row">

    <?php
    if ($success) :
        ?>
        <div class="col-12">
            <div class="callout callout-success alert-success">
                <p><strong>Product created successfully.</strong></p>
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
        <h2>Add new product</h2>
        <form method="post" action="index.php?source=products&addProduct=1">

            <div class="mb-3">
                <div class="d-flex flex-row"><label for="addProductName" class="form-label">Product name</label><div class="required">*</div></div>
                <input type="text" class="form-control" id="addProductName" name="addProductName" maxlength="255" required>
            </div>

            <div class="mb-3">
                Product images
                <div class="callout callout-info alert-info">
                    First image will be product's main image. You can drag and drop selected images to set their order. Image is dropped before selected one.
                </div>
                <div class="container-images-draggable">
                    <ul id="containerImagesDraggable" class="selected-product-images"></ul>
                    <input type="hidden" id="productImagesInput" name="productImagesInput" value="">
                </div>

                <a href="#" id="showProductImagesModal">Select product images</a>
            </div>

            <div class="mb-3">
                <label for="productDescription" class="form-label">Product description</label>
                <textarea name="productDescription" id="productDescription"></textarea>
            </div>

            <div class="mb-3">
                <label for="productTags" class="form-label">Product tags</label>
                <input type="text" class="form-control" id="productTags" name="productTags" aria-describedby="productTagsHelp">
                <div id="productTagsHelp" class="form-text">Separate tags with commas. Tags are used for searching products.</div>
            </div>

            <div class="mb-3">
                <label for="addProductCategory" class="form-label">Product categories</label>
                <ul id="addProductCategory" class="select-product-category list-unstyled">
                <?php
                categoriesHierarchyInProducts($db, 0)
                ?>
                </ul>
            </div>

            <div class="row mb-3">
                <div class="col-lg-6">
                    <div class="d-flex flex-row"><label for="addProductPrice" class="form-label">Price</label><div class="required">*</div></div>
                    <input type="number" step="0.01" class="form-control" id="addProductPrice" name="addProductPrice" required>
                </div>

                <div class="col-lg-6">
                    <div class="d-flex flex-row"><label for="addProductSalePrice" class="form-label">Sale price</label></div>
                    <input type="number" step="0.01" class="form-control" id="addProductSalePrice" name="addProductSalePrice">
                </div>
            </div>


            <div class="mb-3">
                <input type="checkbox" class="form-check-input" id="productManageStock" name="productManageStock" aria-describedby="manageStockHelp">
                <label class="form-check-label" for="productManageStock">Manage stock?</label>
                <div id="manageStockHelp" class="form-text">Check if you want to input exact stock for this products.</div>
            </div>

            <div class="mb-3">
                <div id="noStockManagement" style="display: block">
                    <label for="addProductStockStatus" class="form-label">Stock status</label>
                    <select class="form-select" id="addProductStockStatus" name="addProductStockStatus">
                        <option value="0">Out of stock</option>
                        <option value="1">In stock</option>
                    </select>
                </div>

                <div id="stockManagement" style="display: none">
                    <div class="d-flex flex-row"><label for="addProductStock" class="form-label">Stock</label><div class="required">*</div></div>
                    <input type="number" class="form-control" id="addProductStock" name="addProductStock" max="99999">
                </div>
            </div>

            <div class="mb-3">
                <input type="checkbox" class="form-check-input" id="addProductAllowMultiplePurchases" name="addProductAllowMultiplePurchases" aria-describedby="multipleHelp">
                <label class="form-check-label" for="addProductAllowMultiplePurchases">Allow multiple purchases?</label>
                <div id="multipleHelp" class="form-text">Check if you want to allow clients to select quantity when purchasing this product.</div>
            </div>

            <div class="mb-3">
                <label for="addProductStatus" class="form-label">Product status</label>
                <select class="form-select" id="addProductStatus" name="addProductStatus">
                    <option value="0">Draft</option>
                    <option value="1">Published</option>
                </select>
            </div>



            <button type="submit" class="btn btn-primary" name="productAdd">Submit</button>
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


