<?php
session_start();
include 'uploads/db.php';

if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to chat. <a href='login.php'>Login here</a>");
}

$user_id = $_SESSION['user_id'];
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$product = null;
if ($id > 0) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $product = $res->fetch_assoc();
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = trim($_POST['message']);
    if ($id > 0 && $message !== '') {
        $receiver_id = ($user_id == $product['seller_id']) ? intval($_POST['buyer_id'] ?? 0) : $product['seller_id'];
        $stmt = $conn->prepare("INSERT INTO chat (product_id, sender_id, receiver_id, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiis", $id, $user_id, $receiver_id, $message);
        $stmt->execute();
        $stmt->close();
    }
    exit;
}

if ($id > 0) {
    $stmt = $conn->prepare("UPDATE chat SET is_read = 1 WHERE product_id = ? AND receiver_id = ?");
    $stmt->bind_param("ii", $id, $user_id);
    $stmt->execute();
    $stmt->close();
}

$messages = [];
if ($id > 0) {
    $stmt = $conn->prepare("
        SELECT c.message, c.created_at, u.name AS sender_name
        FROM chat c
        JOIN users u ON u.id = c.sender_id
        WHERE c.product_id = ?
        ORDER BY c.created_at ASC, c.id ASC
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $messages[] = $row;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        :root {
            --primary-blue: #0d1b3d;
            --primary-orange: #ff6700;
            --grey-bg: #f2f2f2;
            --white: #ffffff;
            --success-green: #28a745;
            --danger-red: #dc3545;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: var(--grey-bg);
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 1000px;
            margin: 30px auto;
            background-color: var(--white);
            border-radius: 8px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: row;
            gap: 20px;
            padding: 20px;
            flex-wrap: wrap;
        }

        .product-section {
            flex: 1 1 300px;
            max-width: 400px;
            display: flex;
            flex-direction: column;
            align-items: center;
            border-right: 2px solid #ddd;
            padding-right: 20px;
        }

        .product-section img {
            max-width: 100%;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .product-section h2 {
            margin: 0 0 10px 0;
            color: var(--primary-blue);
        }

        .product-section p {
            margin: 5px 0;
            color: #555;
        }

        /* Description box styling */
        #description-box {
            border: 1px solid #ccc;
            height: 150px;
            overflow-y: auto;
            padding: 10px;
            background-color: #f0f0f0;
            border-radius: 5px;
            width: 100%;
            box-sizing: border-box;
            margin-top: 5px;
        }

        .chat-section {
            flex: 2 1 500px;
            display: flex;
            flex-direction: column;
        }

        .chat-section h3 {
            color: var(--primary-orange);
            margin-bottom: 10px;
        }

        #messages {
            border: 1px solid #ccc;
            height: 400px;
            overflow-y: auto;
            padding: 10px;
            background-color: #f0f0f0;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        #messages div {
            margin-bottom: 8px;
        }

        .chat-input-container {
            display: flex;
        }

        #chat-input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            margin-right: 10px;
            font-size: 1em;
        }

        #send-btn {
            background-color: var(--success-green);
            color: var(--white);
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
        }

        #send-btn:hover {
            background-color: #218838;
        }

        @media(max-width: 800px) {
            .container {
                flex-direction: column;
            }
            .product-section {
                border-right: none;
                border-bottom: 2px solid #ddd;
                padding-bottom: 20px;
                padding-right: 0;
            }
        }
    </style>
</head>
<body>
<div class="container">
<?php if ($product): ?>
    <div class="product-section">
        <img src="uploads/<?= htmlspecialchars($product['image']) ?>" alt="Product Image">
        <h2><?= htmlspecialchars($product['name']) ?></h2>
        <p>Price: ₹<?= htmlspecialchars($product['price']) ?></p>
        <p>Seller: <?= htmlspecialchars($product['seller_id'] == $user_id ? 'You' : 'Seller') ?></p>
        <!-- Description inside a box -->
        <h3>Description:</h3>
        <div id="description-box">
            <?= nl2br(htmlspecialchars($product['description'])) ?>
        </div>
    </div>

    <div class="chat-section">
        <h3>Chat</h3>
        <div id="messages">
            <?php foreach($messages as $msg): ?>
                <div><b><?= htmlspecialchars($msg['sender_name']) ?>:</b> <?= htmlspecialchars($msg['message']) ?></div>
            <?php endforeach; ?>
        </div>
        <div class="chat-input-container">
            <input type="text" id="chat-input" placeholder="Type your message...">
            <button id="send-btn">Send</button>
        </div>
    </div>

    <script>
        const productId = <?= $id ?>;
        const userId = <?= $user_id ?>;
        const messagesDiv = document.getElementById('messages');
        const input = document.getElementById('chat-input');
        const sendBtn = document.getElementById('send-btn');

        function appendMessage(user, message) {
            const div = document.createElement('div');
            div.innerHTML = `<b>${user}:</b> ${message}`;
            messagesDiv.appendChild(div);
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        }

        sendBtn.addEventListener('click', () => {
            const message = input.value.trim();
            if (!message) return;

            fetch(window.location.href, {
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                body: `message=${encodeURIComponent(message)}&buyer_id=${userId}`
            }).then(() => {
                appendMessage("You", message);
                input.value = '';
            });
        });

        setInterval(() => {
            fetch(window.location.href)
            .then(res => res.text())
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newMessages = doc.getElementById('messages').innerHTML;
                messagesDiv.innerHTML = newMessages;
                messagesDiv.scrollTop = messagesDiv.scrollHeight;
            });
        }, 3000);
    </script>
<?php else: ?>
    <p>Product not found.</p>
<?php endif; ?>
</div>
</body>
</html>
