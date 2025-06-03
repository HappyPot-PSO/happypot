<?php 
    //Post a recipe page
    session_start();
    // Check if user is loged in
    if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])){
        // Redirect to index.php if not loged in
        header("Location: index.php");
    } else{
        // Change Title
        $title = 'Post a Recipe !';
        include('header.php');
        include('logout.php');
        include('recipeform.php');
        include('footer.php');
    }
?>