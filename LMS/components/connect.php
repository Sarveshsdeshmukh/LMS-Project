<?php

// PostgreSQL connection parameters
$db_host = 'localhost';
$db_port = '5432'; // Default PostgreSQL port number
$db_name = 'course_db';
$db_user = 'postgres';
$db_password = '1234'; // Change this to your actual password

// Establishing connection
$conn = pg_connect("host=$db_host port=$db_port dbname=$db_name user=$db_user password=$db_password");

// Check if connection was successful
if (!$conn) {
    echo "Failed to connect to PostgreSQL: " . pg_last_error();
    exit;
}

if (!function_exists('unique_id')) {
    function unique_id() {
        $str = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $rand = array();
        $length = strlen($str) - 1;
        for ($i = 0; $i < 20; $i++) {
            $n = mt_rand(0, $length);
            $rand[] = $str[$n];
        }
        return implode($rand);
    }
}

?>
