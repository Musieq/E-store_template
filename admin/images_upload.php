<?php
$errors = [];
$success = false;


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
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (in_array($imageType, $allowedTypes)) {
            // Get image alt and title from form
            $imageAlt = $_POST['addImageAlt'];
            $imageTitle = $_POST['addImageTitle'];
            $imageTitle = empty($imageTitle) ? str_replace('.'.$imageExtension,'', $imageName) : $imageTitle;

            if (strlen($imageAlt) > 255) { array_push($errors, "Image alternative text is too long. Max 255 characters."); }
            if (strlen($imageTitle) > 255) { array_push($errors, "Image title is too long. Max 255 characters."); }

            if (count($errors) == 0) {
                // Generate unique name for file
                $uniqueImageName = uniqid('',true);
                $uniqueImageNameExtension = $uniqueImageName.'.'.$imageExtension;

                // Create folders for storing images if doesn't exist
                $imageDestination = 'uploaded_images/'.date('Y').'/'.date('m');
                if (!file_exists('../'.$imageDestination)) {
                    mkdir('../'.$imageDestination, 0777, true);
                }

                $imageFullDestination = $imageDestination.'/'.$uniqueImageNameExtension;


                // Create copies of this image with lower resolution
                resizeUploadedImages($imageUpload, $uniqueImageName, $imageExtension, $imageType, $imageDestination);


                // Upload image
                move_uploaded_file($imageTmpName, '../'.$imageFullDestination);

                // Update database with image informations
                mysqli_query($db, "INSERT INTO images (unique_name, title, alt, path) VALUES ('$uniqueImageNameExtension', '$imageTitle', '$imageAlt', '$imageFullDestination')");

                $success = true;
            }
        } else {
            array_push($errors, "Wrong file type. Allowed types: jpg, jpeg, png, gif.");
        }
    } else {
        array_push($errors, fileUploadErrors($imageUpload['error']));
    }
}

?>

<div class="row">

    <?php
    if ($success) :
        ?>
        <div class="col-12">
            <div class="callout callout-success alert-success">
                <p><strong>Image uploaded successfully.</strong></p>
                <p><a href="index.php?source=images">Go back to images</a></p>
            </div>
        </div>
    <?php
    endif;
    ?>

    <?php
    displayErrors($errors);
    ?>

    <div class="col-12">
        <h2>Upload new image</h2>
        <form class="form-width-700" action="index.php?source=images&uploadImage=1" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="addImage" class="form-label">Select image to upload</label>
                <input class="form-control" type="file" accept="image/png,image/jpg,image/jpeg,image/gif" id="addImage" name="addImage">
            </div>

            <div class="image-upload-preview mb-3">
                    <span id="image-upload-text" class="text-muted">
                        Image preview
                    </span>
                <img id='imageUpload' class="image-upload">
            </div>

            <div class="mb-3">
                <label for="addImageAlt" class="form-label">Image alternative text</label>
                <input type="text" class="form-control" id="addImageAlt" name="addImageAlt" aria-describedby="altHelp" maxlength="255">
                <div id="altHelp" class="form-text">The alt attribute provides alternative information for an image if a user for some reason cannot view it. Leave empty if image is only decorative.</div>
            </div>

            <div class="mb-3">
                <label for="addImageTitle" class="form-label">Image title</label>
                <input type="text" class="form-control" id="addImageTitle" name="addImageTitle" aria-describedby="titleHelp" maxlength="255">
                <div id="titleHelp" class="form-text">If empty, file name will be the image title.</div>
            </div>


            <button type="submit" class="btn btn-primary " name="addImageButton">Submit</button>
        </form>
    </div>
</div>