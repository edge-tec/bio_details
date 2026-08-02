/**
 * ============================================
 * Dynamic Personal Biography Website
 * Main JavaScript
 * ============================================
 * 
 * Features:
 * - Dark/Light mode toggle
 * - Typing animation
 * - Counter animation
 * - Smooth scrolling
 * - Sticky header
 * - Lazy loading
 * - Gallery lightbox
 * - Search/filter
 * - Cookie consent
 * - Loading screen
 */

document.addEventListener('DOMContentLoaded', () => {
    // ============================================
    // LOADING SCREEN
    // ============================================
    const loadingScreen = document.getElementById('loading-screen');
    if (loadingScreen) {
        const hideLoader = () => {
            loadingScreen.classList.add('hidden');
            setTimeout(() => {
                if (loadingScreen.parentNode) {
                    loadingScreen.remove();
                }
            }, 400);
        };

        if (document.readyState === 'complete') {
            hideLoader();
        } else {
            window.addEventListener('load', hideLoader);
            setTimeout(hideLoader, 600);
        }
    }

    // ============================================
    // INITIALIZE AOS (Animate on Scroll)
    // ============================================
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 800,
            easing: 'ease-out-cubic',
            once: true,
            offset: 50,
            disable: 'mobile'
        });
    }

    // ============================================
    // DARK / LIGHT MODE TOGGLE
    // ============================================
    const themeToggle = document.getElementById('themeToggle');
    const html = document.documentElement;

    // Load saved theme
    const savedTheme = localStorage.getItem('theme') || 'light';
    html.setAttribute('data-theme', savedTheme);

    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            const currentTheme = html.getAttribute('data-theme') || 'light';
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            html.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            
            // Re-init AOS on theme change for smooth transitions
            if (typeof AOS !== 'undefined') {
                AOS.refresh();
            }
        });
    }

    // ============================================
    // STICKY HEADER & BACK TO TOP
    // ============================================
    const header = document.querySelector('.header');
    const backToTop = document.getElementById('backToTop');
    let lastScroll = 0;

    window.addEventListener('scroll', () => {
        const currentScroll = window.pageYOffset;
        
        // Sticky Header
        if (header) {
            if (currentScroll > 50) {
                header.classList.add('sticky');
            } else {
                header.classList.remove('sticky');
            }
        }

        // Back to Top Button
        if (backToTop) {
            if (currentScroll > 300) {
                backToTop.classList.add('visible');
            } else {
                backToTop.classList.remove('visible');
            }
        }

        lastScroll = currentScroll;
    });

    if (backToTop) {
        backToTop.addEventListener('click', (e) => {
            e.preventDefault();
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // ============================================
    // TYPING ANIMATION (Hero Section)
    // ============================================
    const typingElement = document.getElementById('typing-text');
    if (typingElement) {
        const rawTexts = typingElement.getAttribute('data-texts');
        let texts = ['Medical Specialist', 'Consultant Physician', 'Laparoscopic Surgeon'];
        
        try {
            if (rawTexts) {
                texts = JSON.parse(rawTexts);
            }
        } catch (e) {
            console.error('Failed to parse typing texts:', e);
        }

        let textIndex = 0;
        let charIndex = 0;
        let isDeleting = false;
        let typeSpeed = 100;

        function type() {
            const currentText = texts[textIndex];
            
            if (isDeleting) {
                typingElement.textContent = currentText.substring(0, charIndex - 1);
                charIndex--;
                typeSpeed = 50;
            } else {
                typingElement.textContent = currentText.substring(0, charIndex + 1);
                charIndex++;
                typeSpeed = 100;
            }

            if (!isDeleting && charIndex === currentText.length) {
                isDeleting = true;
                typeSpeed = 2000; // Pause at end
            } else if (isDeleting && charIndex === 0) {
                isDeleting = false;
                textIndex = (textIndex + 1) % texts.length;
                typeSpeed = 500; // Pause before new word
            }

            setTimeout(type, typeSpeed);
        }

        type();
    }

    // ============================================
    // COUNTER ANIMATION (Stats Section)
    // ============================================
    const counters = document.querySelectorAll('.counter-value');
    if (counters.length > 0) {
        const counterObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const counter = entry.target;
                    const target = parseInt(counter.getAttribute('data-target') || '0', 10);
                    const duration = 2000; // 2 seconds
                    const step = Math.ceil(target / (duration / 16)); // 60fps
                    let current = 0;

                    const updateCounter = () => {
                        current += step;
                        if (current >= target) {
                            counter.textContent = target.toLocaleString() + '+';
                        } else {
                            counter.textContent = current.toLocaleString();
                            requestAnimationFrame(updateCounter);
                        }
                    };

                    updateCounter();
                    observer.unobserve(counter);
                }
            });
        }, { threshold: 0.5 });

        counters.forEach(counter => counterObserver.observe(counter));
    }

    // ============================================
    // PORTFOLIO FILTER (Portfolio Page)
    // ============================================
    const filterButtons = document.querySelectorAll('.filter-btn');
    const portfolioItems = document.querySelectorAll('.portfolio-item');

    if (filterButtons.length > 0 && portfolioItems.length > 0) {
        filterButtons.forEach(button => {
            button.addEventListener('click', () => {
                // Remove active class from all buttons
                filterButtons.forEach(btn => btn.classList.remove('active'));
                button.classList.add('active');

                const filter = button.getAttribute('data-filter');

                portfolioItems.forEach(item => {
                    const category = item.getAttribute('data-category');
                    if (filter === 'all' || category === filter) {
                        item.style.display = 'block';
                        setTimeout(() => {
                            item.style.opacity = '1';
                            item.style.transform = 'scale(1)';
                        }, 50);
                    } else {
                        item.style.opacity = '0';
                        item.style.transform = 'scale(0.8)';
                        setTimeout(() => {
                            item.style.display = 'none';
                        }, 300);
                    }
                });
            });
        });
    }

    // ============================================
    // BLOG SEARCH & CATEGORY FILTER
    // ============================================
    const blogSearchInput = document.getElementById('blogSearch');
    const blogItems = document.querySelectorAll('.blog-card');

    if (blogSearchInput && blogItems.length > 0) {
        blogSearchInput.addEventListener('keyup', (e) => {
            const query = e.target.value.toLowerCase().trim();

            blogItems.forEach(item => {
                const title = item.querySelector('.blog-title')?.textContent.toLowerCase() || '';
                const excerpt = item.querySelector('.blog-excerpt')?.textContent.toLowerCase() || '';

                if (title.includes(query) || excerpt.includes(query)) {
                    item.closest('.col-md-4, .col-md-6').style.display = 'block';
                } else {
                    item.closest('.col-md-4, .col-md-6').style.display = 'none';
                }
            });
        });
    }

    // ============================================
    // CONTACT FORM HANDLING (AJAX)
    // ============================================
    const contactForm = document.getElementById('contactForm');
    if (contactForm) {
        contactForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const submitBtn = contactForm.querySelector('button[type="submit"]');
            const alertContainer = document.getElementById('formAlert');
            const originalBtnText = submitBtn.innerHTML;

            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Sending...';

            const formData = new FormData(contactForm);

            try {
                const response = await fetch(contactForm.action || window.location.href, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                const rawText = await response.text();
                let data;
                try {
                    data = JSON.parse(rawText);
                } catch (jsonErr) {
                    data = { success: true, message: 'Thank you! Your message has been sent successfully.' };
                }

                if (alertContainer) {
                    alertContainer.className = `alert alert-${data.success ? 'success' : 'danger'} alert-dismissible fade show`;
                    alertContainer.innerHTML = `
                        <i class="bi bi-${data.success ? 'check-circle' : 'exclamation-triangle'}-fill me-2"></i>
                        ${data.message}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    alertContainer.classList.remove('d-none');
                    alertContainer.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                }

                if (data.success) {
                    contactForm.reset();
                }
            } catch (error) {
                if (alertContainer) {
                    alertContainer.className = 'alert alert-danger alert-dismissible fade show';
                    alertContainer.innerHTML = `
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        An error occurred while sending your message. Please try again.
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    `;
                    alertContainer.classList.remove('d-none');
                }
            } finally {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalBtnText;
            }
        });
    }

    // ============================================
    // COOKIE CONSENT BANNER
    // ============================================
    const cookieBanner = document.getElementById('cookieBanner');
    const acceptCookiesBtn = document.getElementById('acceptCookies');

    if (cookieBanner && acceptCookiesBtn) {
        if (!localStorage.getItem('cookiesAccepted')) {
            setTimeout(() => {
                cookieBanner.classList.add('show');
            }, 1000);
        }

        acceptCookiesBtn.addEventListener('click', () => {
            localStorage.setItem('cookiesAccepted', 'true');
            cookieBanner.classList.remove('show');
        });
    }

    // ============================================
    // ACTIVE NAV LINK HIGHLIGHT
    // ============================================
    const currentPath = window.location.pathname.replace(/\/$/, '') || '/';
    const navLinks = document.querySelectorAll('.navbar-nav .nav-link');

    navLinks.forEach(link => {
        const href = link.getAttribute('href');
        if (href) {
            const linkPath = new URL(href, window.location.origin).pathname.replace(/\/$/, '') || '/';
            if (linkPath === currentPath) {
                link.classList.add('active');
            }
        }
    });

    // ============================================
    // GALLERY CATEGORY FILTER & LIGHTBOX MODAL
    // ============================================
    const filterBtns = document.querySelectorAll('.filter-btn');
    const galleryItems = document.querySelectorAll('.gallery-item');
    const lightbox = document.getElementById('lightbox');
    const lightboxImg = document.getElementById('lightboxImg');
    const lightboxCaption = document.getElementById('lightboxCaption');
    const lightboxClose = document.getElementById('lightboxClose');

    // 1. Filter Category Buttons
    if (filterBtns.length > 0 && galleryItems.length > 0) {
        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                filterBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                const filter = btn.getAttribute('data-filter');

                galleryItems.forEach(item => {
                    const category = item.getAttribute('data-category');
                    if (filter === 'all' || category === filter) {
                        item.style.display = 'block';
                        setTimeout(() => {
                            item.style.opacity = '1';
                            item.style.transform = 'scale(1)';
                        }, 50);
                    } else {
                        item.style.opacity = '0';
                        item.style.transform = 'scale(0.8)';
                        setTimeout(() => {
                            item.style.display = 'none';
                        }, 300);
                    }
                });
            });
        });
    }

    // 2. Lightbox Open / Close on Image Click
    if (galleryItems.length > 0 && lightbox && lightboxImg) {
        galleryItems.forEach(item => {
            item.addEventListener('click', () => {
                const img = item.querySelector('img');
                const caption = item.querySelector('.gallery-caption');
                if (img) {
                    lightboxImg.src = img.src;
                    if (lightboxCaption) {
                        lightboxCaption.textContent = caption ? caption.textContent.trim() : (img.alt || '');
                    }
                    lightbox.style.display = 'flex';
                    setTimeout(() => {
                        lightbox.classList.add('active');
                    }, 10);
                }
            });
        });

        const closeLightbox = () => {
            lightbox.classList.remove('active');
            setTimeout(() => {
                lightbox.style.display = 'none';
                lightboxImg.src = '';
            }, 300);
        };

        if (lightboxClose) {
            lightboxClose.addEventListener('click', closeLightbox);
        }

        lightbox.addEventListener('click', (e) => {
            if (e.target === lightbox) {
                closeLightbox();
            }
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && lightbox.classList.contains('active')) {
                closeLightbox();
            }
        });
    }
});
