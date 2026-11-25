<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';
$id = intval($_GET['id']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_id = intval($_POST['customer_id']);
    $package_id = intval($_POST['package_id']);
    $mechanic_id = intval($_POST['mechanic_id']);
    $booking_date = mysqli_real_escape_string($conn, $_POST['booking_date']);
    $service_status = mysqli_real_escape_string($conn, $_POST['service_status']);
    $payment_status = mysqli_real_escape_string($conn, $_POST['payment_status']);
    $total_amount = floatval($_POST['total_amount']);

    $sql = "UPDATE service_bookings SET 
            customer_id = $customer_id,
            package_id = $package_id,
            mechanic_id = $mechanic_id,
            booking_date = '$booking_date',
            service_status = '$service_status',
            payment_status = '$payment_status',
            total_amount = $total_amount
            WHERE booking_id = $id";

    if (mysqli_query($conn, $sql)) {
        header("Location: index.php?msg=Updated");
        exit();
    }
}

$booking = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM service_bookings WHERE booking_id = $id"));
$customers = mysqli_query($conn, "SELECT * FROM customers");
$packages = mysqli_query($conn, "SELECT * FROM service_packages");
$mechanics = mysqli_query($conn, "SELECT * FROM mechanics");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Booking #<?php echo $id; ?> - AutoCare ERP</title>
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
        <a href="report.php">Reports</a>
    </div>
</div>

<div class="container">
    <h2>Update Service Booking #<?php echo $id; ?></h2>
    <div class="form-box">
        <form method="POST">
            <div class="form-grid">
                <div class="form-group">
                    <label>Customer & Vehicle</label>
                    <select name="customer_id" required>
                        <?php while($c = mysqli_fetch_assoc($customers)): ?>
                            <option value="<?php echo $c['customer_id']; ?>" <?php if($c['customer_id'] == $booking['customer_id']) echo 'selected'; ?>>
                                <?php echo $c['customer_name'] . " (" . $c['vehicle_number'] . ")"; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Service Package</label>
                    <select name="package_id" required>
                        <?php while($p = mysqli_fetch_assoc($packages)): ?>
                            <option value="<?php echo $p['package_id']; ?>" <?php if($p['package_id'] == $booking['package_id']) echo 'selected'; ?>>
                                <?php echo $p['package_name'] . " - ₹" . $p['price']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Assigned Mechanic</label>
                    <select name="mechanic_id" required>
                        <?php while($m = mysqli_fetch_assoc($mechanics)): ?>
                            <option value="<?php echo $m['mechanic_id']; ?>" <?php if($m['mechanic_id'] == $booking['mechanic_id']) echo 'selected'; ?>>
                                <?php echo $m['mechanic_name']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Service Date</label>
                    <input type="date" name="booking_date" value="<?php echo $booking['booking_date']; ?>" required>
                </div>

                <div class="form-group">
                    <label>Service Status</label>
                    <select name="service_status">
                        <option value="Pending" <?php if($booking['service_status']=='Pending') echo 'selected'; ?>>Pending</option>
                        <option value="In Progress" <?php if($booking['service_status']=='In Progress') echo 'selected'; ?>>In Progress</option>
                        <option value="Completed" <?php if($booking['service_status']=='Completed') echo 'selected'; ?>>Completed</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Payment Status</label>
                    <select name="payment_status">
                        <option value="Unpaid" <?php if($booking['payment_status']=='Unpaid') echo 'selected'; ?>>Unpaid</option>
                        <option value="Paid" <?php if($booking['payment_status']=='Paid') echo 'selected'; ?>>Paid</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Total Amount (₹)</label>
                    <input type="number" step="0.01" name="total_amount" value="<?php echo $booking['total_amount']; ?>" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Update Record</button>
            <a href="index.php" class="btn btn-delete" style="margin-left:10px;">Cancel</a>
        </form>
    </div>
</div>

</body>
</html>