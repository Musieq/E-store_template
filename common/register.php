<?php


if (isset($_POST['btnRegister'])) {

    $email = $_POST['registerEmail'];
    $password = $_POST['registerPassword'];
    $firstName = $_POST['registerFirstName'];
    $lastName = $_POST['registerLastName'];
    $phoneNumber = $_POST['registerPhone'];
    $city = $_POST['registerCity'];
    $zip = $_POST['registerZip'];
    $street = $_POST['registerStreet'];
    $apartment = $_POST['registerApartment'];
    $agreement = '';
    if(isset($_POST['registerAgree'])) { $agreement = $_POST['registerAgree']; };


    // Check for required fields
    if (empty($email)) { array_push($registerErrors, 'Email is required'); }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        array_push($registerErrors, 'Invalid email');
    }

    if (empty($password)) { array_push($registerErrors, 'Password is required'); }
    if (strlen($password) < 7) {
        array_push($registerErrors, 'Password is too short');
    }

    if (!$agreement) { array_push($registerErrors, 'You have to accept our terms of service'); }


    // Check if data is not too long
    if (strlen($firstName) > 50) { array_push($registerErrors, "First name is too long. Max 50 characters."); }
    if (strlen($lastName) > 50) { array_push($registerErrors, "Last name is too long. Max 50 characters."); }
    if (strlen($phoneNumber) > 20) { array_push($registerErrors, "Phone number is too long. Max 20 characters."); }
    if (strlen($city) > 100) { array_push($registerErrors, "City is too long. Max 100 characters."); }
    if (strlen($zip) > 10) { array_push($registerErrors, "Postal code is too long. Max 10 characters."); }
    if (strlen($street) > 100) { array_push($registerErrors, "Street is too long. Max 100 characters."); }
    if (strlen($apartment) > 25) { array_push($registerErrors, "Apartment is too long. Max 25 characters."); }
    if (strlen($email) > 60) { array_push($registerErrors, "Email is too long. Max 60 characters."); }

    // Check if email exists
    $stmt = mysqli_prepare($db, "SELECT COUNT(email) FROM users WHERE email=?");

    mysqli_stmt_bind_param($stmt, 's', $email);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $result);
    mysqli_stmt_fetch($stmt);

    if ($result > 0) {
        array_push($registerErrors, 'User with this email already exists.');
    }

    mysqli_stmt_close($stmt);

    // Create account if there are no errors
    if (count($registerErrors) == 0) {
        createAccount($db, $password, $email, $firstName, $lastName, $phoneNumber, $city, $zip, $street, $apartment);
    }

}