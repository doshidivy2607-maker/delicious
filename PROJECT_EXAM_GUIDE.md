# Delicious Dispatchers - Complete Project Study Guide

## Table of Contents
1. [Project Overview](#project-overview)
2. [System Architecture](#system-architecture)
3. [Authentication System](#authentication-system)
4. [Menu Management System](#menu-management-system)
5. [Order Processing Flow](#order-processing-flow)
6. [Payment Integration](#payment-integration)
7. [Order Tracking System](#order-tracking-system)
8. [Admin Dashboard](#admin-dashboard)
9. [Database Structure](#database-structure)
10. [Frontend Components](#frontend-components)
11. [JavaScript Functionality](#javascript-functionality)
12. [Security Measures](#security-measures)
13. [Key Files and Their Purpose](#key-files-and-their-purpose)

---

## Project Overview

### What is Delicious Dispatchers?
Delicious Dispatchers is a complete food ordering and delivery management system that allows customers to:
- Browse food menus with filtering options
- Add items to cart and place orders
- Pay online (Razorpay) or cash on delivery
- Track order status in real-time
- View order history

### Key Features
- **User Authentication**: Registration, login, session management
- **Dynamic Menu System**: Category-based food browsing with search/filter
- **Shopping Cart**: Add/remove items, quantity management, local storage
- **Payment Processing**: Razorpay integration + Cash on Delivery
- **Order Management**: Real-time status tracking, order history
- **Admin Panel**: Order management, user management, analytics
- **Export Functionality**: Excel export for orders data

---

## System Architecture

### Technology Stack
- **Backend**: PHP 7.4+ with PDO for database operations
- **Database**: MySQL/MariaDB
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Payment Gateway**: Razorpay
- **UI Framework**: Custom CSS with Font Awesome icons
- **Session Management**: PHP native sessions

### Architecture Pattern
- **MVC-like Structure**: Separation of concerns
- **Modular Design**: Reusable components and includes
- **RESTful Approach**: Clean URL structure and HTTP methods
- **Database Abstraction**: PDO for secure database operations

### File Structure
```
delicious/
├── index.php                 # Landing page
├── login.php                 # User login
├── register.php              # User registration
├── user-dashboard.php        # Customer dashboard
├── user-profile.php          # User profile management
├── order-food.php            # Main ordering page
├── checkout.php              # Checkout and payment
├── order-history.php         # Order history
├── savedorder.php            # Admin order management
├── editmenu.php              # Menu management (admin)
├── users.php                 # User management (admin)
├── includes/
│   ├── db_config.php         # Database configuration
│   ├── menu-config.php       # Menu-related functions
│   ├── header.php            # Header component
│   └── footer.php            # Footer component
├── css/
│   ├── style.css             # Main stylesheet
│   └── order.css             # Order-specific styles
├── js/
│   ├── script.js            # General site scripts
│   └── order.js             # Shopping cart functionality
└── env.js                   # Environment variables
```

---

## Authentication System

### User Registration Process
**File**: `register.php`

1. **Form Validation**: 
   - Name, email, phone, password validation
   - Email uniqueness check in database
   - Password strength requirements

2. **Data Processing**:
   ```php
   // Password hashing
   $hashed_password = password_hash($password, PASSWORD_DEFAULT);
   
   // Insert into database
   $stmt = $pdo->prepare("INSERT INTO users (fullname, email, password, phone) VALUES (?, ?, ?, ?)");
   $stmt->execute([$name, $email, $hashed_password, $phone]);
   ```

3. **Session Creation**:
   ```php
   $_SESSION['user_id'] = $user['id'];
   $_SESSION['user_name'] = $user['fullname'];
   $_SESSION['user_email'] = $user['email'];
   ```

### Login Process
**File**: `login.php`

1. **Credential Verification**:
   ```php
   $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
   $stmt->execute([$email]);
   $user = $stmt->fetch();
   
   if ($user && password_verify($password, $user['password'])) {
       // Login successful
   }
   ```

2. **Session Management**:
   - Creates secure session with user data
   - Sets session timeout and security flags
   - Redirects to appropriate dashboard

3. **Admin Access Control**:
   ```php
   if ($user_email === 'doshidivy2607@gmail.com') {
       header('Location: savedorder.php'); // Admin dashboard
   } else {
       header('Location: user-dashboard.php'); // User dashboard
   }
   ```

### Security Features
- **Password Hashing**: Uses `password_hash()` with BCRYPT
- **Session Security**: HTTP-only cookies, secure flags
- **Input Sanitization**: `htmlspecialchars()` for XSS prevention
- **SQL Injection Prevention**: PDO prepared statements

---

## Menu Management System

### Menu Data Structure
**Database Table**: `menu`
```sql
CREATE TABLE menu (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    category ENUM('veg', 'nonveg', 'diet', 'subscription'),
    price DECIMAL(10,2) NOT NULL,
    description TEXT,
    image VARCHAR(255),
    rating DECIMAL(3,2),
    reviews INT DEFAULT 0,
    calories INT,
    is_bestseller BOOLEAN DEFAULT FALSE,
    is_new BOOLEAN DEFAULT FALSE,
    spice_level INT DEFAULT 1
);
```

### Dynamic Menu Loading
**File**: `order-food.php`

1. **Database Query**:
   ```php
   $stmt = $pdo->prepare("SELECT * FROM menu ORDER BY category, name");
   $stmt->execute();
   $menu_items_db = $stmt->fetchAll(PDO::FETCH_ASSOC);
   ```

2. **Data Transformation**:
   ```php
   foreach ($menu_items_db as $item) {
       $menu_items[] = [
           'id' => $item['id'],
           'name' => $item['name'],
           'category' => $item['category'],
           'price' => (float)$item['price'],
           // ... other fields
       ];
   }
   ```

### Filtering System
**File**: `includes/menu-config.php`

1. **Dynamic Count Calculation**:
   ```php
   public static function getFilterCounts() {
       global $pdo;
       $stmt = $pdo->prepare("SELECT category, COUNT(*) as count FROM menu GROUP BY category");
       $stmt->execute();
       $counts = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
       
       return [
           'all' => array_sum($counts),
           'veg' => $counts['veg'] ?? 0,
           'nonveg' => $counts['nonveg'] ?? 0,
           // ... other categories
       ];
   }
   ```

2. **Client-side Filtering**:
   - JavaScript handles category filtering
   - Search functionality with real-time results
   - Price range and spice level filters

---

## Order Processing Flow

### Shopping Cart Management
**File**: `js/order.js`

1. **Cart Structure**:
   ```javascript
   let cart = JSON.parse(localStorage.getItem('cart')) || [];
   ```

2. **Add to Cart Function**:
   ```javascript
   function addToCart(itemId, quantity = 1) {
       const item = menuItemsData.find(item => item.id === itemId);
       const existingItem = cart.find(item => item.id === itemId);
       
       if (existingItem) {
           existingItem.quantity += quantity;
       } else {
           cart.push({...item, quantity});
       }
       
       localStorage.setItem('cart', JSON.stringify(cart));
       updateCartUI();
   }
   ```

3. **Cart Persistence**:
   - Uses localStorage for cart persistence
   - Syncs with server session during checkout
   - Handles cart clearing after successful order

### Checkout Process
**File**: `checkout.php`

1. **Price Calculation**:
   ```php
   $subtotal = round($total_amount, 2);
   $delivery_fee = $subtotal > 300 ? 0 : 40;
   $tax = round($subtotal * 0.05, 2);
   $grand_total = round($subtotal + $delivery_fee + $tax, 2);
   ```

2. **Order Data Collection**:
   - Customer information (name, email, phone)
   - Delivery address and time preferences
   - Payment method selection
   - Order notes

3. **Order Creation**:
   ```php
   $stmt = $pdo->prepare("INSERT INTO orders (order_id, user_id, items, subtotal, delivery_fee, tax, total_amount, payment_method, payment_id, status, delivery_address, delivery_time, order_notes, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())");
   ```

---

## Payment Integration

### Razorpay Integration
**Files**: `checkout.php`, `env.js`

1. **Configuration Setup**:
   ```javascript
   const razorpayConfig = {
       key: ENV.RAZORPAY.KEY_ID,
       amount: <?php echo round($grand_total * 100); ?>, // Amount in paisa
       currency: 'INR',
       name: 'Delicious Dispatchers',
       description: 'Food Order Payment',
       handler: function (response) {
           // Handle successful payment
       }
   };
   ```

2. **Payment Flow**:
   - User selects Razorpay payment
   - Payment modal opens with order details
   - User completes payment via UPI/cards/wallets
   - Razorpay returns payment response
   - System verifies and processes order

3. **Payment Verification**:
   ```javascript
   handler: function (response) {
       const form = document.getElementById('checkoutForm');
       const paymentIdInput = document.createElement('input');
       paymentIdInput.name = 'razorpay_payment_id';
       paymentIdInput.value = response.razorpay_payment_id;
       form.appendChild(paymentIdInput);
       form.submit();
   }
   ```

### Cash on Delivery (COD)
1. **Direct Order Placement**:
   - Bypasses payment gateway
   - Order status set to 'pending'
   - Payment method marked as 'cod'

2. **Order Status Flow**:
   - COD orders start as 'pending'
   - Admin can mark as 'paid' when delivered
   - Final status becomes 'done'

### Payment Security
- **Amount Validation**: Server-side amount verification
- **Precision Handling**: Proper rounding to prevent mismatches
- **Webhook Security**: (Implementation ready for Razorpay webhooks)

---

## Order Tracking System

### Order Status Flow
1. **Order Placed**: Initial status after successful payment
2. **Confirmed**: Order confirmed by restaurant
3. **Preparing**: Food being prepared
4. **Ready**: Food ready for pickup/delivery
5. **Completed**: Order delivered successfully
6. **Cancelled**: Order cancelled (if applicable)

### Real-time Updates
**File**: `user-dashboard.php`

1. **Status Display**:
   ```php
   $status_badge = '<span class="status-badge ' . strtolower($order['status']) . '">' . ucfirst($order['status']) . '</span>';
   ```

2. **Order History**:
   ```php
   $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
   $stmt->execute([$user_id]);
   $recent_orders = $stmt->fetchAll();
   ```

3. **Order Details**:
   - Order ID, items, total amount
   - Delivery address and time
   - Payment method and status
   - Estimated delivery time

---

## Admin Dashboard

### Admin Authentication
**File**: `savedorder.php`

1. **Admin Check**:
   ```php
   if ($user_email !== 'doshidivy2607@gmail.com') {
       header('Location: user-dashboard.php');
       exit();
   }
   ```

### Order Management
1. **Order Display**:
   - All orders with customer details
   - Status badges for quick identification
   - Action buttons for status updates

2. **Status Updates**:
   ```php
   if (isset($_POST['mark_paid'])) {
       $stmt = $pdo->prepare("UPDATE orders SET status = 'completed' WHERE id = ? AND payment_method = 'cod' AND status = 'pending'");
   }
   
   if (isset($_POST['mark_delivered'])) {
       $stmt = $pdo->prepare("UPDATE orders SET status = 'done' WHERE id = ? AND status IN ('completed', 'confirmed', 'pending')");
   }
   ```

### Analytics Dashboard
1. **Key Metrics**:
   - Total orders count
   - Total revenue calculation
   - Pending orders tracking
   - Revenue order analysis

2. **Revenue Calculation**:
   ```php
   $stmt = $pdo->prepare("SELECT SUM(total_amount) as revenue FROM orders WHERE status = 'done' or (status = 'confirmed' and payment_method = 'razorpay')");
   ```

### Excel Export Feature
1. **Data Extraction**:
   - JavaScript reads table data from DOM
   - Cleans up status badges and formatting
   - Creates structured data array

2. **Excel Generation**:
   ```javascript
   const ws = XLSX.utils.aoa_to_sheet(data);
   const wb = XLSX.utils.book_new();
   XLSX.utils.book_append_sheet(wb, ws, "Orders");
   XLSX.writeFile(wb, filename);
   ```

---

## Database Structure

### Core Tables

#### Users Table
```sql
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    fullname VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

#### Menu Table
```sql
CREATE TABLE menu (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    category ENUM('veg', 'nonveg', 'diet', 'subscription'),
    price DECIMAL(10,2) NOT NULL,
    original_price DECIMAL(10,2),
    description TEXT,
    image VARCHAR(255),
    rating DECIMAL(3,2) DEFAULT 0,
    reviews INT DEFAULT 0,
    calories INT,
    is_bestseller BOOLEAN DEFAULT FALSE,
    is_new BOOLEAN DEFAULT FALSE,
    spice_level INT DEFAULT 1
);
```

#### Orders Table
```sql
CREATE TABLE orders (
    id INT PRIMARY KEY AUTO_INCREMENT,
    order_id VARCHAR(20) UNIQUE NOT NULL,
    user_id INT,
    items JSON,
    subtotal DECIMAL(10,2),
    delivery_fee DECIMAL(10,2),
    tax DECIMAL(10,2),
    total_amount DECIMAL(10,2),
    payment_method ENUM('razorpay', 'cod'),
    payment_id VARCHAR(255),
    status ENUM('pending', 'confirmed', 'preparing', 'ready', 'completed', 'failed', 'done'),
    delivery_address TEXT,
    delivery_time VARCHAR(50),
    order_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
```

### Data Relationships
- **Users → Orders**: One-to-many relationship
- **Orders → Menu Items**: JSON storage for flexible item storage
- **Menu Categories**: Enum for fixed category types

---

## Frontend Components

### Responsive Design
1. **CSS Framework**: Custom CSS with mobile-first approach
2. **Grid System**: CSS Grid and Flexbox for layouts
3. **Breakpoints**: Mobile (320px+), Tablet (768px+), Desktop (1200px+)

### Key UI Components
1. **Header Component** (`includes/header.php`):
   - Navigation menu
   - User authentication status
   - Cart item count

2. **Order Cards**:
   ```html
   <div class="menu-card glass-effect">
       <div class="card-image">
           <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>">
       </div>
       <div class="card-content">
           <h3><?php echo $item['name']; ?></h3>
           <p><?php echo $item['description']; ?></p>
           <div class="card-footer">
               <span class="price">₹<?php echo $item['price']; ?></span>
               <button class="add-to-cart">Add to Cart</button>
           </div>
       </div>
   </div>
   ```

3. **Glass Effect Styling**:
   ```css
   .glass-effect {
       background: rgba(255, 255, 255, 0.1);
       backdrop-filter: blur(10px);
       border: 1px solid rgba(255, 255, 255, 0.2);
   }
   ```

### Animation System
1. **CSS Animations**:
   - Fade-in effects for cards
   - Slide animations for modals
   - Hover effects for interactive elements

2. **JavaScript Animations**:
   - Cart item addition animations
   - Loading spinners
   - Success/error notifications

---

## JavaScript Functionality

### Shopping Cart System
**File**: `js/order.js`

1. **Core Functions**:
   ```javascript
   function addToCart(itemId, quantity = 1)
   function removeFromCart(itemId)
   function updateQuantity(itemId, quantity)
   function clearCart()
   function getCartTotal()
   ```

2. **UI Updates**:
   ```javascript
   function updateCartUI() {
       updateCartCount();
       updateCartTotal();
       renderCartItems();
   }
   ```

3. **Local Storage Management**:
   ```javascript
   function saveCart() {
       localStorage.setItem('cart', JSON.stringify(cart));
   }
   
   function loadCart() {
       cart = JSON.parse(localStorage.getItem('cart')) || [];
   }
   ```

### Search and Filter System
1. **Real-time Search**:
   ```javascript
   function initSearch() {
       searchInput.addEventListener('input', function() {
           const searchTerm = this.value.toLowerCase();
           filterCards(searchTerm);
           updateResultsCount();
       });
   }
   ```

2. **Category Filtering**:
   ```javascript
   function applyFilters() {
       const activeCategory = document.querySelector('.filter-btn.active').dataset.category;
       const activePriceRange = document.querySelector('.price-btn.active').dataset.range;
       
       let visibleCards = menuCards;
       
       // Apply category filter
       if (activeCategory !== 'all') {
           visibleCards = visibleCards.filter(card => 
               card.dataset.category === activeCategory
           );
       }
       
       // Apply price filter
       if (activePriceRange !== 'all') {
           const [min, max] = activePriceRange.split('-').map(Number);
           visibleCards = visibleCards.filter(card => {
               const price = parseFloat(card.dataset.price);
               return price >= min && price <= max;
           });
       }
       
       // Update UI
       visibleCards.forEach(card => card.style.display = 'block');
       hiddenCards.forEach(card => card.style.display = 'none');
   }
   ```

### Payment Integration
1. **Razorpay Integration**:
   ```javascript
   function initiatePayment() {
       const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
       
       if (paymentMethod === 'cod') {
           // Handle COD
           submitCODOrder();
       } else {
           // Handle Razorpay
           const rzp = new Razorpay(razorpayConfig);
           rzp.open();
       }
   }
   ```

2. **Payment Success Handler**:
   ```javascript
   handler: function (response) {
       // Clear cart
       localStorage.removeItem('cart');
       
       // Submit form with payment details
       const form = document.getElementById('checkoutForm');
       form.appendChild(createHiddenInput('razorpay_payment_id', response.razorpay_payment_id));
       form.appendChild(createHiddenInput('razorpay_order_id', response.razorpay_order_id));
       form.appendChild(createHiddenInput('razorpay_signature', response.razorpay_signature));
       form.submit();
   }
   ```

---

## Security Measures

### Authentication Security
1. **Password Security**:
   - BCRYPT hashing with `password_hash()`
   - Secure password verification with `password_verify()`
   - Minimum password length requirements

2. **Session Security**:
   ```php
   ini_set('session.cookie_httponly', 1);
   ini_set('session.cookie_secure', 1);
   ini_set('session.use_strict_mode', 1);
   ```

### Input Validation
1. **XSS Prevention**:
   ```php
   $name = htmlspecialchars(trim($_POST['name']));
   $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
   ```

2. **SQL Injection Prevention**:
   ```php
   $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
   $stmt->execute([$email]);
   ```

3. **CSRF Protection**:
   - Session tokens for form submissions
   - Referer header validation

### Data Protection
1. **Sensitive Data Handling**:
   - Environment variables for API keys
   - No hardcoded credentials
   - Secure file permissions

2. **Error Handling**:
   ```php
   try {
       $stmt = $pdo->prepare($query);
       $stmt->execute($params);
   } catch (PDOException $e) {
       error_log("Database error: " . $e->getMessage());
       // User-friendly error message
   }
   ```

---

## Key Files and Their Purpose

### Core PHP Files

#### `index.php`
- **Purpose**: Landing page and marketing content
- **Features**: Hero section, featured items, call-to-action

#### `login.php`
- **Purpose**: User authentication
- **Features**: Login form, password recovery, session management

#### `register.php`
- **Purpose**: New user registration
- **Features**: Account creation, validation, email verification

#### `user-dashboard.php`
- **Purpose**: Customer dashboard
- **Features**: Order overview, profile summary, quick actions

#### `order-food.php`
- **Purpose**: Main ordering interface
- **Features**: Menu display, cart management, search/filter

#### `checkout.php`
- **Purpose**: Order completion and payment
- **Features**: Address form, payment processing, order confirmation

#### `order-history.php`
- **Purpose**: Order history and tracking
- **Features**: Past orders, status tracking, order details

#### `savedorder.php`
- **Purpose**: Admin order management
- **Features**: Order list, status updates, analytics, export

### Configuration Files

#### `includes/db_config.php`
- **Purpose**: Database connection and configuration
- **Features**: PDO setup, error handling, connection management

#### `includes/menu-config.php`
- **Purpose**: Menu-related functions and configurations
- **Features**: Category counts, filtering logic, menu helpers

#### `env.js`
- **Purpose**: Environment variables and API keys
- **Features**: Razorpay configuration, API endpoints

### Frontend Assets

#### `css/style.css`
- **Purpose**: Main stylesheet
- **Features**: Global styles, components, responsive design

#### `css/order.css`
- **Purpose**: Order-specific styles
- **Features**: Menu cards, cart styling, checkout forms

#### `js/script.js`
- **Purpose**: General site JavaScript
- **Features**: Header effects, animations, form validation

#### `js/order.js`
- **Purpose**: Shopping cart functionality
- **Features**: Cart management, filtering, payment integration

---

## Exam Preparation Tips

### Key Concepts to Remember
1. **Session Management**: How user authentication works
2. **Database Operations**: PDO prepared statements and security
3. **Payment Flow**: Razorpay integration and COD handling
4. **Cart System**: Local storage and server synchronization
5. **Admin Features**: Order management and analytics
6. **Security Measures**: Input validation and XSS prevention

### Common Interview Questions
1. How does the authentication system work?
2. Explain the payment processing flow
3. How is shopping cart data managed?
4. What security measures are implemented?
5. How does the admin dashboard work?
6. Explain the database structure

### Practical Demonstrations
1. **User Registration Flow**: Show complete registration process
2. **Order Placement**: Demonstrate cart to payment flow
3. **Admin Operations**: Show order management features
4. **Export Functionality**: Demonstrate Excel export
5. **Search/Filter**: Show menu filtering capabilities

### Code Explanation Points
1. **Password Hashing**: `password_hash()` and `password_verify()`
2. **PDO Operations**: Prepared statements and parameter binding
3. **JavaScript Cart**: Local storage management
4. **Razorpay Integration**: Payment handler and verification
5. **Excel Export**: XLSX.js implementation

---

## Summary

Delicious Dispatchers is a comprehensive food ordering system that demonstrates:
- **Full-stack Development**: PHP backend with responsive frontend
- **Database Management**: MySQL with PDO for secure operations
- **Payment Integration**: Razorpay gateway with COD support
- **User Experience**: Intuitive interface with real-time updates
- **Admin Functionality**: Complete order management system
- **Security Best Practices**: Input validation and secure coding

The project showcases modern web development practices including responsive design, secure authentication, payment processing, and administrative features. It's suitable for demonstrating proficiency in PHP, JavaScript, database management, and e-commerce functionality.

Good luck with your exam! 🍀
