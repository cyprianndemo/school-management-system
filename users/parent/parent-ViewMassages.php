<?php
// Include the database connection file for PostgreSQL
include_once('../../db-connect.php');

// Get the parent's ID from the session
$reciever_id = $_SESSION['login_id'];

// SQL query to fetch all messages for this parent, sorted by date in descending order
$sql = "SELECT * FROM messages WHERE receiver_id = $1 ORDER BY date DESC";

// Prepare the SQL query
$result = pg_query_params($link, $sql, array($reciever_id));

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
    <!-- Main title -->
    <h1>Parent Portal - View Messages</h1>
</header>
<!-- Include the navigation bar -->
<?php include("navBar.php");?>

<!-- Messages table -->
<table>
    <tr>
        <!-- Table headers -->
        <th class="viewTable">Date</th>
        <th class="viewTable">Sender ID</th>
        <th class="viewTable">Message</th>
    </tr>
    <?php while ($row = pg_fetch_assoc($result)) { ?>
        <tr>
            <!-- Display each message's information in a table row -->
            <td class="viewTable"><?php echo htmlspecialchars($row['date']); ?></td>
            <td class="viewTable"><?php echo htmlspecialchars($row['sender_id']); ?></td>
            <td class="viewTable"><?php echo htmlspecialchars($row['message']); ?></td>
        </tr>
    <?php } ?>
</table>

</body>
</html>
