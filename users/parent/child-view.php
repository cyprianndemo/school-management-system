<?php include_once('../../db-connect.php');  

// Search query
$search = (isset($_GET['search'])) ? $_GET['search'] : '';

// SQL query to fetch all children related to the parentid and calculate age
$sql = "SELECT id, name, DATE_PART('year', AGE(dob)) AS age, sex, parentid FROM students";
$params = [];
if ($search != '') {
    $sql .= " WHERE name ILIKE $1 OR parentid::TEXT ILIKE $1";
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
    <title>Parent Portal - View Child</title>
    <link rel="stylesheet" href="../../sources/css/styles.css">
</head>
<body>
<header>
    <h1>Parent Portal - View Child</h1>
</header>
<?php include("navBar.php");?>

<!-- Search form -->
<form method="get" class="searchForm" action="">
    <input type="text" class="search" name="search" placeholder="Search by child name or parent ID" value="<?php echo htmlspecialchars($search); ?>">
    <input type="submit" value="Search">
</form>

<!-- Children table -->
<table>
    <tr>
        <th class="viewTable">ID</th>
        <th class="viewTable">Child's Name</th>
        <th class="viewTable">Age</th>
        <th class="viewTable">Gender</th>
        <th class="viewTable">Parent ID</th>
    </tr>
    <?php while ($row = pg_fetch_assoc($result)) { ?>
        <tr>
            <td class="viewTable"><?php echo htmlspecialchars($row['id']); ?></td>
            <td class="viewTable"><?php echo htmlspecialchars($row['name']); ?></td>
            <td class="viewTable"><?php echo htmlspecialchars($row['age']); ?></td>
            <td class="viewTable"><?php echo htmlspecialchars($row['sex']); ?></td>
            <td class="viewTable"><?php echo htmlspecialchars($row['parentid']); ?></td>
        </tr>
    <?php } ?>
</table>

</body>
</html>
