<?php
// Include the database connection file
include_once('../../db-connect.php');

// Get the parent's ID from the session
$reciever_id = $_SESSION['login_id'];

// SQL query to fetch all messages for this parent
$sql = "SELECT * FROM messages WHERE receiver_id = $1 ORDER BY date DESC";

// Use pg_query_params to prevent SQL injection
$result = pg_query_params($link, $sql, array($reciever_id));

// Check if the query was successful
if (!$result) {
    echo "Error: " . pg_last_error($link); // Show error message if query fails
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Portal - View Messages</title>
    <link rel="stylesheet" href="../../sources/css/styles.css">
</head>
<body>
<header>
    <h1>Teacher Portal - View Messages</h1>
</header>
<?php include("navBar.php");?>

<!-- Messages table -->
<table>
    <tr>
        <th class="viewTable">Date</th>
        <th class="viewTable">Sender ID</th>
        <th class="viewTable">Message</th>
        <th class="viewTable">Attachment</th> <!-- Added column for file -->
    </tr>
    <?php 
    while($row = pg_fetch_assoc($result)) { 
    ?>
        <tr>
            <td class="viewTable"><?php echo htmlspecialchars($row['date']); ?></td>
            <td class="viewTable"><?php echo htmlspecialchars($row['sender_id']); ?></td>
            <td class="viewTable"><?php echo htmlspecialchars($row['message']); ?></td>
            <td class="viewTable">
                <?php if (!empty($row['file_path'])): ?>
                    <?php 
                        $file_name = basename($row['file_path']); // Get only the file name
                        $correct_file_path = "/users/parent/uploads/" . $file_name; // Correct path
                        $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                    ?>
                    <?php if (file_exists($_SERVER['DOCUMENT_ROOT'] . $correct_file_path)): ?> <!-- Check if file exists -->
                        <?php if (in_array($file_extension, ['jpg', 'jpeg', 'png', 'gif'])): ?>
                            <img src="<?php echo $correct_file_path; ?>" alt="Uploaded Image" width="100">
                        <?php else: ?>
                            <a href="<?php echo $correct_file_path; ?>" target="_blank" download>Download File</a>
                        <?php endif; ?>
                    <?php else: ?>
                        <span style="color: red;">File Not Found</span>
                    <?php endif; ?>
                <?php else: ?>
                    No Attachment
                <?php endif; ?>
            </td>
        </tr>
    <?php 
    } 
    ?>
</table>

</body>
</html>
