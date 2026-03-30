<?php
$con = mysqli_connect("localhost", "root", "", "photo_gallery");

if (!$con) {
    die("Connection failed: " . mysqli_connect_error());
}
?>