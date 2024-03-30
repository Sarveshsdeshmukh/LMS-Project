<?php
// Include the file containing the database connection
include 'connect.php';

// Unset the cookie for user_id
if (isset($_COOKIE['user_id'])) {
    unset($_COOKIE['user_id']);
    // Set the expiration time to a past value to immediately expire the cookie
    setcookie('user_id', '', time() - 1, '/');
}

// Redirect to the home page
header('location:../home.php');
exit; // It's a good practice to exit after redirecting to prevent further execution
?>
