<?php
$connection = mysqli_connect("localhost", "user", "password", "database");

if (!$connection) {
    echo "Error: Unable to connect to MySQL => ".mysqli_connect_errno();
    exit;
}
?>