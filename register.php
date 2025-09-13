<?php
session_start();
include 'uploads/db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if ($name && $email && $password) {
        // Check if email already exists
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $res_check = $check->get_result();

        if ($res_check->num_rows > 0) {
            $message = "<span style='color:red;'>Account with this email already exists. Please <a href='login.php'>login</a>.</span>";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $name, $email, $hashed);

            if ($stmt->execute()) {
                $message = "<span style='color:green;'>Registration successful! You can now <a href='login.php'>login</a>.</span>";
            } else {
                $message = "<span style='color:red;'>Error: " . $stmt->error . "</span>";
            }
            $stmt->close();
        }
        $check->close();
    } else {
        $message = "<span style='color:red;'>All fields are required.</span>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f2f2f2;
            height: 100vh;
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .register-card {
            background: #ffffff;
            padding: 40px 35px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            width: 360px;
            text-align: center;
            transition: transform 0.3s;
        }
        .register-card:hover { transform: translateY(-3px); }

        .register-card h2 {
            color: #0d1b3d;
            margin-bottom: 25px;
            font-size: 26px;
            letter-spacing: 1px;
        }

        .register-card input {
            width: 100%;
            padding: 12px 15px;
            margin: 12px 0;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 15px;
            box-sizing: border-box;
            display: block;
        }
        .register-card input:focus {
            border-color: #ff6700;
            outline: none;
            box-shadow: 0 0 5px rgba(255,103,0,0.3);
        }

        .register-card button {
            width: 100%;
            padding: 12px;
            margin-top: 15px;
            background-color: #ff6700;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            color: #fff;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
        }
        .register-card button:hover {
            background-color: #e65c00;
            transform: translateY(-1px);
        }

        .message { margin: 10px 0; font-size: 14px; }

        .register-card p {
            margin-top: 18px;
            font-size: 14px;
            color: #555;
        }

        .register-card a {
            color: #ff6700;
            text-decoration: none;
            font-weight: 600;
        }
        .register-card a:hover { color: #e65c00; }
    </style>
</head>
<body>
    <div class="register-card">
        <h2>Create Account</h2>
        <?php if($message): ?>
            <div class="message"><?= $message ?></div>
        <?php endif; ?>
        <form method="post">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a></p>
    </div>
</body>
</html>
