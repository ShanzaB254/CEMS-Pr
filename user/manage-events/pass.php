<?php
// user/manage-events/pass.php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: ../../public/login.php");
    exit();
}

require_once '../../includes/manage-db/db_connect.php';
$user_id = $_SESSION['user_id'];
$event_id = intval($_GET['event_id']);

$sql = "SELECT e.title, e.event_date, e.event_time, e.venue, r.type, r.registered_at, u.name AS student_name 
        FROM registrations r JOIN events e ON r.event_id = e.id JOIN users u ON r.user_id = u.id
        WHERE r.event_id = $event_id AND r.user_id = $user_id";

$result = $conn->query($sql);
if ($result->num_rows !== 1) die("Registration not found.");
$ticket = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pass - <?php echo htmlspecialchars($ticket['title']); ?></title>
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background: #f3f4f6; display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .ticket-wrapper { width: 100%; max-width: 400px; padding: 20px; }
        .ticket-card { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.1); border: 2px dashed #cbd5e1; text-align: center; }
        .ticket-header { background: #1e3a8a; color: white; padding: 25px; }
        .ticket-body { padding: 30px 20px; }
        .data-row { margin: 15px 0; color: #334155; font-size: 1.1rem; }
        .badge-role { display: inline-block; padding: 6px 16px; background: #e0e7ff; color: #4338ca; border-radius: 20px; font-weight: bold; margin-top: 10px; text-transform: uppercase;}
        .ticket-footer { background: #f8fafc; padding: 15px; font-size: 0.85rem; color: #64748b; border-top: 1px solid #e2e8f0; }
        .btn-print { background: #3b82f6; color: white; border: none; padding: 12px 24px; border-radius: 8px; font-size: 1rem; font-weight: bold; cursor: pointer; width: 100%; margin-top: 20px;}
        .btn-back { display: block; text-align: center; color: #64748b; text-decoration: none; margin-top: 15px; }
        @media print { body { background: white; } .ticket-card { border-color: #000; box-shadow: none; } .no-print { display: none; } }
    </style>
</head>
<body>
    <div class="ticket-wrapper">
        <div class="ticket-card">
            <div class="ticket-header">
                <h2 style="margin: 0;">CEMS Digital Pass</h2>
            </div>
            <div class="ticket-body">
                <h3 style="color: #0f172a; margin-top: 0; font-size: 1.5rem;"><?php echo htmlspecialchars($ticket['title']); ?></h3>
                <div class="data-row"><strong>Admit:</strong> <?php echo htmlspecialchars($ticket['student_name']); ?></div>
                <div class="data-row"><strong>Date:</strong> <?php echo date('F d, Y', strtotime($ticket['event_date'])); ?></div>
                <div class="data-row"><strong>Time:</strong> <?php echo date('h:i A', strtotime($ticket['event_time'])); ?></div>
                <div class="data-row"><strong>Venue:</strong> <?php echo htmlspecialchars($ticket['venue']); ?></div>
                <span class="badge-role"><?php echo htmlspecialchars($ticket['type']); ?></span>
            </div>
            <div class="ticket-footer">
                Issued: <?php echo date('M d, Y', strtotime($ticket['registered_at'])); ?><br>Present this pass at entry.
            </div>
        </div>
        <div class="no-print">
            <button class="btn-print" onclick="window.print()">Print / Save PDF</button>
            <a href="my_events.php" class="btn-back">&larr; Back to Schedule</a>
        </div>
    </div>
</body>
</html>