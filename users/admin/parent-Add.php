<?php
// Check if the user is in the database
include_once('../../db-connect.php');

// Start the session
session_start();
$check = $_SESSION['login_id'];

// Check if the session is set and query the admin table
$session = pg_query($link, "SELECT name FROM admin WHERE id = '$check'");
$row = pg_fetch_array($session);
$login_session = $loged_user_name = $row['name'];

// Check if the user is logged in by checking the session
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
    <title>School Management System - Parent Registration</title>
    <link rel="stylesheet" href="../../sources/css/styles.css">
    <style>
        .password-requirements {
            font-size: 0.9em;
            color: #666;
            margin-top: 5px;
            position: absolute;
            right: -250px;
            width: 230px;
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            display: none;
        }
        
        .password-field {
            position: relative;
        }
        
        .error {
            color: red;
            font-weight: bold;
        }
        
        .success {
            color: green;
            font-weight: bold;
        }
    </style>
</head>
<body>
<header>
    <h1>Add Parents</h1>
</header>
<?php include("navBar.php"); ?>
<main>
    <h2>Parent Registration</h2>
    <?php
    include_once('../../db-connect.php');

    // Function to validate password strength
    function validatePassword($password) {
        $errors = [];
        
        // Check length
        if (strlen($password) < 8) {
            $errors[] = "Password must be at least 8 characters long";
        }
        
        // Check for uppercase letter
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = "Password must contain at least one uppercase letter";
        }
        
        // Check for lowercase letter
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = "Password must contain at least one lowercase letter";
        }
        
        // Check for numeric character
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = "Password must contain at least one number";
        }
        
        // Check for non-alphanumeric character
        if (!preg_match('/[^a-zA-Z0-9]/', $password)) {
            $errors[] = "Password must contain at least one special character";
        }
        
        return $errors;
    }

    // Process the form submission
    if (isset($_POST['submit'])) {
        $passwordErrors = [];
        $formErrors = [];
        
        // Validate that required fields are not empty
        if (empty($_POST['id']) || empty($_POST['password']) || empty($_POST['fathername'])) {
            $formErrors[] = "Parent ID, password, and father name are required fields.";
        }
        
        // Validate password
        if (!empty($_POST['password'])) {
            $passwordErrors = validatePassword($_POST['password']);
            
            // Check if passwords match
            if ($_POST['password'] !== $_POST['confirm_password']) {
                $passwordErrors[] = "Passwords do not match";
            }
        }
        
        // If there are no errors, proceed with database insertion
        if (empty($formErrors) && empty($passwordErrors)) {
            // Set the values of the form to the variables
            $id = pg_escape_string($link, $_POST['id']);
            $password = $_POST['password']; // Don't escape passwords before hashing
            $fathername = pg_escape_string($link, $_POST['fathername']);
            $mothername = pg_escape_string($link, $_POST['mothername']);
            $fatherphone = pg_escape_string($link, $_POST['fatherphone']);
            $motherphone = pg_escape_string($link, $_POST['motherphone']);
            $address = pg_escape_string($link, $_POST['address']);

            // Hash the password before storing it in the database
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Begin a transaction to ensure data consistency
            pg_query($link, "BEGIN");

            try {
                // First, insert into the 'parents' table using prepared statements
                $parentsSQL = "INSERT INTO parents (id, password, fathername, mothername, fatherphone, motherphone, address) 
                        VALUES ($1, $2, $3, $4, $5, $6, $7)";
                $parentsResult = pg_query_params(
                    $link, 
                    $parentsSQL, 
                    array($id, $hashedPassword, $fathername, $mothername, $fatherphone, $motherphone, $address)
                );
                
                if (!$parentsResult) {
                    throw new Exception("Could not enter data into parents table: " . pg_last_error($link));
                }

                // Then, insert into the 'users' table using prepared statements
                $usersSQL = "INSERT INTO users (userid, password, usertype) VALUES ($1, $2, $3)";
                $usersResult = pg_query_params(
                    $link, 
                    $usersSQL, 
                    array($id, $hashedPassword, 'parent')
                );
                
                if (!$usersResult) {
                    throw new Exception("Could not enter data into users table: " . pg_last_error($link));
                }

                // Commit the transaction if both queries were successful
                pg_query($link, "COMMIT");
                echo "<p class='success'>Parent information and user account created successfully!</p>";
                
                // Clear form values after successful submission
                $_POST = array();
            } catch (Exception $e) {
                // Rollback the transaction if any query failed
                pg_query($link, "ROLLBACK");
                echo "<p class='error'>Error: " . $e->getMessage() . "</p>";
            }
        } else {
            // Display all errors
            foreach ($formErrors as $error) {
                echo "<p class='error'>$error</p>";
            }
            foreach ($passwordErrors as $error) {
                echo "<p class='error'>$error</p>";
            }
        }
    }
    ?>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" id="parentForm">
        <table>
            <tr>
                <td>Parent Id:</td>
                <td><input id="id" type="text" name="id" placeholder="Enter Id" value="<?php echo isset($_POST['id']) ? htmlspecialchars($_POST['id']) : ''; ?>" required></td>
            </tr>
            <tr>
                <td>Parent Password:</td>
                <td class="password-field">
                    <input id="password" type="password" name="password" placeholder="Enter Password" required>
                    <div id="passwordRequirements" class="password-requirements">
                        Password must contain at least 8 characters, including:
                        <ul>
                            <li>At least one uppercase letter</li>
                            <li>At least one lowercase letter</li>
                            <li>At least one number</li>
                            <li>At least one special character</li>
                        </ul>
                    </div>
                </td>
            </tr>
            <tr>
                <td>Confirm Password:</td>
                <td><input id="confirm_password" type="password" name="confirm_password" placeholder="Confirm Password" required></td>
            </tr>
            <tr>
                <td>Father Name:</td>
                <td><input id="fathername" type="text" name="fathername" placeholder="Enter Father Name" value="<?php echo isset($_POST['fathername']) ? htmlspecialchars($_POST['fathername']) : ''; ?>" required></td>
            </tr>
            <tr>
                <td>Mother Name:</td>
                <td><input id="mothername" type="text" name="mothername" placeholder="Enter Mother Name" value="<?php echo isset($_POST['mothername']) ? htmlspecialchars($_POST['mothername']) : ''; ?>"></td>
            </tr>
            <tr>
                <td>Father Phone:</td>
                <td><input id="fatherphone" type="text" name="fatherphone" placeholder="Enter Father Phone" value="<?php echo isset($_POST['fatherphone']) ? htmlspecialchars($_POST['fatherphone']) : ''; ?>"></td>
            </tr>
            <tr>
                <td>Mother Phone:</td>
                <td><input id="motherphone" type="text" name="motherphone" placeholder="Enter Mother Phone" value="<?php echo isset($_POST['motherphone']) ? htmlspecialchars($_POST['motherphone']) : ''; ?>"></td>
            </tr>
            <tr>
                <td>Address:</td>
                <td><input id="address" type="text" name="address" placeholder="Enter Address" value="<?php echo isset($_POST['address']) ? htmlspecialchars($_POST['address']) : ''; ?>"></td>
            </tr>
            <tr>
                <td></td>
                <td><input type="submit" name="submit" value="Submit"></td>
            </tr>
        </table>
    </form>
</main>
<script>
    // Show password requirements when password field is focused
    document.getElementById('password').addEventListener('focus', function() {
        document.getElementById('passwordRequirements').style.display = 'block';
    });
    
    // Hide password requirements when focus leaves password field
    document.getElementById('password').addEventListener('blur', function() {
        document.getElementById('passwordRequirements').style.display = 'none';
    });
    
    // Client-side validation to enhance user experience
    document.getElementById('parentForm').addEventListener('submit', function(e) {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        let errors = [];
        
        // Check password length
        if (password.length < 8) {
            errors.push("Password must be at least 8 characters long");
        }
        
        // Check for uppercase letter
        if (!/[A-Z]/.test(password)) {
            errors.push("Password must contain at least one uppercase letter");
        }
        
        // Check for lowercase letter
        if (!/[a-z]/.test(password)) {
            errors.push("Password must contain at least one lowercase letter");
        }
        
        // Check for numeric character
        if (!/[0-9]/.test(password)) {
            errors.push("Password must contain at least one number");
        }
        
        // Check for non-alphanumeric character
        if (!/[^a-zA-Z0-9]/.test(password)) {
            errors.push("Password must contain at least one special character");
        }
        
        // Check if passwords match
        if (password !== confirmPassword) {
            errors.push("Passwords do not match");
        }
        
        // If there are errors, prevent form submission and show errors
        if (errors.length > 0) {
            e.preventDefault();
            alert("Please fix the following errors:\n" + errors.join("\n"));
        }
    });
</script>
</body>
</html>