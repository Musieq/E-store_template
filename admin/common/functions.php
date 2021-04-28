<?php

function categoriesHierarchyInSelectField($currentParentID, $parentID = 0, $hierarchy = '') {
    global $db;
    $categoriesQuery = mysqli_query($db, "SELECT category_id, category_name FROM categories WHERE parent_id = $parentID ORDER BY category_name ASC");

    if (mysqli_num_rows($categoriesQuery) > 0) {
        while($categoriesResult = mysqli_fetch_assoc($categoriesQuery)) {
            if ($currentParentID != $categoriesResult['category_id']) {
                echo "<option value='{$categoriesResult['category_id']}'>{$hierarchy} {$categoriesResult['category_name']}</option>";
            } else {
                echo "<option selected value='{$categoriesResult['category_id']}'>{$hierarchy} {$categoriesResult['category_name']}</option>";
            }
            categoriesHierarchyInSelectField($currentParentID, $categoriesResult['category_id'], $hierarchy.'—');
        }
    }
}



function displayErrors($errors) {
    if ($errors) {
        echo "<div class='col-12'><div class='callout callout-danger alert-danger'>";
        foreach ($errors as $value) {
            echo "<strong>$value</strong>";
        }
        echo "</div></div>";
    }
}





