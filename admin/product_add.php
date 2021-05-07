<?php
$errors = [];


?>



<div class="row">


    <?php
    displayErrors($errors);
    ?>



    <div class="col-12">
        <h2>Add new product</h2>
        <!-- TODO form validation -->
        <form method="post" action="index.php?source=products&productAdd=1">

            <div class="mb-3">
                <div class="d-flex flex-row"><label for="addProductName" class="form-label">Product name</label><div class="required">*</div></div>
                <input type="text" class="form-control" id="addProductName" name="addProductName">
            </div>

            <div class="mb-3">
                Product images
                <div class="container-images-draggable">
                    <ul id="containerImagesDraggable" class="selected-product-images"></ul>
                    <input type="hidden" id="addProductImages" name="addProductImages" value="">
                </div>

                <a href="#" id="showProductImagesModal">Select product images</a>
            </div>

            <div class="mb-3">
                <label for="addProductDescription" class="form-label">Product description</label>
                <textarea name="addProductDescription" id="addProductDescription"></textarea>
            </div>

            <div class="mb-3">
                <label for="addProductCategory" class="form-label">Product categories</label>
                <ul id="addProductCategory" class="select-product-category list-unstyled">
                <?php
                function categoriesHierarchyInProducts($db, $currentParentID, $parentID = 0, $hierarchy = '') {
                    $categoriesQuery = mysqli_query($db, "SELECT category_id, category_name FROM categories WHERE parent_id = $parentID ORDER BY category_name ASC");

                    if (mysqli_num_rows($categoriesQuery) > 0) {
                        while($categoriesResult = mysqli_fetch_assoc($categoriesQuery)) {
                            ?>
                            <li>
                                <input type="checkbox" class="form-check-input" id="addProductCategory-<?=$categoriesResult['category_id']?>" name="addProductCategory[]" value="<?=$categoriesResult['category_id']?>">
                                <label class="form-check-label" for="addProductCategory-<?=$categoriesResult['category_id']?>"><?=$hierarchy.$categoriesResult['category_name']?></label>
                            </li>
                            <?php
                            categoriesHierarchyInProducts($db, $currentParentID, $categoriesResult['category_id'], $hierarchy.'—');
                        }
                    }
                }
                categoriesHierarchyInProducts($db, 0)
                ?>
                </ul>
            </div>

            <div class="row mb-3">
                <div class="col-lg-6">
                    <div class="d-flex flex-row"><label for="addProductPrice" class="form-label">Price</label><div class="required">*</div></div>
                    <input type="text" class="form-control" id="addProductPrice" name="addProductPrice">
                </div>

                <div class="col-lg-6">
                    <div class="d-flex flex-row"><label for="addProductPrice" class="form-label">Sale price</label></div>
                    <input type="text" class="form-control" id="addProductPrice" name="addProductPrice">
                </div>
            </div>


            <div class="mb-3">
                <input type="checkbox" class="form-check-input" id="addProductManageStock" name="addProductManageStock">
                <label class="form-check-label" for="addProductManageStock">Manage stock?</label>
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
                    <input type="number" class="form-control" id="addProductStock" name="addProductStock">
                </div>
            </div>

            <div class="mb-3">
                <label for="addProductStatus" class="form-label">Product status</label>
                <select class="form-select" id="addProductStatus" name="addProductStatus">
                    <option value="0">Draft</option>
                    <option value="1">Published</option>
                </select>
            </div>



            <button type="submit" class="btn btn-primary " name="categoryAdd">Submit</button>
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


