<?php
require_once ('common/db_connection.php');
require_once ('common/functions.php');

// Include login/register page when not logged in.
if (!isset($_SESSION['userID'])) {
    require_once ('common/login.php');
    require_once ('common/register.php');
}

?>

<!doctype html>
<html lang="en">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="css/bootstrap.css" rel="stylesheet" type="text/css">

    <!-- Website CSS -->
    <link href="css/styles.css" rel="stylesheet" type="text/css">

    <title>Store template</title>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm sticky-top">
    <div class="container-fluid container-xl">
        <a class="navbar-brand" href="./">Store name</a>





        <div class="order-4 order-lg-1">
            <form class="d-flex" method="get" action="index.php">
                <input class="form-control me-2" type="search" placeholder="Search" aria-label="Search" name="query">
                <button class="btn btn-outline-primary" type="submit">Search</button>
            </form>
        </div>




        <div class="navbar-no-collapse order-2 ms-auto">
            <ul>
                <li class="cart-wrapper">
                    <a href="#">
                        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" class="bi bi-cart" viewBox="0 0 16 16">
                            <path d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5zM3.102 4l1.313 7h8.17l1.313-7H3.102zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2zm7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                        </svg>
                    </a>
                </li>

                <li class="my-account-wrapper position-relative">

                    <a href="<?php echo !isset($_SESSION['userID']) ? '#' : 'myaccount.php'; ?>" id="my-account-show-login-form">
                        <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
                            <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                            <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8zm8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1z"/>
                        </svg>
                    </a>



                    <?php
                    // TODO move to login.php / register.php and include
                    if (!isset($_SESSION['userID'])) :
                    ?>

                    <div class="my-account-login-background">
                    </div>
                    <div class="my-account-login-wrap">
                        <div class="my-account-login-form-wrap">
                            <div class="my-account-login-form">
                                <ul class="nav nav-tabs" id="loginTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link active" id="login-tab" data-bs-toggle="tab" data-bs-target="#login" type="button" role="tab" aria-controls="login" aria-selected="true">Login</a>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <a class="nav-link" id="register-tab" data-bs-toggle="tab" data-bs-target="#register" type="button" role="tab" aria-controls="register" aria-selected="false">Register</a>
                                    </li>
                                </ul>

                                <div class="tab-content" id="myTabContent">
                                    <div class="tab-pane fade show active" id="login" role="tabpanel" aria-labelledby="login-tab">


                                        <h1 class="text-center">Login</h1>
                                        <form method="post" action="">
                                            <div class="mb-3">
                                                <label for="loginEmail" class="form-label">Email address</label>
                                                <input type="email" class="form-control" id="loginEmail" name="loginEmail">
                                            </div>
                                            <div class="mb-3">
                                                <label for="loginPassword" class="form-label">Password</label>
                                                <input type="password" class="form-control" id="loginPassword" name="loginPassword">
                                            </div>
                                            <div class="mb-3 form-check">
                                                <input type="checkbox" class="form-check-input" id="loginRememberMe" name="loginRememberMe">
                                                <label class="form-check-label" for="loginRememberMe">Remember me</label>
                                            </div>
                                            <button type="submit" class="btn btn-primary" name="btnLogin">Login</button>
                                        </form>


                                    </div>

                                    <div class="tab-pane fade" id="register" role="tabpanel" aria-labelledby="register-tab">

                                        <!-- TODO validate form -->
                                        <!-- TODO autocomplete dla formularzy / https://developers.google.com/web/fundamentals/design-and-ux/input/forms -->
                                        <h1 class="text-center">Register</h1>
                                        <form method="post" action="">
                                            <div class="mb-3">
                                                <label for="registerEmail" class="form-label">Email address</label>
                                                <input type="email" class="form-control" id="registerEmail" name="registerEmail">
                                            </div>
                                            <div class="mb-3">
                                                <label for="registerPassword" class="form-label">Password</label>
                                                <input type="password" class="form-control" id="registerPassword" name="registerPassword">
                                            </div>
                                            <div class="row g-3 mb-3">
                                                <div class="col-sm">
                                                    <label for="registerFirstName" class="form-label">First name</label>
                                                    <input type="text" class="form-control" id="registerFirstName" name="registerFirstName">
                                                </div>
                                                <div class="col-sm">
                                                    <label for="registerLastName" class="form-label">Last name</label>
                                                    <input type="text" class="form-control" id="registerLastName" name="registerLastName">
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="registerPhone" class="form-label">Phone number</label>
                                                <input type="text" class="form-control" id="registerPhone" name="registerPhone">
                                            </div>
                                            <div class="row g-3 mb-3">
                                                <div class="col-sm-8">
                                                    <label for="registerCity" class="form-label">City</label>
                                                    <input type="text" class="form-control" id="registerCity" name="registerCity">
                                                </div>
                                                <div class="col-sm-4">
                                                    <label for="registerZip" class="form-label">Zip</label>
                                                    <input type="text" class="form-control" id="registerZip" name="registerZip">
                                                </div>
                                            </div>
                                            <div class="row g-3 mb-3">
                                                <div class="col-sm-8">
                                                    <label for="registerStreet" class="form-label">Street</label>
                                                    <input type="text" class="form-control" id="registerStreet" name="registerStreet">
                                                </div>
                                                <div class="col-sm-4">
                                                    <label for="registerApartment" class="form-label">Apartment</label>
                                                    <input type="text" class="form-control" id="registerApartment" name="registerApartment">
                                                </div>
                                            </div>

                                            <div class="mb-3 form-check">
                                                <input type="checkbox" class="form-check-input" id="registerAgree" name="registerAgree">
                                                <label class="form-check-label" for="registerAgree">I agree to to the terms of service</label>
                                            </div>

                                            <button type="submit" class="btn btn-primary" name="btnRegister">Register</button>
                                        </form>


                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php
                    else :
                    ?>

                    <!-- TODO my account links on hover -->

                    <div class="my-account-links shadow">
                        <ul>
                            <li class="nav-item">
                                <a class="nav-link active" href="admin">Panel administratora</a>
                            </li>

                            <li class="nav-item">
                                <a class="nav-link active" href="common/logout.php">Logout</a>
                            </li>
                        </ul>

                    </div>

                    <?php
                    endif;
                    ?>

                </li>
            </ul>
        </div>





    </div>
</nav>