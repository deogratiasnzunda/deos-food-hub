<footer class="main-footer">
    <div class="container">
        <div class="footer-section">
            <h3>Deo's Food Hub</h3>
            <p>Delicious meals delivered to your doorstep. Fast, fresh, and affordable.</p>
            <div class="social-links">
                <a href="#">📘</a>
                <a href="#">📷</a>
                <a href="#">🐦</a>
                <a href="#">▶️</a>
            </div>
        </div>
        
        <div class="footer-section">
            <h3>Quick Links</h3>
            <a href="index.php">Home</a>
            <a href="menu.php">Menu</a>
            <a href="about.php">About Us</a>
            <a href="contact.php">Contact</a>
        </div>
        
        <div class="footer-section">
            <h3>Contact Info</h3>
            <p>📍 123 Food Street, Dar es Salaam</p>
            <p>📞 +255 712 345 678</p>
            <p>✉️ info@deofoodhub.com</p>
            <p>🕒 Mon-Sun: 8AM - 11PM</p>
        </div>
        
        <div class="footer-section">
            <h3>Newsletter</h3>
            <p>Subscribe for discounts & updates</p>
            <form class="newsletter-form">
                <input type="email" placeholder="Your email" required>
                <button type="submit">Subscribe</button>
            </form>
        </div>
    </div>
    
    <div class="footer-bottom">
        <p>&copy; <?php echo date('Y'); ?> Deo's Food Hub. All rights reserved. | Developed by Deo</p>
    </div>
</footer>

<!-- Cart Floating Button -->
<div class="cart-float" onclick="window.location.href='cart.php'">
    🛒 <span id="cart-count">0</span>
</div>

<script src="assets/js/main.js"></script>
<script>
    // Initialize cart count
    updateCartCount();
    updateNavCartCount();
    
    // Mobile menu toggle
    function toggleMobileMenu() {
        const nav = document.querySelector('.main-nav');
        nav.classList.toggle('active');
    }
</script>
</body>
</html>