<?php
?>

<div class="row">

    <div class="col-12">
        <form class="form-width-700" action="index.php?source=images" method="post">
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
