<?php
include_once('../../db-connect.php');

// Search query
$search = (isset($_GET['search'])) ? $_GET['search'] : '';

// SQL query to fetch all parents or search for a specific parent
$sql = "SELECT * FROM parents";
$params = [];
if ($search != '') {
    $sql .= " WHERE fathername ILIKE $1 OR mothername ILIKE $1";
    $params[] = "%$search%";
}

// Execute the query
$result = pg_query_params($link, $sql, $params);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Portal - View Parents</title>
    <link rel="stylesheet" href="../../sources/css/styles.css">
</head>
<body>
<header>
    <h1>Teacher Portal - View Parents</h1>
</header>
<?php include("navBar.php");?>
<!-- Search form -->
<form method="get" class="searchForm" action="">
    <input type="text" class="search" name="search" placeholder="Search by parent name" value="<?php echo htmlspecialchars($search); ?>">
    <input type="submit" value="Search">
</form>

<!-- Parents table -->
<table>
    <tr>
        <th class="viewTable">ID</th>
        <th class="viewTable">Father's Name</th>
        <th class="viewTable">Mother's Name</th>
        <th class="viewTable">Father's Phone</th>
        <th class="viewTable">Mother's Phone</th>
        <th class="viewTable">Address</th>
    </tr>
    <?php while ($row = pg_fetch_assoc($result)) { ?>
        <tr>
            <td class="viewTable"><?php echo htmlspecialchars($row['id']); ?></td>
            <td class="viewTable"><?php echo htmlspecialchars($row['fathername']); ?></td>
            <td class="viewTable"><?php echo htmlspecialchars($row['mothername']); ?></td>
            <td class="viewTable"><?php echo htmlspecialchars($row['fatherphone']); ?></td>
            <td class="viewTable"><?php echo htmlspecialchars($row['motherphone']); ?></td>
            <td class="viewTable"><?php echo htmlspecialchars($row['address']); ?></td>
            <td class="viewTable"><a href="parent-Contact.php?id=<?php echo urlencode($row['id']); ?>"><button>Contact</button></a></td>
        </tr>
    <?php } ?>
</table>

</body>
</html>
