<?php
// Include the database connection file for PostgreSQL
include_once('../../db-connect.php');

// Start session if not started
session_start();

// Get the parent's ID from the session
$receiver_id = $_SESSION['login_id'];

// SQL query to fetch all messages for this parent, sorted by date in descending order
$sql = "SELECT * FROM messages WHERE receiver_id = $1 ORDER BY date DESC";

// Execute the SQL query with the receiver ID parameter
$result = pg_query_params($link, $sql, array($receiver_id));

if (!$result) {
    die("Query failed: " . pg_last_error($link));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Portal - View Messages</title>
    <link rel="stylesheet" href="../../sources/css/styles.css">
</head>
<body>
<header>
    <h1>Parent Portal - View Messages</h1>
</header>

<!-- Include the navigation bar -->
<?php include("navBar.php");?>

<!-- Messages table -->
<table>
    <tr>
        <th class="viewTable">Date</th>
        <th class="viewTable">Sender ID</th>
        <th class="viewTable">Message</th>
        <th class="viewTable">Attachment</th> <!-- New column for attachments -->
    </tr>
    <?php while ($row = pg_fetch_assoc($result)) { ?>
        <tr>
            <td class="viewTable"><?php echo htmlspecialchars($row['date']); ?></td>
            <td class="viewTable"><?php echo htmlspecialchars($row['sender_id']); ?></td>
            <td class="viewTable"><?php echo htmlspecialchars($row['message']); ?></td>
            <td class="viewTable">
    <?php if (!empty($row['attachment'])) { ?>
        <a href="../../users/teacher/uploads/<?php echo htmlspecialchars($row['attachment']); ?>" target="_blank">View</a>
    <?php } else { ?>
        No attachment
    <?php } ?>
</td>

        </tr>
    <?php } ?>
</table>

</body>
</html>
