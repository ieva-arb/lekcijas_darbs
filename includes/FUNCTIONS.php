<?php
function test_input($data) {
    $data = trim($data);          // remove spaces
    $data = stripslashes($data);  // remove backslashes
    $data = htmlspecialchars($data); // prevent HTML injection
    return $data;
}
?>