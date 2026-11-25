<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

if (isset($_POST['add_mechanic'])) {
    $m_name = mysqli_real_escape_string($conn, $_POST['mechanic_name']);
    $spec = mysqli_real_escape_string($conn, $_POST['specialization']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);
    mysqli_query($conn, "INSERT INTO mechanics (mechanic_name, specialization, phone, status) VALUES ('$m_name', '$spec', '$phone', '$status')");
    header("Location: mechanics.php");
    exit();
}

if (isset($_POST['add_package'])) {
    $p_name = mysqli_real_escape_string($conn, $_POST['package_name']);
    $cat = mysqli_real_escape_string($conn, $_POST['vehicle_category']);
    $price = floatval($_POST['price']);
    $desc = mysqli_real_escape_string($conn, $_POST['description']);
    mysqli_query($conn, "INSERT INTO service_packages (package_name, vehicle_category, price, description) VALUES ('$p_name', '$cat', $price, '$desc')");
    header("Location: mechanics.php");
    exit();
}

$mechanics = mysqli_query($conn, "SELECT * FROM mechanics");
$packages = mysqli_query($conn, "SELECT * FROM service_packages");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Mechanics & Packages - AutoCare ERP</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="navbar">
    <div class="logo">Auto<span>Care</span> ERP</div>
    <div class="nav-links">
        <a href="index.php">Dashboard & Bookings</a>
        <a href="add_booking.php">+ New Booking</a>
        <a href="customers.php">Customers</a>
        <a href="mechanics.php" class="active">Mechanics & Packages</a>
        <a href="report.php">Reports</a>
    </div>
</div>

<div class="container">
    <h2>Mechanic Management</h2>
    <div class="form-box">
        <form method="POST">
            <div class="form-grid">
                <div class="form-group">
                    <label>Mechanic Name *</label>
                    <input type="text" name="mechanic_name" required>
                </div>
                <div class="form-group">
                    <label>Specialization *</label>
                    <input type="text" name="specialization" required placeholder="e.g. Engine / Electrical">
                </div>
                <div class="form-group">
                    <label>Phone *</label>
                    <input type="text" name="phone" required>
                </div>
                <div class="form-group">
                    <label>Availability</label>
                    <select name="status">
                        <option value="Available">Available</option>
                        <option value="Busy">Busy</option>
                    </select>
                </div>
            </div>
            <button type="submit" name="add_mechanic" class="btn btn-primary">Add Mechanic</button>
        </form>
    </div>

    <table style="margin-bottom:40px;">
        <thead>
            <tr>
                <th>ID</th>
                <th>Mechanic Name</th>
                <th>Specialization</th>
                <th>Phone</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php while($m = mysqli_fetch_assoc($mechanics)): ?>
            <tr>
                <td><?php echo $m['mechanic_id']; ?></td>
                <td><?php echo htmlspecialchars($m['mechanic_name']); ?></td>
                <td><?php echo htmlspecialchars($m['specialization']); ?></td>
                <td><?php echo htmlspecialchars($m['phone']); ?></td>
                <td><span class="badge <?php echo ($m['status']=='Available')?'bg-green':'bg-yellow'; ?>"><?php echo $m['status']; ?></span></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <h2>Service Packages</h2>
    <div class="form-box">
        <form method="POST">
            <div class="form-grid">
                <div class="form-group">
                    <label>Package Name *</label>
                    <input type="text" name="package_name" required>
                </div>
                <div class="form-group">
                    <label>Vehicle Category</label>
                    <select name="vehicle_category">
                        <option value="Car">Car</option>
                        <option value="Bike">Bike</option>
                        <option value="Both">Both</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Price (₹) *</label>
                    <input type="number" step="0.01" name="price" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <input type="text" name="description" required>
                </div>
            </div>
            <button type="submit" name="add_package" class="btn btn-primary">Add Package</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Package Name</th>
                <th>Category</th>
                <th>Price</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            <?php while($p = mysqli_fetch_assoc($packages)): ?>
            <tr>
                <td><?php echo $p['package_id']; ?></td>
                <td><?php echo htmlspecialchars($p['package_name']); ?></td>
                <td><?php echo $p['vehicle_category']; ?></td>
                <td>₹<?php echo number_format($p['price'], 2); ?></td>
                <td><?php echo htmlspecialchars($p['description']); ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>