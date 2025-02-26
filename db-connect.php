<?php
session_start();
// Database connection details for PostgreSQL
$host = "localhost";
$username = "postgres";  // change this to your PostgreSQL username
$password = "@Omega_2021";  // change this to your PostgreSQL password
$db_name = "tpfinaldb";

// Create connection
$link = pg_connect("host=$host dbname=$db_name user=$username password=$password");

// Check connection
if (!$link) {
    die("Error: Unable to connect to the database.");
}
?>
