<?php
// Include database connection file
include_once('../../db-connect.php');

// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is an admin
if (!isset($_SESSION['login_id'])) {
    header("Location: ../../login.php"); // Redirect to login if not logged in
    exit();
}

// Ensure role is correctly retrieved from the database
$admin_id = $_SESSION['login_id'];
$query = "SELECT usertype FROM users WHERE userid = $1";
$result = pg_query_params($link, $query, array($admin_id));

if (!$result) {
    die("Database error: " . pg_last_error($link)); // Print detailed error
}

if (pg_num_rows($result) > 0) {
    $user = pg_fetch_assoc($result);
    if ($user['usertype'] !== 'admin') {
        header("Location: ../../"); // Redirect if not admin
        exit();
    }
} else {
    header("Location: ../../"); // Redirect if user not found
    exit();
}

// Fetch parental engagement data
$sql = "
    SELECT 
        CONCAT(p.fathername, ' & ', p.mothername) AS parent_name,  -- Use correct column names
        COUNT(DISTINCT pl.id) AS login_count, 
        COALESCE(SUM(pm.message_sent), 0) AS messages_sent,
        COALESCE(SUM(pm.message_received), 0) AS messages_received
    FROM parents p
    LEFT JOIN parent_logins pl ON p.id = pl.parent_id
    LEFT JOIN parent_messages pm ON p.id = pm.parent_id
    GROUP BY p.fathername, p.mothername
    ORDER BY login_count DESC, messages_sent DESC
";


$result = pg_query($link, $sql);

if (!$result) {
    die("Database error: " . pg_last_error($link)); // Exit if query fails
}

$parents = [];
$logins = [];
$messages_sent = [];
$messages_received = [];

while ($row = pg_fetch_assoc($result)) {
    $parents[] = $row['parent_name'];
    $logins[] = $row['login_count'];
    $messages_sent[] = $row['messages_sent'];
    $messages_received[] = $row['messages_received'];
}

// Convert data to JSON for Chart.js
$parents_json = json_encode($parents);
$logins_json = json_encode($logins);
$messages_sent_json = json_encode($messages_sent);
$messages_received_json = json_encode($messages_received);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Parental Engagement Report</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f8f9fa; padding: 20px; }
        .container { max-width: 1000px; background: #fff; padding: 20px; border-radius: 8px; box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1); }
        h1 { text-align: center; color: #198754; font-weight: bold; }
        .table th { background-color: #000; color: white; }
        .table-striped tbody tr:nth-of-type(odd) { background-color: #d4edda; }
        .table-striped tbody tr:nth-of-type(even) { background-color: #f8f9fa; }
        .print-button { margin-top: 20px; text-align: center; }
        .btn-print { background-color: #198754; color: white; border: none; }
        .btn-print:hover { background-color: #145c32; }
    </style>
</head>
<body>

<div class="container mt-4">
    <h1>Parental Engagement Report</h1>
    <p><strong>Date:</strong> <?php echo date("Y-m-d H:i:s"); ?></p>

    <canvas id="engagementChart" width="400" height="200"></canvas>

    <table class="table table-striped table-bordered mt-4">
        <thead>
            <tr>
                <th>Parent Name</th>
                <th>Login Count</th>
                <th>Messages Sent</th>
                <th>Messages Received</th>
            </tr>
        </thead>
        <tbody>
            <?php for ($i = 0; $i < count($parents); $i++): ?>
                <tr>
                    <td><?php echo htmlspecialchars($parents[$i]); ?></td>
                    <td><?php echo htmlspecialchars($logins[$i]); ?></td>
                    <td><?php echo htmlspecialchars($messages_sent[$i]); ?></td>
                    <td><?php echo htmlspecialchars($messages_received[$i]); ?></td>
                </tr>
            <?php endfor; ?>
        </tbody>
    </table>

    <div class="print-button">
        <button class="btn btn-print btn-lg" onclick="window.print()">Print Report</button>
    </div>
</div>

<script>
    const ctx = document.getElementById('engagementChart').getContext('2d');

    const engagementChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: <?php echo $parents_json; ?>,
            datasets: [
                {
                    label: 'Logins',
                    data: <?php echo $logins_json; ?>,
                    backgroundColor: 'rgba(0, 0, 0, 0.7)', // Black
                    borderColor: 'rgba(0, 0, 0, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Messages Sent',
                    data: <?php echo $messages_sent_json; ?>,
                    backgroundColor: 'rgba(25, 135, 84, 0.7)', // Green
                    borderColor: 'rgba(25, 135, 84, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Messages Received',
                    data: <?php echo $messages_received_json; ?>,
                    backgroundColor: 'rgba(25, 135, 84, 0.4)', // Light Green
                    borderColor: 'rgba(25, 135, 84, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
