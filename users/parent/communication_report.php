<?php 
// Include the database connection file
include_once('../../db-connect.php'); 

// Start the session to get the logged-in user's ID
session_start();
$logged_in_user_id = $_SESSION['login_id']; 

// Fetch messages where the user is either a sender or receiver
$sql = "SELECT m.sender_id, m.receiver_id, m.message, m.date
        FROM messages m
        LEFT JOIN users u1 ON m.sender_id = u1.userid
        LEFT JOIN users u2 ON m.receiver_id = u2.userid
        WHERE m.sender_id = $1 OR m.receiver_id = $1
        ORDER BY m.date DESC";

$result = pg_query_params($link, $sql, array($logged_in_user_id));

// Check if the query was successful
if (!$result) {
    echo "Error: " . pg_last_error($link);
    exit;
}

// Count total messages
$total_messages = pg_num_rows($result);

// Function to get user role for display
function getUserRole($id, $logged_in_id) {
    return ($id == $logged_in_id) ? 'You' : 'Other User';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Communication Report</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../sources/css/styles.css">
    
    <style>
        body { 
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f8f9fa;
            color: #212529;
        }
        .container {
            max-width: 1000px;
            background: #ffffff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0px 5px 15px rgba(0, 0, 0, 0.1);
            border-left: 6px solid #198754;
            margin-top: 40px;
            margin-bottom: 40px;
        }
        .report-header {
            background-color: #f1f9f7;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            border-bottom: 2px solid #d1e7dd;
        }
        h1 {
            color: #198754;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .subtitle {
            color: #6c757d;
            font-size: 16px;
        }
        .stats-box {
            background: #f1f9f7;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-around;
        }
        .stat-item {
            text-align: center;
        }
        .stat-value {
            font-size: 24px;
            font-weight: bold;
            color: #198754;
        }
        .stat-label {
            font-size: 14px;
            color: #6c757d;
        }
        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .table {
            margin-bottom: 0;
            color: #212529;
        }
        .table thead th {
            background-color: #198754;
            color: white;
            font-weight: 500;
            border: none;
            padding: 12px 15px;
        }
        .table tbody tr:nth-of-type(odd) {
            background-color: #f8f9fa;
        }
        .table tbody tr:nth-of-type(even) {
            background-color: #ffffff;
        }
        .table tbody tr:hover {
            background-color: #e8f5e9;
        }
        .table td {
            padding: 12px 15px;
            vertical-align: middle;
            border-color: #e9ecef;
        }
        .action-buttons {
            margin-top: 20px;
            text-align: center;
        }
        .btn-action {
            padding: 10px 25px;
            border-radius: 50px;
            font-weight: 500;
            margin: 0 10px;
        }
        .btn-print {
            background-color: #198754;
            color: white;
        }
        .btn-print:hover {
            background-color: #146c43;
        }
        .btn-export {
            background-color: #28a745;
            color: white;
        }
        .btn-export:hover {
            background-color: #218838;
        }
        .message-text {
            max-width: 300px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        .badge-user {
            font-size: 12px;
            padding: 5px 10px;
            border-radius: 50px;
        }
        .badge-you {
            background-color: #198754;
            color: white;
        }
        .badge-other {
            background-color: #6c757d;
            color: white;
        }
        @media print {
            body {
                background-color: white;
                color: black;
            }
            .action-buttons {
                display: none;
            }
            .container {
                box-shadow: none;
                border: none;
                background-color: white;
            }
            .report-header {
                background-color: white;
                border-bottom-color: #ddd;
            }
            .stats-box {
                background-color: #f8f9fa;
            }
            .table thead th {
                background-color: #198754;
                color: white;
            }
            .table tbody tr:nth-of-type(odd) {
                background-color: #f8f9fa;
                color: #212529;
            }
            .table tbody tr:nth-of-type(even) {
                background-color: #ffffff;
                color: #212529;
            }
            .table td {
                border-color: #dee2e6;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="report-header">
        <h1>Communication Report</h1>
        <p class="subtitle">Messages history and analytics</p>
        <p><strong>Generated on:</strong> <?php echo date("F j, Y, g:i a"); ?></p>
    </div>
    
    <div class="stats-box">
        <div class="stat-item">
            <div class="stat-value"><?php echo $total_messages; ?></div>
            <div class="stat-label">Total Messages</div>
        </div>
        <?php
        // Count sent messages
        $sent_count = 0;
        $received_count = 0;
        pg_result_seek($result, 0); // Reset result pointer
        while ($count_row = pg_fetch_assoc($result)) {
            if ($count_row['sender_id'] == $logged_in_user_id) {
                $sent_count++;
            } else {
                $received_count++;
            }
        }
        pg_result_seek($result, 0); // Reset result pointer
        ?>
        <div class="stat-item">
            <div class="stat-value"><?php echo $sent_count; ?></div>
            <div class="stat-label">Sent</div>
        </div>
        <div class="stat-item">
            <div class="stat-value"><?php echo $received_count; ?></div>
            <div class="stat-label">Received</div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th><i class="fas fa-calendar-alt me-2"></i>Date & Time</th>
                    <th><i class="fas fa-user me-2"></i>Sender</th>
                    <th><i class="fas fa-user me-2"></i>Receiver</th>
                    <th><i class="fas fa-comment me-2"></i>Message</th>
                    <th><i class="fas fa-info-circle me-2"></i>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = pg_fetch_assoc($result)): ?>
                    <tr>
                        <td><?php echo date('M j, Y g:i a', strtotime($row['date'])); ?></td>
                        <td>
                            <?php if($row['sender_id'] == $logged_in_user_id): ?>
                                <span class="badge badge-user badge-you">You</span>
                            <?php else: ?>
                                <?php echo htmlspecialchars($row['sender_name'] ?? 'User '.$row['sender_id']); ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($row['receiver_id'] == $logged_in_user_id): ?>
                                <span class="badge badge-user badge-you">You</span>
                            <?php else: ?>
                                <?php echo htmlspecialchars($row['receiver_name'] ?? 'User '.$row['receiver_id']); ?>
                            <?php endif; ?>
                        </td>
                        <td class="message-text" title="<?php echo htmlspecialchars($row['message']); ?>">
                            <?php echo htmlspecialchars($row['message']); ?>
                        </td>
                        <td>
                            <?php if($row['sender_id'] == $logged_in_user_id): ?>
                                <span class="badge bg-success">Sent</span>
                            <?php else: ?>
                                <span class="badge bg-success" style="background-color: #28a745 !important;">Received</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
                
                <?php if (pg_num_rows($result) == 0): ?>
                    <tr>
                        <td colspan="5" class="text-center py-4">No messages found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="action-buttons">
        <button class="btn btn-print btn-action" onclick="window.print()">
            <i class="fas fa-print me-2"></i>Print Report
        </button>
        <button class="btn btn-export btn-action" onclick="exportToCSV()">
            <i class="fas fa-file-export me-2"></i>Export CSV
        </button>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function exportToCSV() {
        // Simple CSV export functionality
        let csvContent = "data:text/csv;charset=utf-8,";
        csvContent += "Date,Sender,Receiver,Message,Status\n";
        
        // Get table data
        const table = document.querySelector('.table');
        const rows = table.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            if (cells.length > 0) {
                let rowData = [];
                cells.forEach(cell => {
                    // Clean and quote the cell data
                    let text = cell.textContent.trim().replace(/"/g, '""');
                    rowData.push(`"${text}"`);
                });
                csvContent += rowData.join(',') + '\n';
            }
        });
        
        // Create download link
        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "communication_report_<?php echo date('Y-m-d'); ?>.csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>

</body>
</html>