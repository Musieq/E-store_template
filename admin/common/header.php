<?php
require_once('../common/db_connection.php');
require_once ('../common/functions.php');

$errors = [];

if(!isset($_SESSION['userRole']) || $_SESSION['userRole'] != 'admin') {
    header("Location: ../index.php");
    exit();
}

if (isset($_SESSION['lastActivity']) && (time() - $_SESSION['lastActivity'] > 1800)) {
    header("Location: ../common/logout.php");
    exit();
} elseif (isset($_SESSION['lastActivity'])) {
    $_SESSION['lastActivity'] = time();
}

?>

<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="robots" content="noindex, nofollow">

    <!-- Bootstrap CSS -->
    <link href="../css/bootstrap.min.css" rel="stylesheet" type="text/css">

    <!-- CSS -->
    <link href="../css/styles.min.css" rel="stylesheet" type="text/css">


    <title><?=getSiteName($db)?> - admin panel</title>
</head>
<body>


<?php
$currentPage = $_GET['source'] ?? '';
?>

<div class="admin-content">

<div class="navbar navbar-expand-lg navbar-dark admin-sidebar text-white bg-dark">
    <div class="container-fluid container-lg d-lg-flex flex-lg-column p-lg-3 mb-lg-auto align-items-lg-start">
        <a class="navbar-brand mb-lg-3" href=".."><?=getSiteName($db)?></a>


        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="nav nav-pills flex-column mb-auto pt-3 pt-lg-0">
                <li class="nav-item">
                    <a href="index.php?source=orders" <?php if($currentPage == '' || $currentPage == 'orders') echo "aria-current='page'" ?> class="nav-link text-white <?php if($currentPage == '' || $currentPage == 'orders') echo "active" ?>">
                        Orders
                    </a>
                </li>

                <li class="nav-item">
                    <a href="index.php?source=images" <?php if($currentPage == 'images' && !isset($_GET['uploadImage'])) echo "aria-current='page'" ?> class="nav-link text-white <?php if($currentPage == 'images'  && !isset($_GET['uploadImage'])) echo "active" ?>">
                        Images
                    </a>

                    <ul class="nav nav-pills nav-child">
                        <li class="nav-item">
                            <a href="index.php?source=images&uploadImage=1" <?php if($currentPage == 'images' && isset($_GET['uploadImage'])) echo "aria-current='page'" ?> class="nav-link text-white <?php if($currentPage == 'images'  && isset($_GET['uploadImage'])) echo "active" ?>">
                                Upload image
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="index.php?source=products" <?php if($currentPage == 'products' && !isset($_GET['addProduct'])) echo "aria-current='page'" ?> class="nav-link text-white <?php if($currentPage == 'products' && !isset($_GET['addProduct'])) echo "active" ?>">
                        Products
                    </a>

                    <ul class="nav nav-pills nav-child">
                        <li class="nav-item">
                            <a href="index.php?source=products&addProduct=1" <?php if($currentPage == 'products' && isset($_GET['addProduct'])) echo "aria-current='page'" ?> class="nav-link text-white <?php if($currentPage == 'products' && isset($_GET['addProduct'])) echo "active" ?>">
                                Add product
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="index.php?source=shippingOptions" <?php if($currentPage == 'shippingOptions') echo "aria-current='page'" ?> class="nav-link text-white <?php if($currentPage == 'shippingOptions') echo "active" ?>">
                        Shipping options
                    </a>
                </li>

                <li class="nav-item">
                    <a href="index.php?source=categories" <?php if($currentPage == 'categories') echo "aria-current='page'" ?> class="nav-link text-white <?php if($currentPage == 'categories') echo "active" ?>">
                        Categories
                    </a>
                </li>

                <li class="nav-item">
                    <a href="index.php?source=settings" <?php if($currentPage == 'settings') echo "aria-current='page'" ?> class="nav-link text-white <?php if($currentPage == 'settings') echo "active" ?>">
                        Settings
                    </a>
                </li>

            </ul>
        </div>
    </div>
</div>