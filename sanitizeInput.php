<?php
// Function to sanitize user input
function sanitizeInput($input) {
    $sanitized = trim($input);
    $sanitized = stripslashes($sanitized);
    $sanitized = htmlspecialchars($sanitized);
    return $sanitized;
}
?>