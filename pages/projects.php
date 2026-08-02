<?php
/**
 * Projects Page (alias for Portfolio)
 * @package PersonalBiography
 */
if (!defined('APP_RUNNING')) { http_response_code(403); exit; }
// Projects is the same as portfolio - include it
include PAGES_PATH . 'portfolio.php';
