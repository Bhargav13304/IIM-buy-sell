

<?php

 'includes/db.php'; 
$product_id = $_POST['product_id'] ?? 0;
if(isset($_POST['message'])) {
    $msg = $_POST['message'];
    $user = $_POST['user'] ?? 'Anonymous';
    // Save message to DB
}
$messages = []; // Fetch messages from DB
echo json_encode($messages);