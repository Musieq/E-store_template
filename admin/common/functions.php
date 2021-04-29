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
        0 => ['name' => 'thumbnail', 'width' => 250, 'height' => -1],
        1 => ['name' => 'main-image', 'width' => 600, 'height' => -1]
    ];

    // Create image from uploaded file
    $imageCreate = imagecreatefromstring(file_get_contents($image['tmp_name']));

    foreach ($sizesArray as $key) {
        $name = $key['name'];
        $width = $key['width'];
        $height = $key['height'];

        // Scale image and save it to given path
        $imageScale = imagescale($imageCreate, $width, $height);
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




