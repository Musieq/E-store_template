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
        $password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = mysqli_prepare($db,"INSERT INTO users(email, password) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, "ss", $email, $password);
        mysqli_stmt_execute($stmt);
        $insertedID = mysqli_stmt_insert_id($stmt);
        mysqli_stmt_close($stmt);

        $stmt = mysqli_prepare($db, "INSERT INTO  user_informations(user_id, first_name, last_name, city, street, postal_code, apartment, telephone) 
                                            VALUES(?, ?, ?, ?, ?, ?, ? ,?)");
        mysqli_stmt_bind_param($stmt, "isssssss", $insertedID, $firstName, $lastName, $city, $street, $zip, $apartment, $phoneNumber);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

    }

}