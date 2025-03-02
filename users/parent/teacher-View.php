<?php
// Include the database connection file
include_once('../../db-connect.php');

// Get the search query from the GET request
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Default SQL query
$sql = "SELECT * FROM teachers";
$params = [];

// Modify query if searching
if (!empty($search)) {
    $sql .= " WHERE name ILIKE $1 OR CAST(id AS TEXT) ILIKE $2"; // Use ILIKE for case-insensitive search
    $params = ['%' . $search . '%', '%' . $search . '%'];
}

// Prepare and execute the query
$result = empty($params) ? pg_query($link, $sql) : pg_query_params($link, $sql, $params);

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
    <title>Parent Portal - View Teachers</title>
    <link rel="stylesheet" href="../../sources/css/styles.css">
</head>
<body>
<header>
    <h1>Parent Portal - View Teachers</h1>
</header>
<?php include("navBar.php"); ?>
<form method="get" class="searchForm" action="">
    <input type="text" class="search" name="search" placeholder="Search by teacher name or id" value="<?php echo htmlspecialchars($search); ?>">
    <input type="submit" value="Search">
</form>

<table>
    <tr>
        <th class="viewTable">ID</th>
        <th class="viewTable">Name</th>
        <th class="viewTable">Phone</th>
        <th class="viewTable">Email</th>
        <th class="viewTable">Address</th>
        <th class="viewTable">Gender</th>
        <th class="viewTable">Date of Birth</th>
    </tr>
    <?php 
    while ($row = pg_fetch_assoc($result)) { ?>
        <tr>
            <td class="viewTable"><?php echo htmlspecialchars($row['id']); ?></td>
            <td class="viewTable"><?php echo htmlspecialchars($row['name']); ?></td>
            <td class="viewTable"><?php echo htmlspecialchars($row['phone']); ?></td>
            <td class="viewTable"><?php echo htmlspecialchars($row['email']); ?></td>
            <td class="viewTable"><?php echo htmlspecialchars($row['address']); ?></td>
            <td class="viewTable"><?php echo htmlspecialchars($row['sex']); ?></td>
            <td class="viewTable"><?php echo htmlspecialchars($row['dob']); ?></td>
            <td class="viewTable"><a href="teacher-Contact.php?id=<?php echo $row['id']; ?>"><button>Contact</button></a></td>
        </tr>
    <?php } ?>
</table>

</body>
</html>
