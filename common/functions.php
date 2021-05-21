<?php
function displayErrors($errors) {
    if ($errors) {
        echo "<div class='col-12'><div class='callout callout-danger alert-danger'>";
        foreach ($errors as $value) {
            echo "<p><strong>$value</strong></p>";
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
                    <a href="index.php?categoryID=<?=$catID?>" class="link-location link-info"><?=$catName?></a>
                <?php
                else :
                    ?>
                    &nbsp>&nbsp<a href="index.php?categoryID=<?=$catID?>" class="link-location link-info"><?=$catName?></a>
                <?php
                endif;
            }
        }
    }

    createNavigation($catArr, $db);
}




function categoriesHierarchyInSelectField($db, $currentParentID, $parentID = 0, $hierarchy = '') {
    $categoriesQuery = mysqli_query($db, "SELECT category_id, category_name FROM categories WHERE parent_id = $parentID ORDER BY category_name ASC");

    if (mysqli_num_rows($categoriesQuery) > 0) {
        while($categoriesResult = mysqli_fetch_assoc($categoriesQuery)) {
            if ($currentParentID != $categoriesResult['category_id']) {
                echo "<option value='{$categoriesResult['category_id']}'>{$hierarchy} {$categoriesResult['category_name']}</option>";
            } else {
                echo "<option selected value='{$categoriesResult['category_id']}'>{$hierarchy} {$categoriesResult['category_name']}</option>";
            }
            categoriesHierarchyInSelectField($db, $currentParentID, $categoriesResult['category_id'], $hierarchy.'—');
        }
    }
}



function fileUploadErrors ($err): string {
    $phpFileUploadErrors = [
        1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
        2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
        3 => 'The uploaded file was only partially uploaded',
        4 => 'No file was uploaded',
        6 => 'Missing a temporary folder',
        7 => 'Failed to write file to disk.',
        8 => 'A PHP extension stopped the file upload.',
    ];

    return $phpFileUploadErrors[$err];
}


function resizeUploadedImages($image, $uniqueImageName, $imageExtension, $imageType, $imageDestination) {

    // Array with size name and image width
    $sizesArray = [
        0 => ['name' => 'thumbnail', 'width' => 250, 'height' => -1, 'mode' => IMG_SINC],
        1 => ['name' => 'main-image', 'width' => 950, 'height' => -1, 'mode' => IMG_SINC],
    ];

    // Create image from uploaded file
    $imageCreate = imagecreatefromstring(file_get_contents($image['tmp_name']));

    foreach ($sizesArray as $key) {
        $name = $key['name'];
        $width = $key['width'];
        $height = $key['height'];
        $mode = $key['mode'];

        // Scale image and save it to given path
        $imageScale = imagescale($imageCreate, $width, $height, $mode);
        switch ($imageType) {
            case 'image/jpeg':
            case 'image/jpg':
                imagejpeg($imageScale, '../'.$imageDestination.'/'.$uniqueImageName.'-'.$name.'.'.$imageExtension);
                break;

            case 'image/png':
                imagepng($imageScale, '../'.$imageDestination.'/'.$uniqueImageName.'-'.$name.'.'.$imageExtension);
                break;

            case 'image/gif':
                imagegif($imageScale, '../'.$imageDestination.'/'.$uniqueImageName.'-'.$name.'.'.$imageExtension);
                break;
        }
    }
}




function deleteImage($db, $imageID) {
    global $errors;
    if (is_numeric($imageID)) {
        // 1. Get unique file name and path
        $stmt = mysqli_prepare($db, "SELECT unique_name, path FROM images WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $imageID);
        mysqli_stmt_execute($stmt);
        $stmtResults = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);
        if (mysqli_num_rows($stmtResults) == 1) {
            $deleteImageResults = mysqli_fetch_assoc($stmtResults);

            // 2. Get directory w/o filename AND file name w/o extension
            $deleteImageDir = pathinfo('../'.$deleteImageResults['path'], PATHINFO_DIRNAME);
            $deleteImageName = pathinfo($deleteImageResults['unique_name'], PATHINFO_FILENAME);

            // 3. Get all files from directory
            $filesInDir = glob($deleteImageDir.'/*');

            // 4. Loop through the files to find every file that contains unique_name and delete them - deletes scaled images
            foreach ($filesInDir as $file) {
                if (is_file($file)) {
                    if (strpos($file, $deleteImageName)) {
                        unlink($file);
                    }
                }
            }

            // Delete from product_image_order
            $stmt = mysqli_prepare($db, "DELETE FROM product_image_order WHERE image_id = ?");
            mysqli_stmt_bind_param($stmt, 'i', $imageID);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            // 5. Delete DB entry
            $stmt = mysqli_prepare($db, "DELETE FROM images WHERE id = ?");
            mysqli_stmt_bind_param($stmt, 'i', $imageID);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

            // Delete ID of deleted image from URL
            unset($_GET['deleteImageID']);
            $cleanURL = http_build_query($_GET);
            header("Location: index.php?$cleanURL");

        } else {
            array_push($errors, "Image with given ID doesn't exist");
        }
    } else {
        array_push($errors, "Given image ID isn't a numeric value.");
    }
}





function imageFilters($db, $limit): array {
    $titleFilter = '';
    $dateFilter = 0;
    if (isset($_GET['imageFilterSubmit'])) {
        $titleFilter = $_GET['imageFilterTitle'];
        $dateFilter = $_GET['imageFilterDate'];
        $yearFilter = substr($dateFilter, 0, 4);
        $monthFilter = substr($dateFilter, 4, 2);
    }

    $currentPage = $_GET['page'] ?? 1;
    $offset = ($currentPage - 1) * $limit;

    // Create query according to applied filters
    if ($titleFilter != '' && $dateFilter != 0) {
        $titleFilter = '%'.$titleFilter.'%';
        $displayImagesQuery = mysqli_prepare($db, "SELECT * FROM images WHERE title LIKE ? AND YEAR(upload_date) LIKE ? AND MONTH(upload_date) LIKE ? ORDER BY upload_date DESC LIMIT $limit OFFSET $offset");
        mysqli_stmt_bind_param($displayImagesQuery, "sii",$titleFilter, $yearFilter, $monthFilter);

        // Query to count all images - pagination
        $imageCountQuery = mysqli_prepare($db, "SELECT COUNT(id) FROM images WHERE title LIKE ? AND YEAR(upload_date) LIKE ? AND MONTH(upload_date) LIKE ?");
        mysqli_stmt_bind_param($imageCountQuery, "sii",$titleFilter, $yearFilter, $monthFilter);
    } elseif ($titleFilter != '') {
        $titleFilter = '%'.$titleFilter.'%';
        $displayImagesQuery = mysqli_prepare($db, "SELECT * FROM images WHERE title LIKE ? ORDER BY upload_date DESC LIMIT $limit OFFSET $offset");
        mysqli_stmt_bind_param($displayImagesQuery, "s",$titleFilter);

        // Query to count all images - pagination
        $imageCountQuery = mysqli_prepare($db, "SELECT COUNT(id) FROM images WHERE title LIKE ?");
        mysqli_stmt_bind_param($imageCountQuery, "s",$titleFilter);
    } elseif ($dateFilter != 0) {
        $displayImagesQuery = mysqli_prepare($db, "SELECT * FROM images WHERE YEAR(upload_date) LIKE ? AND MONTH(upload_date) LIKE ? ORDER BY upload_date DESC LIMIT $limit OFFSET $offset");
        mysqli_stmt_bind_param($displayImagesQuery, "ii",$yearFilter, $monthFilter);

        // Query to count all images - pagination
        $imageCountQuery = mysqli_prepare($db, "SELECT COUNT(id) FROM images WHERE YEAR(upload_date) LIKE ? AND MONTH(upload_date) LIKE ?");
        mysqli_stmt_bind_param($imageCountQuery, "ii",$yearFilter, $monthFilter);
    } else {
        $displayImagesQuery = mysqli_prepare($db, "SELECT * FROM images ORDER BY upload_date DESC LIMIT $limit OFFSET $offset");

        // Query to count all images - pagination
        $imageCountQuery = mysqli_prepare($db, "SELECT COUNT(id) FROM images");
    }


    // Get image count
    mysqli_stmt_execute($imageCountQuery);
    $imagesCount = mysqli_stmt_get_result($imageCountQuery);
    $imagesCount = mysqli_fetch_array($imagesCount)[0];
    mysqli_stmt_close($imageCountQuery);

    // Calculate how many pages are there
    $pages = ceil($imagesCount / $limit);

    return [$displayImagesQuery, $pages, $currentPage];
}



function createPagination($label, $pages, $currentPage, $fileName) {
    if ($pages > 1) :
        ?>

        <nav aria-label="<?=$label?>">
            <ul class="pagination justify-content-end">

                <?php
                // Unset $_GET['page'] so we can set it when building link. Necessary for pagination when filters are applied.
                unset($_GET['page']);
                if ($pages <= 3) :
                    for ($i = 1; $i <= $pages; $i++) :
                        $paginationURL = http_build_query(array_merge($_GET,['page' => $i]));
                        ?>

                        <li class="page-item <?php if($i == $currentPage) { echo 'active'; } ?>" <?php if($i == $currentPage) { echo 'aria-current="page"'; } ?>>
                            <a class="page-link" href="<?=$fileName?>?<?=$paginationURL?>"><?=$i?></a>
                        </li>

                    <?php
                    endfor;
                else :
                    ?>

                    <?php
                    if ($currentPage == 1) :
                        ?>
                        <li class="page-item disabled">
                            <span class="page-link">First</span>
                        </li>
                    <?php
                    else :
                        $paginationURL = http_build_query(array_merge($_GET,['page' => 1]));
                        ?>
                        <li class="page-item">
                            <a class="page-link" href="<?=$fileName?>?<?=$paginationURL?>">First</a>
                        </li>
                    <?php
                    endif;
                    ?>

                    <?php
                    if ($currentPage > 1) :
                        $paginationURL = http_build_query(array_merge($_GET,['page' => $currentPage-1]));
                        ?>
                        <li class="page-item">
                            <a class="page-link" href="<?=$fileName?>?<?=$paginationURL?>"><?=$currentPage-1?></a>
                        </li>
                    <?php
                    endif;
                    ?>

                    <li class="page-item active" aria-current="page">
                        <span class="page-link"><?=$currentPage?></span>
                    </li>

                    <?php
                    if ($currentPage < $pages) :
                        $paginationURL = http_build_query(array_merge($_GET,['page' => $currentPage+1]));
                        ?>
                        <li class="page-item">
                            <a class="page-link" href="<?=$fileName?>?<?=$paginationURL?>"><?=$currentPage+1?></a>
                        </li>
                    <?php
                    endif;
                    ?>

                    <?php
                    if ($currentPage == $pages) :
                        ?>
                        <li class="page-item disabled">
                            <span class="page-link">Last</span>
                        </li>
                    <?php
                    else :
                        $paginationURL = http_build_query(array_merge($_GET,['page' => $pages]));
                        ?>
                        <li class="page-item">
                            <a class="page-link" href="<?=$fileName?>?<?=$paginationURL?>">Last</a>
                        </li>
                    <?php
                    endif;
                    ?>

                <?php
                endif;
                ?>

            </ul>
        </nav>

    <?php
    endif;
}


function getImageFields($displayImagesResults): array {
    $imageID = $displayImagesResults['id'];
    $imageUniqueName = $displayImagesResults['unique_name'];
    $imageTitle = $displayImagesResults['title'];
    $imageAlt = $displayImagesResults['alt'];
    $uploadDate = $displayImagesResults['upload_date'];
    $fullPath = $displayImagesResults['path'];

    // Get path without extension and extension
    $path = pathinfo($fullPath, PATHINFO_DIRNAME);
    $path .= '/'.pathinfo($fullPath, PATHINFO_FILENAME);
    $extension = pathinfo($fullPath, PATHINFO_EXTENSION);

    return ['imageID' => $imageID, 'imageUniqueName' => $imageUniqueName, 'imageTitle' => $imageTitle, 'imageAlt' => $imageAlt,
        'imageUploadDate' => $uploadDate, 'path' => $path, 'extension' => $extension, 'fullPath' => $fullPath];
}

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

