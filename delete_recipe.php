<?php

session_start();
require_once 'connect.php';
if (!isset($_SESSION['user_id'])) {
    header("Location: login_page.php");
    exit();
}

if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $recipe_id_to_delete = $_GET['id'];
    $current_user_id = $_SESSION['user_id'];
    $check_owner_query = "SELECT user_id, img FROM recipe WHERE idrec = ?";
    $stmt_check = mysqli_prepare($dbc, $check_owner_query);
    mysqli_stmt_bind_param($stmt_check, "i", $recipe_id_to_delete);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);
    if ($recipe_data = mysqli_fetch_assoc($result_check)) {
        $owner_id = $recipe_data['user_id'];
        $image_path_to_delete = $recipe_data['img'];
        if ($owner_id == $current_user_id) {
            if (!empty($image_path_to_delete) && file_exists($image_path_to_delete)) {
                unlink($image_path_to_delete);
            }

            $delete_comments_query = "DELETE FROM comment WHERE recipe_idrec = ?";
            $stmt_delete_comments = mysqli_prepare($dbc, $delete_comments_query);
            mysqli_stmt_bind_param($stmt_delete_comments, "i", $recipe_id_to_delete);
            mysqli_stmt_execute($stmt_delete_comments);
            mysqli_stmt_close($stmt_delete_comments);
            $delete_recipe_query = "DELETE FROM recipe WHERE idrec = ? AND user_id = ?";
            $stmt_delete = mysqli_prepare($dbc, $delete_recipe_query);
            mysqli_stmt_bind_param($stmt_delete, "ii", $recipe_id_to_delete, $current_user_id);
            if (mysqli_stmt_execute($stmt_delete)) {
                $_SESSION['recipe_action_status'] = "Recipe successfully deleted!";
                $_SESSION['recipe_action_type'] = "success";
            } else {
                $_SESSION['recipe_action_status'] = "Error deleting recipe: " . mysqli_stmt_error($stmt_delete);
                $_SESSION['recipe_action_type'] = "error";
            }
            mysqli_stmt_close($stmt_delete);
        } else {
            $_SESSION['recipe_action_status'] = "You are not authorized to delete this recipe.";
            $_SESSION['recipe_action_type'] = "error";
        }
    } else {
        $_SESSION['recipe_action_status'] = "Recipe not found or already deleted.";
        $_SESSION['recipe_action_type'] = "error";
    }
    mysqli_stmt_close($stmt_check);
} else {
    $_SESSION['recipe_action_status'] = "Invalid recipe ID for deletion.";
    $_SESSION['recipe_action_type'] = "error";
}

mysqli_close($dbc);
header("Location: profile.php");
exit();
