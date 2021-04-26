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
        $editCatDefault = isset($_POST['editCategoryDefault']) ?  1 : 0;

        if ($editCatDefault == 0) {
            mysqli_query($db, "UPDATE categories SET category_name = '$editCatName', category_slug = '$editCatSlug', parent_id = $editCatParent WHERE category_id = $editCatID");
        } else {
            mysqli_query($db, "UPDATE categories SET is_default = 0 WHERE is_default = 1");
            mysqli_query($db, "UPDATE categories SET category_name = '$editCatName', category_slug = '$editCatSlug', parent_id = $editCatParent, is_default = $editCatDefault WHERE category_id = $editCatID");
        }

        $success = true;

    } else {
        array_push($errors, "Category name is required");
    }
}


/** Check if ID is correct, then get informations about category to fill form. **/
if (!is_numeric($editCatID)) {
    array_push($errors, 'Category ID is not a number. Choose category to edit again or check your URL.');
} else {
    $editCatQuery = mysqli_query($db, "SELECT parent_id, category_name, category_slug, is_default FROM categories WHERE category_id = $editCatID");
    $editCatResult = mysqli_fetch_assoc($editCatQuery);
}

?>




<div class="row">

    <?php
    if ($success) :
    ?>
    <div class="alert-success">
        <p>Category updated.</p>
        <p><a href="index.php?source=categories">Go back to categories</a></p>
    </div>
    <?php
    endif;
    ?>

    <?php
    if ($errors) {
        echo "<div class='col-12'><div class='callout callout-danger'>";
        foreach ($errors as $value) {
            echo "<strong>$value</strong>";
        }
        echo "</div></div>";
    }

    ?>

    <form method="post" action="index.php?source=categories&editCatID=<?php echo $_GET['editCatID'] ?>" style="max-width: 700px">
        <div class="mb-3">
            <div class="d-flex flex-row"><label for="EditCategoryName" class="form-label">Category name</label><div class="required">*</div></div>
            <input type="text" class="form-control" id="EditCategoryName" name="EditCategoryName" aria-describedby="categoryNameHelp" value="<?php if (is_numeric($editCatID)) { echo $editCatResult['category_name']; } ?>">
            <div id="categoryNameHelp" class="form-text">Category name is the one visible on website.</div>
        </div>

        <!-- TODO pretty links - category slug names required? Maybe auto generate them. -->
        <div class="mb-3">
            <label for="EditCategorySlug" class="form-label">Category slug</label>
            <input type="text" class="form-control" id="EditCategorySlug" name="EditCategorySlug" aria-describedby="slugHelp" value="<?php if (is_numeric($editCatID)) { echo $editCatResult['category_slug']; } ?>">
            <div id="slugHelp" class="form-text">Category slug is short name of your category which will be used in URLs. It can't contain special characters. For example slug name for category name "Car parts" could be "car-parts". Leave empty to automatically generate.</div>
        </div>

        <div class="mb-3">
            <label for="EditCategoryParent" class="form-label">Choose parent category</label>
            <select class="form-select" id="EditCategoryParent" name="EditCategoryParent" aria-describedby="parentHelp">
                <option value="0">None</option>
                <?php
                /** Get categories from database and display them in select field **/
                if (is_numeric($editCatID)) { categoriesHierarchyInSelectField($editCatResult['parent_id']); }
                ?>
            </select>
            <div id="parentHelp" class="form-text">Choose parent category to create hierarchy.</div>
        </div>

        <?php
        if (is_numeric($editCatID)) :
        if ($editCatResult['is_default'] == 0) : ?>
        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="editCategoryDefault" name="editCategoryDefault">
            <label class="form-check-label" for="editCategoryDefault">Set to default category</label>
        </div>
        <?php endif; endif; ?>

        <button type="submit" class="btn btn-primary " name="categoryEdit">Submit</button>
    </form>

</div>
