<?php
/**
 * Admin Login Page
 * @package PersonalBiography
 */

if (!defined('APP_RUNNING')) { http_response_code(403); exit; }

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (Session::validateCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        $auth = new Auth();
        $result = $auth->login($email, $password);

        if ($result['success']) {
            Session::flash('success', 'Welcome back, ' . e($result['user']['name']) . '!');
            redirect(SITE_URL . 'admin/dashboard');
        } else {
            $error = $result['message'];
        }
    } else {
        $error = 'Invalid CSRF token. Please try again.';
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="<?php echo CHARSET; ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - <?php echo SITE_NAME; ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Global CSS -->
    <link rel="stylesheet" href="<?php echo asset('css/style.css'); ?>">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--gradient-hero);
        }
        .login-card {
            width: 100%;
            max-width: 420px;
            padding: 2.5rem;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-5">
            <div class="glass-card login-card mx-auto" data-aos="fade-up">
                <div class="text-center mb-4">
                    <div class="brand-text fs-2 fw-bold mb-2">
                        <span class="brand-accent">&lt;</span>Admin<span class="brand-accent">/&gt;</span>
                    </div>
                    <p class="text-secondary">Sign in to manage your biography website</p>
                </div>

                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo e($error); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php echo renderFlashMessages(); ?>

                <form method="post" action="">
                    <?php echo Session::csrfField(); ?>
                    
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                            <input type="email" class="form-control" id="email" name="email" required placeholder="admin@example.com" value="<?php echo e($_POST['email'] ?? ''); ?>">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control" id="password" name="password" required placeholder="••••••••">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 btn-lg mb-3">
                        <i class="bi bi-box-arrow-in-right me-2"></i> Sign In
                    </button>
                    
                    <div class="text-center">
                        <a href="<?php echo SITE_URL; ?>" class="text-secondary small text-decoration-none">
                            <i class="bi bi-arrow-left me-1"></i> Back to Main Website
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
