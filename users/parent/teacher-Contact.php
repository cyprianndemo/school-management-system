<?php
// Include the database connection file for PostgreSQL
include_once('../../db-connect.php');

// Get the teacher's ID from the URL
$reciever_id = $_GET['id'];

// Get the sender's ID from the session
$sender_id = $_SESSION['login_id'];

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the message from the form
    $message = pg_escape_string($link, $_POST['message']); // Escape the message to prevent SQL injection
    // Get the current date
    $date = date('Y-m-d');

    // SQL query to insert the message into the database
    $sql = "INSERT INTO messages (message, sender_id, receiver_id, date) VALUES ($1, $2, $3, $4)";
    
    // Execute the SQL query using prepared statement with pg_query_params
    $result = pg_query_params($link, $sql, array($message, $sender_id, $reciever_id, $date));
    
    // Check if the query was successful
    if ($result) {
        echo "Message sent successfully";
    } else {
        echo "Error: " . pg_last_error($link); // Show error message if something goes wrong
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Parent Portal - Contact Teacher</title>
    <link rel="stylesheet" href="../../sources/css/styles.css">
</head>
<body>
<header>
    <!-- Main title -->
    <h1>Parent Portal - Contact Teacher</h1>
</header>
<!-- Include the navigation bar -->
<?php include("navBar.php");?>
<!-- Message form -->
<form method="post" action="">
    <!-- Textarea for the message -->
    <textarea name="message" placeholder="Type your message here"></textarea>
    <!-- Submit button -->
    <input type="submit" value="Send">
</form>
</body>
</html>
