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
    $repassword = sanitizeInput($_POST["repassword"]); // Tambahkan ini untuk validasi password

    // Basic server-side validation for empty fields (optional, but good practice)
    if (empty($fname) || empty($lname) || empty($email) || empty($password) || empty($repassword)) {
        $_SESSION["register_error"] = true;
        // Optionally, set a more specific error message if you wish to display it with SweetAlert
        // $_SESSION['register_error_message'] = "All fields are required.";
        header("Location: register_page.php");
        exit();
    }

    // Check if passwords match
    if ($password !== $repassword) {
        $_SESSION["register_error"] = true;
        // Optionally, set a more specific error message
        // $_SESSION['register_error_message'] = "Passwords do not match.";
        header("Location: register_page.php");
        exit();
    }

    // Encrypt the password using bcrypt
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Check if the email already exists in the database
    $stmt_check_email = $dbc->prepare("SELECT email FROM user WHERE email = ?");
    $stmt_check_email->bind_param("s", $email);
    $stmt_check_email->execute();
    $stmt_check_email->store_result();

    // If the email already exists, display an error message
    if ($stmt_check_email->num_rows > 0) {
        $_SESSION["register_error"] = true;
        // Ubah redirect ke register_page.php agar SweetAlert muncul
        header("Location: register_page.php");
        exit();
    } else {
        // Prepare and execute the SQL statement to insert the new user
        $stmt_insert_user = $dbc->prepare("INSERT INTO user (fname, lname, email, password) VALUES (?, ?, ?, ?)");
        $stmt_insert_user->bind_param("ssss", $fname, $lname, $email, $hashedPassword);
        $stmt_insert_user->execute();

        // Check if the insertion was successful
        if ($stmt_insert_user->affected_rows > 0) {
            // Set a session variable to indicate successful registration
            $_SESSION["registration_success"] = true;

            // Redirect to register_page.php (atau halaman login, sesuai alur aplikasi Anda)
            header("Location: register_page.php");
            exit();
        } else {
            // Handle database insert error
            // Ini akan mencetak error jika ada masalah dengan query INSERT
            // Anda mungkin ingin mengarahkannya kembali ke register_page.php dengan pesan error session
            $_SESSION["register_error"] = true; // Set general error
            // $_SESSION['register_error_message'] = "Error during user registration. Please try again.";
            header("Location: register_page.php");
            exit();
        }

        // Close the prepared statement for insertion
        $stmt_insert_user->close();
    }

    // Close the prepared statement for email check
    $stmt_check_email->close();

    // Close the database connection
    $dbc->close();
} else {
    // If not a POST request, redirect to the registration page
    header("Location: register_page.php");
    exit();
}
?>