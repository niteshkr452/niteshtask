<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Test basic PHP functionality
echo "PHP is working. Server time: " . date('Y-m-d H:i:s');

// Test file inclusion
$test_path = __DIR__ . '/../assets/vendor/php-email-form/php-email-form.php';
if(file_exists($test_path)) {
    echo "\nLibrary found at: " . $test_path;
} else {
    echo "\nLibrary NOT found at: " . $test_path;
}