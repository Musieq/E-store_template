<?php

if (isset($_POST['btnLogin'])) {

    $email = $_POST['loginEmail'];
    $password = $_POST['loginPassword'];
    $rememberMe = '';
    if(isset($_POST['loginRememberMe'])) { $rememberMe = $_POST['loginRememberMe']; };

    if (empty($email)) { array_push($loginErrors, 'Email is required'); }
    if (empty($password)) { array_push($loginErrors, 'Password is required'); }

    if (count($loginErrors) == 0) {
        $stmt = mysqli_prepare($db, "SELECT user_id, password, role FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $userID,$passwordDB, $userRole);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);

        // Check if passwords match
        if(password_verify($password, $passwordDB)) {
            // Fetch account data
            $query = mysqli_query($db, "SELECT first_name FROM user_informations WHERE user_id = '$userID'");

            // Create session variables
            while ($result = mysqli_fetch_assoc($query)) {
                $_SESSION['userFirstName'] = $result['first_name'];
            }
            $_SESSION['userID'] = $userID;
            $_SESSION['userRole'] = $userRole;

            // "Remember me" button
            if (!isset($_POST['loginRememberMe'])) {
                $_SESSION['lastActivity'] = time();
            }

        } else {
            array_push($loginErrors, 'Incorrect account details.');
        }
    }
}