<?php
$errors = [];
$currency = getCurrency($db);


if (isset($_GET['orderStatus'])) {
    $orderStatus = $_GET['orderStatus'];
} else {
    $orderStatus = '0';
}

// pagination variables
$limit = 50;
$currentPage = $_GET['page'] ?? 1;
$offset = ($currentPage - 1) * $limit;
if ($orderStatus == '0') {
    $ordersCountQuery = mysqli_query($db, "SELECT COUNT(order_id) FROM orders");
} else {
    $ordersCountQuery = mysqli_query($db, "SELECT COUNT(order_id) FROM orders WHERE order_status = '$orderStatus'");
}

$ordersCount = mysqli_fetch_array($ordersCountQuery)[0];
$pages =  ceil($ordersCount / $limit);



?>

<div class="row">
    <?php
    displayErrors($errors);
    ?>

    <div class="col-12">
        <h2>Orders</h2>

        <form action="index.php" method="get" class="form-width-700">

            <input type="hidden" value="orders" name="source">

            <div class="mb-3">
                <label for="orderStatus" class="form-label">Choose order status</label>
                <select class="form-select" id="orderStatus" name="orderStatus">
                    <option value="0" <?php if($orderStatus == '0'){ echo 'selected'; } ?>>All orders</option>
                    <option value="Pending payment" <?php if($orderStatus == 'Pending payment'){ echo 'selected'; } ?>>Pending payment</option>
                    <option value="Processing" <?php if($orderStatus == 'Processing'){ echo 'selected';} ?>>Processing</option>
                    <option value="Completed" <?php if($orderStatus == 'Completed'){ echo 'selected';} ?>>Completed</option>
                    <option value="Cancelled" <?php if($orderStatus == 'Cancelled'){ echo 'selected';} ?>>Cancelled</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary " name="orderStatusSubmit" value="1">Submit</button>
        </form>


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
                if ($orderStatus == '0') {
                    $stmt = mysqli_prepare($db, "SELECT order_id, order_cost, order_status, order_date FROM orders ORDER BY order_id DESC LIMIT $limit OFFSET $offset");
                } else {
                    $stmt = mysqli_prepare($db, "SELECT order_id, order_cost, order_status, order_date FROM orders WHERE order_status = ? ORDER BY order_id DESC LIMIT $limit OFFSET $offset");
                    mysqli_stmt_bind_param($stmt, 's', $orderStatus);
                }

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
