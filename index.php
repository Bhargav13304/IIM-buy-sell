<?php
session_start();
include 'uploads/db.php';

// Check login
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in. <a href='login.php'>Login here</a>");
}

$user_id = $_SESSION['user_id'];
$view = $_GET['view'] ?? 'products';
$search = $_GET['search'] ?? '';

// Total unread messages for top badge
$stmt_unread = $conn->prepare("
    SELECT IFNULL(COUNT(*),0) as unread_count
    FROM chat
    WHERE receiver_id=? AND is_read=0
");
$stmt_unread->bind_param("i", $user_id);
$stmt_unread->execute();
$res_unread = $stmt_unread->get_result();
$unread_count = ($row = $res_unread->fetch_assoc()) ? $row['unread_count'] : 0;
$stmt_unread->close();

if ($view === 'mychats') {
    $stmt = $conn->prepare("
        SELECT p.id, p.name, p.image, p.price, p.seller_id,
               MAX(c.created_at) as last_time,
               SUBSTRING_INDEX(MAX(CONCAT(c.created_at, '||', c.message)), '||', -1) as last_message,
               IFNULL(SUM(CASE WHEN c.receiver_id=? AND c.is_read=0 THEN 1 ELSE 0 END),0) as unread_count
        FROM chat c
        JOIN products p ON p.id=c.product_id
        WHERE c.sender_id=? OR c.receiver_id=?
        GROUP BY p.id, p.name, p.image, p.price, p.seller_id
        ORDER BY last_time DESC
    ");
    $stmt->bind_param("iii", $user_id, $user_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    $sql = "SELECT p.*, 
                   (SELECT IFNULL(COUNT(*),0) FROM chat c WHERE c.product_id=p.id AND c.receiver_id=$user_id AND c.is_read=0) AS unread_count
            FROM products p";
    if ($search) {
        $sql .= " WHERE p.name LIKE '%" . $conn->real_escape_string($search) . "%'";
    }
    $result = $conn->query($sql);
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

        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: var(--grey-bg); margin: 0; padding: 0; }

        .top-nav { display: flex; justify-content: flex-start; align-items: center; background-color: var(--primary-blue); padding: 15px 30px; }
        .top-nav a { color: var(--white); text-decoration: none; margin-right: 25px; font-weight: 600; position: relative; padding: 8px 12px; border-radius: 4px; transition: background 0.3s; }
        .top-nav a:hover { background-color: var(--primary-orange); }

        .chat-badge { position: absolute; top: -5px; right: -10px; background-color: var(--danger-red); color: var(--white); border-radius: 50%; padding: 3px 7px; font-size: 0.75em; font-weight: bold; }

        .search-form { padding: 20px 30px; text-align: center; }
        .search-form input[type="text"] { width: 300px; padding: 8px 12px; border-radius: 4px; border: 1px solid #ccc; }
        .search-form button { padding: 8px 15px; border: none; border-radius: 4px; background-color: var(--success-green); color: var(--white); cursor: pointer; font-weight: 600; margin-left: 8px; transition: background 0.3s; }
        .search-form button:hover { background-color: #218838; }

        .product-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; padding: 20px 30px; }
        .product-card { background-color: var(--white); border-radius: 6px; padding: 15px; box-shadow: 0px 2px 8px rgba(0,0,0,0.1); display: flex; flex-direction: column; align-items: center; text-align: center; position: relative; transition: transform 0.2s, box-shadow 0.2s; }
        .product-card:hover { transform: translateY(-5px); box-shadow: 0px 5px 15px rgba(0,0,0,0.15); }
        .product-card img { max-width: 150px; max-height: 150px; object-fit: contain; margin-bottom: 12px; }
        .product-card h3 { color: var(--primary-blue); margin: 8px 0; }
        .product-card p { color: #555; margin: 5px 0; }
        .product-card .chat-preview { font-size: 0.85em; color: #888; margin: 5px 0; }

        .button-group { display: flex; gap: 10px; margin-top: 10px; }
        .product-card a,
        .product-card button { text-decoration: none; padding: 8px 12px; border-radius: 4px; font-weight: 600; transition: background 0.3s; cursor: pointer; border: none; }

        .product-card a { background-color: var(--primary-orange); color: var(--white); }
        .product-card a:hover { background-color: #e65c00; }

        /* Buy button style */
        .product-card .buy-btn { background-color: var(--success-green); color: #fff; }
        .product-card .buy-btn:hover { background-color: #218838; }
    </style>
</head>
<body>

<div class="top-nav">
    <a href="index.php?view=products">All Products</a>
    <a href="index.php?view=mychats">My Chats
        <?php if ($unread_count > 0): ?>
            <span class="chat-badge"><?= $unread_count ?></span>
        <?php endif; ?>
    </a>
    <a href="post_product.php">Sell a Product</a>
</div>

<?php if ($view === 'products'): ?>
    <div class="search-form">
        <form method="get" action="index.php">
            <input type="hidden" name="view" value="products">
            <input type="text" name="search" placeholder="Search products..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit">Search</button>
        </form>
    </div>
<?php endif; ?>

<div class="product-list">
    <?php if ($result && $result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="product-card">
                <img src="uploads/<?= htmlspecialchars($row['image']) ?>" alt="Product Image">
                <h3><?= htmlspecialchars($row['name']) ?></h3>
                <p>Price: ₹<?= htmlspecialchars($row['price']) ?></p>

                <?php if ($view === 'mychats'): ?>
                    <p class="chat-preview"><i>Last message:</i> <?= htmlspecialchars($row['last_message'] ?? '') ?></p>
                <?php endif; ?>

                <?php if (!empty($row['unread_count']) && $row['unread_count'] > 0): ?>
                    <span class="chat-badge"><?= $row['unread_count'] ?></span>
                <?php endif; ?>

                <div class="button-group">
                    <a href="product.php?id=<?= $row['id'] ?>">View & Chat</a>
                    <?php if ($row['seller_id'] != $user_id): ?>
                        <button class="buy-btn">Buy</button>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="text-align:center; color:#555; padding:20px;">No results found.</p>
    <?php endif; ?>
</div>

</body>
</html>
