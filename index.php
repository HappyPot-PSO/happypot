<?php
session_start();

// Register Success
if (isset($_SESSION["registration_success"]) && $_SESSION["registration_success"]) {
    echo '<script>
    document.addEventListener("DOMContentLoaded", function() {
        swal("Account created successfully!", "You can now log in.", "success");
    });
    </script>';
    // Reset the session variable to prevent showing the alert again
    $_SESSION["registration_success"] = false;
}

// User already exists
if (isset($_SESSION["register_error"]) && $_SESSION["register_error"]) {
    echo '<script>
    document.addEventListener("DOMContentLoaded", function() {
        swal("User already exists!", "Use a different email to sign up", "error");
    });
    </script>';
    $_SESSION["register_error"] = false;
}

// Log-in Error
if (isset($_SESSION["login_error"]) && $_SESSION["login_error"] == true) {
	echo '<script>
    document.addEventListener("DOMContentLoaded", function() {
        swal("Sorry!", "Incorrect password.", "error");
    });
    </script>';
    $_SESSION["login_error"] = false;
}

// Log-in Error 1
if (isset($_SESSION["login_error1"]) && $_SESSION["login_error1"] == true) {
    echo '<script>
    document.addEventListener("DOMContentLoaded", function() {
        swal("Sorry!", "User doesn\'t exist.", "error");
    });
    </script>';
    $_SESSION["login_error1"] = false;
}

// Recipe post succes
if (isset($_SESSION["post_success"]) && $_SESSION["post_success"]) {
    echo '<script>
    window.onload = function() {
        swal("Post successful", "You can now view your post or make another one!", "success");
    };
    </script>';
    // Reset the session variable to prevent showing the alert again
    $_SESSION["post_success"] = false;
}

// Include php
if (!isset($_SESSION['user_id'])) {
	$title = 'Happy Pot';
	include('header.php');
    include('login.php');
    include('register.php');
} else {
	$title = 'Welcome Back !';
	include('header.php');
    include('logout.php');
    include('usermenu.php');
}

?>


<H1 class="title">Recipes</H1>
<!-- Main Content -->
<main>
    <?php
    // Fetch recipes from the "recipe" table
    $query = "SELECT idrec, title, img, time, user_id FROM recipe";
    $result = mysqli_query($dbc, $query);

    // Check if there are any recipes
    if (mysqli_num_rows($result) == 0) {
        echo '<p class="norec">No recipes posted yet. Be the first one to post!</p>';
    } else {
        // Display recipes in a table format
        echo '<table>';
        $count = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            // Fetch user details based on user_id
            $query1 = "SELECT fname, lname FROM user WHERE id=" . $row['user_id'];
            $userResult = mysqli_query($dbc, $query1);
            $userRow = mysqli_fetch_assoc($userResult);

            // Start a new row after every third recipe
            if ($count % 3 == 0) {
                echo '<tr>';
            }

            echo '<td>';
            echo '<a href="display.php?id=' . $row['idrec'] . '"><img class="postimg" src="' . $row['img'] . '" alt="Recipe Image"></a><br>';
            echo '<a href="display.php?id=' . $row['idrec'] . '" class="postitle"><strong>' . $row['title'] . '</strong></a><br>';
            echo ' <i class="fa-regular fa-clock" style="color: #4dc9f7;"></i> ' . $row['time'] . ' mins<br>';
            echo 'By ' . $userRow['fname'] . " " . $userRow['lname'] . '<br>';
            echo '</td>';

            // End the row after every third recipe
            if ($count % 3 == 2) {
                echo '</tr>';
            }

            $count++;
        }

        // Close the last row if there are fewer than three recipes
        if ($count % 3 != 0) {
            echo '</tr>';
        }

        echo '</table>';
    }

    // Close the database connection
    mysqli_close($dbc);
    ?>
</main>

<?php include('footer.php'); ?>