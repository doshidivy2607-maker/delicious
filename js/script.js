// ===== DOM ELEMENTS =====
document.addEventListener('DOMContentLoaded', function() {
    
    // ===== HEADER SCROLL EFFECT =====
    const header = document.querySelector('.header');
    
    window.addEventListener('scroll', () => {
        if (window.scrollY > 100) {
            header?.classList.add('scrolled');
            header.style.background = 'rgba(26, 32, 44, 0.95)';
            header.style.boxShadow = '0 10px 30px rgba(0, 0, 0, 0.3)';
        } else {
            header?.classList.remove('scrolled');
            header.style.background = 'rgba(255, 255, 255, 0.05)';
            header.style.boxShadow = 'none';
        }
    });

    // ===== MOBILE MENU =====
    const hamburger = document.getElementById('hamburger');
    const navMenu = document.getElementById('navMenu');
    
    hamburger?.addEventListener('click', () => {
        hamburger.classList.toggle('active');
        navMenu?.classList.toggle('active');
    });

    // ===== SMOOTH SCROLLING =====
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // ===== COUNTER ANIMATION =====
    const counters = document.querySelectorAll('.stat-number');
    const speed = 200;
    
    const animateCounters = () => {
        counters.forEach(counter => {
            const target = +counter.getAttribute('data-count');
            const count = +counter.innerText;
            const increment = target / speed;
            
            if (count < target) {
                counter.innerText = Math.ceil(count + increment);
                setTimeout(animateCounters, 1);
            } else {
                counter.innerText = target;
            }
        });
    };

    // Intersection Observer for counter animation
    const observerOptions = {
        threshold: 0.5
    };

    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounters();
                counterObserver.unobserve(entry.target);
            }
        });
    }, observerOptions);

    const statsSection = document.querySelector('.hero-stats');
    if (statsSection) {
        counterObserver.observe(statsSection);
    }

    // ===== PRICING TOGGLE =====
    const pricingToggle = document.getElementById('pricingToggle');
    const monthlyPrices = document.querySelectorAll('.monthly-price');
    const yearlyPrices = document.querySelectorAll('.yearly-price');
    const toggleLabels = document.querySelectorAll('.toggle-label');

    pricingToggle?.addEventListener('change', function() {
        if (this.checked) {
            monthlyPrices.forEach(price => price.style.display = 'none');
            yearlyPrices.forEach(price => price.style.display = 'inline');
            toggleLabels[0].classList.remove('active');
            toggleLabels[1].classList.add('active');
        } else {
            monthlyPrices.forEach(price => price.style.display = 'inline');
            yearlyPrices.forEach(price => price.style.display = 'none');
            toggleLabels[0].classList.add('active');
            toggleLabels[1].classList.remove('active');
        }
    });

    // ===== SCROLL ANIMATIONS =====
    const animateElements = document.querySelectorAll('.animate-fadeInUp, .animate-fadeInLeft, .animate-fadeInRight');
    
    const animateObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0) translateX(0)';
            }
        });
    }, { threshold: 0.1 });

    animateElements.forEach(el => {
        el.style.opacity = '0';
        animateObserver.observe(el);
    });

    // ===== FORM VALIDATION =====
    const loginForm = document.getElementById('loginForm');
    const registerForm = document.getElementById('registerForm');
    const contactForm = document.getElementById('contactForm');

    // Email validation
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    // Password strength checker
    const passwordInput = document.getElementById('password');
    const strengthFill = document.querySelector('.strength-fill');
    const strengthText = document.querySelector('.strength-text');

    if (passwordInput && strengthFill) {
        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;
            
            if (password.length >= 6) strength++;
            if (password.length >= 8) strength++;
            if (/[A-Z]/.test(password)) strength++;
            if (/[0-9]/.test(password)) strength++;
            if (/[^A-Za-z0-9]/.test(password)) strength++;

            strengthFill.className = 'strength-fill';
            
            if (strength <= 2) {
                strengthFill.classList.add('weak');
                strengthText.textContent = 'Weak Password';
            } else if (strength <= 3) {
                strengthFill.classList.add('medium');
                strengthText.textContent = 'Medium Password';
            } else {
                strengthFill.classList.add('strong');
                strengthText.textContent = 'Strong Password';
            }
        });
    }

    // Contact form submission
    contactForm?.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Add loading state
        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
        submitBtn.disabled = true;

        // Simulate form submission
        setTimeout(() => {
            submitBtn.innerHTML = '<i class="fas fa-check"></i> Sent Successfully!';
            submitBtn.style.background = 'linear-gradient(135deg, #28a745, #20c997)';
            
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.style.background = '';
                submitBtn.disabled = false;
                this.reset();
            }, 2000);
        }, 1500);
    });

    // Newsletter form
    const newsletterForm = document.querySelector('.newsletter-form');
    newsletterForm?.addEventListener('submit', function(e) {
        e.preventDefault();
        const input = this.querySelector('input');
        const btn = this.querySelector('button');
        
        if (isValidEmail(input.value)) {
            btn.innerHTML = '<i class="fas fa-check"></i> Subscribed!';
            input.value = '';
            setTimeout(() => {
                btn.innerHTML = 'Subscribe';
            }, 2000);
        }
    });

    // ===== TYPING EFFECT =====
    function typeEffect(element, text, speed = 100) {
        let i = 0;
        element.innerHTML = '';
        
        function type() {
            if (i < text.length) {
                element.innerHTML += text.charAt(i);
                i++;
                setTimeout(type, speed);
            }
        }
        type();
    }

    // ===== PARALLAX EFFECT =====
    window.addEventListener('scroll', () => {
        const scrolled = window.scrollY;
        const heroImage = document.querySelector('.hero-image');
        
        if (heroImage) {
            heroImage.style.transform = `translateY(${scrolled * 0.1}px)`;
        }
    });

    // ===== RIPPLE EFFECT ON BUTTONS =====
    document.querySelectorAll('.btn').forEach(button => {
        button.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            ripple.classList.add('ripple');
            
            const rect = this.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            
            ripple.style.left = `${x}px`;
            ripple.style.top = `${y}px`;
            
            this.appendChild(ripple);
            
            setTimeout(() => ripple.remove(), 600);
        });
    });

    // ===== TOOLTIP =====
    const navItems = document.querySelectorAll('[data-tooltip]');
    
    navItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            const tooltip = document.createElement('div');
            tooltip.className = 'tooltip';
            tooltip.textContent = this.getAttribute('data-tooltip');
            
            document.body.appendChild(tooltip);
            
            const rect = this.getBoundingClientRect();
            tooltip.style.top = `${rect.top + rect.height / 2}px`;
            tooltip.style.left = `${rect.right + 10}px`;
            tooltip.style.transform = 'translateY(-50%)';
            
            this.tooltip = tooltip;
        });
        
        item.addEventListener('mouseleave', function() {
            if (this.tooltip) {
                this.tooltip.remove();
            }
        });
    });

    // ===== LOADING ANIMATION =====
    window.addEventListener('load', () => {
        document.body.classList.add('loaded');
    });

    // ===== ACTIVE NAV LINK ON SCROLL =====
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.nav-link');

    window.addEventListener('scroll', () => {
        let current = '';
        
        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.clientHeight;
            
            if (scrollY >= sectionTop - 200) {
                current = section.getAttribute('id');
            }
        });

        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === `#${current}`) {
                link.classList.add('active');
            }
        });
    });

    console.log('🍱 Delicious Dispatchers - Scripts Loaded Successfully!');
});

// ===== PASSWORD TOGGLE =====
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = input.nextElementSibling.querySelector('i');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// ===== ADD TOOLTIP STYLES =====
const tooltipStyles = document.createElement('style');
tooltipStyles.textContent = `
    .tooltip {
        position: fixed;
        background: rgba(255, 107, 53, 0.9);
        color: white;
        padding: 8px 15px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 500;
        z-index: 10000;
        pointer-events: none;
        white-space: nowrap;
        animation: fadeIn 0.2s ease;
    }
    
    .ripple {
        position: absolute;
        background: rgba(255, 255, 255, 0.3);
        border-radius: 50%;
        transform: scale(0);
        animation: ripple 0.6s linear;
        pointer-events: none;
    }
    
    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
`;
document.head.appendChild(tooltipStyles);