<?php
$errors = [];
$phpFileUploadErrors = [
    1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
    2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
    3 => 'The uploaded file was only partially uploaded',
    4 => 'No file was uploaded',
    6 => 'Missing a temporary folder',
    7 => 'Failed to write file to disk.',
    8 => 'A PHP extension stopped the file upload.',
];

/** Upload image to server and database **/
if (isset($_POST['addImageButton'])) {
    $imageUpload = $_FILES['addImage'];

    // Check if there are no errors when uploading file
    if ($imageUpload['error'] == 0) {
        $imageName = $imageUpload['name'];
        $imageTmpName = $imageUpload['tmp_name'];
        $imageType = $imageUpload['type'];
        $imageSize = $imageUpload['size'];
        $imageExtension = pathinfo($imageName, PATHINFO_EXTENSION);

        // Check if uploaded file's type is allowed
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
        if (in_array($imageType, $allowedTypes)) {
            // Get image alt and title from form
            $imageAlt = $_POST['addImageAlt'];
            $imageTitle = $_POST['addImageTitle'];
            $imageTitle = empty($imageTitle) ? str_replace('.'.$imageExtension,'', $imageName) : $imageTitle;

            // Generate unique name for file
            $uniqueImageName = uniqid('',true).'.'.$imageExtension;

            // Create folders for storing images if doesn't exist
            $imageDestination = 'uploaded_images/'.date('Y').'/'.date('m');
            if (!file_exists('../'.$imageDestination)) {
                mkdir('../'.$imageDestination, 0777, true);
            }

            $imageFullDestination = $imageDestination.'/'.$uniqueImageName;

            // Upload image
            move_uploaded_file($imageTmpName, '../'.$imageFullDestination);

            // Update database with image informations
            mysqli_query($db, "INSERT INTO images (unique_name, title, alt, path) VALUES ('$uniqueImageName', '$imageTitle', '$imageAlt', '$imageFullDestination')");

            // TODO copies of image with lower resolutions - function which will also be used in products.php

        } else {
            array_push($errors, "Wrong file type. Allowed types: jpg, jpeg, png.");
        }
    } else {
        array_push($errors, $phpFileUploadErrors[$imageUpload['error']]);
    }
}
?>

<div class="row">

    <?php
    displayErrors($errors);
    ?>

    <div class="col-12">
        <form class="form-width-700" action="index.php?source=images" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="addImage" class="form-label">Select image to upload</label>
                <input class="form-control" type="file" accept="image/png,image/jpg,image/jpeg" id="addImage" name="addImage">
            </div>

            <div class="image-upload-preview mb-3">
                <span id="image-upload-text" class="text-muted">
                    Image preview
                </span>
                <img id='imageUpload' class="image-upload">
            </div>

            <div class="mb-3">
                <label for="addImageAlt" class="form-label">Image alternative text</label>
                <input type="text" class="form-control" id="addImageAlt" name="addImageAlt" aria-describedby="altHelp">
                <div id="altHelp" class="form-text">The alt attribute provides alternative information for an image if a user for some reason cannot view it. Leave empty if image is only decorative.</div>
            </div>

            <div class="mb-3">
                <label for="addImageTitle" class="form-label">Image title</label>
                <input type="text" class="form-control" id="addImageTitle" name="addImageTitle" aria-describedby="titleHelp">
                <div id="titleHelp" class="form-text">If empty, file name will be the image title.</div>
            </div>


            <button type="submit" class="btn btn-primary " name="addImageButton">Submit</button>
        </form>
    </div>

    <div class="col-12">
        <!--display img here-->display
    </div>

</div>
