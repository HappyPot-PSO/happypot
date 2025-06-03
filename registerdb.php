<?php
// Start a session
session_start();

// Include sanitizeInput function
include('sanitizeInput.php');

// Include database connection
include('connect.php');

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize the form data
    $fname = sanitizeInput($_POST["fname"]);
    $lname = sanitizeInput($_POST["lname"]);
    $email = sanitizeInput($_POST["remail"]);
    $password = sanitizeInput($_POST["rpassword"]);

    // Encrypt the password using bcrypt
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Check if the email already exists in the database
    $stmt = $dbc->prepare("SELECT email FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    // If the email already exists, display an error message
    if ($stmt->num_rows > 0) {
        $_SESSION["register_error"] = true;
        header("Location: index.php");
        exit();
    } else {
        // Prepare and execute the SQL statement to insert the new user
        $stmt = $dbc->prepare("INSERT INTO user (fname, lname, email, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $fname, $lname, $email, $hashedPassword);
        $stmt->execute();

        // Check if the insertion was successful
        if ($stmt->affected_rows > 0) {
            // Set a session variable to indicate successful registration
            $_SESSION["registration_success"] = true;

            // Redirect to index.php
            header("Location: index.php");
            exit();
        } else {
            echo "Error inserting data: " . $dbc->error;
        }

        // Close the prepared statement
        $stmt->close();
    }

    // Close the database connection
    $dbc->close();
}
?>