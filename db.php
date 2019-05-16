<?php

// database credits
 $db_host   = "localhost";
 $db_user   = "root";
 $db_pass   = "1234";
 $db        = "example";


$connection = mysqli_connect($dbhost, $db_user, $db_pass, $db);

if (!$connection) {
    echo "Error: Unable to connect to MySQL => ".mysqli_connect_errno();
    exit;
}
?>