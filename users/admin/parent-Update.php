<?php
// Include the database connection file for PostgreSQL
include_once('../../db-connect.php');

// Check if the 'id' is set in the GET request or POST request
$parentId = isset($_GET['id']) ? $_GET['id'] : '';

// If the form is submitted, update the parent's record
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    // Get the new values from the form
    $parentId = pg_escape_string($link, $_POST['id']);
    $parentPassword = pg_escape_string($link, $_POST['password']);
    $fatherName = pg_escape_string($link, $_POST['fathername']);
    $motherName = pg_escape_string($link, $_POST['mothername']);
    $fatherPhone = pg_escape_string($link, $_POST['fatherphone']);
    $motherPhone = pg_escape_string($link, $_POST['motherphone']);
    $parentAddress = pg_escape_string($link, $_POST['address']);

    // SQL query to update the parent's record in the 'parents' table
    $sql = "UPDATE parents SET password='$parentPassword', fathername='$fatherName', mothername='$motherName', fatherphone='$fatherPhone', motherphone='$motherPhone', address='$parentAddress' WHERE id='$parentId'";
    
    // Execute the SQL query
    $success = pg_query($link, $sql);
    if (!$success) {
        die('Could not update data: ' . pg_last_error($link));
    }
    echo "Updated data successfully\n";
}

// Fetch the current parent's information
$sql = "SELECT * FROM parents WHERE id='$parentId'";
$result = pg_query($link, $sql);

// Fetch data into an associative array
$parent = pg_fetch_assoc($result);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Parent</title>
    <link rel="stylesheet" href="../../sources/css/styles.css">
</head>
<body>
<header>
    <h1>Update Parent</h1>
</header>
<?php include("navBar.php");?>
<!-- Update form pre-filled with the current parent's information -->
<form class="updateForm" method="post" action="">
    <input type="text" name="id" placeholder="Enter Parent ID" value="<?php echo isset($parent['id']) ? $parent['id'] : ''; ?>" required>
    <input type="text" name="password" placeholder="Enter New Password" value="<?php echo isset($parent['password']) ? $parent['password'] : ''; ?>">
    <input type="text" name="fathername" placeholder="Enter Father's New Name" value="<?php echo isset($parent['fathername']) ? $parent['fathername'] : ''; ?>">
    <input type="text" name="mothername" placeholder="Enter Mother's New Name" value="<?php echo isset($parent['mothername']) ? $parent['mothername'] : ''; ?>">
    <input type="text" name="fatherphone" placeholder="Enter Father's New Phone" value="<?php echo isset($parent['fatherphone']) ? $parent['fatherphone'] : ''; ?>">
    <input type="text" name="motherphone" placeholder="Enter Mother's New Phone" value="<?php echo isset($parent['motherphone']) ? $parent['motherphone'] : ''; ?>">
    <input type="text" name="address" placeholder="Enter New Address" value="<?php echo isset($parent['address']) ? $parent['address'] : ''; ?>">
    <input type="submit" value="Update">
</form>
</body>
</html>
