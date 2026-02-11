// DEOS FOOD HUB - MAIN JAVASCRIPT

// CART FUNCTIONS
function getCart() {
    const cart = localStorage.getItem('cart');
    return cart ? JSON.parse(cart) : [];
}

function saveCart(cart) {
    localStorage.setItem('cart', JSON.stringify(cart));
    updateCartCount();
    updateNavCartCount();
}

function addToCart(productId, productName, price) {
    let cart = getCart();
    
    const existingItem = cart.find(item => item.id === productId);
    
    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push({
            id: productId,
            name: productName,
            price: parseFloat(price),
            quantity: 1
        });
    }
    
    saveCart(cart);
    showToast(`${productName} added to cart!`, 'success');
}

function removeFromCart(productId) {
    let cart = getCart();
    cart = cart.filter(item => item.id !== productId);
    saveCart(cart);
}

function updateCartQuantity(productId, quantity) {
    let cart = getCart();
    const item = cart.find(item => item.id === productId);
    
    if (item) {
        if (quantity <= 0) {
            cart = cart.filter(item => item.id !== productId);
        } else {
            item.quantity = quantity;
        }
    }
    
    saveCart(cart);
}

function updateCartCount() {
    const cart = getCart();
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    const cartCountElement = document.getElementById('cart-count');
    if (cartCountElement) {
        cartCountElement.textContent = totalItems;
    }
}

function updateNavCartCount() {
    const cart = getCart();
    const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
    const navCartCountElement = document.getElementById('nav-cart-count');
    if (navCartCountElement) {
        navCartCountElement.textContent = totalItems;
        navCartCountElement.style.display = totalItems > 0 ? 'inline-block' : 'none';
    }
}

function clearCart() {
    localStorage.removeItem('cart');
    updateCartCount();
    updateNavCartCount();
}

// TOAST NOTIFICATIONS
function showToast(message, type = 'success') {
    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.textContent = message;
    toast.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        background: ${type === 'success' ? '#4CAF50' : '#f44336'};
        color: white;
        border-radius: 5px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        z-index: 9999;
        animation: slideIn 0.3s ease;
    `;
    
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => toast.remove(), 300);
    }, 3000);
}

// SEARCH FUNCTIONALITY
function searchProducts() {
    const searchTerm = document.getElementById('search-input').value.toLowerCase();
    const products = document.querySelectorAll('.product-card');
    
    products.forEach(product => {
        const name = product.querySelector('.product-name').textContent.toLowerCase();
        const desc = product.querySelector('.product-description').textContent.toLowerCase();
        
        if (name.includes(searchTerm) || desc.includes(searchTerm)) {
            product.style.display = 'block';
        } else {
            product.style.display = 'none';
        }
    });
}

// FILTER PRODUCTS
function filterProducts(categoryId) {
    const products = document.querySelectorAll('.product-card');
    
    products.forEach(product => {
        if (categoryId === 'all' || product.dataset.category == categoryId) {
            product.style.display = 'block';
        } else {
            product.style.display = 'none';
        }
    });
    
    // Update active button
    document.querySelectorAll('.category-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.classList.add('active');
}

// INITIALIZATION
document.addEventListener('DOMContentLoaded', function() {
    updateCartCount();
    updateNavCartCount();
    
    // Add to cart buttons
    document.querySelectorAll('.btn-cart').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.dataset.productId;
            const productName = this.dataset.productName;
            const productPrice = this.dataset.productPrice;
            
            if (productId && productName && productPrice) {
                addToCart(productId, productName, productPrice);
            }
        });
    });
    
    // Auto-hide messages
    setTimeout(() => {
        document.querySelectorAll('.message').forEach(msg => {
            msg.style.opacity = '0';
            setTimeout(() => msg.remove(), 300);
        });
    }, 5000);
});

// Add CSS animations
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }
    
    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }
`;
document.head.appendChild(style);