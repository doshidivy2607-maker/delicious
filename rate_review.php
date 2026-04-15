<?php
include 'includes/db_config.php';

$success = '';
$error = '';
$selected_menu_id = isset($_GET['item']) ? (int)$_GET['item'] : null;
$user_name = '';

if (isset($_SESSION['user_id'])) {
    $userStmt = $pdo->prepare("SELECT fullname FROM users WHERE id = ?");
    $userStmt->execute([$_SESSION['user_id']]);
    $user = $userStmt->fetch(PDO::FETCH_ASSOC);
    $user_name = $user ? $user['fullname'] : '';
}

$menuStmt = $pdo->prepare("SELECT * FROM menu ORDER BY category, name");
$menuStmt->execute();
$menu_items = $menuStmt->fetchAll(PDO::FETCH_ASSOC);

if (!$selected_menu_id && !empty($menu_items)) {
    $selected_menu_id = (int)$menu_items[0]['id'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_menu_id = isset($_POST['menu_id']) ? (int)$_POST['menu_id'] : $selected_menu_id;
    $rating = isset($_POST['rating']) ? (float)$_POST['rating'] : 0;
    $review_text = trim($_POST['review_text'] ?? '');
    $reviewer_name = $user_name ?: trim($_POST['reviewer_name'] ?? 'Guest');

    if ($selected_menu_id <= 0) {
        $error = 'Please choose a valid menu item to review.';
    } elseif ($rating < 1 || $rating > 5) {
        $error = 'Rating must be between 1 and 5.';
    } elseif ($review_text === '') {
        $error = 'Please write a review before submitting.';
    }

    if (!$error) {
        $insertReview = $pdo->prepare("INSERT INTO reviews (menu_id, user_id, reviewer_name, rating, review_text) VALUES (?, ?, ?, ?, ?)");
        $insertReview->execute([
            $selected_menu_id,
            isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null,
            $reviewer_name,
            $rating,
            $review_text
        ]);

        $ratingSummary = $pdo->prepare("SELECT AVG(rating) AS avg_rating, COUNT(*) AS review_count FROM reviews WHERE menu_id = ?");
        $ratingSummary->execute([$selected_menu_id]);
        $summary = $ratingSummary->fetch(PDO::FETCH_ASSOC);

        if ($summary) {
            $avg_rating = round((float)$summary['avg_rating'], 1);
            $review_count = (int)$summary['review_count'];
            $updateMenu = $pdo->prepare("UPDATE menu SET rating = ?, reviews = ? WHERE id = ?");
            $updateMenu->execute([$avg_rating, $review_count, $selected_menu_id]);
        }

        $success = 'Thank you! Your rating and review have been submitted successfully.';

        // Refresh menu data with updated rating values
        $menuStmt->execute();
        $menu_items = $menuStmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

$selected_item = null;
foreach ($menu_items as $item) {
    if ((int)$item['id'] === (int)$selected_menu_id) {
        $selected_item = $item;
        break;
    }
}

$reviews = [];
if ($selected_item) {
    $reviewsStmt = $pdo->prepare("SELECT r.*, m.name AS menu_name FROM reviews r JOIN menu m ON r.menu_id = m.id WHERE r.menu_id = ? ORDER BY r.created_at DESC LIMIT 10");
    $reviewsStmt->execute([$selected_menu_id]);
    $reviews = $reviewsStmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<?php include 'includes/header.php'; ?>

<section class="rate-review-page">
    <div class="container">
        <div class="section-header animate-fadeIn">
            <span class="section-badge">Rate & Review</span>
            <h2>Share Your Food Experience</h2>
            <p>Help us improve by rating your favorite tiffin dishes and leaving honest reviews.</p>
        </div>

        <div class="cta-review">
            <p>Choose any item below to submit a star rating and review. Your feedback helps us keep our food quality high and our service responsive.</p>
            <a href="#review-form" class="btn btn-primary">Write a Review</a>
        </div>

        <div class="review-grid">
            <div class="review-sidebar">
                <div class="review-summary glass-effect">
                    <h3>Pick a Dish</h3>
                    <p>Select one of our menu items to rate and review.</p>
                    <form method="get" action="rate_review.php">
                        <label for="item-select">Food Item</label>
                        <select id="item-select" name="item" onchange="this.form.submit()">
                            <?php foreach ($menu_items as $item): ?>
                                <option value="<?php echo $item['id']; ?>" <?php echo $selected_item && $item['id'] == $selected_item['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($item['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>

                <?php if ($selected_item): ?>
                    <div class="review-card glass-effect">
                        <img src="<?php echo htmlspecialchars($selected_item['image'] ?: 'https://images.unsplash.com/photo-1543353071-873f17a7a088?w=800'); ?>" alt="<?php echo htmlspecialchars($selected_item['name']); ?>">
                        <div class="review-card-content">
                            <h3><?php echo htmlspecialchars($selected_item['name']); ?></h3>
                            <p><?php echo htmlspecialchars($selected_item['description']); ?></p>
                            <div class="review-meta">
                                <span class="rating-stars">
                                    <?php for ($i = 1; $i <= 5; $i++): ?>
                                        <i class="<?php echo $i <= round($selected_item['rating']) ? 'fas fa-star' : 'far fa-star'; ?>"></i>
                                    <?php endfor; ?>
                                </span>
                                <span><?php echo number_format($selected_item['rating'], 1); ?> / 5</span>
                            </div>
                            <span class="reviews-count"><?php echo (int)$selected_item['reviews']; ?> reviews</span>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <div class="review-main">
                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <div id="review-form" class="review-form glass-effect">
                    <h3>Submit Your Review</h3>
                    <form method="post" action="rate_review.php">
                        <input type="hidden" name="menu_id" value="<?php echo htmlspecialchars($selected_menu_id); ?>">

                        <?php if (!$user_name): ?>
                            <label for="reviewer_name">Your Name</label>
                            <input type="text" id="reviewer_name" name="reviewer_name" placeholder="Enter your name" required>
                        <?php endif; ?>

                        <label for="rating">Rating</label>
                        <select id="rating" name="rating" required>
                            <option value="">Choose rating</option>
                            <?php for ($i = 1; $i <= 5; $i++): ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?> star<?php echo $i > 1 ? 's' : ''; ?></option>
                            <?php endfor; ?>
                        </select>

                        <label for="review_text">Review</label>
                        <textarea id="review_text" name="review_text" placeholder="Tell us what you liked and how we can improve" required></textarea>

                        <button type="submit" class="btn btn-primary">Submit Review</button>
                    </form>
                </div>

                <div class="review-list glass-effect">
                    <h3>Recent Reviews</h3>
                    <?php if (empty($reviews)): ?>
                        <p class="no-reviews">No reviews yet for this item. Be the first to share your experience!</p>
                    <?php else: ?>
                        <?php foreach ($reviews as $review): ?>
                            <div class="review-item">
                                <div class="reviewer">
                                    <strong><?php echo htmlspecialchars($review['reviewer_name'] ?: 'Guest'); ?></strong>
                                    <span class="rating-stars">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="<?php echo $i <= round($review['rating']) ? 'fas fa-star' : 'far fa-star'; ?>"></i>
                                        <?php endfor; ?>
                                    </span>
                                </div>
                                <p class="review-text"><?php echo nl2br(htmlspecialchars($review['review_text'])); ?></p>
                                <span class="review-date"><?php echo date('M d, Y', strtotime($review['created_at'])); ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
