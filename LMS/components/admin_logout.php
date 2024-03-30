<?php

include 'connect.php'; // Include the file containing database connection code

// Unset the cookie for tutor_id
if (isset($_COOKIE['tutor_id'])) {
    unset($_COOKIE['tutor_id']);
    // Set the expiration time to a past value to immediately expire the cookie
    setcookie('tutor_id', '', time() - 1, '/');
}

// Redirect to the login page
header('location:../admin/login.php');
exit; // It's a good practice to exit after redirecting to prevent further execution
?>
