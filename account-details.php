<?php
$errors = [];
$success = false;

/** Update account details **/
if (isset($_POST['accountDetailsSubmit'])) {
    $email = $_POST['email'];
    $currentPassword = $_POST['currentPassword'];
    $newPassword = $_POST['newPassword'];


    if (empty($email)) { array_push($errors, 'Email is required'); }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        array_push($errors, 'Invalid email');
    }

    if (empty($currentPassword)) { array_push($errors, 'Current password is required'); }
    if (strlen($currentPassword) < 7) {
        array_push($errors, 'Current password is too short');
    }

    if (!empty($newPassword)) {
        if (strlen($newPassword) < 7) {
            array_push($errors, 'New password is too short');
        }
    }

    if (strlen($email) > 60) { array_push($errors, "Email is too long. Max 60 characters."); }


    if (count($errors) == 0) {

        // Update email
        if ($email != $_SESSION['userEmail']) {
            // Check if email exists
            $stmt = mysqli_prepare($db, "SELECT COUNT(email) FROM users WHERE email=?");

            mysqli_stmt_bind_param($stmt, 's', $email);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $result);
            mysqli_stmt_fetch($stmt);

            if ($result > 0) {
                array_push($errors, 'User with this email already exists.');
            }

            mysqli_stmt_close($stmt);

            // Update email if given email doesnt exist
            if (count($errors) == 0) {
                $stmt = mysqli_prepare($db, "SELECT password FROM users WHERE email=?");
                mysqli_stmt_bind_param($stmt, 's', $_SESSION['userEmail']);
                mysqli_stmt_execute($stmt);
                $res = mysqli_stmt_get_result($stmt);
                mysqli_stmt_close($stmt);

                $dbPassword = mysqli_fetch_array($res)[0];

                // Check if passwords match
                if(password_verify($currentPassword, $dbPassword)) {
                    $stmt = mysqli_prepare($db, "UPDATE users SET email = ? WHERE email = ?");
                    mysqli_stmt_bind_param($stmt, 'ss', $email, $_SESSION['userEmail']);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);

                    // Update session email
                    $_SESSION['userEmail'] = $email;

                    $success = true;

                } else {
                    array_push($errors, 'Wrong password.');
                }
            }
        }

        // Update password
        if (!empty($newPassword)) {
            // Get password from database
            $stmt = mysqli_prepare($db, "SELECT password FROM users WHERE email=?");
            mysqli_stmt_bind_param($stmt, 's', $_SESSION['userEmail']);
            mysqli_stmt_execute($stmt);
            $res = mysqli_stmt_get_result($stmt);
            mysqli_stmt_close($stmt);

            $dbPassword = mysqli_fetch_array($res)[0];

            // Check if passwords match
            if(password_verify($currentPassword, $dbPassword)) {
                $newPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt = mysqli_prepare($db, "UPDATE users SET password = ? WHERE email = ?");
                mysqli_stmt_bind_param($stmt, 'ss', $newPassword, $_SESSION['userEmail']);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);

                $success = true;

            } else {
                array_push($errors, 'Wrong password.');
            }
        }

    }

}
?>

<h2 class="mb-3">Account details</h2>


<div class="row">
    <div class="col-12">

        <div class="account-details-wrapper shadow-sm">

            <?php
            displayErrors($errors);

            if ($success) :
            ?>
            <div class="callout callout-success alert-success">
                Account details updated successfully.
            </div>
            <?php
            endif;
            ?>


            <form action="my-account.php?source=account-details" method="post">

                <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <input type="email" id="email" name="email" class="form-control" maxlength="60" value="<?=$_SESSION['userEmail']?>" required>
                </div>

                <div class="mb-3">
                    <label for="currentPassword" class="form-label">Current password</label>
                    <input type="password" id="currentPassword" name="currentPassword" class="form-control" aria-describedby="currentPasswordHelp" minlength="7" required>
                    <div id="currentPasswordHelp" class="form-text">Type in your current password if you want to change your email or password.</div>
                </div>

                <div class="mb-3">
                    <label for="newPassword" class="form-label">New password</label>
                    <input type="password" id="newPassword" name="newPassword" class="form-control" minlength="7">
                </div>

                <input type="submit" class="btn btn-primary" name="accountDetailsSubmit" value="Submit">

            </form>

        </div>

    </div>
</div>