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



