<?php
/**
 * Resume Upload Manager
 * @package PersonalBiography
 */

if (!defined('APP_RUNNING')) { http_response_code(403); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (Session::validateCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
        if (!empty($_FILES['resume_file']['name'])) {
            $uploader = new FileUpload('', 'document');
            $uploaded = $uploader->upload($_FILES['resume_file'], 'resume');
            if ($uploaded) {
                // Deactivate old resumes
                $db->query("UPDATE resume SET is_active = 0");
                
                $db->insert('resume', [
                    'file_path'     => $uploaded,
                    'original_name' => Validator::sanitize($_FILES['resume_file']['name']),
                    'file_size'     => $_FILES['resume_file']['size'],
                    'is_active'     => 1,
                ]);
                Session::flash('success', 'Resume uploaded successfully.');
            } else {
                Session::flash('error', $uploader->getFirstError());
            }
        } else {
            Session::flash('error', 'Please select a PDF file.');
        }
        redirect(SITE_URL . 'admin/resume');
    }
}

$currentResume = $db->fetch("SELECT * FROM resume WHERE is_active = 1 ORDER BY uploaded_at DESC LIMIT 1");
?>

<div class="mb-4">
    <h3 class="fw-bold">Resume Upload Manager</h3>
    <p class="text-secondary">Upload your latest CV / Resume in PDF format.</p>
</div>

<div class="row g-4">
    <div class="col-lg-6">
        <div class="admin-card p-4">
            <h5 class="fw-bold mb-3">Upload New Resume (PDF)</h5>
            <form method="post" enctype="multipart/form-data">
                <?php echo Session::csrfField(); ?>
                <div class="mb-3">
                    <label class="form-label">Select PDF File *</label>
                    <input type="file" name="resume_file" class="form-control" accept="application/pdf" required>
                    <small class="text-muted">Maximum file size: 5 MB</small>
                </div>
                <button type="submit" class="btn btn-primary"><i class="bi bi-upload me-1"></i> Upload Resume</button>
            </form>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="admin-card p-4">
            <h5 class="fw-bold mb-3">Active Resume Details</h5>
            <?php if ($currentResume): ?>
                <div class="p-3 border rounded mb-3 bg-tertiary">
                    <div class="fw-bold"><i class="bi bi-file-earmark-pdf text-danger me-2"></i><?php echo e($currentResume['original_name']); ?></div>
                    <div class="small text-muted mt-1">Uploaded: <?php echo formatDate($currentResume['uploaded_at'], 'F d, Y h:i A'); ?></div>
                    <div class="small text-muted">Downloads: <?php echo (int)$currentResume['download_count']; ?></div>
                </div>
                <a href="<?php echo uploadUrl($currentResume['file_path']); ?>" target="_blank" class="btn btn-outline-primary me-2">
                    <i class="bi bi-eye me-1"></i> View Document
                </a>
            <?php else: ?>
                <p class="text-muted">No resume uploaded yet.</p>
            <?php endif; ?>
        </div>
    </div>
</div>
