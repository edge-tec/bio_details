<?php
/**
 * ============================================
 * File Upload Class
 * ============================================
 * 
 * Secure file upload handling with validation,
 * type checking, and safe file naming.
 * 
 * @package PersonalBiography
 */

class FileUpload
{
    /** @var array Upload errors */
    private array $errors = [];

    /** @var string Upload directory */
    private string $uploadDir;

    /** @var array Allowed MIME types */
    private array $allowedTypes;

    /** @var array Allowed extensions */
    private array $allowedExtensions;

    /** @var int Max file size in bytes */
    private int $maxSize;

    /**
     * Constructor
     *
     * @param string $uploadDir Upload directory path
     * @param string $type 'image' or 'document'
     */
    public function __construct(string $uploadDir = '', string $type = 'image')
    {
        $this->uploadDir = $uploadDir ?: UPLOADS_PATH;
        $this->maxSize = MAX_UPLOAD_SIZE;
        
        if ($type === 'document') {
            $this->allowedTypes = ALLOWED_DOC_TYPES;
            $this->allowedExtensions = ALLOWED_DOC_EXTENSIONS;
        } else {
            $this->allowedTypes = ALLOWED_IMAGE_TYPES;
            $this->allowedExtensions = ALLOWED_IMAGE_EXTENSIONS;
        }
        
        // Ensure upload directory exists
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0755, true);
        }
    }

    /**
     * Upload a file
     *
     * @param array $file $_FILES array element
     * @param string $prefix Optional filename prefix
     * @param string|null $subfolder Optional subfolder within upload dir
     * @return string|false Filename on success, false on failure
     */
    public function upload(array $file, string $prefix = '', ?string $subfolder = null): string|false
    {
        $this->errors = [];

        // Check for upload errors
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->errors[] = $this->getUploadErrorMessage($file['error']);
            return false;
        }

        // Validate file size
        if ($file['size'] > $this->maxSize) {
            $this->errors[] = 'File size exceeds the maximum allowed size of ' . $this->formatBytes($this->maxSize) . '.';
            return false;
        }

        // Validate file extension
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $this->allowedExtensions, true)) {
            $this->errors[] = 'File type ".' . $extension . '" is not allowed. Allowed types: ' . implode(', ', $this->allowedExtensions) . '.';
            return false;
        }

        // Validate MIME type
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);
        if (!in_array($mimeType, $this->allowedTypes, true)) {
            $this->errors[] = 'File MIME type "' . $mimeType . '" is not allowed.';
            return false;
        }

        // Determine upload directory
        $targetDir = $this->uploadDir;
        if ($subfolder) {
            $targetDir .= rtrim($subfolder, '/') . '/';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }
        }

        // Generate safe filename
        $filename = $this->generateSafeFilename($prefix, $extension);
        $targetPath = $targetDir . $filename;

        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
            $this->errors[] = 'Failed to move uploaded file. Check directory permissions.';
            return false;
        }

        // Return relative path from uploads dir
        if ($subfolder) {
            return $subfolder . '/' . $filename;
        }
        return $filename;
    }

    /**
     * Delete a file
     *
     * @param string $filename Filename relative to upload dir
     * @return bool
     */
    public function delete(string $filename): bool
    {
        $filepath = $this->uploadDir . $filename;
        if (file_exists($filepath) && is_file($filepath)) {
            return unlink($filepath);
        }
        return false;
    }

    /**
     * Check if upload had errors
     */
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    /**
     * Get upload errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get first error
     */
    public function getFirstError(): string
    {
        return $this->errors[0] ?? '';
    }

    /**
     * Generate a safe, unique filename
     */
    private function generateSafeFilename(string $prefix, string $extension): string
    {
        $prefix = $prefix ? preg_replace('/[^a-zA-Z0-9_-]/', '', $prefix) . '_' : '';
        return $prefix . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $extension;
    }

    /**
     * Get human-readable upload error message
     */
    private function getUploadErrorMessage(int $errorCode): string
    {
        return match ($errorCode) {
            UPLOAD_ERR_INI_SIZE   => 'The uploaded file exceeds the server upload limit.',
            UPLOAD_ERR_FORM_SIZE  => 'The uploaded file exceeds the form upload limit.',
            UPLOAD_ERR_PARTIAL    => 'The file was only partially uploaded.',
            UPLOAD_ERR_NO_FILE    => 'No file was uploaded.',
            UPLOAD_ERR_NO_TMP_DIR => 'Missing temporary upload folder.',
            UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk.',
            UPLOAD_ERR_EXTENSION  => 'A PHP extension stopped the file upload.',
            default               => 'Unknown upload error.',
        };
    }

    /**
     * Format bytes to human-readable string
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $factor = floor((strlen((string) $bytes) - 1) / 3);
        return sprintf("%.1f %s", $bytes / pow(1024, $factor), $units[(int) $factor]);
    }
}
