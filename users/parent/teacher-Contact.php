<?php
// Include the database connection file for PostgreSQL
include_once('../../db-connect.php');

// Ensure uploads directory exists
$upload_dir = __DIR__ . "/uploads/";
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Get the teacher's ID from the URL
$reciever_id = $_GET['id'];

// Get the sender's ID from the session
$sender_id = $_SESSION['login_id'];

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the message from the form
    $message = pg_escape_string($link, $_POST['message']);
    $date = date('Y-m-d');

    // File upload handling
    $db_file_path = null;
    if (!empty($_FILES['attachment']['name'])) {
        $file_name = time() . "_" . basename($_FILES["attachment"]["name"]);
        $file_path = $upload_dir . $file_name;
        $db_file_path = "uploads/" . $file_name;

        // Allowed file types
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];

        // Get real MIME type
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $file_mime = finfo_file($finfo, $_FILES["attachment"]["tmp_name"]);
        finfo_close($finfo);

        if (in_array($file_mime, $allowed_types)) {
            if ($_FILES["attachment"]["error"] === UPLOAD_ERR_OK) {
                if (move_uploaded_file($_FILES["attachment"]["tmp_name"], $file_path)) {
                    echo "File uploaded successfully.";
                } else {
                    echo "Error moving uploaded file.";
                    $db_file_path = null;
                }
            } else {
                echo "File upload error: " . $_FILES["attachment"]["error"];
                $db_file_path = null;
            }
        } else {
            echo "Invalid file type. Allowed types: JPG, PNG, GIF, PDF, DOC, DOCX.";
            $db_file_path = null;
        }
    }

    // SQL query to insert the message into the database
    $sql = "INSERT INTO messages (message, sender_id, receiver_id, date, file_path) VALUES ($1, $2, $3, $4, $5)";
    
    // Execute the SQL query using prepared statement
    $result = pg_query_params($link, $sql, array($message, $sender_id, $reciever_id, $date, $db_file_path));
    
    // Check if the query was successful
    if ($result) {
        echo "Message sent successfully";
    } else {
        echo "Database error: " . pg_last_error($link);
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
    <h1>Parent Portal - Contact Teacher</h1>
</header>
<?php include("navBar.php");?>

<!-- Message form -->
<form method="post" action="" enctype="multipart/form-data">
    <textarea name="message" placeholder="Type your message here"></textarea>
    <input type="file" name="attachment">
    <input type="submit" value="Send">
</form>
</body>
</html>
