<?php

// Include database connection
include('connect.php');

// Include sanitizeInput function
include('sanitizeInput.php');

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    session_start();

    // Retrieve and sanitize the form data
    $email = sanitizeInput($_POST["email"]);
    $password = sanitizeInput($_POST["password"]);

    // Prepare and execute the SQL statement
    $stmt = $dbc->prepare("SELECT * FROM user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();

    // Get the result
    $result = $stmt->get_result();

    // Check if the user exists
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $hashedPassword = $row["password"];

        // Verify the password
        if (password_verify($password, $hashedPassword)) {
            $_SESSION["user_id"] = $row["id"];
            $_SESSION["username"] = $row["fname"];

            // Unset any previous login/registration status from session
            unset($_SESSION["login_error"]);
            unset($_SESSION["login_errorl"]);
            unset($_SESSION["registration_success"]);
            unset($_SESSION["email_attempt"]);

            // Redirect to dashboard.php on successful login
            header("Location: dashboard.php");
            exit();
        } else {
            // Incorrect password
            $_SESSION["login_error"] = true;
            $_SESSION["email_attempt"] = $email;
            header("Location: login_page.php");
            exit();
        }
    } else {
        // User doesn't exist
        $_SESSION["login_errorl"] = true;
        $_SESSION["email_attempt"] = $email;
        header("Location: login_page.php");
        exit();
    }
} else {
    // If someone tries to access logindb.php directly without POST
    header("Location: login_page.php");
    exit();
}

// Close the database connection
if (isset($dbc)) {
    $dbc->close();
}
