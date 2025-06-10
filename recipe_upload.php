<?php

// Include sanitizeInput function
include('sanitizeInput.php');

// Include database connection
include('connect.php');

// Start the session
session_start();

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Retrieve and sanitize the form data
  $name = sanitizeInput($_POST["name"]);
  $prepTime = sanitizeInput($_POST["time"]);
  $ingredients = sanitizeInput($_POST["ingredients"]);
  $instructions = sanitizeInput($_POST["instructions"]);

  // Upload the image file
  if (isset($_FILES["img"]) && $_FILES["img"]["error"] == 0) {
    $imageTempPath = $_FILES["img"]["tmp_name"];
    $extension = pathinfo($_FILES["img"]["name"], PATHINFO_EXTENSION);
    $imageName = uniqid() . "." . $extension;
    
    // Create postimages directory if it doesn't exist
    $uploadDir = 'postimages/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }
    
    // Set the full path for the image
    $imagePath = $uploadDir . $imageName;
    
    // Move the uploaded file to the postimages directory
    if (!move_uploaded_file($imageTempPath, $imagePath)) {
        die("Error uploading file. Please try again.");
    }
    
  } else {
    die("Image upload error: No file uploaded or file upload error occurred.");
  }

  // Check if user ID is available in the $_POST array
  if (isset($_POST['user_id'])) {
    $userId = $_POST['user_id'];

    try {
      $stmt = $dbc->prepare("INSERT INTO recipe (title, img, time, ingredients, instructions, user_id) VALUES (?, ?, ?, ?, ?, ?)");
      $stmt->bind_param("ssisss", $name, $imagePath, $prepTime, $ingredients, $instructions, $userId);
      $stmt->execute();

      // Redirect the user back to the index.php
      $_SESSION["post_success"] = true;
      header("Location: index.php");
      exit();
    } catch (Exception $e) {
      die("Database Error: " . $e->getMessage());
    }
  } else {
    die("User ID not found in form data.");
  }
}

// Close the database connection
$dbc->close();

?>