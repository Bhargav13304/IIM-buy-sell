<?php
session_start();
include 'uploads/db.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if ($email && $password) {
        $stmt = $conn->prepare("SELECT id, name, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($user = $res->fetch_assoc()) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];
                header('Location: index.php');
                exit;
            } else {
                $message = "Invalid password.";
            }
        } else {
            $message = "User not found.";
        }
        $stmt->close();
    } else {
        $message = "All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
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

        .login-card {
            background: #ffffff;
            padding: 40px 35px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            width: 360px;
            text-align: center;
            transition: transform 0.3s;
        }
        .login-card:hover { transform: translateY(-3px); }

        .login-card h2 {
            color: #0d1b3d;
            margin-bottom: 25px;
            font-size: 26px;
            letter-spacing: 1px;
        }

        .login-card input {
            width: 100%;
            padding: 12px 15px;
            margin: 12px 0;
            border-radius: 6px;
            border: 1px solid #ccc;
            font-size: 15px;
            box-sizing: border-box;
        }
        .login-card input:focus {
            border-color: #ff6700;
            outline: none;
            box-shadow: 0 0 5px rgba(255,103,0,0.3);
        }

        .login-card button {
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
        .login-card button:hover {
            background-color: #e65c00;
            transform: translateY(-1px);
        }

        .message { margin: 10px 0; font-size: 14px; color: #dc3545; }

        .login-card p {
            margin-top: 18px;
            font-size: 14px;
            color: #555;
        }

        .login-card a {
            color: #ff6700;
            text-decoration: none;
            font-weight: 600;
        }
        .login-card a:hover { color: #e65c00; }
    </style>
</head>
<body>
    <div class="login-card">
        <h2>Login</h2>
        <?php if($message): ?>
            <div class="message"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>
        <form method="post">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</body>
</html>
