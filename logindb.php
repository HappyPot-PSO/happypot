<?php

// Include database connection
include('connect.php');

// Include sanitizeInput function
include('sanitizeInput.php');

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        $hashedPassword = $row["password"];

        // Verify the password
        if (password_verify($password, $hashedPassword)) {
            $_SESSION["user_id"] = $row["id"];
            $_SESSION["username"] = $row["fname"];

            // Redirect to index.php
            header("Location: index.php");
            exit();
        } else {
            $_SESSION["login_error"] = true;
            header("Location: index.php");
            exit();
        }
    } else {
        $_SESSION["login_error1"] = true;
        header("Location: index.php");
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}

// Close the database connection
$dbc->close();

?>