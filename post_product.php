<?php
session_start();
include 'uploads/db.php';
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to post a product. <a href='login.php'>Login here</a>");
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $description = $_POST['description'];
    $image = '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $img_name = uniqid() . '_' . basename($_FILES['image']['name']);
        $target = 'uploads/' . $img_name;
        if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
            $image = $img_name;
        } else {
            $message = "<p style='color:red;'>Image upload failed.</p>";
        }
    } else {
        $message = "<p style='color:red;'>No image uploaded or upload error.</p>";
    }

    if ($image) {
        $stmt = $conn->prepare("INSERT INTO products (name, price, description, image, seller_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sdssi", $name, $price, $description, $image, $_SESSION['user_id']);
        if ($stmt->execute()) {
            // Redirect to index page automatically after success
            header("Location: index.php");
            exit;
        } else {
            $message = "<p style='color:red;'>Database error: " . $stmt->error . "</p>";
        }
        $stmt->close();
    } else {
        $message = "<p style='color:red;'>Product not saved because image was not uploaded.</p>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #F5F5F5;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            max-width: 500px;
            margin: 50px auto;
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #0B3D91;
            margin-bottom: 20px;
        }
        form input, form textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
        }
        form textarea {
            resize: vertical;
            min-height: 80px;
        }
        form button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            width: 100%;
            font-size: 1em;
        }
        form button:hover {
            background-color: #218838;
        }
        .message {
            margin-bottom: 15px;
            text-align: center;
        }
        a {
            color: #FF6F00;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Post a Product for Sale</h2>
        <?php if($message) echo '<div class="message">'.$message.'</div>'; ?>
        <form method="post" enctype="multipart/form-data">
            <input type="text" name="name" placeholder="Product Name" required>
            <input type="number" name="price" placeholder="Price" required>
            <textarea name="description" placeholder="Description"></textarea>
            <input type="file" name="image" required>
            <button type="submit">Post Product</button>
        </form>
    </div>
</body>
</html>
