<?php
$errors = [];
$currency = getCurrency($db);

// pagination variables
$limit = 50;
$currentPage = $_GET['page'] ?? 1;
$offset = ($currentPage - 1) * $limit;
$ordersCountQuery = mysqli_query($db, "SELECT COUNT(order_id) FROM orders");
$ordersCount = mysqli_fetch_array($ordersCountQuery)[0];
$pages =  ceil($ordersCount / $limit);

?>

<div class="row">
    <?php
    displayErrors($errors);
    ?>

    <div class="col-12">
        <h2>Orders</h2>


        <div class="table-responsive">
            <table class="table table-orders">
                <thead>
                <tr>
                    <th scope="col">Order</th>
                    <th scope="col">Order date</th>
                    <th scope="col">Order status</th>
                    <th scope="col">Order cost</th>
                    <th scope="col">Details / edit</th>
                </tr>
                </thead>

                <tbody>
                <?php
                /** Get all orders **/
                $stmt = mysqli_prepare($db, "SELECT order_id, order_cost, order_status, order_date FROM orders ORDER BY order_id DESC LIMIT $limit OFFSET $offset");
                mysqli_stmt_execute($stmt);
                $res = mysqli_stmt_get_result($stmt);

                while ($resArr = mysqli_fetch_assoc($res)) {
                    ?>
                    <tr>
                        <td><a href="index.php?source=orders&order-info=<?= $resArr['order_id'] ?>">#<?= $resArr['order_id'] ?></a></td>
                        <td><?=$resArr['order_date']?></td>
                        <td><?=$resArr['order_status']?></td>
                        <td><?=$resArr['order_cost']?> <?=$currency?></td>
                        <td><a href="index.php?source=orders&order-info=<?= $resArr['order_id'] ?>">Details / edit</a></td>
                    </tr>
                    <?php
                }

                ?>
                </tbody>
            </table>
        </div>

        <?php
        createPagination('Admin orders pagination', $pages, $currentPage, 'index.php?source=orders');
        ?>


    </div>

</div>
