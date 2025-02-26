<?php
// Include the database connection file
include_once('../../db-connect.php');

// Start the session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in by checking the session
if (isset($_SESSION['login_id'])) {
    $check = $_SESSION['login_id'];
    $session = pg_query($link, "SELECT name FROM admin WHERE id='$check'");
    if ($session && pg_num_rows($session) > 0) {
        $row = pg_fetch_assoc($session);
        $login_session = $loged_user_name = $row['name'];
    } else {
        header("Location:../../");
        exit();
    }
} else {
    // If the user is not logged in, redirect to the main page
    header("Location:../../");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>School Management System - Student Registration</title>
    <link rel="stylesheet" href="../../sources/css/styles.css">
</head>
<body>
<header>
    <h1>Add Students</h1>
</header>
<?php include("navBar.php"); ?>
<main>
    <h2>Student Registration</h2>
    
    <?php
    // Process form submission
    if (isset($_POST['submit'])) {
        // Validate that required fields are not empty
        if (empty($_POST['studentId']) || empty($_POST['studentName']) || empty($_POST['studentPassword'])) {
            echo "<p class='error'>Student ID, Name, and Password are required fields.</p>";
        } else {
            // Get and sanitize the values from the form
            $stuId = pg_escape_string($link, $_POST['studentId']);
            $stuName = pg_escape_string($link, $_POST['studentName']);
            $stuPassword = $_POST['studentPassword']; // Don't escape passwords before hashing
            $stuPhone = pg_escape_string($link, $_POST['studentPhone'] ?? '');
            $stuEmail = pg_escape_string($link, $_POST['studentEmail'] ?? '');
            $stuSex = isset($_POST['sex']) ? pg_escape_string($link, $_POST['sex']) : '';
            $stuDOB = pg_escape_string($link, $_POST['studentDOB'] ?? '');
            $stuAddress = pg_escape_string($link, $_POST['studentAddress'] ?? '');
            $stuParentId = pg_escape_string($link, $_POST['studentParentId'] ?? '');

            // Hash the password before saving it to the database
            $hashedPassword = password_hash($stuPassword, PASSWORD_DEFAULT);

            // Begin a transaction to ensure data consistency
            pg_query($link, "BEGIN");
            
            // Flag to track if all operations are successful
            $success = true;
            
            // Check if student ID already exists in the students table
            $checkStudent = pg_query($link, "SELECT id FROM students WHERE id = '$stuId'");
            if (pg_num_rows($checkStudent) > 0) {
                echo "<p class='error'>Error: Student ID already exists.</p>";
                $success = false;
            }
            
            // Check if student ID already exists in the users table
            $checkUser = pg_query($link, "SELECT userid FROM users WHERE userid = '$stuId'");
            if (pg_num_rows($checkUser) > 0) {
                echo "<p class='error'>Error: User ID already exists.</p>";
                $success = false;
            }
            
            if ($success) {
                try {
                    // SQL query to insert the new student's record into the 'students' table
                    // Changed parent_id to parentid
                    $sqlStudents = "INSERT INTO students (id, name, password, phone, email, sex, dob, address, parentid) 
                            VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9)";
                    
                    // Execute the SQL query with prepared statements for better security
                    $resultStudents = pg_query_params(
                        $link, 
                        $sqlStudents, 
                        array($stuId, $stuName, $hashedPassword, $stuPhone, $stuEmail, $stuSex, $stuDOB, $stuAddress, $stuParentId)
                    );
                    
                    if (!$resultStudents) {
                        throw new Exception("Could not enter data into students table: " . pg_last_error($link));
                    }

                    // SQL query to insert the new student's record into the 'users' table
                    $sqlUsers = "INSERT INTO users (userid, password, usertype) VALUES ($1, $2, $3)";
                    
                    // Execute the SQL query with prepared statements
                    $resultUsers = pg_query_params(
                        $link, 
                        $sqlUsers, 
                        array($stuId, $hashedPassword, 'student')
                    );
                    
                    if (!$resultUsers) {
                        throw new Exception("Could not enter data into users table: " . pg_last_error($link));
                    }

                    // Commit the transaction if both queries were successful
                    pg_query($link, "COMMIT");
                    
                    echo "<p class='success'>Student record created successfully! The student can now log in.</p>";
                    
                    // Reset the form after successful submission
                    $_POST = array();
                } catch (Exception $e) {
                    // Rollback the transaction if any query failed
                    pg_query($link, "ROLLBACK");
                    echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
                }
            } else {
                // Rollback the transaction if validation failed
                pg_query($link, "ROLLBACK");
            }
        }
    }
    ?>
    
    <!-- Student registration form -->
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
        <table>
            <!-- Form fields for student's information -->
            <tr>
                <td>Student ID:</td>
                <td><input id="studentId" type="text" name="studentId" placeholder="Enter Student ID" value="<?php echo isset($_POST['studentId']) ? htmlspecialchars($_POST['studentId']) : ''; ?>" required></td>
            </tr>
            <tr>
                <td>Student Name:</td>
                <td><input id="studentName" type="text" name="studentName" placeholder="Enter Student Name" value="<?php echo isset($_POST['studentName']) ? htmlspecialchars($_POST['studentName']) : ''; ?>" required></td>
            </tr>
            <tr>
                <td>Student Password:</td>
                <td><input id="studentPassword" type="password" name="studentPassword" placeholder="Enter Password" required></td>
            </tr>
            <tr>
                <td>Student Phone:</td>
                <td><input id="studentPhone" type="text" name="studentPhone" placeholder="Enter Student Phone" value="<?php echo isset($_POST['studentPhone']) ? htmlspecialchars($_POST['studentPhone']) : ''; ?>"></td>
            </tr>
            <tr>
                <td>Student Email:</td>
                <td><input id="studentEmail" type="email" name="studentEmail" placeholder="Enter Student Email" value="<?php echo isset($_POST['studentEmail']) ? htmlspecialchars($_POST['studentEmail']) : ''; ?>"></td>
            </tr>
            <tr>
                <td>Sex:</td>
                <td>
                    <input type="radio" id="male" name="sex" value="male" <?php echo (isset($_POST['sex']) && $_POST['sex'] == 'male') ? 'checked' : ''; ?>>
                    <label for="male">Male</label><br>
                    <input type="radio" id="female" name="sex" value="female" <?php echo (isset($_POST['sex']) && $_POST['sex'] == 'female') ? 'checked' : ''; ?>>
                    <label for="female">Female</label><br>
                </td>
            </tr>
            <tr>
                <td>Date of Birth:</td>
                <td><input id="studentDOB" type="date" name="studentDOB" value="<?php echo isset($_POST['studentDOB']) ? htmlspecialchars($_POST['studentDOB']) : ''; ?>"></td>
            </tr>
            <tr>
                <td>Address:</td>
                <td><input id="studentAddress" type="text" name="studentAddress" placeholder="Enter Student Address" value="<?php echo isset($_POST['studentAddress']) ? htmlspecialchars($_POST['studentAddress']) : ''; ?>"></td>
            </tr>
            <tr>
                <td>Parent ID:</td>
                <td><input id="studentParentId" type="text" name="studentParentId" placeholder="Enter Parent ID" value="<?php echo isset($_POST['studentParentId']) ? htmlspecialchars($_POST['studentParentId']) : ''; ?>"></td>
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