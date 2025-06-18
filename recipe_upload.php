<?php

require 'sanitizeInput.php';
require 'connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    $_SESSION['recipe_action_status'] = "You must be logged in to perform this action.";
    $_SESSION['recipe_action_type'] = "error";
    header("Location: login_page.php");
    exit();
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = isset($_POST["name"]) ? sanitizeInput($_POST["name"]) : '';
    $prepTime = isset($_POST["time"]) ? sanitizeInput($_POST["time"]) : '';
    $ingredients = isset($_POST["ingredients"]) ? sanitizeInput($_POST["ingredients"]) : '';
    $instructions = isset($_POST["instructions"]) ? sanitizeInput($_POST["instructions"]) : '';
    $userId = $_SESSION['user_id'];
    $category = isset($_POST["category"]) ? sanitizeInput($_POST["category"]) : '';

    if (empty($name) || empty($prepTime) || empty($ingredients) || empty($instructions) || empty($category)) {
        $_SESSION['recipe_action_status'] = "All fields, including category, are required.";
        $_SESSION['recipe_action_type'] = "error";
        if (isset($_POST['action']) && $_POST['action'] == 'update_recipe' && isset($_POST['recipe_id'])) {
            header("Location: edit_recipe.php?id=" . $_POST['recipe_id']);
        } else {
            header("Location: recipe.php");
        }
        exit();
    }

    $max_title_length_db = 20;

    $imagePath = null;
    $is_update_action = (isset($_POST['action']) && $_POST['action'] == 'update_recipe' && isset($_POST['recipe_id']) && !empty($_POST['recipe_id']));

    if ($is_update_action) {
        $recipe_id_to_update = (int)$_POST['recipe_id'];
        if (isset($_POST['existing_image']) && !empty(trim($_POST['existing_image']))) {
            $imagePath = sanitizeInput($_POST['existing_image']);
        }
    }

    if (isset($_FILES["img"]) && $_FILES["img"]["error"] == 0 && $_FILES["img"]["size"] > 0) {
        $imageTempPath = $_FILES["img"]["tmp_name"];
        $fileName = $_FILES["img"]["name"];
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($extension, $allowed_extensions)) {
            if ($is_update_action && !empty($imagePath) && file_exists($imagePath) && $imagePath == sanitizeInput($_POST['existing_image'])) {
                if (is_writable($imagePath)) {
                    unlink($imagePath);
                } else {
                }
            }
            $newImageName = uniqid('recipe_', true) . "." . $extension;
            $newImagePath = "postimages/" . $newImageName;

            if (move_uploaded_file($imageTempPath, $newImagePath)) {
                $imagePath = $newImagePath;
            } else {
                $_SESSION['recipe_action_status'] = "Image upload error: Failed to move new uploaded file. Check folder permissions.";
                $_SESSION['recipe_action_type'] = "error";
                header("Location: " . ($is_update_action ? "edit_recipe.php?id=$recipe_id_to_update" : "recipe.php"));
                exit();
            }
        } else {
            $_SESSION['recipe_action_status'] = "Image upload error: Invalid file type. Only JPG, JPEG, PNG, GIF are allowed.";
            $_SESSION['recipe_action_type'] = "error";
            header("Location: " . ($is_update_action ? "edit_recipe.php?id=$recipe_id_to_update" : "recipe.php"));
            exit();
        }
    }

    if ($imagePath === null) {
        if (!$is_update_action) {
            $_SESSION['recipe_action_status'] = "Image is required for a new recipe.";
            $_SESSION['recipe_action_type'] = "error";
            header("Location: recipe.php");
            exit();
        } else {
            $_SESSION['recipe_action_status'] = "Image error during update. An image is required.";
            $_SESSION['recipe_action_type'] = "error";
            header("Location: edit_recipe.php?id=$recipe_id_to_update");
            exit();
        }
    }
    $combined_name = $name . " :: Category:" . $category;

    if (strlen($combined_name) > $max_title_length_db) {
        $category_suffix = " :: Category:" . $category;
        $available_length_for_name = $max_title_length_db - strlen($category_suffix);
        if ($available_length_for_name < 0) {
            $combined_name = substr($combined_name, 0, $max_title_length_db);
        } else {
            $truncated_name = substr($name, 0, $available_length_for_name);
            $combined_name = $truncated_name . $category_suffix;
        }
    }

    try {
        if ($is_update_action) {
            $check_owner_update_query = "SELECT user_id FROM recipe WHERE idrec = ? AND user_id = ?";
            $stmt_check_update = mysqli_prepare($dbc, $check_owner_update_query);
            mysqli_stmt_bind_param($stmt_check_update, "ii", $recipe_id_to_update, $userId);
            mysqli_stmt_execute($stmt_check_update);
            $result_check_update = mysqli_stmt_get_result($stmt_check_update);

            if (mysqli_num_rows($result_check_update) == 1) {
                $sql_update = "UPDATE recipe SET title=?, img=?, time=?, ingredients=?, instructions=?, category=? WHERE idrec=? AND user_id=?";
                $stmt_update = $dbc->prepare($sql_update);
                $stmt_update->bind_param("ssisssii", $name, $imagePath, $prepTime, $ingredients, $instructions, $category, $recipe_id_to_update, $userId);

                if ($stmt_update->execute()) {
                    $_SESSION["recipe_action_status"] = "Recipe updated successfully!";
                    $_SESSION['recipe_action_type'] = "success";
                    header("Location: display.php?id=$recipe_id_to_update&action=updated");
                    exit();
                } else {
                    $_SESSION["recipe_action_status"] = "Error updating recipe: " . $stmt_update->error;
                    $_SESSION['recipe_action_type'] = "error";
                    header("Location: edit_recipe.php?id=$recipe_id_to_update");
                    exit();
                }
                $stmt_update->close();
            } else {
                $_SESSION["recipe_action_status"] = "Authorization error or recipe not found for update.";
                $_SESSION['recipe_action_type'] = "error";
                header("Location: profile.php");
                exit();
            }
            mysqli_stmt_close($stmt_check_update);
        } else {
            $sql_insert = "INSERT INTO recipe (title, img, time, ingredients, instructions, user_id, category) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt_insert = $dbc->prepare($sql_insert);
            $stmt_insert->bind_param("ssisssi", $name, $imagePath, $prepTime, $ingredients, $instructions, $userId, $category);

            if ($stmt_insert->execute()) {
                $new_recipe_id = $stmt_insert->insert_id;
                $_SESSION['recipe_action_status'] = "Recipe posted successfully!";
                $_SESSION['recipe_action_type'] = "success";
                header("Location: display.php?id=$new_recipe_id&action=posted");
                exit();
            } else {
                 $_SESSION['recipe_action_status'] = "Error posting recipe: " . $stmt_insert->error;
                 $_SESSION['recipe_action_type'] = "error";
                 header("Location: recipe.php");
                 exit();
            }
            $stmt_insert->close();
        }
    } catch (Exception $e) {
        $_SESSION['recipe_action_status'] = "Database Error: " . $e->getMessage();
        $_SESSION['recipe_action_type'] = "error";
        // Arahkan kembali ke form yang sesuai
        if ($is_update_action && isset($recipe_id_to_update)) {
            header("Location: edit_recipe.php?id=$recipe_id_to_update");
        } else {
            header("Location: recipe.php");
        }
        exit();
    }
} else {
    header("Location: index.php");
    exit();
}

if (isset($dbc)) {
    mysqli_close($dbc);
}
