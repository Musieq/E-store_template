<?php

function displayErrors($errors) {
    if ($errors) {
        echo "<div class='col-12'><div class='callout callout-danger alert-danger'>";
        foreach ($errors as $value) {
            echo "<strong>$value</strong>";
        }
        echo "</div></div>";
    }
}


function getScaledImagePath($path, $scaleName): string {
    $dir = pathinfo($path, PATHINFO_DIRNAME);
    $file = pathinfo($path, PATHINFO_FILENAME);
    $ext = pathinfo($path, PATHINFO_EXTENSION);

    return $dir.'/'.$file.'-'.$scaleName.'.'.$ext;
}


function displayCurrentLocation($categoryID, $db) {
    $catArr = [];
    array_push($catArr, $categoryID);

    function getParents($categoryID, $db, &$catArr) {
        $stmt = mysqli_prepare($db, "SELECT parent_id FROM categories WHERE category_id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $categoryID);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);
        if (mysqli_num_rows($res) == 1) {
            $parentID = mysqli_fetch_array($res)[0];
            array_push($catArr, $parentID);
            if ($parentID != 0) {
                getParents($parentID, $db, $catArr);
            }
        }

        return $catArr;
    }

    $catArr = getParents($categoryID, $db, $catArr);

    function createNavigation($catArr, $db) {
        $catArr = array_reverse($catArr);
        foreach ($catArr as $i => $catID) {
            if ($catID != 0) {
                $stmt = mysqli_prepare($db, "SELECT category_name FROM categories WHERE category_id = ?");
                mysqli_stmt_bind_param($stmt, 'i', $catID);
                mysqli_stmt_execute($stmt);
                $res = mysqli_stmt_get_result($stmt);
                mysqli_stmt_close($stmt);
                $catName = mysqli_fetch_array($res)[0];

                if ($i == 1) :
                    ?>
                    <a href="index.php?source=category&categoryID=<?=$catID?>" class="link-location link-info"><?=$catName?></a>
                <?php
                else :
                    ?>
                    &nbsp>&nbsp<a href="index.php?source=category&categoryID=<?=$catID?>" class="link-location link-info"><?=$catName?></a>
                <?php
                endif;
            }
        }
    }

    createNavigation($catArr, $db);
}