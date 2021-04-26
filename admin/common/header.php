<?php
require_once('../common/db_connection.php');
require_once ('common/functions.php');

if(!isset($_SESSION['userRole']) || $_SESSION['userRole'] != 'admin') {
    header("Location: ../index.php");
    exit;
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


<div class="admin-content">

<!-- TODO toggle sidebar on small devices / position: absolute -->
<div class="admin-sidebar d-flex flex-column p-3 text-white bg-dark" style="width: 280px;">
    <a href=".." class="d-flex align-items-center mb-3 mb-md-0 me-md-auto text-white text-decoration-none">
        Store name
    </a>
    <hr>
    <ul class="nav nav-pills flex-column mb-auto">
        <li class="nav-item">
            <a href="index.php" aria-current="page" class="nav-link active">
                Dashboard
            </a>
        </li>
        <li>
            <a href="index.php?source=categories" class="nav-link text-white">
                Categories
            </a>
        </li>

    </ul>
    <hr>
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
    </div>
</div>