// ===== CART FUNCTIONALITY =====
let cart = JSON.parse(localStorage.getItem('cart')) || [];

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    updateCartUI();
    initFilters();
    initSearch();
    initCartToggle();
    initImageErrorHandling();
});

// ===== IMAGE ERROR HANDLING =====
function initImageErrorHandling() {
    const images = document.querySelectorAll('.card-image img');
    images.forEach(img => {
        img.addEventListener('error', function() {
            // Hide broken image
            this.style.display = 'none';
            // Show fallback icon
            const fallback = document.createElement('div');
            fallback.className = 'image-fallback';
            fallback.innerHTML = '<i class="fas fa-image"></i><span>No Image</span>';
            this.parentElement.appendChild(fallback);
        });

        // Check if image is from Google (which often fails due to CORS)
        if (img.src.includes('google') || img.src.includes('lh3') || img.src.includes('ggpht')) {
            // Try to load the image with a timeout
            let imgLoaded = false;
            img.addEventListener('load', () => { imgLoaded = true; });

            setTimeout(() => {
                if (!imgLoaded) {
                    // Image didn't load within 3 seconds, show fallback
                    img.style.display = 'none';
                    const fallback = document.createElement('div');
                    fallback.className = 'image-fallback';
                    fallback.innerHTML = '<i class="fas fa-image"></i><span>Image Unavailable</span>';
                    img.parentElement.appendChild(fallback);
                }
            }, 3000);
        }
    });
}

// ===== ADD TO CART =====
function addToCart(id, name, price, image, button) {
    const qtyInput = button.parentElement.querySelector('.qty-input');
    const quantity = parseInt(qtyInput.value);
    
    const existingItem = cart.find(item => item.id === id);
    
    if (existingItem) {
        existingItem.quantity += quantity;
    } else {
        cart.push({
            id: id,
            name: name,
            price: price,
            image: image,
            quantity: quantity
        });
    }
    
    // Save to localStorage
    localStorage.setItem('cart', JSON.stringify(cart));
    
    // Update UI
    updateCartUI();
    
    // Button animation
    button.classList.add('added');
    button.innerHTML = '<i class="fas fa-check"></i> Added!';
    
    setTimeout(() => {
        button.classList.remove('added');
        button.innerHTML = '<i class="fas fa-cart-plus"></i> <span>Add</span>';
    }, 1500);
    
    // Show notification
    showNotification(`${name} added to cart!`, 'success');
    
    // Reset quantity
    qtyInput.value = 1;
}

// ===== UPDATE CART UI =====
function updateCartUI() {
    const cartItems = document.getElementById('cartItems');
    const cartCount = document.getElementById('cartCount');
    const floatingCartCount = document.getElementById('floatingCartCount');
    const floatingCartTotal = document.getElementById('floatingCartTotal');
    const cartSummary = document.getElementById('cartSummary');
    const emptyCart = document.getElementById('emptyCart');
    
    // Update cart count
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    if (cartCount) cartCount.textContent = totalItems;
    if (floatingCartCount) floatingCartCount.textContent = totalItems;
    
    // Calculate totals
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const deliveryFee = subtotal > 300 ? 0 : 40; // Free delivery above ₹300
    const tax = Math.round(subtotal * 0.05);
    const total = subtotal + deliveryFee + tax;
    
    if (floatingCartTotal) floatingCartTotal.textContent = `₹${total}`;
    
    // Update cart items
    if (cart.length === 0) {
        if (emptyCart) emptyCart.style.display = 'block';
        if (cartSummary) cartSummary.style.display = 'none';
        
        // Clear cart items except empty cart message
        const items = cartItems.querySelectorAll('.cart-item');
        items.forEach(item => item.remove());
    } else {
        if (emptyCart) emptyCart.style.display = 'none';
        if (cartSummary) cartSummary.style.display = 'block';
        
        // Update summary
        document.getElementById('subtotal').textContent = `₹${subtotal}`;
        document.getElementById('deliveryFee').textContent = `₹${deliveryFee}`;
        document.getElementById('tax').textContent = `₹${tax}`;
        document.getElementById('total').textContent = `₹${total}`;
        
        // Render cart items
        renderCartItems();
    }
}

// ===== RENDER CART ITEMS =====
function renderCartItems() {
    const cartItems = document.getElementById('cartItems');
    const emptyCart = document.getElementById('emptyCart');
    
    // Clear existing items (keep empty cart)
    const items = cartItems.querySelectorAll('.cart-item');
    items.forEach(item => item.remove());
    
    cart.forEach(item => {
        const cartItemHTML = `
            <div class="cart-item" data-id="${item.id}">
                <div class="cart-item-image">
                    <img src="${item.image}" alt="${item.name}">
                </div>
                <div class="cart-item-details">
                    <h4 class="cart-item-name">${item.name}</h4>
                    <p class="cart-item-price">₹${item.price * item.quantity}</p>
                    <div class="cart-item-qty">
                        <button class="qty-btn minus" onclick="updateCartItemQty(${item.id}, -1)">
                            <i class="fas fa-minus"></i>
                        </button>
                        <input type="number" value="${item.quantity}" min="1" max="10" class="qty-input" 
                               onchange="setCartItemQty(${item.id}, this.value)">
                        <button class="qty-btn plus" onclick="updateCartItemQty(${item.id}, 1)">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
                <button class="remove-item" onclick="removeFromCart(${item.id})">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        cartItems.insertAdjacentHTML('beforeend', cartItemHTML);
    });
}

// ===== UPDATE QUANTITY =====
function updateQty(button, change) {
    const input = button.parentElement.querySelector('.qty-input');
    let value = parseInt(input.value) + change;
    value = Math.max(1, Math.min(10, value));
    input.value = value;
}

function updateCartItemQty(id, change) {
    const item = cart.find(item => item.id === id);
    if (item) {
        item.quantity += change;
        if (item.quantity <= 0) {
            removeFromCart(id);
        } else {
            item.quantity = Math.min(10, item.quantity);
            localStorage.setItem('cart', JSON.stringify(cart));
            updateCartUI();
        }
    }
}

function setCartItemQty(id, value) {
    const item = cart.find(item => item.id === id);
    if (item) {
        item.quantity = Math.max(1, Math.min(10, parseInt(value)));
        localStorage.setItem('cart', JSON.stringify(cart));
        updateCartUI();
    }
}

// ===== REMOVE FROM CART =====
function removeFromCart(id) {
    const item = cart.find(item => item.id === id);
    cart = cart.filter(item => item.id !== id);
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartUI();
    showNotification(`Item removed from cart`, 'info');
}

// ===== CART TOGGLE =====
function initCartToggle() {
    const cartToggle = document.getElementById('cartToggle');
    const cartSidebar = document.getElementById('cartSidebar');
    const cartOverlay = document.getElementById('cartOverlay');
    const closeCart = document.getElementById('closeCart');
    const floatingCartBtn = document.getElementById('floatingCartBtn');
    
    function openCart() {
        cartSidebar.classList.add('active');
        cartOverlay.classList.add('active');
        document.body.style.overflow = 'hidden';
    }
    
    function closeCartSidebar() {
        cartSidebar.classList.remove('active');
        cartOverlay.classList.remove('active');
        document.body.style.overflow = '';
    }
    
    cartToggle?.addEventListener('click', openCart);
    floatingCartBtn?.addEventListener('click', openCart);
    closeCart?.addEventListener('click', closeCartSidebar);
    cartOverlay?.addEventListener('click', closeCartSidebar);
}

// ===== FILTERS =====
function initFilters() {
    const filterOptions = document.querySelectorAll('.filter-option input');
    const tabBtns = document.querySelectorAll('.tab-btn');
    const priceRange = document.getElementById('priceRange');
    const spiceOptions = document.querySelectorAll('.spice-option input');
    const specialFilters = document.querySelectorAll('.special-filters input');
    const clearFilters = document.getElementById('clearFilters');
    const sortSelect = document.getElementById('sortSelect');
    const viewBtns = document.querySelectorAll('.view-btn');
    
    // Category filters
    filterOptions.forEach(option => {
        option.addEventListener('change', function() {
            document.querySelectorAll('.filter-option').forEach(opt => opt.classList.remove('active'));
            this.closest('.filter-option').classList.add('active');
            applyFilters();
        });
    });
    
    // Tab buttons
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            tabBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            const category = this.dataset.category;
            const filterInput = document.querySelector(`.filter-option input[value="${category}"]`);
            if (filterInput) {
                filterInput.checked = true;
                document.querySelectorAll('.filter-option').forEach(opt => opt.classList.remove('active'));
                filterInput.closest('.filter-option').classList.add('active');
            }
            
            applyFilters();
        });
    });
    
    // Price range
    priceRange?.addEventListener('input', function() {
        document.getElementById('priceValue').textContent = `₹${this.value}`;
        applyFilters();
    });
    
    // Spice filters
    spiceOptions.forEach(option => {
        option.addEventListener('change', applyFilters);
    });
    
    // Special filters
    specialFilters.forEach(filter => {
        filter.addEventListener('change', applyFilters);
    });
    
    // Clear filters
    clearFilters?.addEventListener('click', function() {
        document.querySelector('.filter-option input[value="all"]').checked = true;
        document.querySelectorAll('.filter-option').forEach(opt => opt.classList.remove('active'));
        document.querySelector('.filter-option:first-child').classList.add('active');
        
        if (priceRange) priceRange.value = 500;
        document.getElementById('priceValue').textContent = '₹500';
        
        spiceOptions.forEach(opt => opt.checked = false);
        specialFilters.forEach(filter => filter.checked = false);
        
        tabBtns.forEach(b => b.classList.remove('active'));
        tabBtns[0].classList.add('active');
        
        applyFilters();
    });
    
    // Sort
    sortSelect?.addEventListener('change', applyFilters);
    
    // View toggle
    viewBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            viewBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            const menuGrid = document.getElementById('menuGrid');
            if (this.dataset.view === 'list') {
                menuGrid.classList.add('list-view');
            } else {
                menuGrid.classList.remove('list-view');
            }
        });
    });
}

// ===== APPLY FILTERS =====
function applyFilters() {
    const cards = document.querySelectorAll('.menu-card');
    const category = document.querySelector('.filter-option input:checked')?.value || 'all';
    const maxPrice = parseInt(document.getElementById('priceRange')?.value || 500);
    const spiceLevels = Array.from(document.querySelectorAll('.spice-option input:checked')).map(i => i.value);
    const bestseller = document.getElementById('bestsellerFilter')?.checked;
    const newItems = document.getElementById('newFilter')?.checked;
    const discount = document.getElementById('discountFilter')?.checked;
    const sortBy = document.getElementById('sortSelect')?.value || 'popular';
    
    let visibleCount = 0;
    const cardsArray = Array.from(cards);
    
    // Filter
    cardsArray.forEach(card => {
        const cardCategory = card.dataset.category;
        const cardPrice = parseInt(card.dataset.price);
        const cardSpice = card.dataset.spice;
        const cardBestseller = card.dataset.bestseller === '1';
        const cardNew = card.dataset.new === '1';
        const originalPrice = card.querySelector('.original-price');
        const hasDiscount = originalPrice !== null;
        
        let show = true;
        
        // Category filter
        if (category !== 'all' && cardCategory !== category) show = false;
        
        // Price filter
        if (cardPrice > maxPrice) show = false;
        
        // Spice filter
        if (spiceLevels.length > 0 && !spiceLevels.includes(cardSpice)) show = false;
        
        // Special filters
        if (bestseller && !cardBestseller) show = false;
        if (newItems && !cardNew) show = false;
        if (discount && !hasDiscount) show = false;
        
        card.style.display = show ? '' : 'none';
        if (show) visibleCount++;
    });
    
    // Sort
    const menuGrid = document.getElementById('menuGrid');
    const sortedCards = cardsArray.filter(card => card.style.display !== 'none');
    
    sortedCards.sort((a, b) => {
        switch(sortBy) {
            case 'price-low':
                return parseInt(a.dataset.price) - parseInt(b.dataset.price);
            case 'price-high':
                return parseInt(b.dataset.price) - parseInt(a.dataset.price);
            case 'rating':
                return parseFloat(b.dataset.rating) - parseFloat(a.dataset.rating);
            case 'newest':
                return (b.dataset.new === '1' ? 1 : 0) - (a.dataset.new === '1' ? 1 : 0);
            default:
                return (b.dataset.bestseller === '1' ? 1 : 0) - (a.dataset.bestseller === '1' ? 1 : 0);
        }
    });
    
    sortedCards.forEach(card => menuGrid.appendChild(card));
    
    // Update count
    document.getElementById('resultsCount').textContent = visibleCount;
}

// ===== SEARCH =====
function initSearch() {
    const searchInput = document.getElementById('searchInput');
    
    searchInput?.addEventListener('input', function() {
        const query = this.value.toLowerCase();
        const cards = document.querySelectorAll('.menu-card');
        
        cards.forEach(card => {
            const title = card.querySelector('.card-title').textContent.toLowerCase();
            const desc = card.querySelector('.card-description').textContent.toLowerCase();
            const category = card.dataset.category.toLowerCase();
            
            if (title.includes(query) || desc.includes(query) || category.includes(query)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
        
        // Update count
        const visibleCount = document.querySelectorAll('.menu-card:not([style*="display: none"])').length;
        document.getElementById('resultsCount').textContent = visibleCount;
    });
}

// ===== QUICK VIEW MODAL =====
function openQuickView(id) {
    const item = menuItemsData.find(i => i.id === id);
    if (!item) return;
    
    const modal = document.getElementById('quickViewModal');
    
    document.getElementById('modalImage').src = item.image;
    document.getElementById('modalImage').alt = item.name;
    document.getElementById('modalTitle').textContent = item.name;
    document.getElementById('modalDescription').textContent = item.description;
    
    // Category
    let categoryText = '';
    if (item.category === 'veg') categoryText = '<i class="fas fa-leaf"></i> Vegetarian';
    else if (item.category === 'nonveg') categoryText = '<i class="fas fa-drumstick-bite"></i> Non-Vegetarian';
    else if (item.category === 'diet') categoryText = '<i class="fas fa-heartbeat"></i> Diet & Healthy';
    else categoryText = '<i class="fas fa-calendar-check"></i> Subscription Plan';
    document.getElementById('modalCategory').innerHTML = categoryText;
    
    // Rating
    document.getElementById('modalRating').innerHTML = `
        <i class="fas fa-star"></i>
        <span>${item.rating}</span>
        <span style="color: rgba(255,255,255,0.5)">(${item.reviews} reviews)</span>
    `;
    
    // Meta
    let spiceIcons = '';
    for (let i = 0; i < item.spice_level; i++) {
        spiceIcons += '<i class="fas fa-pepper-hot" style="color: #dc3545"></i>';
    }
    document.getElementById('modalMeta').innerHTML = `
        <span><i class="fas fa-fire-alt"></i> ${item.calories} calories</span>
        <span>${spiceIcons || '<i class="fas fa-pepper-hot" style="color: #28a745"></i> Mild'}</span>
    `;
    
    // Price
    let priceHTML = `<span class="current-price">₹${item.price}</span>`;
    if (item.original_price > item.price) {
        priceHTML += ` <span class="original-price">₹${item.original_price}</span>`;
    }
    document.getElementById('modalPrice').innerHTML = priceHTML;
    
    // Reset quantity
    document.getElementById('modalQty').value = 1;
    
    // Add button
    document.getElementById('modalAddBtn').onclick = function() {
        const qty = parseInt(document.getElementById('modalQty').value);
        addToCartFromModal(item.id, item.name, item.price, item.image, qty);
        closeQuickView();
    };
    
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeQuickView() {
    document.getElementById('quickViewModal').classList.remove('active');
    document.body.style.overflow = '';
}

function updateModalQty(change) {
    const input = document.getElementById('modalQty');
    let value = parseInt(input.value) + change;
    value = Math.max(1, Math.min(10, value));
    input.value = value;
}

function addToCartFromModal(id, name, price, image, quantity) {
    const existingItem = cart.find(item => item.id === id);
    
    if (existingItem) {
        existingItem.quantity += quantity;
    } else {
        cart.push({ id, name, price, image, quantity });
    }
    
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartUI();
    showNotification(`${name} added to cart!`, 'success');
}

// ===== WISHLIST =====
document.querySelectorAll('.quick-btn.wishlist').forEach(btn => {
    btn.addEventListener('click', function(e) {
        e.stopPropagation();
        this.classList.toggle('active');
        
        if (this.classList.contains('active')) {
            this.innerHTML = '<i class="fas fa-heart"></i>';
            showNotification('Added to wishlist!', 'success');
        } else {
            this.innerHTML = '<i class="far fa-heart"></i>';
            showNotification('Removed from wishlist', 'info');
        }
    });
});

// ===== NOTIFICATIONS =====
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <i class="fas ${type === 'success' ? 'fa-check-circle' : type === 'error' ? 'fa-times-circle' : 'fa-info-circle'}"></i>
        <span>${message}</span>
    `;
    
    // Add styles if not exist
    if (!document.getElementById('notificationStyles')) {
        const style = document.createElement('style');
        style.id = 'notificationStyles';
        style.textContent = `
            .notification {
                position: fixed;
                top: 100px;
                right: 20px;
                padding: 15px 25px;
                background: rgba(26, 32, 44, 0.95);
                backdrop-filter: blur(10px);
                border-radius: 12px;
                display: flex;
                align-items: center;
                gap: 12px;
                z-index: 5000;
                animation: slideInNotif 0.3s ease, slideOutNotif 0.3s ease 2.7s forwards;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            }
            .notification.success { border-left: 4px solid #28a745; }
            .notification.error { border-left: 4px solid #dc3545; }
            .notification.info { border-left: 4px solid #17a2b8; }
            .notification.success i { color: #28a745; }
            .notification.error i { color: #dc3545; }
            .notification.info i { color: #17a2b8; }
            @keyframes slideInNotif {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOutNotif {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(100%); opacity: 0; }
            }
        `;
        document.head.appendChild(style);
    }
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}

// ===== CALCULATE TOTAL =====
function calculateTotal() {
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const deliveryFee = subtotal > 300 ? 0 : 40; // Free delivery above ₹300
    const tax = subtotal * 0.05; // 5% GST
    return subtotal + deliveryFee + tax;
}

// ===== PROCEED TO CHECKOUT =====
function proceedToCheckout() {
    if (cart.length === 0) {
        showNotification('Your cart is empty!', 'error');
        return;
    }

    // Store cart data and redirect to checkout
    showNotification('Preparing checkout...', 'info');

    fetch('store_cart_session.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            cart: cart,
            total: calculateTotal()
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = 'checkout.php';
        } else {
            showNotification('Error preparing checkout. Please try again.', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error preparing checkout. Please try again.', 'error');
    });
}

// ===== CLOSE MODAL ON ESCAPE =====
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeQuickView();
        document.getElementById('cartSidebar')?.classList.remove('active');
        document.getElementById('cartOverlay')?.classList.remove('active');
        document.body.style.overflow = '';
    }
});

// ===== LOAD MORE ITEMS =====
document.getElementById('loadMoreBtn')?.addEventListener('click', function() {
    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Loading...';
    
    setTimeout(() => {
        this.innerHTML = '<i class="fas fa-check"></i> All Items Loaded';
        this.disabled = true;
        showNotification('All items loaded!', 'info');
    }, 1500);
});

console.log('🛒 Order page scripts loaded successfully!');