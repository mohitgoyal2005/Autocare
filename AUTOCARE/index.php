<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';

// Delete Operation
if (isset($_GET['delete_id'])) {
    $del_id = intval($_GET['delete_id']);
    mysqli_query($conn, "DELETE FROM service_bookings WHERE booking_id = $del_id");
    header("Location: index.php?msg=Deleted");
    exit();
}

// Dashboard Stats Queries
$total_bookings = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS count FROM service_bookings"))['count'];
$total_customers = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS count FROM customers"))['count'];
$pending_services = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS count FROM service_bookings WHERE service_status != 'Completed'"))['count'];
$total_revenue = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_amount) AS total FROM service_bookings WHERE payment_status = 'Paid'"))['total'] ?? 0;

// Graph 1 Data: Service Status Counts
$completed_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM service_bookings WHERE service_status = 'Completed'"))['c'];
$inprogress_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM service_bookings WHERE service_status = 'In Progress'"))['c'];
$pending_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM service_bookings WHERE service_status = 'Pending'"))['c'];

// Graph 2 Data: Paid vs Unpaid Revenue
$paid_rev = $total_revenue;
$unpaid_rev = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(total_amount) AS total FROM service_bookings WHERE payment_status = 'Unpaid'"))['total'] ?? 0;

// Search & Filter Logic
$search = "";
$where_clause = "";
if (isset($_GET['search']) && !empty(trim($_GET['search']))) {
    $search = mysqli_real_escape_string($conn, trim($_GET['search']));
    $where_clause = "WHERE c.customer_name LIKE '%$search%' OR c.vehicle_number LIKE '%$search%' OR sb.service_status LIKE '%$search%' OR m.mechanic_name LIKE '%$search%'";
}

$query = "SELECT sb.*, c.customer_name, c.vehicle_type, c.vehicle_number, c.vehicle_model, 
                 p.package_name, m.mechanic_name 
          FROM service_bookings sb
          JOIN customers c ON sb.customer_id = c.customer_id
          JOIN service_packages p ON sb.package_id = p.package_id
          JOIN mechanics m ON sb.mechanic_id = m.mechanic_id
          $where_clause
          ORDER BY sb.booking_id DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AutoCare ERP - Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <!-- Chart.js CDN for Graphical Representation -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .charts-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 20px;
            margin-bottom: 35px;
        }
        .chart-card {
            background-color: #1e293b;
            padding: 20px;
            border-radius: 10px;
            border-top: 3px solid #22c55e;
            box-shadow: 0 4px 6px rgba(0,0,0,0.3);
        }
        .chart-card h3 {
            color: #cbd5e1;
            font-size: 16px;
            margin-bottom: 15px;
            text-align: center;
        }
        .chart-wrapper {
            position: relative;
            height: 240px;
            width: 100%;
            display: flex;
            justify-content: center;
        }
    </style>
</head>
<body>

<div class="navbar">
    <div class="logo">Auto<span>Care</span> ERP</div>
    <div class="nav-links">
        <a href="index.php" class="active">Dashboard & Bookings</a>
        <a href="add_booking.php">+ New Booking</a>
        <a href="customers.php">Customers</a>
        <a href="mechanics.php">Mechanics & Packages</a>
        <a href="report.php">Reports</a>
        <a href="logout.php" style="background:#ef4444; color:#fff;">Logout</a>
    </div>
</div>

<div class="container">
    <h2>Service Center Dashboard</h2>
    
    <div class="cards">
        <div class="card">
            <h3>TOTAL BOOKINGS</h3>
            <p><?php echo $total_bookings; ?></p>
        </div>
        <div class="card">
            <h3>REGISTERED VEHICLES</h3>
            <p><?php echo $total_customers; ?></p>
        </div>
        <div class="card">
            <h3>ACTIVE / PENDING SERVICES</h3>
            <p><?php echo $pending_services; ?></p>
        </div>
        <div class="card">
            <h3>COLLECTED REVENUE</h3>
            <p>₹<?php echo number_format($total_revenue, 2); ?></p>
        </div>
    </div>

    <!-- Graphical Analytics Section -->
    <div class="charts-grid">
        <div class="chart-card">
            <h3>Service Status Overview</h3>
            <div class="chart-wrapper">
                <canvas id="statusChart"></canvas>
            </div>
        </div>
        <div class="chart-card">
            <h3>Revenue Collection vs Pending Dues (₹)</h3>
            <div class="chart-wrapper">
                <canvas id="revenueChart"></canvas>
            </div>
        </div>
    </div>

    <h2>Vehicle Service Bookings (CRUD & Search)</h2>
    
    <form method="GET" class="search-bar">
        <input type="text" name="search" placeholder="Search by Customer Name, Vehicle Number (e.g. RJ19), Mechanic, or Status..." value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit" class="btn btn-primary" style="margin-top:0;">Search</button>
        <?php if($search != ""): ?>
            <a href="index.php" class="btn btn-delete" style="padding:10px 15px;">Reset</a>
        <?php endif; ?>
    </form>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Customer & Vehicle</th>
                <th>Service Package</th>
                <th>Assigned Mechanic</th>
                <th>Date</th>
                <th>Service Status</th>
                <th>Payment</th>
                <th>Amount</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if(mysqli_num_rows($result) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td>#<?php echo $row['booking_id']; ?></td>
                    <td>
                        <strong><?php echo htmlspecialchars($row['customer_name']); ?></strong><br>
                        <small style="color:#94a3b8;"><?php echo $row['vehicle_type']; ?>: <?php echo htmlspecialchars($row['vehicle_model']); ?> (<?php echo htmlspecialchars($row['vehicle_number']); ?>)</small>
                    </td>
                    <td><?php echo htmlspecialchars($row['package_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['mechanic_name']); ?></td>
                    <td><?php echo $row['booking_date']; ?></td>
                    <td>
                        <?php 
                        $s_class = ($row['service_status'] == 'Completed') ? 'bg-green' : (($row['service_status'] == 'In Progress') ? 'bg-blue' : 'bg-yellow');
                        ?>
                        <span class="badge <?php echo $s_class; ?>"><?php echo $row['service_status']; ?></span>
                    </td>
                    <td>
                        <span class="badge <?php echo ($row['payment_status'] == 'Paid') ? 'bg-green' : 'bg-red'; ?>">
                            <?php echo $row['payment_status']; ?>
                        </span>
                    </td>
                    <td>₹<?php echo number_format($row['total_amount'], 2); ?></td>
                    <td>
                        <a href="edit_booking.php?id=<?php echo $row['booking_id']; ?>" class="btn btn-edit">Edit</a>
                        <a href="index.php?delete_id=<?php echo $row['booking_id']; ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this booking?');">Delete</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="9" style="text-align:center;">No service booking records found.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<script>
    // 1. Service Status Doughnut Chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    new Chart(statusCtx, {
        type: 'doughnut',
        data: {
            labels: ['Completed', 'In Progress', 'Pending'],
            datasets: [{
                data: [<?php echo $completed_count; ?>, <?php echo $inprogress_count; ?>, <?php echo $pending_count; ?>],
                backgroundColor: ['#22c55e', '#3b82f6', '#eab308'],
                borderColor: '#1e293b',
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { color: '#cbd5e1', font: { size: 12 } }
                }
            }
        }
    });

    // 2. Revenue Bar Chart
    const revCtx = document.getElementById('revenueChart').getContext('2d');
    new Chart(revCtx, {
        type: 'bar',
        data: {
            labels: ['Collected (Paid)', 'Pending Dues (Unpaid)'],
            datasets: [{
                label: 'Amount in ₹',
                data: [<?php echo $paid_rev; ?>, <?php echo $unpaid_rev; ?>],
                backgroundColor: ['#22c55e', '#ef4444'],
                borderRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { color: '#94a3b8' },
                    grid: { color: '#334155' }
                },
                x: {
                    ticks: { color: '#cbd5e1' },
                    grid: { display: false }
                }
            }
        }
    });
</script>

</body>
</html>