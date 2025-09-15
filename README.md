// generate-readme.js
// Node.js script to generate README.md for IIM-buy-sell

const fs = require("fs");

const content = `# IIM Buy & Sell

This project is a **web application** designed for the IIM community to facilitate buying and selling items within the campus.  
It provides an easy-to-use interface for students, faculty, and staff to post, browse, and manage listings.

---

## Features
- User authentication and role-based access.
- Post ads for items with images, price, and description.
- Browse items by category and search functionality.
- Direct communication between buyer and seller.
- Admin panel to manage users and listings.

---

## Tech Stack
- **Frontend**: HTML, CSS, JavaScript  
- **Backend**: PHP  
- **Database**: MySQL  
- **Server**: XAMPP  

---

## Setup Instructions

1. **Clone the repository**
   \`\`\`bash
   git clone https://github.com/Bhargav13304/IIM-buy-sell.git
   cd IIM-buy-sell
   \`\`\`

2. **Setup Database**
   - Start **Apache** and **MySQL** via XAMPP Control Panel.  
   - Open [phpMyAdmin](http://localhost/phpmyadmin/).  
   - Create a new database named \`iim_buy_sell\`.  
   - Import the SQL file from \`uploads/db.sql\`.  

3. **Configure Database Connection**
   - Edit \`uploads/db.php\` to match your local database credentials.  
   Example:
   \`\`\`php
   <?php
   $host = "localhost";
   $user = "root";
   $password = "";
   $database = "iim_buy_sell";
   $conn = new mysqli($host, $user, $password, $database);
   if ($conn->connect_error) {
       die("Connection failed: " . $conn->connect_error);
   }
   ?>
   \`\`\`

4. **Run the Application**
   - Move the project folder to \`htdocs\` in XAMPP.  
   - Open in browser: [http://localhost/IIM-buy-sell](http://localhost/IIM-buy-sell).  

---

## Folder Structure
\`\`\`
IIM-buy-sell/
│── uploads/          # Database connection & SQL files
│── assets/           # CSS, JS, images
│── index.php         # Homepage
│── login.php         # User login
│── register.php      # User registration
│── product.php       # Product listing
│── chat.php          # Buyer-seller chat
│── admin_interface.php # Admin panel
│── README.md
\`\`\`

---

## Future Enhancements
- Payment gateway integration.  
- Notification system for messages/offers.  
- Mobile-friendly responsive UI.  
- Advanced search filters.  

---

## License
This project is licensed under the MIT License.  
`;

fs.writeFileSync("README.md", content.trim());
console.log("✅ README.md has been generated successfully!");
