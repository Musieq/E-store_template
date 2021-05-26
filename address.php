<?php
$errors = [];
$success = false;


/** Update user info **/
if (isset($_POST['addressSubmit'])) {
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $phone = $_POST['phone'];
    $city = $_POST['city'];
    $zip = $_POST['zip'];
    $street = $_POST['street'];
    $apartment = $_POST['apartment'];

    if (strlen($firstName) > 50) { array_push($errors, "First name is too long. Max 50 characters."); }
    if (strlen($lastName) > 50) { array_push($errors, "Last name is too long. Max 50 characters."); }
    if (strlen($phone) > 20) { array_push($errors, "Phone number is too long. Max 20 characters."); }
    if (strlen($city) > 100) { array_push($errors, "City is too long. Max 100 characters."); }
    if (strlen($zip) > 10) { array_push($errors, "Postal code is too long. Max 10 characters."); }
    if (strlen($street) > 100) { array_push($errors, "Street is too long. Max 100 characters."); }
    if (strlen($apartment) > 25) { array_push($errors, "Apartment is too long. Max 25 characters."); }

    if (count($errors) == 0) {
        $stmt = mysqli_prepare($db, "UPDATE user_informations SET first_name = ?, last_name = ?, telephone = ?, city = ?, street = ?, postal_code = ?, apartment = ? WHERE user_id = ?");
        mysqli_stmt_bind_param($stmt, 'sssssssi', $firstName, $lastName, $phone, $city, $street, $zip, $apartment, $_SESSION['userID']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $success = true;
    }
}



/** Get user info from DB **/
$stmt = mysqli_prepare($db, "SELECT * FROM user_informations WHERE user_id = ?");
mysqli_stmt_bind_param($stmt, 'i', $_SESSION['userID']);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
mysqli_stmt_close($stmt);

while ($resArr = mysqli_fetch_assoc($res)) {
    $dbFirstName = $resArr['first_name'];
    $dbLastName = $resArr['last_name'];
    $dbCity = $resArr['city'];
    $dbStreet = $resArr['street'];
    $dbZip = $resArr['postal_code'];
    $dbApartment = $resArr['apartment'];
    $dbTelephone = $resArr['telephone'];
}


?>


<h2 class="mb-3">Address</h2>


<div class="row">
    <div class="col-12">

        <div class="addresses-wrapper shadow-sm">

            <?php
            displayErrors($errors);

            if ($success) :
                ?>
                <div class="callout callout-success alert-success">
                    Address updated successfully.
                </div>
            <?php
            endif;
            ?>


            <form action="my-account.php?source=address" method="post">

                <div class="row g-3 mb-3">
                    <div class="col-sm">
                        <label for="firstName" class="form-label">First name</label>
                        <input type="text" class="form-control" id="firstName" name="firstName" value="<?=$dbFirstName?>" maxlength="50" autocomplete="given-name">
                    </div>

                    <div class="col-sm">
                        <label for="lastName" class="form-label">Last name</label>
                        <input type="text" class="form-control" id="lastName" name="lastName" value="<?=$dbLastName?>" maxlength="50" autocomplete="family-name">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label">Phone number</label>
                    <input type="text" class="form-control" id="phone" name="phone" value="<?=$dbTelephone?>" maxlength="20" autocomplete="tel">
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-sm-8">
                        <label for="city" class="form-label">City</label>
                        <input type="text" class="form-control" id="city" name="city" value="<?=$dbCity?>" maxlength="100" autocomplete="address-level2">
                    </div>

                    <div class="col-sm-4">
                        <label for="zip" class="form-label">Zip</label>
                        <input type="text" class="form-control" id="zip" name="zip" value="<?=$dbZip?>" maxlength="10" autocomplete="postal-code">
                    </div>
                </div>

                <div class="row g-3 mb-3">
                    <div class="col-sm-8">
                        <label for="street" class="form-label">Street</label>
                        <input type="text" class="form-control" id="street" name="street" value="<?=$dbStreet?>" maxlength="100" autocomplete="address-line1">
                    </div>

                    <div class="col-sm-4">
                        <label for="apartment" class="form-label">Apartment</label>
                        <input type="text" class="form-control" id="apartment" name="apartment" value="<?=$dbApartment?>" maxlength="25" autocomplete="address-line2">
                    </div>
                </div>

                <input type="submit" class="btn btn-primary" name="addressSubmit" value="Submit">

            </form>

        </div>

    </div>
</div>