<?php
$errors = [];


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


        } else {
            array_push($errors, "Wrong file type. Allowed types: jpg, jpeg, png, gif.");
        }
    } else {
        array_push($errors, fileUploadErrors($imageUpload['error']));
    }
}


//TODO Delete images
/** Delete image **/
if (isset($_GET['deleteImageID'])) {
    $deleteImageID = $_GET['deleteImageID'];
    if (is_numeric($deleteImageID)) {
        // 1. Get unique file name and path
        $deleteImageQuery = mysqli_query($db, "SELECT unique_name, path FROM images WHERE id = $deleteImageID");
        if (mysqli_num_rows($deleteImageQuery) == 1) {
            $deleteImageResults = mysqli_fetch_assoc($deleteImageQuery);

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

            // 5. Delete DB entry
            mysqli_query($db, "DELETE FROM images WHERE id = $deleteImageID");

        } else {
            array_push($errors, "Image with given ID doesn't exist");
        }
    } else {
        array_push($errors, "Given image ID isn't a numeric value.");
    }
}


?>

<div class="row">

    <?php
    displayErrors($errors);
    ?>

    <div class="col-12 mb-3">
        <h2>Upload new image</h2>
        <!-- TODO form validation -->
        <form class="form-width-700" action="index.php?source=images" method="post" enctype="multipart/form-data">
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
        <h2>Uploaded images</h2>
        <div class="alert-warning callout callout-warning"><strong>Do not remove</strong> uploaded images directly from the directory.</div>

        <!-- TODO bulk option for images and live search by title -->
        <table class="table table-images">
            <thead>
                <tr>
                    <th scope="col">Bulk</th>
                    <th scope="col">Image</th>
                    <th scope="col">Title</th>
                    <th scope="col">Alternative text</th>
                    <th scope="col">Date</th>
                    <th scope="col">Edit</th>
                    <th scope="col">Delete</th>
                </tr>
            </thead>
            <tbody>
               
                <?php

                /** Display all images **/
                $displayImagesQuery = mysqli_query($db, "SELECT * FROM images ORDER BY upload_date DESC LIMIT 50");
                while($displayImagesResults = mysqli_fetch_assoc($displayImagesQuery)){
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
                    ?>
                    
                    <tr>
                        <td>Bulk</td>
                        <td><a href="../<?=$fullPath?>">
                                <div class="admin-image-container" style="background-image: url('../<?=$path?>-thumbnail.<?=$extension?>') "></div>
                            </a>
                        </td>
                        <td><?=$imageTitle?></td>
                        <td><?=$imageAlt?></td>
                        <td><?=$uploadDate?></td>
                        <td><a href="index.php?source=images&editImageID=<?=$imageID?>">Edit</td>
                        <td><a href="index.php?source=images&deleteImageID=<?=$imageID?>" class="link-danger delete-image-link" data-bs-toggle="modal" data-bs-target="#modalImageDeleteWarning">Delete</td>
                    </tr>
                    
                    <?php  
                }

                ?>
              
            </tbody>
        </table>
        <!-- TODO pagination for images -->
    </div>

</div>



<!-- Modal - delete image -->
<div class="modal fade" id="modalImageDeleteWarning" tabindex="-1" aria-labelledby="modalImageDeleteWarningLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalImageDeleteWarningLabel">Delete category</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete this image? This operation cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-danger" id="deleteImageConfirm">Delete image</button>
            </div>
        </div>
    </div>
</div>

<script>
    window.onload = function() { deleteAndShowModal('delete-image-link', 'deleteImageConfirm') };
</script>