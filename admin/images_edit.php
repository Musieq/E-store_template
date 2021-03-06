<?php
$errors = [];
$success = false;
$editImageID = $_GET['editImageID'];


/** Update image info **/
if (isset($_POST['editImageButton'])) {
    if (is_numeric($editImageID)) {
        $editImageAlt = $_POST['editImageAlt'];
        $editImageTitle = $_POST['editImageTitle'];

        if (strlen($editImageAlt) > 255) { array_push($errors, "Image alternative text is too long. Max 255 characters."); }
        if (strlen($editImageTitle) > 255) { array_push($errors, "Image title is too long. Max 255 characters."); }

        if (count($errors) == 0) {
            mysqli_query($db, "UPDATE images SET title = '$editImageTitle', alt = '$editImageAlt' WHERE id = $editImageID");

            $success = true;
        }
    } else {
        array_push($errors, "Given image ID isn't a numeric value.");
    }
}


/** Get image information from DB **/
$getImageTitle = '';
$getImageAlt = '';
$getImagePath = '';
$getScaledImage = '';

if (is_numeric($editImageID)) {
    $editImageQuery = mysqli_query($db, "SELECT title, alt, path FROM images WHERE id = $editImageID");

    if (mysqli_num_rows($editImageQuery) == 1) {
        $editImageResults = mysqli_fetch_assoc($editImageQuery);

        $getImageTitle = $editImageResults['title'];
        $getImageAlt = $editImageResults['alt'];
        $getImagePath = $editImageResults['path'];

        $getScaledImage = getScaledImagePath($getImagePath, 'main-image');
    } else {
        array_push($errors, "Image with given ID doesn't exist.");
    }
} else {
    array_push($errors, "Given image ID isn't a numeric value.");
}


?>

<div class="row">

    <?php
    if ($success) :
    ?>
    <div class="col-12">
        <div class="callout callout-success alert-success">
            <p><strong>Image updated.</strong></p>
            <p><a href="index.php?source=images">Go back to images</a></p>
        </div>
    </div>
    <?php
    endif;
    ?>

    <?php
    displayErrors($errors);
    ?>

    <div class="col-12 mb-3">
        <h2>Edit image information</h2>
        <form class="form-width-700" action="index.php?source=images&editImageID=<?=$_GET['editImageID']?>" method="post">

            <div class="image-upload-preview mb-3">
                <span id="image-upload-text" class="text-muted">
                    Image preview
                </span>
                <?php if ($getImagePath != '' && $getScaledImage != '') :?>
                <a href="../<?=$getImagePath?>"><img class="image-preview" src="../<?=$getScaledImage?>"></a>
                <?php endif; ?>
            </div>

            <div class="mb-3">
                <label for="editImageAlt" class="form-label">Image alternative text</label>
                <input type="text" class="form-control" id="editImageAlt" name="editImageAlt" aria-describedby="altHelp" maxlength="255" value="<?=$getImageAlt?>">
                <div id="altHelp" class="form-text">The alt attribute provides alternative information for an image if a user for some reason cannot view it. Leave empty if image is only decorative.</div>
            </div>

            <div class="mb-3">
                <label for="editImageTitle" class="form-label">Image title</label>
                <input type="text" class="form-control" id="editImageTitle" name="editImageTitle" aria-describedby="titleHelp" maxlength="255" value="<?=$getImageTitle?>">
                <div id="titleHelp" class="form-text">If empty, file name will be the image title.</div>
            </div>


            <button type="submit" class="btn btn-primary " name="editImageButton">Submit</button>
        </form>
    </div>

</div>
