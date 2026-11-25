<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

$msg = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['customer_name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $v_type = mysqli_real_escape_string($conn, $_POST['vehicle_type']);
    $v_num = strtoupper(mysqli_real_escape_string($conn, $_POST['vehicle_number']));
    $v_model = mysqli_real_escape_string($conn, $_POST['vehicle_model']);

    $check = mysqli_query($conn, "SELECT * FROM customers WHERE vehicle_number = '$v_num'");
    if (mysqli_num_rows($check) > 0) {
        $msg = "Error: Vehicle Number $v_num is already registered!";
    } else {
        mysqli_query($conn, "INSERT INTO customers (customer_name, phone, email, vehicle_type, vehicle_number, vehicle_model) 
                             VALUES ('$name', '$phone', '$email', '$v_type', '$v_num', '$v_model')");
        header("Location: customers.php");
        exit();
    }
}

if (isset($_GET['del'])) {
    $id = intval($_GET['del']);
    mysqli_query($conn, "DELETE FROM customers WHERE customer_id = $id");
    header("Location: customers.php");
    exit();
}

$customers = mysqli_query($conn, "SELECT * FROM customers ORDER BY customer_id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Records - AutoCare ERP</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="navbar">
    <div class="logo">Auto<span>Care</span> ERP</div>
    <div class="nav-links">
        <a href="index.php">Dashboard & Bookings</a>
        <a href="add_booking.php">+ New Booking</a>
        <a href="customers.php" class="active">Customers</a>
        <a href="mechanics.php">Mechanics & Packages</a>
        <a href="report.php">Reports</a>
        <a href="logout.php" style="background:#ef4444; color:#fff;">Logout</a>
    </div>
</div>

<div class="container">
    <h2>Register New Customer & Vehicle</h2>
    <?php if($msg != ""): ?>
        <p style="background:#991b1b; color:#fecaca; padding:10px; border-radius:6px; margin-bottom:15px;"><?php echo $msg; ?></p>
    <?php endif; ?>

    <div class="form-box">
        <form method="POST">
            <div class="form-grid">
                <div class="form-group">
                    <label>Customer Name *</label>
                    <input type="text" name="customer_name" required placeholder="e.g. Rohit Meena">
                </div>
                <div class="form-group">
                    <label>Phone Number *</label>
                    <input type="text" name="phone" required pattern="[0-9]{10}" placeholder="10-digit mobile">
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="customer@email.com">
                </div>
                <div class="form-group">
                    <label>Vehicle Type *</label>
                    <select name="vehicle_type">
                        <option value="Car">Car</option>
                        <option value="Bike">Bike</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Vehicle Number *</label>
                    <input type="text" name="vehicle_number" required placeholder="e.g. RJ19CA1234">
                </div>
                <div class="form-group">
                    <label>Vehicle Model *</label>
                    <input type="text" name="vehicle_model" required placeholder="e.g. Honda City / Pulsar 150">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Add Customer</button>
        </form>
    </div>

    <h2>Registered Customers & Vehicles</h2>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Customer Name</th>
                <th>Phone</th>
                <th>Vehicle Type</th>
                <th>Vehicle Number</th>
                <th>Model</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while($c = mysqli_fetch_assoc($customers)): ?>
            <tr>
                <td><?php echo $c['customer_id']; ?></td>
                <td><?php echo htmlspecialchars($c['customer_name']); ?></td>
                <td><?php echo htmlspecialchars($c['phone']); ?></td>
                <td><?php echo $c['vehicle_type']; ?></td>
                <td><strong><?php echo htmlspecialchars($c['vehicle_number']); ?></strong></td>
                <td><?php echo htmlspecialchars($c['vehicle_model']); ?></td>
                <td><a href="customers.php?del=<?php echo $c['customer_id']; ?>" class="btn btn-delete" onclick="return confirm('Delete customer?');">Delete</a></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>