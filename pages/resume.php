<?php
/**
 * Resume Page
 * @package PersonalBiography
 */
if (!defined('APP_RUNNING')) { http_response_code(403); exit; }

$resume = null;
try {
    $resume = $db->fetch("SELECT * FROM resume WHERE is_active = 1 ORDER BY uploaded_at DESC LIMIT 1");
    
    // Handle download
    if (isset($_GET['download']) && $resume) {
        $filePath = UPLOADS_PATH . $resume['file_path'];
        if (file_exists($filePath)) {
            $db->query("UPDATE resume SET download_count = download_count + 1 WHERE id = ?", [$resume['id']]);
            header('Content-Type: application/pdf');
            header('Content-Disposition: attachment; filename="' . ($resume['original_name'] ?? 'resume.pdf') . '"');
            header('Content-Length: ' . filesize($filePath));
            readfile($filePath);
            exit;
        }
    }
} catch (Exception $e) {}
?>

<section class="page-header">
    <div class="container">
        <h1 class="page-title" data-aos="fade-up">My <span class="gradient-text">Resume</span></h1>
        <nav aria-label="breadcrumb" data-aos="fade-up" data-aos-delay="100">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo url('home'); ?>">Home</a></li>
                <li class="breadcrumb-item active">Resume</li>
            </ol>
        </nav>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="text-center mb-4" data-aos="fade-up">
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <?php if ($resume): ?>
                    <a href="<?php echo url('resume'); ?>?download=1" class="btn btn-primary btn-lg">
                        <i class="bi bi-download"></i> Download Resume
                    </a>
                <?php endif; ?>
                <button type="button" class="btn btn-outline-primary btn-lg" id="printResume">
                    <i class="bi bi-printer"></i> Print Resume
                </button>
            </div>
            <?php if ($resume): ?>
                <small class="text-muted d-block mt-2">
                    <i class="bi bi-download me-1"></i><?php echo formatNumber((int)$resume['download_count']); ?> downloads
                    • Updated <?php echo formatDate($resume['updated_at']); ?>
                </small>
            <?php endif; ?>
        </div>
        
        <div class="resume-container" data-aos="fade-up">
            <?php if ($resume && file_exists(UPLOADS_PATH . $resume['file_path'])): ?>
                <embed src="<?php echo e(uploadUrl($resume['file_path'])); ?>" type="application/pdf">
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-file-earmark-pdf" style="font-size: 4rem; color: var(--text-muted);"></i>
                    <p class="text-secondary mt-3">Resume will be displayed here once uploaded via the admin panel.</p>
                    <p class="text-muted">Supported format: PDF</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
