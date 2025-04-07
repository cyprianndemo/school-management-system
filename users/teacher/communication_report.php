<?php
// Include the database connection file
include_once('../../db-connect.php');

// Start the session to get the logged-in user's ID
session_start();
$logged_in_user_id = $_SESSION['login_id'];

// Fetch messages where the user is either a sender or receiver
$sql = "SELECT sender_id, receiver_id, message, date 
        FROM messages 
        WHERE sender_id = $1 OR receiver_id = $1 
        ORDER BY date DESC";

$result = pg_query_params($link, $sql, array($logged_in_user_id));

// Check if the query was successful
if (!$result) {
    echo "Error: " . pg_last_error($link);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Communication Report</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../sources/css/styles.css">
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; background-color: #f8f9fa; }
        .container { 
            max-width: 900px; 
            background: #fff; 
            padding: 20px; 
            border-radius: 8px; 
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            border-left: 6px solid #198754; /* Green accent on the side */
        }
        h1 { text-align: center; color: #198754; font-weight: bold; } /* Green heading */
        table { border-radius: 8px; overflow: hidden; }
        .table th { background-color: #000; color: white; }
        .table-striped tbody tr:nth-of-type(odd) { background-color: #d4edda; } /* Light green */
        .table-striped tbody tr:nth-of-type(even) { background-color: #f8f9fa; } /* Light gray */
        .print-button { margin-top: 20px; text-align: center; }
        .btn-print { background-color: #198754; color: white; border: none; } /* Green print button */
        .btn-print:hover { background-color: #145c32; } /* Darker green on hover */
    </style>
</head>
<body>

<div class="container mt-4">
    <h1>Communication Report</h1>
    <p><strong>Date:</strong> <?php echo date("Y-m-d H:i:s"); ?></p>

    <table class="table table-striped table-bordered">
        <thead>
            <tr>
                <th>Date</th>
                <th>Sender ID</th>
                <th>Receiver ID</th>
                <th>Message</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = pg_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['date']); ?></td>
                    <td><?php echo htmlspecialchars($row['sender_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['receiver_id']); ?></td>
                    <td><?php echo htmlspecialchars($row['message']); ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <div class="print-button">
        <button class="btn btn-print btn-lg" onclick="window.print()">Print Report</button>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
