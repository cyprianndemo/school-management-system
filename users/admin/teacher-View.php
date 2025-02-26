<?php
// Include the database connection file
include_once('../../db-connect.php');

// Get the search query from the GET request
$search = (isset($_GET['search'])) ? $_GET['search'] : '';

// Base SQL query
$sql = "SELECT * FROM teachers";
$params = [];

// If search query is provided, use a parameterized query
if ($search != '') {
    $sql .= " WHERE name ILIKE $1 OR id::TEXT ILIKE $1"; // Use ILIKE for case-insensitive search
    $params[] = "%{$search}%"; // Assign parameter value
}

// Prepare and execute the query
$result = pg_query_params($link, $sql, $params);

if (!$result) {
    die("Query failed: " . pg_last_error($link));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Management System - Teacher Viewing</title>
    <link rel="stylesheet" href="../../sources/css/styles.css">
</head>
<body>
<header>
    <h1>View Teachers</h1>
</header>
<?php include("navBar.php");?>
<!-- Search form -->
<form method="get" class="searchForm" action="">
    <input type="text" class="search" name="search" placeholder="Search by teacher name or ID" value="<?php echo htmlspecialchars($search); ?>">
    <input type="submit" value="Search">
</form>

<!-- Teachers table -->
<table>
    <tr>
        <th class="viewTable">ID</th>
        <th class="viewTable">Name</th>
        <th class="viewTable">Phone</th>
        <th class="viewTable">Email</th>
        <th class="viewTable">Address</th>
        <th class="viewTable">Gender</th>
        <th class="viewTable">Date Of Birth</th>
        <th class="viewTable">Actions</th>
    </tr>
    <?php while ($row = pg_fetch_assoc($result)) { ?>
        <tr>
            <td class="viewTable"><?php echo htmlspecialchars($row['id']); ?></td>
            <td class="viewTable"><?php echo htmlspecialchars($row['name']); ?></td>
            <td class="viewTable"><?php echo htmlspecialchars($row['phone']); ?></td>
            <td class="viewTable"><?php echo htmlspecialchars($row['email']); ?></td>
            <td class="viewTable"><?php echo htmlspecialchars($row['address']); ?></td>
            <td class="viewTable"><?php echo htmlspecialchars($row['sex']); ?></td>
            <td class="viewTable"><?php echo htmlspecialchars($row['dob']); ?></td>
            <td class="viewTable">
                <a href="teacher-Update.php?id=<?php echo urlencode($row['id']); ?>"><button>Update</button></a>
                <a href="teacher-Delete.php?id=<?php echo urlencode($row['id']); ?>"><button>Delete</button></a>
            </td>
        </tr>
    <?php } ?>
</table>

</body>
</html>
