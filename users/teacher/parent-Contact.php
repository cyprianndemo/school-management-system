<?php
include_once('../../db-connect.php');

// Get the receiver's ID from the URL
$receiver_id = $_GET['id'];

// Get the sender's ID from the session
$sender_id = $_SESSION['login_id'];

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $message = $_POST['message'];
    $date = date('Y-m-d'); // Get the current date
    
    // File upload handling
    $file_name = NULL;
    if (!empty($_FILES['attachment']['name'])) {
        $target_dir = "uploads/";
        $file_name = time() . "_" . basename($_FILES["attachment"]["name"]);
        $target_file = $target_dir . $file_name;
        
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx'];
        
        if (in_array($file_type, $allowed_types)) {
            if (move_uploaded_file($_FILES["attachment"]["tmp_name"], $target_file)) {
                echo "File uploaded successfully.";
            } else {
                echo "Error uploading file.";
                $file_name = NULL;
            }
        } else {
            echo "Invalid file type. Allowed types: jpg, jpeg, png, gif, pdf, doc, docx.";
            $file_name = NULL;
        }
    }
    
    // Insert the message into the database
    $sql = "INSERT INTO messages (message, sender_id, receiver_id, date, attachment) VALUES ($1, $2, $3, $4, $5)";
    $params = [$message, $sender_id, $receiver_id, $date, $file_name];
    
    $result = pg_query_params($link, $sql, $params);
    
    if ($result) {
        echo "Message sent successfully";
    } else {
        echo "Error: " . pg_last_error($link);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teacher Portal - Parent Contact</title>
    <link rel="stylesheet" href="../../sources/css/styles.css">
</head>
<body>
<header>
    <h1>Teacher Portal - Parent Contact</h1>
</header>
<?php include("navBar.php"); ?>
<form method="post" action="" enctype="multipart/form-data">
    <textarea name="message" placeholder="Type your message here"></textarea>
    <input type="file" name="attachment" accept=".jpg,.jpeg,.png,.gif,.pdf,.doc,.docx">
    <input type="submit" value="Send">
</form>
</body>
</html>
