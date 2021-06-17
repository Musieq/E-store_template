<?php
$errors = [];



/** Delete image **/
if (isset($_GET['deleteImageID'])) {
    $deleteImageID = $_GET['deleteImageID'];
    deleteImage($db, $deleteImageID);
}


/** Bulk delete **/
if (isset($_POST['imageBulkOption'])) {
    if ($_POST['imageBulkOption'] == 1) {
        if(!empty($_POST['imageDeleteCheckbox'])) {
            foreach ($_POST['imageDeleteCheckbox'] as $imageBulkDeleteID) {
                deleteImage($db, $imageBulkDeleteID);
            }
        }
    }
}


?>

<div class="row">

    <?php
    displayErrors($errors);
    ?>


    <div class="col-12">
        <h2>Uploaded images</h2>
        <div class="alert-warning callout callout-warning"><strong>Do not remove</strong> uploaded images directly from the directory.</div>


        <!-- Search images by date and title -->
        <form class="row form-width-700 g-3 mb-3" method="get">
            <input type="hidden" name="source" value="images">

            <div class="col-md-6">
                <label for="imageFilterTitle" class="form-label">Search for an image by title</label>
                <input type="text" class="form-control" id="imageFilterTitle" name="imageFilterTitle" value="<?=$_GET['imageFilterTitle'] ?? ''?>">
            </div>

            <div class="col-md-6">
                <label for="imageFilterDate" class="form-label">Select the date of upload</label>
                <select class="form-select" id="imageFilterDate" name="imageFilterDate">
                    <option value="0">Every image</option>
                    <?php
                    /** Create options for select field with dates - image filters **/
                    // Get dates of images uploads
                    $dateQuery = mysqli_query($db, "SELECT upload_date FROM images");
                    $dateArray = [];
                    // Create array with month and year
                    while ($dateQueryResult = mysqli_fetch_assoc($dateQuery)) {
                        $date = strtotime($dateQueryResult['upload_date']);
                        $yearAndMonth = date("F Y", $date);
                        if (!in_array($yearAndMonth, $dateArray)) {
                            array_push($dateArray, $yearAndMonth);
                        }
                    }
                    $dateFilterSelected = $_GET['imageFilterDate'] ?? 0;
                    // Create options to filter by month/year
                    foreach ($dateArray as $value) {
                        $date = strtotime($value);
                        $dateNumeric = date("Ym", $date);
                        if ($dateFilterSelected == $dateNumeric) {
                            echo "<option value='$dateNumeric' selected>$value</option>";
                        } else {
                            echo "<option value='$dateNumeric'>$value</option>";
                        }
                    }
                    ?>
                </select>
            </div>

            <div class="col-12">
                <button type="submit" class="btn btn-primary" name="imageFilterSubmit" value="filter">Filter</button>
            </div>
        </form>



        <!-- Bulk delete form -->
        <form class="row row-cols-lg-auto g-3" id="imageBulkDeleteForm" name="imageBulkDeleteForm" method="post" action="index.php?<?=http_build_query(array_merge($_GET))?>">

            <div class="col-12">
                <label for="imageBulkOption" class="form-label visually-hidden">Bulk action</label>
                <select class="form-select" id="imageBulkOption" name="imageBulkOption">
                    <option value="0" selected>Bulk action</option>
                    <option value="1">Delete all</option>
                </select>
            </div>

            <div class="col-12">
                <button type="button" class="btn btn-primary" name="imageBulkDelete" onclick="bulkDeleteModal('imageBulkDeleteForm', 'imageBulkOption', 'modalImageDeleteWarning', 'deleteImageConfirm')">Submit</button>
            </div>



            <div class="table-responsive" style="width: 100%">
                    <table class="table table-images">
                        <thead>
                        <tr>
                            <th scope="col"><input type="checkbox" class="form-check-input" aria-label="Check to delete every image shown below" id="imageDeleteSelectAll" onclick="selectCheckboxes(this.id, 'imageDeleteCheckbox[]')"></th>
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
                        // Limit for images per page
                        $limit = 50;


                        /** Filter images by title and/or by chosen date **/
                        $imageFiltersArr = imageFilters($db, $limit);
                        $displayImagesQuery = $imageFiltersArr[0];
                        $pages = $imageFiltersArr[1];
                        $currentPage = $imageFiltersArr[2];

                        // Execute prepared statement
                        mysqli_stmt_execute($displayImagesQuery);
                        $displayImagesGetResults = mysqli_stmt_get_result($displayImagesQuery);

                        /** Display images **/
                        while($displayImagesResults = mysqli_fetch_assoc($displayImagesGetResults)){
                            $imageFieldsArr = getImageFields($displayImagesResults);

                            // Create URL when deleting images after applying filters
                            $deleteURL = http_build_query(array_merge($_GET,['deleteImageID' => $imageFieldsArr['imageID']]));
                            ?>

                            <tr>
                                <td><input type="checkbox" name="imageDeleteCheckbox[]" class="form-check-input" value="<?=$imageFieldsArr['imageID']?>" aria-label="Check for bulk deleting image <?=$imageFieldsArr['imagetitle']?>"></td>
                                <td><a href="../<?=$imageFieldsArr['fullPath']?>">
                                        <div class="admin-image-container" style="background-image: url('../<?=$imageFieldsArr['path']?>-thumbnail.<?=$imageFieldsArr['extension']?>') "></div>
                                    </a>
                                </td>
                                <td><?=$imageFieldsArr['imageTitle']?></td>
                                <td><?=$imageFieldsArr['imageAlt']?></td>
                                <td><?=$imageFieldsArr['imageUploadDate']?></td>
                                <td><a href="index.php?source=images&editImageID=<?=$imageFieldsArr['imageID']?>">Edit</td>
                                <td><a href="index.php?<?=$deleteURL?>" class="link-danger delete-image-link" data-bs-toggle="modal" data-bs-target="#modalImageDeleteWarning">Delete</td>
                            </tr>

                            <?php
                        }
                        mysqli_stmt_close($displayImagesQuery)

                        ?>

                        </tbody>
                    </table>
                </div>



        </form>

        <?php
        // Create pagination if there is more than 1 page
        createPagination('Pagination for images in product add', $pages, $currentPage, 'index.php');
        ?>




    </div>

</div>



<!-- Modal - delete image -->
<div class="modal fade" id="modalImageDeleteWarning" tabindex="-1" aria-labelledby="modalImageDeleteWarningLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalImageDeleteWarningLabel">Delete image</h5>
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