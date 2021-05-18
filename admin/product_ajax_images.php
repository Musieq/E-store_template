<?php
require_once ("../common/db_connection.php");
require_once ("../common/functions.php");
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

    ?>

    <li role="checkbox" tabindex="0" aria-checked="false" aria-label="<?=$imageFieldsArr['imageTitle']?>" class="imageList list-unstyled" data-id="<?=$imageFieldsArr['imageID']?>">
        <div class="admin-image-container" style="background-image: url('../<?=$imageFieldsArr['path']?>-thumbnail.<?=$imageFieldsArr['extension']?>') "></div>
    </li>

    <?php
}
mysqli_stmt_close($displayImagesQuery);

createPagination('Pagination for images in product add', $pages, $currentPage, 'product_ajax_images.php');
?>


