<?php
/**
 * ============================================
 * Validator Class
 * ============================================
 * 
 * Input validation and sanitization.
 * All validation methods return boolean results.
 * 
 * @package PersonalBiography
 */

class Validator
{
    /** @var array Validation errors */
    private array $errors = [];

    /** @var array Data to validate */
    private array $data = [];

    /**
     * Constructor
     *
     * @param array $data Data to validate
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * Validate required field
     */
    public function required(string $field, string $label = ''): self
    {
        $label = $label ?: ucfirst(str_replace('_', ' ', $field));
        if (!isset($this->data[$field]) || trim((string)$this->data[$field]) === '') {
            $this->errors[$field] = "{$label} is required.";
        }
        return $this;
    }

    /**
     * Validate email format
     */
    public function email(string $field, string $label = ''): self
    {
        $label = $label ?: ucfirst(str_replace('_', ' ', $field));
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            if (!filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
                $this->errors[$field] = "{$label} must be a valid email address.";
            }
        }
        return $this;
    }

    /**
     * Validate minimum length
     */
    public function minLength(string $field, int $min, string $label = ''): self
    {
        $label = $label ?: ucfirst(str_replace('_', ' ', $field));
        if (isset($this->data[$field]) && strlen(trim((string)$this->data[$field])) < $min) {
            $this->errors[$field] = "{$label} must be at least {$min} characters.";
        }
        return $this;
    }

    /**
     * Validate maximum length
     */
    public function maxLength(string $field, int $max, string $label = ''): self
    {
        $label = $label ?: ucfirst(str_replace('_', ' ', $field));
        if (isset($this->data[$field]) && strlen(trim((string)$this->data[$field])) > $max) {
            $this->errors[$field] = "{$label} must not exceed {$max} characters.";
        }
        return $this;
    }

    /**
     * Validate URL format
     */
    public function url(string $field, string $label = ''): self
    {
        $label = $label ?: ucfirst(str_replace('_', ' ', $field));
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            if (!filter_var($this->data[$field], FILTER_VALIDATE_URL)) {
                $this->errors[$field] = "{$label} must be a valid URL.";
            }
        }
        return $this;
    }

    /**
     * Validate numeric value
     */
    public function numeric(string $field, string $label = ''): self
    {
        $label = $label ?: ucfirst(str_replace('_', ' ', $field));
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            if (!is_numeric($this->data[$field])) {
                $this->errors[$field] = "{$label} must be a number.";
            }
        }
        return $this;
    }

    /**
     * Validate integer value
     */
    public function integer(string $field, string $label = ''): self
    {
        $label = $label ?: ucfirst(str_replace('_', ' ', $field));
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            if (filter_var($this->data[$field], FILTER_VALIDATE_INT) === false) {
                $this->errors[$field] = "{$label} must be an integer.";
            }
        }
        return $this;
    }

    /**
     * Validate value is between min and max
     */
    public function between(string $field, int $min, int $max, string $label = ''): self
    {
        $label = $label ?: ucfirst(str_replace('_', ' ', $field));
        if (isset($this->data[$field]) && is_numeric($this->data[$field])) {
            $val = (int) $this->data[$field];
            if ($val < $min || $val > $max) {
                $this->errors[$field] = "{$label} must be between {$min} and {$max}.";
            }
        }
        return $this;
    }

    /**
     * Validate field matches another field
     */
    public function matches(string $field, string $matchField, string $label = ''): self
    {
        $label = $label ?: ucfirst(str_replace('_', ' ', $field));
        $matchLabel = ucfirst(str_replace('_', ' ', $matchField));
        if (isset($this->data[$field]) && isset($this->data[$matchField])) {
            if ($this->data[$field] !== $this->data[$matchField]) {
                $this->errors[$field] = "{$label} must match {$matchLabel}.";
            }
        }
        return $this;
    }

    /**
     * Validate value is in allowed list
     */
    public function in(string $field, array $allowed, string $label = ''): self
    {
        $label = $label ?: ucfirst(str_replace('_', ' ', $field));
        if (isset($this->data[$field]) && !in_array($this->data[$field], $allowed, true)) {
            $this->errors[$field] = "{$label} contains an invalid value.";
        }
        return $this;
    }

    /**
     * Custom validation with callback
     */
    public function custom(string $field, callable $callback, string $errorMessage): self
    {
        if (isset($this->data[$field])) {
            if (!$callback($this->data[$field])) {
                $this->errors[$field] = $errorMessage;
            }
        }
        return $this;
    }

    /**
     * Check if validation passed
     */
    public function passes(): bool
    {
        return empty($this->errors);
    }

    /**
     * Check if validation failed
     */
    public function fails(): bool
    {
        return !empty($this->errors);
    }

    /**
     * Get all validation errors
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Get first error message
     */
    public function getFirstError(): string
    {
        return !empty($this->errors) ? reset($this->errors) : '';
    }

    /**
     * Get error for specific field
     */
    public function getError(string $field): string
    {
        return $this->errors[$field] ?? '';
    }

    /**
     * Check if specific field has error
     */
    public function hasError(string $field): bool
    {
        return isset($this->errors[$field]);
    }

    // ========================================
    // STATIC SANITIZATION METHODS
    // ========================================

    /**
     * Sanitize a string
     */
    public static function sanitize(string $value): string
    {
        return htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Sanitize email
     */
    public static function sanitizeEmail(string $email): string
    {
        return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
    }

    /**
     * Sanitize integer
     */
    public static function sanitizeInt(mixed $value): int
    {
        return (int) filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }

    /**
     * Sanitize URL
     */
    public static function sanitizeUrl(string $url): string
    {
        return filter_var(trim($url), FILTER_SANITIZE_URL);
    }

    /**
     * Sanitize array of strings
     */
    public static function sanitizeArray(array $data): array
    {
        return array_map(function ($value) {
            if (is_string($value)) {
                return self::sanitize($value);
            }
            if (is_array($value)) {
                return self::sanitizeArray($value);
            }
            return $value;
        }, $data);
    }
}
