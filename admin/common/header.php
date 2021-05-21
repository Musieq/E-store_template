<?php
require_once('../common/db_connection.php');
require_once ('../common/functions.php');

if(!isset($_SESSION['userRole']) || $_SESSION['userRole'] != 'admin') {
    header("Location: ../index.php");
    exit;
}

if (isset($_SESSION['lastActivity']) && (time() - $_SESSION['lastActivity'] > 1800)) {
    header("Location: ../common/logout.php");
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

    <!-- Bootstrap CSS -->
    <link href="../css/bootstrap.css" rel="stylesheet" type="text/css">

    <!-- CSS -->
    <link href="../css/styles.css" rel="stylesheet" type="text/css">


    <title>Store Name - admin panel</title>
</head>
<body>


<?php
$currentPage = $_GET['source'] ?? '';
?>

<div class="admin-content">

<div class="navbar navbar-expand-lg navbar-dark admin-sidebar text-white bg-dark">
    <div class="container-fluid container-lg d-lg-flex flex-lg-column p-lg-3 mb-lg-auto align-items-lg-start">
        <a class="navbar-brand mb-lg-3" href="..">Store name</a>


        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="nav nav-pills flex-column mb-auto pt-3 pt-lg-0">
                <li class="nav-item">
                    <a href="index.php" <?php if($currentPage == '') echo "aria-current='page'" ?> class="nav-link text-white <?php if($currentPage == '') echo "active" ?>">
                        Dashboard
                    </a>
                </li>

                <li class="nav-item">
                    <a href="index.php?source=images" <?php if($currentPage == 'images') echo "aria-current='page'" ?> class="nav-link text-white <?php if($currentPage == 'images') echo "active" ?>">
                        Images
                    </a>

                    <ul class="nav nav-pills nav-child">
                        <li class="nav-item">
                            <a href="index.php?source=images&uploadImage=1" class="nav-link text-white">
                                Upload image
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="index.php?source=products" <?php if($currentPage == 'products') echo "aria-current='page'" ?> class="nav-link text-white <?php if($currentPage == 'products') echo "active" ?>">
                        Products
                    </a>

                    <ul class="nav nav-pills nav-child">
                        <li class="nav-item">
                            <a href="index.php?source=products&addProduct=1" class="nav-link text-white">
                                Add product
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a href="index.php?source=categories" <?php if($currentPage == 'categories') echo "aria-current='page'" ?> class="nav-link text-white <?php if($currentPage == 'categories') echo "active" ?>">
                        Categories
                    </a>
                </li>

            </ul>
        </div>
    </div>
<!--    <hr>
    <div class="dropdown">
        <a href="#" class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="https://github.com/mdo.png" alt="mdo" width="32" height="32" class="rounded-circle me-2">
            <strong>mdo</strong>
        </a>
        <ul class="dropdown-menu dropdown-menu-dark text-small shadow" aria-labelledby="dropdownUser1">
            <li><a class="dropdown-item" href="#">New project...</a></li>
            <li><a class="dropdown-item" href="#">Settings</a></li>
            <li><a class="dropdown-item" href="#">Profile</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="#">Sign out</a></li>
        </ul>
    </div>-->
</div>