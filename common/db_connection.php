<?php
define("serverName", "");
define("serverAdmin", "");
define("serverPassword", "");
define("serverDatabase", "");


// Create connection
$db = mysqli_connect(serverName, serverAdmin, serverPassword, serverDatabase);

// Check connection
if (!$db) {
    die("Connection to database failed: " . mysqli_connect_error());
}

session_start();