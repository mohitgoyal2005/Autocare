<?php
session_start();
if (isset($_SESSION['admin_logged_in'])) {
    header("Location: index.php");
    exit();
}

$error = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if ($username === "admin" && $password === "admin123") {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_user'] = "Admin";
        header("Location: index.php");
        exit();
    } else {
        $error = "Invalid Username or Password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - AutoCare ERP</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: radial-gradient(circle at top, #1e293b, #0f172a);
        }
        .login-card {
            background-color: #1e293b;
            padding: 40px;
            border-radius: 12px;
            width: 100%;
            max-width: 400px;
            border-top: 4px solid #22c55e;
            box-shadow: 0 10px 25px rgba(0,0,0,0.5);
        }
        .login-card h1 {
            text-align: center;
            color: #22c55e;
            margin-bottom: 5px;
        }
        .login-card p.sub {
            text-align: center;
            color: #94a3b8;
            font-size: 14px;
            margin-bottom: 25px;
        }
        .error-msg {
            background-color: #991b1b;
            color: #fecaca;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
            text-align: center;
            font-size: 14px;
        }
        .hint-box {
            margin-top: 20px;
            padding: 10px;
            background-color: #0f172a;
            border-radius: 6px;
            font-size: 12px;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="login-card">
    <h1>Auto<span style="color:#fff;">Care</span> ERP</h1>
    <p class="sub">Car & Bike Service Management System</p>

    <?php if($error != ""): ?>
        <div class="error-msg"><?php echo $error; ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group" style="margin-bottom:15px;">
            <label>Admin Username</label>
            <input type="text" name="username" required placeholder="Enter username">
        </div>
        <div class="form-group" style="margin-bottom:20px;">
            <label>Password</label>
            <input type="password" name="password" required placeholder="Enter password">
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%; margin-top:0; padding:12px;">Login to Dashboard</button>
    </form>

    <div class="hint-box">
        Demo Credentials — Username: <strong style="color:#22c55e;">admin</strong> | Password: <strong style="color:#22c55e;">admin123</strong>
    </div>
</div>

</body>
</html>