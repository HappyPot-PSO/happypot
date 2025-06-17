<?php
session_start();
require_once('connect.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login_page.php");
    exit();
}

$recipe_data = null;
$page_title = "Edit Recipe - Happy Pot";
$recipe_id_to_edit = null;
$error_message = '';

if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $recipe_id_to_edit = $_GET['id'];
    $current_user_id = $_SESSION['user_id'];

    // MODIFICATION 1: Add 'category' to the SELECT query
    $query_fetch_recipe = "SELECT idrec, title, img, time, ingredients, instructions, category FROM recipe WHERE idrec = ? AND user_id = ?";
    $stmt_fetch = mysqli_prepare($dbc, $query_fetch_recipe);
    
    if ($stmt_fetch === false) {
        $error_message = "Database prepare error: " . $dbc->error;
    } else {
        mysqli_stmt_bind_param($stmt_fetch, "ii", $recipe_id_to_edit, $current_user_id);
        mysqli_stmt_execute($stmt_fetch);
        $result_fetch = mysqli_stmt_get_result($stmt_fetch);

        if ($result_fetch && mysqli_num_rows($result_fetch) > 0) {
            $recipe_data = mysqli_fetch_assoc($result_fetch);
            $page_title = "Edit: " . htmlspecialchars($recipe_data['title']) . " - Happy Pot";
        } else {
            $error_message = "Recipe not found or you are not authorized to edit this recipe.";
            $recipe_data = null;
        }
        mysqli_stmt_close($stmt_fetch);
    }
} else {
    $error_message = "Invalid recipe ID provided for editing.";
}

// Handle SweetAlert messages from recipe_upload.php redirects
$alert_script = '';
if (isset($_SESSION['recipe_action_status']) && isset($_SESSION['recipe_action_type'])) {
    $status_message = htmlspecialchars($_SESSION['recipe_action_status']);
    $status_type = htmlspecialchars($_SESSION['recipe_action_type']);
    
    $alert_script = "<script>
        document.addEventListener('DOMContentLoaded', function() {
            if(typeof swal === 'function') {
                swal({
                    title: '" . ucfirst($status_type) . "!',
                    text: '" . addslashes($status_message) . "',
                    icon: '" . $status_type . "',
                    button: 'OK'
                });
            }
        });
    </script>";
    unset($_SESSION['recipe_action_status']);
    unset($_SESSION['recipe_action_type']);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?></title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://kit.fontawesome.com/ff00f0a9ab.js" crossorigin="anonymous"></script>
    <link rel="icon" type="image/ico" href="images/favi.ico">

    <style>
        html, body.edit-recipe-page-body {
            height: 100%; margin: 0; padding: 0;
        }
        body.edit-recipe-page-body {
            display: flex; flex-direction: column; min-height: 100vh;
            background-color: #ccf1ff; font-family: "Montserrat", sans-serif;
        }
        .edit-recipe-site-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 15px 30px; background-color: #fff;
            border-bottom: 1px solid #cccccc; width: 100%;
            box-sizing: border-box; flex-shrink: 0;
        }
        .edit-recipe-site-header .logo-link { display: flex; align-items: center; text-decoration: none; }
        .edit-recipe-site-header .logo-image { height: 50px; width: auto; }
        .edit-recipe-site-header .logo-text {
            padding-left: 10px; color: #4dc9f7; font-size: 38px;
            font-weight: bold; margin: 0;
        }
        .edit-recipe-header-nav {
            display: flex;
            align-items: center;
        }

        .edit-recipe-header-nav .usermenu-greeting {
            font-weight: bold;
            color: #555;
            margin-right: 15px; 
        }
        .edit-recipe-header-nav .username {
            color: #4dc9f7;
        }

        .edit-recipe-header-nav .btn, .edit-recipe-header-nav .logoutbtn {
            padding: 8px 15px; font-size:0.9em; border-radius:8px; text-decoration:none; cursor:pointer;
            border: 1px solid #ccc; background-color: #e9e9e9; color: #333;
        }

        .edit-recipe-header-nav .btn {
            margin-left: 15px; 
        }

        .edit-recipe-header-nav form {
            margin-left: 10px; 
        }

        .edit-recipe-header-nav .btn.btnhov:hover, .edit-recipe-header-nav .logoutbtn.btnhovel:hover {
            background-color: #d0d0d0;
        }

        .edit-recipe-main-content-wrapper {
            flex-grow: 1; width: 100%; display: flex;
            justify-content: center; align-items: flex-start;
            padding: 30px 15px; box-sizing: border-box;
        }

        .edit-recipe-content-box {
            background-color: #fff; padding: 30px 40px; border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1); width: 95%;
            max-width: 1400px;
            box-sizing: border-box;
        }
        .edit-recipe-content-box .form-title {
            text-align: center; color: #4dc9f7; font-size: 2em;
            margin-top: 0; margin-bottom: 25px;
        }
        .edit-recipe-content-box #error-edit-recipe {
            min-height: 20px; margin-bottom: 15px; color: #D8000C;
            background-color: #FFD2D2; padding: 8px; border-radius: 5px;
            font-size: 0.9em; text-align: center; display: none;
        }
        .edit-recipe-content-box #error-edit-recipe:not(:empty) { display: block; }

        .edit-recipe-content-box form p {
            margin-bottom: 15px; display: flex; flex-wrap: wrap; align-items: center;
        }
        .edit-recipe-content-box form label {
            font-weight: 500; color: #333; margin-bottom: 5px;
            display: block; width: 100%;
        }
        .edit-recipe-content-box .recipebox,
        .edit-recipe-content-box .txtarea,
        .edit-recipe-content-box select,
        .edit-recipe-content-box input[type="file"] {
            width: 100%; padding: 10px 12px; box-sizing: border-box;
            border: 1px solid #ccc; border-radius: 8px;
            font-size: 0.95em; font-family: "Montserrat", sans-serif;
        }
        .edit-recipe-content-box .current-image-preview {
            display: block; max-width: 200px; max-height: 150px;
            margin-top: 5px; margin-bottom:10px; border-radius: 5px; border: 1px solid #eee;
        }
        .edit-recipe-content-box .txtarea { min-height: 100px; resize: vertical; }
        .edit-recipe-content-box input[type="file"] { padding: 7px; }
        .edit-recipe-content-box #ingr-counter-edit,
        .edit-recipe-content-box #instr-counter-edit {
            font-size: 0.85em; color: #777; text-align: right;
            display: block; width: 100%; margin-top: 3px; margin-bottom: 15px;
        }
        .edit-recipe-content-box .button-group {
            margin-top: 30px; display: flex; justify-content: flex-end; gap: 10px;
        }
        .edit-recipe-content-box .postbtn,
        .edit-recipe-content-box .cancelbtn {
            padding: 10px 25px; font-size: 1em; font-weight: bold;
            border-radius: 8px; border: none; cursor: pointer;
            transition: background-color 0.2s ease, transform 0.1s ease;
        }
        .edit-recipe-content-box .postbtn.btnhov {
            background-color: #a3e6ff; color: #334752; border: 1px solid #cccccc;
        }
        .edit-recipe-content-box .postbtn.btnhov:hover { background-color: #4dc9f7; transform: translateY(-1px); }
        .edit-recipe-content-box .cancelbtn.btnhovcl {
            background-color: #e7e6e6; color: #333; border: 1px solid #cccccc;
        }
        .edit-recipe-content-box .cancelbtn.btnhovcl:hover { background-color: #afafaf; transform: translateY(-1px); }
        .error-message-page {
            text-align:center; font-size:1.2em; color:red; padding: 20px;
        }

        /* SweetAlert custom styles for centering text */
        .swal-text {
            text-align: center; 
            margin-top: 15px; 
            margin-bottom: 15px;
            font-size: 16px; 
        }

    </style>
    <?php echo $alert_script; ?>
</head>
<body class="edit-recipe-page-body">

    <header class="edit-recipe-site-header">
        <a href="index.php" class="logo-link">
            <img class="logo-image" src="images/logo.png" alt="Happy Pot Logo">
            <h1 class="logo-text">Happy Pot</h1>
        </a>
        <div class="edit-recipe-header-nav">
            <?php
            $username_display_header = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'User';
            echo '<div class="usermenu-greeting">Welcome, <span class="username">' . $username_display_header . '!</span></div>';
            echo '<button class="btn btnhov" type="button" onClick="location.href=\'profile.php\'">Profile</button>';
            echo '<button class="btn btnhov" type="button" onClick="location.href=\'recipe.php\'">Post a recipe</button>';
            echo '<form method="POST" action="logout.php" style="display:inline;">';
            echo '<button class="logoutbtn btnhovel" type="submit" name="logout">Log-out</button>';
            echo '</form>';
            ?>
        </div>
    </header>

    <div class="edit-recipe-main-content-wrapper">
        <div class="edit-recipe-content-box">

            <h2 class="form-title">Edit Your Recipe</h2>
            <?php if ($error_message): ?>
                <p class="error-message-page"><?php echo $error_message; ?></p>
                <div style="text-align:center; margin-top:20px;">
                    <a href="profile.php" style="padding:10px 20px; background-color:#ddd; color:#333; text-decoration:none; border-radius:5px;">Go to Profile</a>
                </div>
            <?php elseif ($recipe_data): ?>
                <form id="editrecipeform" method="POST" action="recipe_upload.php" enctype="multipart/form-data">
                    <input type="hidden" name="recipe_id" value="<?php echo htmlspecialchars($recipe_data['idrec']); ?>">
                    <input type="hidden" name="existing_image" value="<?php echo htmlspecialchars($recipe_data['img']); ?>">

                    <p>
                        <label for="name">Recipe name:</label>
                        <input id="name" class="recipebox" type="text" name="name" required value="<?php echo htmlspecialchars($recipe_data['title']); ?>">
                    </p>
                    <p>
                        <label for="img">Recipe image (leave blank to keep current image):</label>
                        Current image: <br>
                        <img src="<?php echo htmlspecialchars($recipe_data['img']); ?>" alt="Current Recipe Image" class="current-image-preview">
                        <input id="img" type="file" name="img" accept="image/*">
                    </p>
                    
                    <p>
                        <label for="category">Category:</label>
                        <select id="category" name="category" size="1" required>
                            <option value="">Select Category</option>
                            <option value="food" <?php echo ($recipe_data['category'] == 'food') ? 'selected' : ''; ?>>Food</option>
                            <option value="drink" <?php echo ($recipe_data['category'] == 'drink') ? 'selected' : ''; ?>>Drink</option>
                        </select>
                    </p>
                    
                    <p>
                        <label for="time">Prep time:</label>
                        <select id="time" name="time" size="1" required>
                            <option value="">Pick Time</option>
                            <?php
                            $times = [5, 10, 15, 30, 45, 60, 90, 120];
                            foreach ($times as $time_option) {
                                $selected = ($recipe_data['time'] == $time_option) ? 'selected' : '';
                                echo "<option value=\"$time_option\" $selected>$time_option min</option>";
                            }
                            ?>
                        </select>
                    </p>

                    <p>
                        <label for="ingr-edit">Ingredients:</label>
                        <textarea id="ingr-edit" class="txtarea" name="ingredients" cols="100" rows="5" required><?php echo htmlspecialchars($recipe_data['ingredients']); ?></textarea>
                        <span id="ingr-counter-edit"></span>
                    </p>

                    <p>
                        <label for="instr-edit">Instructions:</label>
                        <textarea id="instr-edit" class="txtarea" name="instructions" cols="100" rows="10" required><?php echo htmlspecialchars($recipe_data['instructions']); ?></textarea>
                        <span id="instr-counter-edit"></span>
                    </p>

                    <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($_SESSION['user_id']); ?>">

                    <div class="button-group">
                        <button id="cancelEditButton" class="cancelbtn btnhovcl" type="button" name="cancel">Cancel</button>
                        <input type="hidden" name="action" value="update_recipe">
                        <button id="updateRecipeButton" class="postbtn btnhov" type="button" name="update">Update Recipe</button>
                    </div>
                </form>
            <?php else: ?>
                   <p class="error-message-page">Could not load recipe data for editing.</p>
                     <div style="text-align:center; margin-top:20px;">
                    <a href="profile.php" style="padding:10px 20px; background-color:#ddd; color:#333; text-decoration:none; border-radius:5px;">Go to Profile</a>
                </div>
            <?php endif; ?>

        </div>
    </div>

<script>
    const ingrTextareaEdit = document.getElementById("ingr-edit");
    const instrTextareaEdit = document.getElementById("instr-edit");
    const ingrCharCountEdit = document.getElementById("ingr-counter-edit");
    const instrCharCountEdit = document.getElementById("instr-counter-edit");

    function updateCharCount(textarea, counterElement, maxLength) {
        if (textarea && counterElement) {
            let currentLength = textarea.value.length;
            if (currentLength > maxLength) {
                textarea.value = textarea.value.slice(0, maxLength);
                currentLength = maxLength;
            }
            counterElement.textContent = currentLength + "/" + maxLength;
        }
    }

    if (ingrTextareaEdit) {
        ingrTextareaEdit.addEventListener("input", function() {
            updateCharCount(ingrTextareaEdit, ingrCharCountEdit, 500);
        });
        updateCharCount(ingrTextareaEdit, ingrCharCountEdit, 500);
    }

    if (instrTextareaEdit) {
        instrTextareaEdit.addEventListener("input", function() {
            updateCharCount(instrTextareaEdit, instrCharCountEdit, 1000);
        });
        updateCharCount(instrTextareaEdit, instrCharCountEdit, 1000);
    }

    const editRecipeForm = document.getElementById('editrecipeform');
    const updateRecipeButton = document.getElementById('updateRecipeButton');
    const cancelEditButton = document.getElementById('cancelEditButton'); 
    const errorElement = document.getElementById('error-edit-recipe'); // Get the error element


    if (updateRecipeButton && editRecipeForm) {
        updateRecipeButton.addEventListener('click', (e) => {
            let messages = [];
            const name = document.getElementById('name');
            const category = document.getElementById('category'); // Get category element
            const time = document.getElementById('time');
            const ingr = document.getElementById('ingr-edit');
            const instr = document.getElementById('instr-edit');

            if (!name.value.trim()) {
                messages.push('Recipe name is required');
            } else if (name.value.length > 20) { 
                messages.push('Recipe name can\'t be longer than 20 characters');
            }

            // MODIFICATION 3: Add category validation
            if (!category.value || category.value === '') {
                messages.push('Category is required');
            }

            if (!time.value || time.value === '') messages.push('Prep time is required');

            if (!ingr.value.trim()) messages.push('Ingredients are required');
            else if (ingr.value.length > 500) messages.push('Ingredients can\'t be longer than 500 characters');

            if (!instr.value.trim()) messages.push('Instructions are required');
            else if (instr.value.length > 1000) messages.push('Instructions can\'t be longer than 1000 characters');

            if (messages.length > 0) {
                // Display error messages using SweetAlert
                swal({
                    title: 'Error!',
                    text: messages.join('\n'), // Join messages with newline
                    icon: 'error',
                    button: 'OK'
                });
                // No need to set errorElement.innerText and display: 'block' if using swal for all errors
            } else {
                // If all validations pass, show confirmation for update
                swal({
                    title: 'Confirm Update',
                    text: 'Are you sure you want to update this recipe?',
                    icon: 'warning',
                    buttons: {
                        cancel: 'No, cancel!',
                        confirm: {
                            text: 'Yes, update it!',
                            value: true,
                        },
                    },
                    dangerMode: true,
                }).then((willUpdate) => {
                    if (willUpdate) {
                        editRecipeForm.submit(); // Submit the form if confirmed
                    } else {
                        // User cancelled, do nothing, stay on the page
                    }
                });
            }
        });
    }

    // New event listener for the Cancel button
    if (cancelEditButton) {
        cancelEditButton.addEventListener('click', () => {
            swal({
                title: 'Confirm Cancel',
                text: 'Are you sure you want to cancel editing? Any unsaved changes will be lost.',
                icon: 'warning',
                buttons: {
                    cancel: 'No, stay here!',
                    confirm: {
                        text: 'Yes, go to Profile',
                        value: true,
                    },
                },
                dangerMode: true,
            }).then((willCancel) => {
                if (willCancel) {
                    window.location.href = 'profile.php'; // Redirect to profile if confirmed
                }
            });
        });
    }

    // Use a MutationObserver to apply text-align: center to the SweetAlert text
    const observer = new MutationObserver((mutationsList, observer) => {
        for (const mutation of mutationsList) {
            if (mutation.type === 'childList') {
                const swalText = document.querySelector('.swal-text');
                if (swalText) {
                    swalText.style.textAlign = 'center';
                    observer.disconnect(); // Disconnect after styling
                }
            }
        }
    });
    observer.observe(document.body, { childList: true, subtree: true });

</script>
</body>
</html>
<?php
if(isset($dbc)) mysqli_close($dbc);
?>