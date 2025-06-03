<?php
session_start();

// Title
$title = 'Happy Pot';

// Include php
if (!isset($_SESSION['user_id'])) {
	include('header.php');
    include('login.php');
    include('back.php');
} else {
	include('header.php');
    include('logout.php');
    include('back.php');
}

    // Check if a recipe ID is provided in the URL
    if (isset($_GET['id'])) {
        $recipeId = $_GET['id'];

        // Fetch recipe details based on the provided ID
        $query = "SELECT idrec, title, img, time, user_id, instructions, ingredients FROM recipe WHERE idrec = $recipeId";
        $result = mysqli_query($dbc, $query);
        $row = mysqli_fetch_assoc($result);

        if ($row) {
            // Fetch user details based on user_id
            $query1 = "SELECT fname, lname FROM user WHERE id = " . $row['user_id'];
            $userResult = mysqli_query($dbc, $query1);
            $userRow = mysqli_fetch_assoc($userResult);

            // Display the recipe details
            echo '<div class="container">';
            echo '<img class="postimg1" src="' . $row['img'] . '" alt="Recipe Image"><br>';
            echo '<div class="segment">';
            echo '<h2>' . $row['title'] . '</h2>';
            echo '<i class="fa-regular fa-clock" style="color: #4dc9f7;"></i> ' . $row['time'] . ' mins<br>';
            echo 'By ' . $userRow['fname'] . ' ' . $userRow['lname'] . '<br>';
            echo '</div>';
            echo '<div class="segment">';
            echo '<h3>Ingredients:</h3>';
            echo '<p>' . $row['ingredients'] . '</p>';
            echo '</div>';

            echo '<div class="segment">';
            echo '<h3>Instructions:</h3>';
            echo '<p>' . $row['instructions'] . '</p>';
            echo '</div>';

            // Display existing comments for the recipe
            echo '<div class="segment">';
            echo '<h3 class="comtitle">Comments:</h3>';
            $commentQuery = "SELECT comm, user_id FROM comment WHERE recipe_idrec = $recipeId";
            $commentResult = mysqli_query($dbc, $commentQuery);

            // Check if there are any comments
            if (mysqli_num_rows($commentResult) == 0) {
                echo '<p>No comments yet. Be the first to comment.</p>';
            } else {
                while ($commentRow = mysqli_fetch_assoc($commentResult)) {
                    $commentUserQuery = "SELECT fname, lname FROM user WHERE id = " . $commentRow['user_id'];
                    $commentUserResult = mysqli_query($dbc, $commentUserQuery);
                    $commentUserRow = mysqli_fetch_assoc($commentUserResult);

                    echo '<p class="comment">'. $commentUserRow['fname'] . ' ' . $commentUserRow['lname'] . ' says: </p>';
                    echo '<p class="comment1">' . $commentRow['comm'] . '</p>';
                }
            }

            echo '</div>';

            // Display comment form for logged-in users
            if (isset($_SESSION['user_id'])) {
                echo '<div class="segment">';
                echo '<h3>Add a Comment:</h3>';
                echo '<form method="post" action="">';
                echo '<textarea name="comment" class="addcomment" rows="3" cols="95" maxlength="150"></textarea><br>';
                echo '<span id="charCount">0/150</span>'; // Counter for character count
                echo '<input type="submit" name="submit" class="commentbtn" value="Submit">';
                echo '</form>';
                echo '</div>';

                // Handle comment form submission
                if (isset($_POST['submit'])) {
                    $comment = $_POST['comment'];
                    $userId = $_SESSION['user_id'];

                    // Insert the new comment into the database
                    $insertQuery = "INSERT INTO comment (comm, recipe_idrec, user_id) VALUES ('$comment', $recipeId, $userId)";
                    mysqli_query($dbc, $insertQuery);

                    // Redirect back to the recipe page
                    header("Location: display.php?id=$recipeId");
                    exit();
                }

                // JavaScript code to count and display remaining characters
                echo '<script>
                    const commentTextarea = document.querySelector("textarea[name=\'comment\']");
                    const charCount = document.getElementById("charCount");

                    commentTextarea.addEventListener("input", function() {
                        const maxLength = 150;
                        const currentLength = commentTextarea.value.length;
                        charCount.textContent = currentLength + "/" + maxLength;

                        if (currentLength % 82 === 0 && currentLength !== 0) {
                            const newCommentValue = commentTextarea.value + "\\n";
                            commentTextarea.value = newCommentValue;
                        }
                    });
                </script>';
            }

            echo '</div>';
        } else {
            // Recipe not found
            echo 'Error: Recipe not found.';
        }
    } else {
        // No recipe ID provided, show an error message or redirect to the index page
        echo 'Error: Recipe ID not provided.';
    }

// Include Footer
include('footer.php');
?>
?>



