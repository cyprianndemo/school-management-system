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

// Retrieve the user record based on userid
$sql = "SELECT userid, password, usertype FROM users WHERE userid=$1";
$result = pg_query_params($link, $sql, array($myid));

if (!$result) {
    die("Database error: " . pg_last_error($link)); // Display database error
}

// Check if the user exists
if (pg_num_rows($result) == 1) {
    $user = pg_fetch_assoc($result);
    $hashedPassword = $user['password'];
    $usertype = $user['usertype'];

    // Verify the password
    if (password_verify($mypassword, $hashedPassword)) {
        // Store session variables
        $_SESSION['login_id'] = $myid;
        $_SESSION['role'] = $usertype;

        // ✅ Update last login timestamp in users table
        pg_query_params($link, "UPDATE users SET last_login = NOW() WHERE userid = $1", array($myid));

        // ✅ Insert login record into parent_logins if the user is a parent
        if ($usertype == 'parent') {
            pg_query_params($link, "INSERT INTO parent_logins (parent_id) VALUES ($1)", array($myid));
        }

        // Redirect based on user type
        switch ($usertype) {
            case "admin":
                header("Location: ../users/admin");
                break;
            case "teacher":
                header("Location: ../users/teacher");
                break;
            case "parent":
                header("Location: ../users/parent");
                break;
            default:
                header("Location: ../index.php?login=false");
                break;
        }
        exit();
    } else {
        header("Location: ../index.php?login=false"); // Incorrect password
        exit();
    }
} else {
    header("Location: ../index.php?login=false"); // User not found
    exit();
}
?>
