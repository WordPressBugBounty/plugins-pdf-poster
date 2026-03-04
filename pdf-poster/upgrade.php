<?php
if ( ! defined( 'ABSPATH' ) ) exit;

$required_files = [
    'inc/functions.php',
    'inc/admin.php',
    'vendor/codestar-framework/codestar-framework.php',
    'blocks.php',
    'inc/Base/LicenseActivation.php',

];

foreach ($required_files as $file) {
    if(file_exists(__DIR__ . '/' . $file)) {
        require_once(__DIR__ . '/' . $file);
    }
}
