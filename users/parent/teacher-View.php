<?php
// Include the database connection file
include_once('../../db-connect.php');

// Get the search query from the GET request
$search = (isset($_GET['search'])) ? $_GET['search'] : '';

// SQL query to fetch all teachers or search for a specific teacher
$sql = "SELECT * FROM teachers";
if($search != '') {
    // If a search query is provided, add a WHERE clause to the SQL query
    $sql .= " WHERE name LIKE $1 OR id LIKE $2";
}

// Prepare the query using pg_query_params to prevent SQL injection
$result = pg_query_params($link, $sql, array('%'.$search.'%', '%'.$search.'%'));

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
    <!-- Main title -->
    <h1>Parent Portal - View Teachers</h1>
</header>
<!-- Include the navigation bar -->
<?php include("navBar.php");?>
<!-- Search form -->
<form method="get" class="searchForm" action="">
    <!-- Input field for the search query -->
    <input type="text" class="search" name="search" placeholder="Search by teacher name or id" value="<?php echo htmlspecialchars($search); ?>">
    <!-- Submit button for the search form -->
    <input type="submit" value="Search">
</form>

<!-- Teachers table -->
<table>
    <tr>
        <!-- Table headers -->
        <th class="viewTable">ID</th>
        <th class="viewTable">Name</th>
        <th class="viewTable">Phone</th>
        <th class="viewTable">Email</th>
        <th class="viewTable">Address</th>
        <th class="viewTable">Gender</th>
        <th class="viewTable">Date of Birth</th>
    </tr>
    <?php 
    while ($row = pg_fetch_assoc($result)) { // Fetch results from PostgreSQL
    ?>
        <tr>
            <!-- Display each teacher's information in a table row -->
            <td class="viewTable"><?php echo htmlspecialchars($row['id']); ?></td>
            <td class="viewTable"><?php echo htmlspecialchars($row['name']); ?></td>
            <td class="viewTable"><?php echo htmlspecialchars($row['phone']); ?></td>
            <td class="viewTable"><?php echo htmlspecialchars($row['email']); ?></td>
            <td class="viewTable"><?php echo htmlspecialchars($row['address']); ?></td>
            <td class="viewTable"><?php echo htmlspecialchars($row['sex']); ?></td>
            <td class="viewTable"><?php echo htmlspecialchars($row['dob']); ?></td>
            <!-- Contact button -->
            <td class="viewTable"><a href="teacher-Contact.php?id=<?php echo $row['id']; ?>"><button>Contact</button></a></td>
        </tr>
    <?php 
    }
    ?>
</table>

</body>
</html>
