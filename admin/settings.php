<?php
/**
 * Site Settings Manager
 * @package PersonalBiography
 */

if (!defined('APP_RUNNING')) { http_response_code(403); exit; }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (Session::validateCsrfToken($_POST[CSRF_TOKEN_NAME] ?? '')) {
        $settingsToUpdate = [
            'site_name'             => Validator::sanitize($_POST['site_name'] ?? ''),
            'site_description'      => Validator::sanitize($_POST['site_description'] ?? ''),
            'footer_text'           => Validator::sanitize($_POST['footer_text'] ?? ''),
            'contact_email'         => Validator::sanitizeEmail($_POST['contact_email'] ?? ''),
            'contact_phone'         => Validator::sanitize($_POST['contact_phone'] ?? ''),
            'contact_address'       => Validator::sanitize($_POST['contact_address'] ?? ''),
            'contact_whatsapp'      => Validator::sanitize($_POST['contact_whatsapp'] ?? ''),
            'contact_telegram'      => Validator::sanitize($_POST['contact_telegram'] ?? ''),
            'map_embed_url'         => Validator::sanitizeUrl($_POST['map_embed_url'] ?? ''),
            'ga_tracking_id'        => Validator::sanitize($_POST['ga_tracking_id'] ?? ''),
            'gsc_verification'      => Validator::sanitize($_POST['gsc_verification'] ?? ''),
            'newsletter_enabled'    => isset($_POST['newsletter_enabled']) ? '1' : '0',
            'comments_enabled'      => isset($_POST['comments_enabled']) ? '1' : '0',
            'cookie_consent_enabled'=> isset($_POST['cookie_consent_enabled']) ? '1' : '0',
        ];

        // Handles logo & favicon uploads
        $uploader = new FileUpload();
        if (!empty($_FILES['site_logo']['name'])) {
            $logo = $uploader->upload($_FILES['site_logo'], 'logo');
            if ($logo) $settingsToUpdate['site_logo'] = $logo;
        }
        if (!empty($_FILES['site_favicon']['name'])) {
            $favicon = $uploader->upload($_FILES['site_favicon'], 'favicon');
            if ($favicon) $settingsToUpdate['site_favicon'] = $favicon;
        }

        foreach ($settingsToUpdate as $key => $val) {
            if ($db->exists('settings', 'setting_key = ?', [$key])) {
                $db->update('settings', ['setting_value' => $val], 'setting_key = ?', [$key]);
            } else {
                $db->insert('settings', ['setting_key' => $key, 'setting_value' => $val, 'setting_group' => 'general']);
            }
        }

        Session::flash('success', 'Site settings updated successfully.');
        redirect(SITE_URL . 'admin/settings');
    }
}

// Reload settings
$allSettings = [];
$rows = $db->fetchAll("SELECT setting_key, setting_value FROM settings");
foreach ($rows as $r) {
    $allSettings[$r['setting_key']] = $r['setting_value'];
}
?>

<div class="mb-4">
    <h3 class="fw-bold">General Settings</h3>
    <p class="text-secondary">Configure branding, contact information, Google Analytics, and site features.</p>
</div>

<form method="post" enctype="multipart/form-data" class="admin-card p-4">
    <?php echo Session::csrfField(); ?>

    <h5 class="fw-bold border-bottom pb-2 mb-3">Branding & General</h5>
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <label class="form-label">Site Name</label>
            <input type="text" name="site_name" class="form-control" value="<?php echo e($allSettings['site_name'] ?? SITE_NAME); ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label">Site Description</label>
            <input type="text" name="site_description" class="form-control" value="<?php echo e($allSettings['site_description'] ?? ''); ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label">Site Logo Image</label>
            <input type="file" name="site_logo" class="form-control" accept="image/*">
            <?php if (!empty($allSettings['site_logo'])): ?>
                <div class="mt-2"><img src="<?php echo uploadUrl($allSettings['site_logo']); ?>" style="height: 35px;"></div>
            <?php endif; ?>
        </div>
        <div class="col-md-6">
            <label class="form-label">Favicon Image</label>
            <input type="file" name="site_favicon" class="form-control" accept="image/*">
            <?php if (!empty($allSettings['site_favicon'])): ?>
                <div class="mt-2"><img src="<?php echo uploadUrl($allSettings['site_favicon']); ?>" style="height: 25px;"></div>
            <?php endif; ?>
        </div>
        <div class="col-12">
            <label class="form-label">Footer Copyright Text</label>
            <input type="text" name="footer_text" class="form-control" value="<?php echo e($allSettings['footer_text'] ?? ''); ?>">
        </div>
    </div>

    <h5 class="fw-bold border-bottom pb-2 mb-3">Contact & Social Links</h5>
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <label class="form-label">Contact Email</label>
            <input type="email" name="contact_email" class="form-control" value="<?php echo e($allSettings['contact_email'] ?? ''); ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label">Contact Phone</label>
            <input type="text" name="contact_phone" class="form-control" value="<?php echo e($allSettings['contact_phone'] ?? ''); ?>">
        </div>
        <div class="col-md-4">
            <label class="form-label">Address / Location</label>
            <input type="text" name="contact_address" class="form-control" value="<?php echo e($allSettings['contact_address'] ?? ''); ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label">WhatsApp Number (with country code)</label>
            <input type="text" name="contact_whatsapp" class="form-control" placeholder="+8801234567890" value="<?php echo e($allSettings['contact_whatsapp'] ?? ''); ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label">Telegram Username</label>
            <input type="text" name="contact_telegram" class="form-control" placeholder="username" value="<?php echo e($allSettings['contact_telegram'] ?? ''); ?>">
        </div>
        <div class="col-12">
            <label class="form-label">Google Maps Embed iframe URL</label>
            <input type="url" name="map_embed_url" class="form-control" value="<?php echo e($allSettings['map_embed_url'] ?? ''); ?>">
        </div>
    </div>

    <h5 class="fw-bold border-bottom pb-2 mb-3">Analytics & Search Engines</h5>
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <label class="form-label">Google Analytics Measurement ID (G-XXXXXXXXXX)</label>
            <input type="text" name="ga_tracking_id" class="form-control" placeholder="G-XXXXXXXXXX" value="<?php echo e($allSettings['ga_tracking_id'] ?? ''); ?>">
        </div>
        <div class="col-md-6">
            <label class="form-label">Google Search Console Verification Tag</label>
            <input type="text" name="gsc_verification" class="form-control" value="<?php echo e($allSettings['gsc_verification'] ?? ''); ?>">
        </div>
    </div>

    <h5 class="fw-bold border-bottom pb-2 mb-3">Feature Toggles</h5>
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="form-check">
                <input type="checkbox" name="newsletter_enabled" class="form-check-input" id="newsletter_enabled" <?php echo (($allSettings['newsletter_enabled'] ?? '1') === '1') ? 'checked' : ''; ?>>
                <label class="form-check-label" for="newsletter_enabled">Enable Newsletter Subscription</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-check">
                <input type="checkbox" name="comments_enabled" class="form-check-input" id="comments_enabled" <?php echo (($allSettings['comments_enabled'] ?? '1') === '1') ? 'checked' : ''; ?>>
                <label class="form-check-label" for="comments_enabled">Enable Blog Comments</label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-check">
                <input type="checkbox" name="cookie_consent_enabled" class="form-check-input" id="cookie_consent_enabled" <?php echo (($allSettings['cookie_consent_enabled'] ?? '1') === '1') ? 'checked' : ''; ?>>
                <label class="form-check-label" for="cookie_consent_enabled">Enable Cookie Consent Bar</label>
            </div>
        </div>
    </div>

    <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-save me-1"></i> Save General Settings</button>
</form>
