<?php
// Include the database connection file
include_once('db-connect.php');

// Get the user ID and password from the login form
$myid = $_POST['myid'];
$mypassword = $_POST['mypassword'];

// Secure the user ID from SQL injection
$myid = stripslashes($myid);

// Start the session
session_start();

// First, retrieve the user record based on userid only
$sql = "SELECT userid, password, usertype FROM users WHERE userid=$1";

// Run the query with prepared statements to prevent SQL injection
$result = pg_query_params($link, $sql, array($myid));

// Check if the query executed successfully
if (!$result) {
    echo "An error occurred.\n";
    exit;
}

// Count the number of rows in the result
$count = pg_num_rows($result);

// If a user is found, verify the password
if ($count == 1) {
    // Fetch the user data
    $user = pg_fetch_assoc($result);
    $hashedPassword = $user['password'];
    $usertype = $user['usertype'];
    
    // Verify the password using password_verify
    if (password_verify($mypassword, $hashedPassword)) {
        // Password is correct, store the user ID in a session variable
        $_SESSION['login_id'] = $myid;
        
        // Redirect based on the user type
        switch ($usertype) {
            case "admin":
                // Redirect to the admin module
                header("Location: ../users/admin");
                break;
            case "teacher":
                // Redirect to the teacher module
                header("Location: ../users/teacher");
                break;
            case "parent":
                // Redirect to the parent module
                header("Location: ../users/parent");
                break;
            default:
                // If the user type doesn't match any case, redirect to the login page with an error
                header("Location: ../index.php?login=false");
                break;
        }
        exit;
    } else {
        // Password is incorrect
        header("Location: ../index.php?login=false");
        exit;
    }
} else {
    // No user found with that ID
    header("Location: ../index.php?login=false");
    exit;
}
?>