# Delicious Dispatchers - Quick Summary

## Project Overview
Complete food ordering and delivery management system with user authentication, menu browsing, cart functionality, payment processing, and admin dashboard.

## Core Features
- **User System**: Registration, login, session management
- **Menu System**: Dynamic food browsing with filtering/search
- **Shopping Cart**: Local storage-based cart management
- **Payment**: Razorpay integration + Cash on Delivery
- **Order Tracking**: Real-time status updates
- **Admin Panel**: Order management, analytics, Excel export

## Technology Stack
- **Backend**: PHP 7.4+ with PDO
- **Database**: MySQL/MariaDB
- **Frontend**: HTML5, CSS3, JavaScript (ES6+)
- **Payment**: Razorpay Gateway
- **UI**: Custom CSS with Font Awesome

## Key Files & Purpose

### User-Facing Files
- `index.php` - Landing page
- `login.php` - User authentication
- `register.php` - New user registration
- `user-dashboard.php` - Customer dashboard
- `order-food.php` - Main ordering interface
- `checkout.php` - Payment processing
- `order-history.php` - Order tracking

### Admin Files
- `savedorder.php` - Admin order management
- `editmenu.php` - Menu management
- `users.php` - User management

### Configuration
- `includes/db_config.php` - Database connection
- `includes/menu-config.php` - Menu functions
- `env.js` - Environment variables

### Assets
- `css/style.css` - Main styles
- `css/order.css` - Order-specific styles
- `js/script.js` - General JavaScript
- `js/order.js` - Cart functionality

## Database Structure
```sql
users (id, fullname, email, password, phone)
menu (id, name, category, price, description, image, rating)
orders (id, order_id, user_id, items, total_amount, status, payment_method)
```

## Key Workflows

### User Registration/Login
1. User fills form → Validation → Password hashing → Database storage
2. Login → Credential verification → Session creation → Dashboard redirect

### Order Process
1. Browse menu → Add to cart (localStorage) → Checkout
2. Address form → Payment (Razorpay/COD) → Order creation
3. Status tracking → Order completion

### Payment Flow
- **Razorpay**: Amount calculation → Payment modal → Verification → Order confirmation
- **COD**: Direct order placement → Status 'pending' → Admin marks as paid

### Admin Operations
1. View all orders with customer details
2. Update order status (mark paid/delivered)
3. View analytics (total orders, revenue, pending)
4. Export orders to Excel

## Security Features
- **Password Security**: BCRYPT hashing
- **SQL Injection**: PDO prepared statements
- **XSS Prevention**: htmlspecialchars() sanitization
- **Session Security**: HTTP-only cookies, secure flags

## JavaScript Functionality
- **Cart Management**: localStorage persistence, quantity updates
- **Search/Filter**: Real-time menu filtering by category/price
- **Payment Integration**: Razorpay modal handling
- **UI Updates**: Dynamic cart count, animations

## Key Code Examples

### Authentication
```php
// Login verification
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();

if ($user && password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    // Login successful
}
```

### Cart Management
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

### Payment Processing
```javascript
const razorpayConfig = {
    key: ENV.RAZORPAY.KEY_ID,
    amount: <?php echo round($grand_total * 100); ?>, // Amount in paisa
    handler: function (response) {
        // Handle successful payment
        form.submit();
    }
};
```

## Exam Key Points
1. **Authentication Flow**: Registration → Login → Session → Dashboard
2. **Order Flow**: Browse → Cart → Checkout → Payment → Tracking
3. **Payment System**: Razorpay integration + COD alternative
4. **Admin Features**: Order management, analytics, export
5. **Security**: Password hashing, PDO, XSS prevention
6. **Frontend**: Responsive design, localStorage, real-time updates

## Important Functions
- `password_hash()` / `password_verify()` - Security
- `PDO::prepare()` - Database security
- `localStorage.setItem()` - Cart persistence
- `round()` - Payment precision
- `htmlspecialchars()` - XSS prevention

## Status Flow
`pending → confirmed → preparing → ready → completed/done`

## Quick Demo Steps
1. Show user registration/login
2. Demonstrate menu browsing and filtering
3. Add items to cart and checkout
4. Show payment process (Razorpay/COD)
5. Display order tracking
6. Show admin dashboard and order management
7. Demonstrate Excel export functionality

---

**This summary covers all essential aspects of the Delicious Dispatchers project for quick exam review.**
