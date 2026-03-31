<?php
session_start();
require_once("includes/CONFIG.php");

// User must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if (isset($_GET['id'])) {
    $photo_id = intval($_GET['id']);
    $user_id  = $_SESSION['user_id'];

    // Check if this photo belongs to the logged-in user
    $check = mysqli_query($con, "
        SELECT filename 
        FROM photos 
        WHERE photo_id = '$photo_id' AND user_id = '$user_id'
        LIMIT 1
    ");

    if ($check && mysqli_num_rows($check) === 1) {
        $photo = mysqli_fetch_assoc($check);
        $filePath = "uploads/" . $photo['filename'];

        // Delete file from server
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        // Delete from DB
        mysqli_query($con, "DELETE FROM photos WHERE photo_id = '$photo_id' AND user_id = '$user_id'");
    }
}

header("Location: index.php");
exit();
