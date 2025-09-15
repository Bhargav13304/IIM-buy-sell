# IIM Buy & Sell

This project is a **web application** developed  as part of an internship project for IIM Trichy exclusively for the students.  
It is designed to provide a secure and convenient platform for students living in hostels to **buy, sell, or exchange products** within the campus community.  

Students can list items they wish to sell — such as books, electronics, bicycles, or other personal belongings — and other students can browse these listings, express interest, and initiate conversations with sellers.  
The platform includes a **chat feature** that enables direct communication between buyers and sellers, making it easier to negotiate prices, clarify details, and finalize transactions.  

Transactions can be conducted either:
- **Physically**: by meeting in person within the campus/hostel premises.  
- **Online**: through preferred digital payment methods between students.  

To ensure accessibility and ease of use, the system has been **integrated into the IIM Trichy student portal (live website)**. This means students do not need to create a separate account or log in multiple times — they can directly access the platform from their existing portal credentials.  

The goal of this project is to:
- Encourage a sustainable campus culture by **reusing and recycling goods**.  
- Reduce the effort and cost for students when buying or selling products.  
- Provide a **trusted, campus-only marketplace** where all members belong to the same institution.  

With its simple interface and hostel-focused design, the platform ensures that transactions remain **student-centric, fast, and secure**.


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

### 1. Clone the repository
```bash
git clone https://github.com/Bhargav13304/IIM-buy-sell.git
cd IIM-buy-sell
```

### 2. Setup Database
- Start **Apache** and **MySQL** via XAMPP Control Panel.  
- Open [phpMyAdmin](http://localhost/phpmyadmin/).  
- Create a new database named `iim_buy_sell`.  
- Import the SQL file from `uploads/db.sql`.  

### 3. Configure Database Connection
```php
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
```

### 4. Run the Application
- Move the project folder to `htdocs` in XAMPP.  
- Open in browser: [http://localhost/IIM-buy-sell](http://localhost/IIM-buy-sell).  

---

## Folder Structure
```
IIM-buy-sell/
│── uploads/            # Database connection & SQL files
│── assets/             # CSS, JS, images
│── index.php           # Homepage
│── login.php           # User login
│── register.php        # User registration
│── product.php         # Product listing
│── chat.php            # Buyer-seller chat
│── admin_interface.php # Admin panel
│── README.md
```

---

## Future Enhancements
- Payment gateway integration.  
- Notification system for messages/offers.  
- Mobile-friendly responsive UI.  
- Advanced search filters.  

---

## License
This project is licensed under the MIT License.  
