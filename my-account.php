<?php
require_once('common/header.php');

if (!isset($_SESSION['userID'])) {
    ?>
    <script> location.replace("index.php"); </script>
<?php
    exit;
}
?>


<div class="container-fluid container-xl mt-3">
    <div class="row">

    <div class="col-md-4 col-lg-3">
        <div class="my-account-sidebar-wrapper">
            <div class="d-flex justify-content-end my-account-toggle-container d-md-none">
                <h4>
                    <a class="my-account-sidebar-toggle" data-bs-toggle="collapse" href="#myAccountSidebar" role="button" aria-expanded="false" aria-controls="myAccountSidebar">
                        Menu
                        <span class="my-account-toggle-icon">
                            <svg width="1em" height="1em" viewBox="0 0 16 16" class="bi bi-menu-up" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                              <path fill-rule="evenodd" d="M15 3.207v9a1 1 0 0 1-1 1h-3.586A2 2 0 0 0 9 13.793l-1 1-1-1a2 2 0 0 0-1.414-.586H2a1 1 0 0 1-1-1v-9a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1zm-13 11a2 2 0 0 1-2-2v-9a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v9a2 2 0 0 1-2 2h-3.586a1 1 0 0 0-.707.293l-1.353 1.354a.5.5 0 0 1-.708 0L6.293 14.5a1 1 0 0 0-.707-.293H2z"></path>
                              <path fill-rule="evenodd" d="M15 5.207H1v1h14v-1zm0 4H1v1h14v-1zm-13-5.5a.5.5 0 0 0 .5.5h6a.5.5 0 1 0 0-1h-6a.5.5 0 0 0-.5.5zm0 4a.5.5 0 0 0 .5.5h11a.5.5 0 0 0 0-1h-11a.5.5 0 0 0-.5.5zm0 4a.5.5 0 0 0 .5.5h8a.5.5 0 0 0 0-1h-8a.5.5 0 0 0-.5.5z"></path>
                            </svg>
                        </span>
                    </a>
                </h4>
            </div>

            <nav id="myAccountSidebar" class="shadow-sm my-account-sidebar d-md-block collapse">
                <div class="my-account-links shadow-sm">
                    <ul class="nav">

                        <li class="nav-item">
                            <a class="nav-link" href="my-account.php?source=orders">Orders</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="my-account.php?source=address">Address</a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link" href="my-account.php?source=account-details">Account details</a>
                        </li>

                        <?php
                        if (isset($_SESSION['userRole']) && $_SESSION['userRole'] == 'admin') :
                            ?>
                            <li class="nav-item">
                                <a class="nav-link" href="admin">Admin panel</a>
                            </li>
                        <?php
                        endif;
                        ?>

                        <li class="nav-item">
                            <a class="nav-link" href="common/logout.php">Logout</a>
                        </li>
                    </ul>

                </div>
            </nav>
        </div>
    </div>


    <div class="col-md-8 col-lg-9">
        <div class="my-account-content-wrapper">

            <?php

            if (isset($_GET['source'])) {
                $source = $_GET['source'];
            } else {
                $source = '';
            }

            switch ($source) {
                case 'address':
                    include 'address.php';
                    break;

                case 'account-details':
                    include 'account-details.php';
                    break;

                default:
                    include 'orders.php';
                    break;
            }

            ?>

        </div>
    </div>


    </div>
</div>





<?php
require_once('common/footer.php');
?>


