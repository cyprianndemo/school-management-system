<?php
// Check if the user is in the database
include_once('../../db-connect.php');

// Start the session
session_start();
$check = $_SESSION['login_id'];

// Check if the session is set and query the admin table
$session = pg_query($link, "SELECT name FROM admin WHERE id = '$check'");
$row = pg_fetch_array($session);
$login_session = $loged_user_name = $row['name'];

// Check if the user is logged in by checking the session
if (!isset($login_session)) {
    header("Location:../../");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Management System - Parent Registration</title>
    <link rel="stylesheet" href="../../sources/css/styles.css">
</head>
<body>
<header>
    <h1>Add Parents</h1>
</header>
<?php include("navBar.php"); ?>
<main>
    <h2>Parent Registration</h2>
    <?php
    include_once('../../db-connect.php');

    // Process the form submission
    if (isset($_POST['submit'])) {
        // Validate that required fields are not empty
        if (empty($_POST['id']) || empty($_POST['password']) || empty($_POST['fathername'])) {
            echo "<p class='error'>Parent ID, password, and father name are required fields.</p>";
        } else {
            // Set the values of the form to the variables
            $id = pg_escape_string($link, $_POST['id']);
            $password = pg_escape_string($link, $_POST['password']);
            $fathername = pg_escape_string($link, $_POST['fathername']);
            $mothername = pg_escape_string($link, $_POST['mothername']);
            $fatherphone = pg_escape_string($link, $_POST['fatherphone']);
            $motherphone = pg_escape_string($link, $_POST['motherphone']);
            $address = pg_escape_string($link, $_POST['address']);

            // Hash the password before storing it in the database
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Begin a transaction to ensure data consistency
            pg_query($link, "BEGIN");

            // First, insert into the 'parents' table
            $parentsSQL = "INSERT INTO parents (id, password, fathername, mothername, fatherphone, motherphone, address) 
                    VALUES ('$id', '$hashedPassword', '$fathername', '$mothername', '$fatherphone', '$motherphone', '$address')";
            $parentsResult = pg_query($link, $parentsSQL);

            // Then, insert into the 'users' table
            $usersSQL = "INSERT INTO users (userid, password, usertype) VALUES ('$id', '$hashedPassword', 'parent')";
            $usersResult = pg_query($link, $usersSQL);

            // Check if both operations were successful
            if ($parentsResult && $usersResult) {
                pg_query($link, "COMMIT");
                echo "<p class='success'>Parent information and user account created successfully!</p>";
                // Clear form values after successful submission
                $_POST = array();
            } else {
                pg_query($link, "ROLLBACK");
                echo "<p class='error'>Error: Could not save data. " . pg_last_error($link) . "</p>";
            }
        }
    }
    ?>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <table>
            <tr>
                <td>Parent Id:</td>
                <td><input id="id" type="text" name="id" placeholder="Enter Id" value="<?php echo isset($_POST['id']) ? htmlspecialchars($_POST['id']) : ''; ?>" required></td>
            </tr>
            <tr>
                <td>Parent Password:</td>
                <td><input id="password" type="password" name="password" placeholder="Enter Password" required></td>
            </tr>
            <tr>
                <td>Father Name:</td>
                <td><input id="fathername" type="text" name="fathername" placeholder="Enter Father Name" value="<?php echo isset($_POST['fathername']) ? htmlspecialchars($_POST['fathername']) : ''; ?>" required></td>
            </tr>
            <tr>
                <td>Mother Name:</td>
                <td><input id="mothername" type="text" name="mothername" placeholder="Enter Mother Name" value="<?php echo isset($_POST['mothername']) ? htmlspecialchars($_POST['mothername']) : ''; ?>"></td>
            </tr>
            <tr>
                <td>Father Phone:</td>
                <td><input id="fatherphone" type="text" name="fatherphone" placeholder="Enter Father Phone" value="<?php echo isset($_POST['fatherphone']) ? htmlspecialchars($_POST['fatherphone']) : ''; ?>"></td>
            </tr>
            <tr>
                <td>Mother Phone:</td>
                <td><input id="motherphone" type="text" name="motherphone" placeholder="Enter Mother Phone" value="<?php echo isset($_POST['motherphone']) ? htmlspecialchars($_POST['motherphone']) : ''; ?>"></td>
            </tr>
            <tr>
                <td>Address:</td>
                <td><input id="address" type="text" name="address" placeholder="Enter Address" value="<?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?>"></td>
            </tr>
            <tr>
                <td></td>
                <td><input type="submit" name="submit" value="Submit"></td>
            </tr>
        </table>
    </form>
</main>
</body>
</html>