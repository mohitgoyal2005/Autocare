<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

$paid_total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_amount) AS amt FROM service_bookings WHERE payment_status='Paid'"))['amt'] ?? 0;
$unpaid_total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_amount) AS amt FROM service_bookings WHERE payment_status='Unpaid'"))['amt'] ?? 0;

$mech_report = mysqli_query($conn, "SELECT m.mechanic_name, m.specialization, COUNT(sb.booking_id) AS total_jobs, COALESCE(SUM(sb.total_amount),0) AS revenue 
                                   FROM mechanics m 
                                   LEFT JOIN service_bookings sb ON m.mechanic_id = sb.mechanic_id 
                                   GROUP BY m.mechanic_id");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reports & Summary - AutoCare ERP</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="navbar">
    <div class="logo">Auto<span>Care</span> ERP</div>
    <div class="nav-links">
        <a href="index.php">Dashboard & Bookings</a>
        <a href="add_booking.php">+ New Booking</a>
        <a href="customers.php">Customers</a>
        <a href="mechanics.php">Mechanics & Packages</a>
        <a href="report.php" class="active">Reports</a>
<a href="logout.php" style="background:#ef4444; color:#fff;">Logout</a>
    </div>
</div>

<div class="container">
    <h2>Financial & Service Summary Report</h2>
    
    <div class="cards">
        <div class="card">
            <h3>PAID REVENUE (COLLECTED)</h3>
            <p style="color:#22c55e;">₹<?php echo number_format($paid_total, 2); ?></p>
        </div>
        <div class="card" style="border-left-color:#ef4444;">
            <h3>UNPAID DUES (PENDING)</h3>
            <p style="color:#ef4444;">₹<?php echo number_format($unpaid_total, 2); ?></p>
        </div>
    </div>

    <h2>Mechanic-Wise Workload & Revenue Report</h2>
    <table>
        <thead>
            <tr>
                <th>Mechanic Name</th>
                <th>Specialization</th>
                <th>Total Assigned Services</th>
                <th>Total Billing Generated</th>
            </tr>
        </thead>
        <tbody>
            <?php while($r = mysqli_fetch_assoc($mech_report)): ?>
            <tr>
                <td><strong><?php echo htmlspecialchars($r['mechanic_name']); ?></strong></td>
                <td><?php echo htmlspecialchars($r['specialization']); ?></td>
                <td><?php echo $r['total_jobs']; ?> Services</td>
                <td>₹<?php echo number_format($r['revenue'], 2); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>