<?php
// Include the database connection file
include_once('../../db-connect.php');

// Start the session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in by checking the session
$check = $_SESSION['login_id'];

// PostgreSQL query to select the name from the admin table
$session = pg_query($link, "SELECT name FROM admin WHERE id='$check'");
$row = pg_fetch_assoc($session);
$login_session = $loged_user_name = $row['name'];

// If the user is not logged in, redirect to the main page
if (!isset($login_session)) {
    header("Location:../../");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Management System - Teacher Registration</title>
    <link rel="stylesheet" href="../../sources/css/styles.css">
</head>
<body>
<header>
    <h1>Add Teachers</h1>
</header>
<?php include("navBar.php");?>
<main>
    <h2>Teacher Registration</h2>
    
    <?php
    include_once('../../db-connect.php');

    // Process form submission
    if (isset($_POST['submit'])) {
        // Validate that required fields are not empty
        if (empty($_POST['teacherId']) || empty($_POST['teacherName']) || empty($_POST['teacherPassword'])) {
            echo "<p class='error'>Teacher ID, Name, and Password are required fields.</p>";
        } else {
            // Get and sanitize the values from the form
            $id = pg_escape_string($link, $_POST['teacherId']);
            $name = pg_escape_string($link, $_POST['teacherName']);
            $password = $_POST['teacherPassword']; // Don't escape passwords before hashing
            $phone = pg_escape_string($link, $_POST['teacherPhone']);
            $email = pg_escape_string($link, $_POST['teacherEmail']);
            $address = pg_escape_string($link, $_POST['teacherAddress']);
            $gender = isset($_POST['gender']) ? pg_escape_string($link, $_POST['gender']) : '';
            $dob = pg_escape_string($link, $_POST['teacherDOB']);

            // Hash the password before saving it to the database
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Begin a transaction to ensure data consistency
            pg_query($link, "BEGIN");
            
            try {
                // PostgreSQL query to insert the new teacher's record into the 'teachers' table
                $sqlTeachers = "INSERT INTO teachers (id, name, password, phone, email, address, sex, dob) 
                        VALUES ($1, $2, $3, $4, $5, $6, $7, $8)";
                
                // Execute the SQL query with prepared statements for better security
                $resultTeachers = pg_query_params(
                    $link, 
                    $sqlTeachers, 
                    array($id, $name, $hashedPassword, $phone, $email, $address, $gender, $dob)
                );
                
                if (!$resultTeachers) {
                    throw new Exception("Could not enter data into teachers table: " . pg_last_error($link));
                }

                // SQL query to insert the new teacher's record into the 'users' table
                $sqlUsers = "INSERT INTO users (userid, password, usertype) VALUES ($1, $2, $3)";
                
                // Execute the SQL query with prepared statements
                $resultUsers = pg_query_params(
                    $link, 
                    $sqlUsers, 
                    array($id, $hashedPassword, 'teacher')
                );
                
                if (!$resultUsers) {
                    throw new Exception("Could not enter data into users table: " . pg_last_error($link));
                }

                // Commit the transaction if both queries were successful
                pg_query($link, "COMMIT");
                
                echo "<p class='success'>Teacher record created successfully! The teacher can now log in.</p>";
                
                // Reset the form after successful submission
                $_POST = array();
            } catch (Exception $e) {
                // Rollback the transaction if any query failed
                pg_query($link, "ROLLBACK");
                echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
            }
        }
    }
    ?>
    
    <!-- Teacher registration form -->
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <table>
            <!-- Form fields for teacher's information -->
            <tr>
                <td>Teacher ID:</td>
                <td><input id="teacherId" type="text" name="teacherId" placeholder="Enter Teacher ID" value="<?php echo isset($_POST['teacherId']) ? htmlspecialchars($_POST['teacherId']) : ''; ?>" required></td>
            </tr>
            <tr>
                <td>Teacher Name:</td>
                <td><input id="teacherName" type="text" name="teacherName" placeholder="Enter Teacher Name" value="<?php echo isset($_POST['teacherName']) ? htmlspecialchars($_POST['teacherName']) : ''; ?>" required></td>
            </tr>
            <tr>
                <td>Teacher Password:</td>
                <td><input id="teacherPassword" type="password" name="teacherPassword" placeholder="Enter Password" required></td>
            </tr>
            <tr>
                <td>Teacher Phone:</td>
                <td><input id="teacherPhone" type="text" name="teacherPhone" placeholder="Enter Teacher Phone" value="<?php echo isset($_POST['teacherPhone']) ? htmlspecialchars($_POST['teacherPhone']) : ''; ?>"></td>
            </tr>
            <tr>
                <td>Teacher Email:</td>
                <td><input id="teacherEmail" type="email" name="teacherEmail" placeholder="Enter Teacher Email" value="<?php echo isset($_POST['teacherEmail']) ? htmlspecialchars($_POST['teacherEmail']) : ''; ?>"></td>
            </tr>
            <tr>
                <td>Gender:</td>
                <td>
                    <input type="radio" id="male" name="gender" value="male" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'male') ? 'checked' : ''; ?>>
                    <label for="male">Male</label><br>
                    <input type="radio" id="female" name="gender" value="female" <?php echo (isset($_POST['gender']) && $_POST['gender'] == 'female') ? 'checked' : ''; ?>>
                    <label for="female">Female</label><br>
                </td>
            </tr>
            <tr>
                <td>Date of Birth:</td>
                <td><input id="teacherDOB" type="date" name="teacherDOB" value="<?php echo isset($_POST['teacherDOB']) ? htmlspecialchars($_POST['teacherDOB']) : ''; ?>"></td>
            </tr>
            <tr>
                <td>Address:</td>
                <td><input id="teacherAddress" type="text" name="teacherAddress" placeholder="Enter Teacher Address" value="<?php echo isset($_POST['teacherAddress']) ? htmlspecialchars($_POST['teacherAddress']) : ''; ?>"></td>
            </tr>
            <tr>
                <td></td>
                <!-- Submit button -->
                <td><input type="submit" name="submit" value="Submit"></td>
            </tr>
        </table>
    </form>
</main>
</body>
</html>