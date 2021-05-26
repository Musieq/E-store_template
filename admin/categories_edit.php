<?php

$errors = [];
$success = false;
$editCatID = $_GET['editCatID'];


/** Get data from form and update category **/
if (isset($_POST['categoryEdit']) && is_numeric($editCatID)) {

    $editCatName = $_POST['EditCategoryName'];
    if (!empty($editCatName)) {
        $editCatSlug = $_POST['EditCategorySlug'];
        $editCatParent = $_POST['EditCategoryParent'];

        if (strlen($editCatName) > 100) { array_push($errors, "Category name is too long. Max 100 characters."); }
        if (strlen($editCatSlug) > 150) { array_push($errors, "Category name is too long. Max 150 characters."); }

        if (count($errors) == 0) {
            mysqli_query($db, "UPDATE categories SET category_name = '$editCatName', category_slug = '$editCatSlug', parent_id = $editCatParent WHERE category_id = $editCatID");


            $success = true;
        }
    } else {
        array_push($errors, "Category name is required");
    }
}


/** Check if ID is correct, then get informations about category to fill form. **/
if (!is_numeric($editCatID)) {
    array_push($errors, "Given category ID isn't a numeric value.");
} else {
    $editCatQuery = mysqli_query($db, "SELECT parent_id, category_name, category_slug FROM categories WHERE category_id = $editCatID");
    $editCatResult = mysqli_fetch_assoc($editCatQuery);
}

?>




<div class="row">

    <?php
    if ($success) :
    ?>
    <div class="col-12">
        <div class="alert-success">
            <p><strong>Category updated.</strong></p>
            <p><a href="index.php?source=categories">Go back to categories</a></p>
        </div>
    </div>
    <?php
    endif;
    ?>

    <?php
    displayErrors($errors);
    ?>

    <div class="col-12">
        <h2>Edit category</h2>

        <form class="form-width-700" method="post" action="index.php?source=categories&editCatID=<?php echo $_GET['editCatID'] ?>">
            <div class="mb-3">
                <div class="d-flex flex-row"><label for="EditCategoryName" class="form-label">Category name</label><div class="required">*</div></div>
                <input type="text" class="form-control" id="EditCategoryName" name="EditCategoryName" maxlength="100" aria-describedby="categoryNameHelp" value="<?php if (is_numeric($editCatID)) { echo $editCatResult['category_name']; } ?>">
                <div id="categoryNameHelp" class="form-text">Category name is the one visible on website.</div>
            </div>

            <!-- TODO pretty links - category slug names required? Maybe auto generate them. -->
            <div class="mb-3">
                <label for="EditCategorySlug" class="form-label">Category slug</label>
                <input type="text" class="form-control" id="EditCategorySlug" name="EditCategorySlug" aria-describedby="slugHelp" maxlength="150" value="<?php if (is_numeric($editCatID)) { echo $editCatResult['category_slug']; } ?>">
                <div id="slugHelp" class="form-text">Category slug is short name of your category which will be used in URLs. It can't contain special characters. For example slug name for category name "Car parts" could be "car-parts". Leave empty to automatically generate.</div>
            </div>

            <div class="mb-3">
                <label for="EditCategoryParent" class="form-label">Choose parent category</label>
                <select class="form-select" id="EditCategoryParent" name="EditCategoryParent" aria-describedby="parentHelp">
                    <option value="0">None</option>
                    <?php
                    /** Get categories from database and display them in select field **/
                    if (is_numeric($editCatID)) { categoriesHierarchyInSelectField($db, $editCatResult['parent_id']); }
                    ?>
                </select>
                <div id="parentHelp" class="form-text">Choose parent category to create hierarchy.</div>
            </div>

            <button type="submit" class="btn btn-primary" name="categoryEdit">Submit</button>
        </form>
    </div>
</div>
