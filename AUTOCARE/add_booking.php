<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_id = intval($_POST['customer_id']);
    $package_id = intval($_POST['package_id']);
    $mechanic_id = intval($_POST['mechanic_id']);
    $booking_date = mysqli_real_escape_string($conn, $_POST['booking_date']);
    $service_status = mysqli_real_escape_string($conn, $_POST['service_status']);
    $payment_status = mysqli_real_escape_string($conn, $_POST['payment_status']);

    $pkg_res = mysqli_query($conn, "SELECT price FROM service_packages WHERE package_id = $package_id");
    $pkg_row = mysqli_fetch_assoc($pkg_res);
    $total_amount = $pkg_row['price'];

    $sql = "INSERT INTO service_bookings (customer_id, package_id, mechanic_id, booking_date, service_status, payment_status, total_amount) 
            VALUES ($customer_id, $package_id, $mechanic_id, '$booking_date', '$service_status', '$payment_status', $total_amount)";

    if (mysqli_query($conn, $sql)) {
        header("Location: index.php?msg=Added");
        exit();
    }
}

$customers = mysqli_query($conn, "SELECT * FROM customers");
$packages = mysqli_query($conn, "SELECT * FROM service_packages");
$mechanics = mysqli_query($conn, "SELECT * FROM mechanics");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New Service Booking - AutoCare ERP</title>
    <link rel="stylesheet" href="style.css">
    <script>
        function validateForm() {
            let cust = document.forms["bookingForm"]["customer_id"].value;
            let pkg = document.forms["bookingForm"]["package_id"].value;
            let mech = document.forms["bookingForm"]["mechanic_id"].value;
            let date = document.forms["bookingForm"]["booking_date"].value;
            if (cust == "" || pkg == "" || mech == "" || date == "") {
                alert("All fields are mandatory! Please select all details.");
                return false;
            }
            return true;
        }
    </script>
</head>
<body>

<div class="navbar">
    <div class="logo">Auto<span>Care</span> ERP</div>
    <div class="nav-links">
        <a href="index.php">Dashboard & Bookings</a>
        <a href="add_booking.php" class="active">+ New Booking</a>
        <a href="customers.php">Customers</a>
        <a href="mechanics.php">Mechanics & Packages</a>
        <a href="report.php">Reports</a>
        <a href="logout.php" style="background:#ef4444; color:#fff;">Logout</a>
    </div>
</div>

<div class="container">
    <h2>Create New Vehicle Service Booking</h2>
    <div class="form-box">
        <form name="bookingForm" method="POST" onsubmit="return validateForm()">
            <div class="form-grid">
                <div class="form-group">
                    <label>Select Customer & Vehicle *</label>
                    <select name="customer_id" required>
                        <option value="">-- Choose Customer --</option>
                        <?php while($c = mysqli_fetch_assoc($customers)): ?>
                            <option value="<?php echo $c['customer_id']; ?>">
                                <?php echo $c['customer_name'] . " (" . $c['vehicle_type'] . " - " . $c['vehicle_number'] . ")"; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Select Service Package *</label>
                    <select name="package_id" required>
                        <option value="">-- Choose Package --</option>
                        <?php while($p = mysqli_fetch_assoc($packages)): ?>
                            <option value="<?php echo $p['package_id']; ?>">
                                <?php echo $p['package_name'] . " - ₹" . $p['price']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Assign Mechanic *</label>
                    <select name="mechanic_id" required>
                        <option value="">-- Choose Mechanic --</option>
                        <?php while($m = mysqli_fetch_assoc($mechanics)): ?>
                            <option value="<?php echo $m['mechanic_id']; ?>">
                                <?php echo $m['mechanic_name'] . " (" . $m['specialization'] . ")"; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label>Service Date *</label>
                    <input type="date" name="booking_date" value="<?php echo date('Y-m-d'); ?>" required>
                </div>

                <div class="form-group">
                    <label>Service Status</label>
                    <select name="service_status">
                        <option value="Pending">Pending</option>
                        <option value="In Progress">In Progress</option>
                        <option value="Completed">Completed</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Payment Status</label>
                    <select name="payment_status">
                        <option value="Unpaid">Unpaid</option>
                        <option value="Paid">Paid</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Save Service Booking</button>
            <a href="index.php" class="btn btn-delete" style="margin-left:10px;">Cancel</a>
        </form>
    </div>
</div>

</body>
</html>