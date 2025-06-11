<?php
session_start();
require_once('connect.php'); 

$page_title_text = 'Recipe Details - Happy Pot';
$recipe_details = null;
$recipe_user_details = null;
$comments_data = [];
$recipeId_from_url = null; 
$comment_submission_error = '';


if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $recipeId_from_url = $_GET['id'];
}

if (isset($_POST['submit_comment']) && isset($_SESSION['user_id']) && $recipeId_from_url) {
    $recipeId_safe_for_comment_action = mysqli_real_escape_string($dbc, $recipeId_from_url);
    $comment_text = mysqli_real_escape_string($dbc, $_POST['comment']);
    $session_user_id = $_SESSION['user_id'];

    if (!empty(trim($comment_text))) {
        $insert_comment_query = "INSERT INTO comment (comm, recipe_idrec, user_id) VALUES ('$comment_text', '$recipeId_safe_for_comment_action', '$session_user_id')";
        $insert_comment_result = mysqli_query($dbc, $insert_comment_query);
        if ($insert_comment_result) {
            $_SESSION['recipe_action_status'] = "Your comment has been posted successfully!";
            $_SESSION['recipe_action_type'] = "success";
            header("Location: display.php?id=$recipeId_safe_for_comment_action&action=comment_posted#comments-section");
            exit();
        } else {
            $comment_submission_error = "Error submitting comment: " . mysqli_error($dbc);
        }
    } else {
        $comment_submission_error = "Comment cannot be empty.";
    }
}


$alert_script = '';
if (isset($_GET['action']) && isset($_SESSION['recipe_action_status']) && isset($_SESSION['recipe_action_type'])) {
    $action_performed = $_GET['action'];
    if ($action_performed == 'updated' || $action_performed == 'posted' || $action_performed == 'comment_posted') {
        $status_message = htmlspecialchars($_SESSION['recipe_action_status']);
        $status_type = htmlspecialchars($_SESSION['recipe_action_type']);
        
        $id_for_url_cleanup = isset($recipeId_from_url) ? $recipeId_from_url : '';

        $alert_script = "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    if(typeof swal === 'function') {
                        swal({
                            title: '" . ucfirst($status_type) . "!',
                            text: '" . addslashes($status_message) . "',
                            icon: '" . $status_type . "',
                            button: 'OK'
                        }).then(function() {
                            if (window.history.replaceState) {
                                const cleanUrl = window.location.protocol + '//' + window.location.host + window.location.pathname + '?id=" . $id_for_url_cleanup . "';
                                window.history.replaceState({ path: cleanUrl }, '', cleanUrl);
                            }
                        });
                    }
                });
              </script>";
        
        unset($_SESSION['recipe_action_status']);
        unset($_SESSION['recipe_action_type']);
    }
}

if ($recipeId_from_url) {
    $recipeId_safe = mysqli_real_escape_string($dbc, $recipeId_from_url); 

    $query_recipe = "SELECT idrec, title, img, time, user_id, instructions, ingredients FROM recipe WHERE idrec = '$recipeId_safe'";
    $result_recipe = mysqli_query($dbc, $query_recipe);

    if ($result_recipe && mysqli_num_rows($result_recipe) > 0) {
        $recipe_details = mysqli_fetch_assoc($result_recipe);
        $page_title_text = htmlspecialchars($recipe_details['title']) . ' - Happy Pot';

        $userIdFromRecipe = mysqli_real_escape_string($dbc, $recipe_details['user_id']);
        $query_user = "SELECT fname, lname FROM user WHERE id = '$userIdFromRecipe'";
        $result_user = mysqli_query($dbc, $query_user);
        $recipe_user_details = ($result_user && mysqli_num_rows($result_user) > 0) ? mysqli_fetch_assoc($result_user) : ['fname' => 'Unknown', 'lname' => 'User'];

        $query_comments = "SELECT comm, user_id FROM comment WHERE recipe_idrec = '$recipeId_safe' ORDER BY idcomment DESC";
        $result_comments = mysqli_query($dbc, $query_comments);
        if ($result_comments) {
            while ($comment_row = mysqli_fetch_assoc($result_comments)) {
                $commentUserId = mysqli_real_escape_string($dbc, $comment_row['user_id']);
                $commentUserQuery = "SELECT fname, lname FROM user WHERE id = '$commentUserId'";
                $commentUserResult = mysqli_query($dbc, $commentUserQuery);
                $commentAuthorDetails = ($commentUserResult && mysqli_num_rows($commentUserResult) > 0) ? mysqli_fetch_assoc($commentUserResult) : ['fname' => 'User', 'lname' => ''];
                $comments_data[] = [
                    'author_fname' => $commentAuthorDetails['fname'],
                    'author_lname' => $commentAuthorDetails['lname'],
                    'comment_text' => $comment_row['comm']
                ];
            }
        }
    } else {
        $page_title_text = 'Recipe Not Found - Happy Pot';
    }
} else {
    if (empty($alert_script)) { 
        $page_title_text = 'Error - Recipe ID Not Provided - Happy Pot';
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title_text; ?></title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://kit.fontawesome.com/ff00f0a9ab.js" crossorigin="anonymous"></script>
    <link rel="icon" type="image/ico" href="images/favi.ico">

    <style>
        html, body.display-page-body { height: 100%; margin: 0; padding: 0; }
        body.display-page-body {
            display: flex; flex-direction: column; min-height: 100vh;
            background-color: #ccf1ff; font-family: "Montserrat", sans-serif;
        }
        .display-site-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 15px 30px; background-color: #fff;
            border-bottom: 1px solid #cccccc; width: 100%;
            box-sizing: border-box; flex-shrink: 0;
        }
        .display-site-header .logo-link { display: flex; align-items: center; text-decoration: none; }
        .display-site-header .logo-image { height: 50px; width: auto; }
        .display-site-header .logo-text {
            padding-left: 10px; color: #4dc9f7; font-size: 38px;
            font-weight: bold; margin: 0;
        }
        .display-header-nav { display: flex; align-items: center; }
        .display-header-nav .usermenu-greeting,
        .display-header-nav .btn,
        .display-header-nav form { margin-left: 15px; }
        .display-header-nav .usermenu-greeting {font-weight:bold; color:#555;}
        .display-header-nav .username {color:#4dc9f7;}
        .display-header-nav .btn, .display-header-nav .logoutbtn {
            padding: 8px 15px; font-size:0.9em; border-radius:8px; text-decoration:none; cursor:pointer;
            border: 1px solid #ccc; background-color: #e9e9e9; color: #333;
        }
        .display-header-nav .btn.btnhov:hover, .display-header-nav .logoutbtn.btnhovel:hover {
            background-color: #d0d0d0;
        }
        .display-main-content-wrapper {
            flex-grow: 1; width: 100%; display: flex;
            justify-content: center; align-items: flex-start; 
            padding: 30px 15px; box-sizing: border-box;
        }
        .display-content-box {
            background-color: #fff; padding: 30px 35px; border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1); width: 90%;
            max-width: 1280px; 
            box-sizing: border-box;
            display: flex;
            flex-wrap: wrap; 
            gap: 30px; 
        }
        .recipe-image-column {
            flex: 1 1 300px; 
            max-width: 350px; 
        }
        .recipe-image-detail { 
            width: 100%; height: auto;
            border-radius: 10px; border: 1px solid #eee; display: block; 
        }
        .recipe-details-column {
            flex: 2 1 500px; 
        }
        .recipe-title-main {
            font-size: 2.2em; color: #333; text-align: left; 
            margin-top: 0; margin-bottom: 8px; font-weight: 600;
        }
        .recipe-meta {
            text-align: left; color: #555; font-size: 0.95em; margin-bottom: 20px;
        }
        .recipe-meta .fa-clock { margin-right: 5px; }
        .recipe-meta span.author-name { margin-left: 10px; padding-left:10px; border-left:1px solid #ddd; }
        .recipe-section { margin-bottom: 25px; }
        .recipe-section h3 {
            font-size: 1.3em; color: #4dc9f7; margin-bottom: 10px;
            border-bottom: 2px solid #f0f0f0; padding-bottom: 5px;
        }
        .recipe-section p, .recipe-section ul { font-size: 0.95em; line-height: 1.7; color: #444; }
        .recipe-section ul { padding-left: 20px; margin-top:0; }
        .comment-item { border-bottom: 1px dashed #eee; padding-bottom:12px; margin-bottom:12px; }
        .comment-item:last-child { border-bottom: none; margin-bottom:0; }
        .comment-author { font-weight: bold; color: #007bff; margin-bottom: 4px; font-size:0.9em; }
        .comment-text { font-size: 0.9em; color: #555; line-height:1.5; }
        .no-comments-text { color: #777; font-style: italic; font-size:0.9em; }
        .add-comment-form textarea {
            width: 100%; padding: 10px; margin-bottom: 8px; box-sizing: border-box;
            border: 1px solid #ccc; border-radius: 8px; font-family: "Montserrat", sans-serif;
            min-height: 70px; font-size:0.9em;
        }
        .add-comment-form #charCountContainer { text-align:right; font-size:0.8em; color:#888; margin-bottom:10px;}
        .add-comment-form .comment-submit-btn {
            padding: 9px 18px; font-size: 0.95em; border-radius: 8px;
            background-color: #4dc9f7; color: white; border: none; cursor: pointer;
            transition: background-color 0.2s ease;
        }
        .add-comment-form .comment-submit-btn:hover { background-color: #36a2c9; }
        .comment-submission-error-text { color: red; font-size:0.9em; margin-top:10px;}
        .back-button-container { 
            flex-basis:100%; 
            text-align:center; 
            margin-top:20px; 
        }
        .back-button {
            padding: 10px 25px; text-decoration:none; background-color:#f0f0f0; color:#333;
            border-radius:8px; border:1px solid #ccc; font-size:0.95em;
        }
        .back-button:hover { background-color: #e0e0e0; }
        @media (max-width: 767px) {
            .display-content-box { flex-direction: column; gap: 0; }
            .recipe-image-column, .recipe-details-column { flex-basis: 100%; max-width: 100%; }
            .recipe-image-column { margin-bottom: 20px; }
            .recipe-title-main, .recipe-meta { text-align:center; } 
        }
         .error-page-message { 
            text-align:center; font-size:1.2em; color:red; width:100%; padding: 20px 0;
        }
    </style>
    <?php echo $alert_script;?>
</head>
<body class="display-page-body">

    <header class="display-site-header">
        <a href="index.php" class="logo-link">
            <img class="logo-image" src="images/logo.png" alt="Happy Pot Logo">
            <h1 class="logo-text">Happy Pot</h1>
        </a>
        <div class="display-header-nav">
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if (isset($_SESSION['username'])): ?>
                    <div class="usermenu-greeting">Welcome, <span class="username"><?php echo htmlspecialchars($_SESSION['username']); ?>!</span></div>
                <?php endif; ?>
                <button class="btn btnhov" type="button" onclick="location.href='profile.php'">Profile</button>
                <button class="btn btnhov" type="button" onclick="location.href='recipe.php'">Post a recipe</button>
                <form method="POST" action="logout.php" style="display:inline;">
                    <button class="logoutbtn btnhovel" type="submit" name="logout">Log-out</button>
                </form>
            <?php else: ?>
                <a href="login_page.php" class="btn btnhov">Login</a>
                <a href="register_page.php" class="btn btnhov">Sign Up</a>
            <?php endif; ?>
        </div>
    </header>

    <div class="display-main-content-wrapper">
        <div class="display-content-box">
            <?php if ($recipe_details): ?>
                <div class="recipe-image-column">
                    <img class="recipe-image-detail" src="<?php echo htmlspecialchars($recipe_details['img']); ?>" alt="<?php echo htmlspecialchars($recipe_details['title']); ?>">
                </div>

                <div class="recipe-details-column">
                    <h1 class="recipe-title-main"><?php echo htmlspecialchars($recipe_details['title']); ?></h1>
                    
                    <div class="recipe-meta">
                        <i class="fa-regular fa-clock"></i> <?php echo htmlspecialchars($recipe_details['time']); ?> mins
                        <span class="author-name">
                           By: <?php echo htmlspecialchars($recipe_user_details['fname']) . ' ' . htmlspecialchars($recipe_user_details['lname']); ?>
                        </span>
                    </div>

                    <div class="recipe-section ingredients-section">
                        <h3>Ingredients:</h3>
                        <p><?php echo nl2br(htmlspecialchars($recipe_details['ingredients'])); ?></p>
                    </div>

                    <div class="recipe-section instructions-section">
                        <h3>Instructions:</h3>
                        <p><?php echo nl2br(htmlspecialchars($recipe_details['instructions'])); ?></p>
                    </div>

                    <div id="comments-section" class="recipe-section comments-section">
                        <h3>Comments:</h3>
                        <?php if (!empty($comments_data)): ?>
                            <?php foreach ($comments_data as $comment): ?>
                                <div class="comment-item">
                                    <p class="comment-author"><?php echo htmlspecialchars($comment['author_fname']) . ' ' . htmlspecialchars($comment['author_lname']); ?> says:</p>
                                    <p class="comment-text"><?php echo nl2br(htmlspecialchars($comment['comment_text'])); ?></p>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="no-comments-text">No comments yet. Be the first to comment.</p>
                        <?php endif; ?>
                    </div>

                    <?php if (isset($_SESSION['user_id'])): ?>
                        <div class="recipe-section add-comment-section">
                            <h3>Add a Comment:</h3>
                            <?php if ($comment_submission_error): ?>
                                <p class="comment-submission-error-text"><?php echo $comment_submission_error; ?></p>
                            <?php endif; ?>
                            <form class="add-comment-form" method="post" action="display.php?id=<?php echo htmlspecialchars($recipeId_from_url); ?>#comments-section">
                                <textarea name="comment" placeholder="Write your comment here..." rows="4" maxlength="150" required></textarea>
                                <div id="charCountContainer">
                                    <span id="charCount">0</span>/150
                                </div>
                                <input type="submit" name="submit_comment" class="comment-submit-btn" value="Submit Comment">
                            </form>
                        </div>
                    <?php else: ?>
                        <div class="recipe-section">
                            <p>Please <a href="login_page.php?redirect=display.php?id=<?php echo htmlspecialchars($recipeId_from_url); ?>">login</a> to add a comment.</p>
                        </div>
                    <?php endif; ?>
                </div> 
                
                <div class="back-button-container">
                    <a href="dashboard.php" class="back-button">Go Back to Recipes</a>
                </div>

            <?php elseif ($recipeId_from_url): ?>
                <p class="error-page-message">Error: Recipe not found.</p>
                 <div class="back-button-container">
                    <a href="dashboard.php" class="back-button">Go Back to Recipes</a>
                </div>
            <?php else: ?>
                <p class="error-page-message">Error: Recipe ID not provided.</p>
                 <div class="back-button-container">
                    <a href="dashboard.php" class="back-button">Go Back to Recipes</a>
                </div>
            <?php endif; ?>

        </div>
    </div>
    
<script>
    const commentTextarea = document.querySelector("textarea[name='comment']");
    const charCountSpan = document.getElementById("charCount");
    if (commentTextarea && charCountSpan) {
        commentTextarea.addEventListener("input", function() {
            const maxLength = 150;
            let currentLength = commentTextarea.value.length;
            if (currentLength > maxLength) {
                commentTextarea.value = commentTextarea.value.substring(0, maxLength);
                currentLength = maxLength; 
            }
            charCountSpan.textContent = currentLength;
        });
        if(commentTextarea.value.length > 0){ 
            charCountSpan.textContent = commentTextarea.value.length;
        } else {
             charCountSpan.textContent = '0';
        }
    }
</script>
</body>
</html>
<?php
if(isset($dbc)) mysqli_close($dbc);
?>