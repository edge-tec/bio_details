<?php
/**
 * Profile & Biography Manager
 * @package PersonalBiography
 */

if (!defined('APP_RUNNING')) { http_response_code(403); exit; }

$profile = $db->fetch("SELECT * FROM profile LIMIT 1") ?: [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (Session::validateCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
        $data = [
            'full_name'              => Validator::sanitize($_POST['full_name'] ?? ''),
            'profession'             => Validator::sanitize($_POST['profession'] ?? ''),
            'bio_short'              => Validator::sanitize($_POST['bio_short'] ?? ''),
            'bio_full'               => $_POST['bio_full'] ?? '', // Allow HTML/multiline
            'birthday'               => $_POST['birthday'] ?: null,
            'nationality'            => Validator::sanitize($_POST['nationality'] ?? ''),
            'location'               => Validator::sanitize($_POST['location'] ?? ''),
            'languages'              => Validator::sanitize($_POST['languages'] ?? ''),
            'email'                  => Validator::sanitizeEmail($_POST['email'] ?? ''),
            'phone'                  => Validator::sanitize($_POST['phone'] ?? ''),
            'website'                => Validator::sanitizeUrl($_POST['website'] ?? ''),
            'mission'                => Validator::sanitize($_POST['mission'] ?? ''),
            'vision'                 => Validator::sanitize($_POST['vision'] ?? ''),
            'goals'                  => Validator::sanitize($_POST['goals'] ?? ''),
            'typing_texts'           => $_POST['typing_texts'] ?? '[]',
            'stats_experience_years' => (int)($_POST['stats_experience_years'] ?? 0),
            'stats_projects'         => (int)($_POST['stats_projects'] ?? 0),
            'stats_clients'          => (int)($_POST['stats_clients'] ?? 0),
            'stats_awards'           => (int)($_POST['stats_awards'] ?? 0),
        ];

        // Handle Photo Upload
        if (!empty($_FILES['photo']['name'])) {
            $uploader = new FileUpload();
            $uploaded = $uploader->upload($_FILES['photo'], 'profile');
            if ($uploaded) {
                if (!empty($profile['photo'])) {
                    $uploader->delete($profile['photo']);
                }
                $data['photo'] = $uploaded;
            } else {
                Session::flash('error', $uploader->getFirstError());
            }
        }

        if (!empty($profile)) {
            $db->update('profile', $data, 'id = ?', [$profile['id']]);
        } else {
            $data['user_id'] = Session::get('user_id');
            $db->insert('profile', $data);
        }

        Session::flash('success', 'Profile updated successfully.');
        redirect(SITE_URL . 'admin/profile');
    }
}

$profile = $db->fetch("SELECT * FROM profile LIMIT 1") ?: [];
?>

<div class="mb-4">
    <h3 class="fw-bold">Profile & Biography Manager</h3>
    <p class="text-secondary">Update your personal details, biography, statistics, and profile photo.</p>
</div>

<form method="post" enctype="multipart/form-data" class="admin-card p-4">
    <?php echo Session::csrfField(); ?>
    
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Full Name *</label>
            <input type="text" name="full_name" class="form-control" required value="<?php echo e($profile['full_name'] ?? ''); ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label">Profession / Tagline *</label>
            <input type="text" name="profession" class="form-control" required value="<?php echo e($profile['profession'] ?? ''); ?>">
        </div>

        <div class="col-md-4">
            <label class="form-label">Profile Photo</label>
            <input type="file" name="photo" class="form-control" accept="image/*">
            <?php if (!empty($profile['photo'])): ?>
                <div class="mt-2">
                    <img src="<?php echo uploadUrl($profile['photo']); ?>" alt="Profile" style="height: 60px; border-radius: 8px;">
                </div>
            <?php endif; ?>
        </div>
        <div class="col-md-4">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?php echo e($profile['email'] ?? ''); ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label">Phone</label>
            <input type="text" name="phone" class="form-control" value="<?php echo e($profile['phone'] ?? ''); ?>">
        </div>

        <div class="col-md-3">
            <label class="form-label">Birthday</label>
            <input type="date" name="birthday" class="form-control" value="<?php echo e($profile['birthday'] ?? ''); ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label">Nationality</label>
            <input type="text" name="nationality" class="form-control" value="<?php echo e($profile['nationality'] ?? ''); ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label">Location</label>
            <input type="text" name="location" class="form-control" value="<?php echo e($profile['location'] ?? ''); ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label">Website</label>
            <input type="url" name="website" class="form-control" value="<?php echo e($profile['website'] ?? ''); ?>">
        </div>

        <div class="col-12">
            <label class="form-label">Languages (comma-separated)</label>
            <input type="text" name="languages" class="form-control" value="<?php echo e($profile['languages'] ?? ''); ?>">
        </div>

        <div class="col-12">
            <label class="form-label">Hero Typing Animation Texts (JSON array format)</label>
            <input type="text" name="typing_texts" class="form-control" value="<?php echo e($profile['typing_texts'] ?? '["Full Stack Developer", "PHP Expert"]'); ?>">
            <small class="text-muted">Example: ["PHP Developer", "Full Stack Engineer", "SEO Specialist"]</small>
        </div>

        <div class="col-12">
            <label class="form-label">Short Biography (Shown on Hero)</label>
            <textarea name="bio_short" class="form-control" rows="2"><?php echo e($profile['bio_short'] ?? ''); ?></textarea>
        </div>

        <div class="col-12">
            <label class="form-label">Full Biography (Shown on About page)</label>
            <textarea name="bio_full" class="form-control" rows="6"><?php echo e($profile['bio_full'] ?? ''); ?></textarea>
        </div>

        <div class="col-md-4">
            <label class="form-label">Mission</label>
            <textarea name="mission" class="form-control" rows="3"><?php echo e($profile['mission'] ?? ''); ?></textarea>
        </div>
        <div class="col-md-4">
            <label class="form-label">Vision</label>
            <textarea name="vision" class="form-control" rows="3"><?php echo e($profile['vision'] ?? ''); ?></textarea>
        </div>
        <div class="col-md-4">
            <label class="form-label">Goals</label>
            <textarea name="goals" class="form-control" rows="3"><?php echo e($profile['goals'] ?? ''); ?></textarea>
        </div>

        <h5 class="fw-bold mt-4 mb-2">Statistics Counter Settings</h5>

        <div class="col-md-3">
            <label class="form-label">Years Experience</label>
            <input type="number" name="stats_experience_years" class="form-control" value="<?php echo (int)($profile['stats_experience_years'] ?? 0); ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label">Completed Projects</label>
            <input type="number" name="stats_projects" class="form-control" value="<?php echo (int)($profile['stats_projects'] ?? 0); ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label">Happy Clients</label>
            <input type="number" name="stats_clients" class="form-control" value="<?php echo (int)($profile['stats_clients'] ?? 0); ?>">
        </div>
        <div class="col-md-3">
            <label class="form-label">Awards Won</label>
            <input type="number" name="stats_awards" class="form-control" value="<?php echo (int)($profile['stats_awards'] ?? 0); ?>">
        </div>

        <div class="col-12 mt-4">
            <button type="submit" class="btn btn-primary btn-lg">
                <i class="bi bi-save me-1"></i> Save Changes
            </button>
        </div>
    </div>
</form>
