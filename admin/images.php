<?php
$errors = [];


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

            // TODO remove connection to products in DB

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


    <div class="col-12">
        <h2>Uploaded images</h2>
        <div class="alert-warning callout callout-warning"><strong>Do not remove</strong> uploaded images directly from the directory.</div>


        <!-- Search images by date and title -->
        <form class="row form-width-700 g-3 mb-3" method="post" action="index.php?source=images">
            <div class="col-md-6">
                <label for="imageSearch" class="form-label">Search for an image by title</label>
                <input type="text" class="form-control" id="imageSearch" name="imageSearch">
            </div>

            <div class="col-md-6">
                <label for="imageDate" class="form-label">Select the date of upload</label>
                <select class="form-select" id="imageDate" name="imageDate">
                    <option value="0" selected>Every image</option>
                </select>
            </div>

            <div class="col-12">
                <button type="submit" class="btn btn-primary " name="imageFilter">Filter</button>
            </div>
        </form>



        <!-- Bulk delete form -->
        <!-- TODO show modal before deleting -->
        <form class="row row-cols-lg-auto g-3" method="post" action="index.php?source=images">

            <div class="col-12">
                <label for="imageDate" class="form-label visually-hidden">Bulk action</label>
                <select class="form-select" id="imageDate" name="imageDate">
                    <option value="0" selected>Bulk action</option>
                    <option value="1">Delete all</option>
                </select>
            </div>

            <div class="col-12">
                <button type="submit" class="btn btn-primary " name="imageFilter">Submit</button>
            </div>
        </form>


<div class="table-responsive">
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

                // Variables for pagination
                $imagesCountQuery = mysqli_query($db, "SELECT COUNT(id) FROM images");
                $imagesCount = mysqli_fetch_array($imagesCountQuery)[0];
                $limit = 20;
                $pages = ceil($imagesCount / $limit);
                $currentPage = $_GET['page'] ?? 1;
                $offset = ($currentPage - 1) * $limit;

                /** Display all images **/
                $displayImagesQuery = mysqli_query($db, "SELECT * FROM images ORDER BY upload_date DESC LIMIT $limit OFFSET $offset");
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
</div>

        <?php
            // Create pagination if there is more than 1 page
            if ($pages > 1) :
            ?>

                <nav aria-label="Pagination for images">
                    <ul class="pagination justify-content-center">

                        <?php
                        if ($pages <= 3) :
                            for ($i = 1; $i <= $pages; $i++) :
                            ?>

                                <li class="page-item <?php if($i == $currentPage) { echo 'active'; } ?>" <?php if($i == $currentPage) { echo 'aria-current="page"'; } ?>>
                                    <a class="page-link" href="index.php?source=images&page=<?=$i?>"><?=$i?></a>
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
                            ?>
                                <li class="page-item">
                                    <a class="page-link" href="index.php?source=images&page=1">First</a>
                                </li>
                            <?php
                            endif;
                            ?>

                            <?php
                            if ($currentPage > 1) :
                            ?>
                                <li class="page-item">
                                    <a class="page-link" href="index.php?source=images&page=<?=$currentPage-1?>"><?=$currentPage-1?></a>
                                </li>
                            <?php
                            endif;
                            ?>

                                <li class="page-item active" aria-current="page">
                                    <span class="page-link"><?=$currentPage?></span>
                                </li>

                            <?php
                            if ($currentPage < $pages) :
                            ?>
                                <li class="page-item">
                                    <a class="page-link" href="index.php?source=images&page=<?=$currentPage+1?>"><?=$currentPage+1?></a>
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
                                ?>
                                <li class="page-item">
                                    <a class="page-link" href="index.php?source=images&page=<?=$pages?>">Last</a>
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
        ?>




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