<?php
/**
 * Contact Page
 * @package PersonalBiography
 */
if (!defined('APP_RUNNING')) { http_response_code(403); exit; }

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'contact') {
        if (Session::validateCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
            if (!isRateLimited('contact', 3, 600)) {
                $validator = new Validator($_POST);
                $validator->required('name', 'Name')
                          ->required('email', 'Email')
                          ->email('email', 'Email')
                          ->required('message', 'Message')
                          ->minLength('message', 10, 'Message')
                          ->maxLength('message', 5000, 'Message');
                
                if ($validator->passes()) {
                    try {
                        $contactData = [
                            'name'       => Validator::sanitize($_POST['name']),
                            'email'      => Validator::sanitizeEmail($_POST['email']),
                            'phone'      => Validator::sanitize($_POST['phone'] ?? ''),
                            'subject'    => Validator::sanitize($_POST['subject'] ?? ''),
                            'message'    => Validator::sanitize($_POST['message']),
                            'ip_address' => getClientIp(),
                        ];
                        $db->insert('contact_messages', $contactData);

                        // Send automatic email notification to admin email address
                        sendContactNotification($contactData);

                        Session::flash('success', 'Thank you! Your message has been sent successfully. I\'ll get back to you soon.');
                    } catch (Exception $e) {
                        Session::flash('error', 'Sorry, something went wrong. Please try again later.');
                    }
                } else {
                    Session::flash('error', $validator->getFirstError());
                }
            } else {
                Session::flash('error', 'Too many submissions. Please wait a few minutes before trying again.');
            }
        } else {
            Session::flash('error', 'Invalid security token. Please try again.');
        }
        redirect(url('contact'));
    }
    
    if ($action === 'newsletter') {
        if (Session::validateCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
            $email = Validator::sanitizeEmail($_POST['newsletter_email'] ?? '');
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                try {
                    if (!$db->exists('newsletter_subscribers', 'email = ?', [$email])) {
                        $db->insert('newsletter_subscribers', ['email' => $email]);
                    }
                } catch (Exception $e) {}
            }
        }
        redirect(url('contact'));
    }
}
?>

<section class="page-header">
    <div class="container">
        <h1 class="page-title" data-aos="fade-up">Contact <span class="gradient-text">Me</span></h1>
        <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-delay="100">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo url('home'); ?>">Home</a></li>
                <li class="breadcrumb-item active">Contact</li>
            </ol>
        </nav>
    </div>
</section>

<section class="section">
    <div class="container">
        <!-- Flash Messages -->
        <?php echo renderFlashMessages(); ?>
        
        <!-- Contact Info Cards -->
        <div class="row g-4 mb-5">
            <div class="col-lg-3 col-md-6" data-aos="fade-up">
                <div class="contact-info-card">
                    <div class="info-icon"><i class="bi bi-envelope"></i></div>
                    <div>
                        <div class="info-label">Email</div>
                        <div class="info-value"><?php echo e(getSetting('contact_email', 'contact@example.com')); ?></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="contact-info-card">
                    <div class="info-icon"><i class="bi bi-telephone"></i></div>
                    <div>
                        <div class="info-label">Phone</div>
                        <div class="info-value"><?php echo e(getSetting('contact_phone', '+880 1234 567890')); ?></div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="contact-info-card">
                    <a href="https://wa.me/<?php echo e(getSetting('contact_whatsapp', '')); ?>" target="_blank" style="color: inherit; text-decoration: none;" class="d-flex align-items-center gap-3 w-100">
                        <div class="info-icon" style="background: rgba(37, 211, 102, 0.1); color: #25D366;"><i class="bi bi-whatsapp"></i></div>
                        <div>
                            <div class="info-label">WhatsApp</div>
                            <div class="info-value">Chat Now</div>
                        </div>
                    </a>
                </div>
            </div>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="contact-info-card">
                    <a href="https://t.me/<?php echo e(getSetting('contact_telegram', '')); ?>" target="_blank" style="color: inherit; text-decoration: none;" class="d-flex align-items-center gap-3 w-100">
                        <div class="info-icon" style="background: rgba(0, 136, 204, 0.1); color: #0088CC;"><i class="bi bi-telegram"></i></div>
                        <div>
                            <div class="info-label">Telegram</div>
                            <div class="info-value">Message Me</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="row g-5">
            <!-- Contact Form -->
            <div class="col-lg-7" data-aos="fade-right">
                <div class="glass-card">
                    <h4 class="mb-4"><i class="bi bi-chat-dots me-2 text-primary"></i>Send Me a Message</h4>
                    <form method="post" class="contact-form" id="contactForm">
                        <?php echo Session::csrfField(); ?>
                        <input type="hidden" name="action" value="contact">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Full Name *</label>
                                <input type="text" id="name" name="name" class="form-control" placeholder="Your full name" required>
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" id="email" name="email" class="form-control" placeholder="your@email.com" required>
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Phone</label>
                                <input type="tel" id="phone" name="phone" class="form-control" placeholder="+880 xxxxxxxxxx">
                            </div>
                            <div class="col-md-6">
                                <label for="subject" class="form-label">Subject</label>
                                <input type="text" id="subject" name="subject" class="form-control" placeholder="What's this about?">
                            </div>
                            <div class="col-12">
                                <label for="message" class="form-label">Message *</label>
                                <textarea id="message" name="message" class="form-control" rows="5" placeholder="Tell me about your project..." required></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    <i class="bi bi-send"></i> Send Message
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Map -->
            <div class="col-lg-5" data-aos="fade-left">
                <div class="map-wrapper mb-4">
                    <iframe src="<?php echo e(getSetting('map_embed_url', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d233668.38703692693!2d90.27487408963817!3d23.780573258035702!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3755b8b087026b81%3A0x8fa563962f3cff37!2sDhaka!5e0!3m2!1sen!2sbd!4v1700000000000!5m2!1sen!2sbd')); ?>" allowfullscreen="" loading="lazy" title="Location Map"></iframe>
                </div>
                
                <div class="glass-card">
                    <h5 class="mb-3"><i class="bi bi-geo-alt me-2 text-primary"></i>Location</h5>
                    <p class="text-secondary mb-3"><?php echo e(getSetting('contact_address', 'Dhaka, Bangladesh')); ?></p>
                    
                    <h5 class="mb-3"><i class="bi bi-share me-2 text-primary"></i>Follow Me</h5>
                    <div class="d-flex flex-wrap gap-2">
                        <?php foreach ($socialLinks as $social): ?>
                        <a href="<?php echo e($social['url']); ?>" target="_blank" rel="noopener noreferrer" class="social-icon" title="<?php echo e($social['platform']); ?>" style="--social-color: <?php echo e($social['color'] ?? '#2563EB'); ?>">
                            <i class="<?php echo e($social['icon']); ?>"></i>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
