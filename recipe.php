<?php
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

$title = 'Post a New Recipe - Happy Pot'; 
$username_display = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'User';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
    <script src="https://kit.fontawesome.com/ff00f0a9ab.js" crossorigin="anonymous"></script>
    <link rel="icon" type="image/ico" href="images/favi.ico">

    <style>
        html, body.recipe-page-body { 
            height: 100%;
            margin: 0;
            padding: 0;
        }
        body.recipe-page-body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #ccf1ff;
            font-family: "Montserrat", sans-serif;
        }
        .recipe-site-header { 
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 15px 30px;
            background-color: #fff;
            border-bottom: 1px solid #cccccc;
            width: 100%;
            box-sizing: border-box;
            flex-shrink: 0;
        }
        .recipe-site-header .logo-link { display: flex; align-items: center; text-decoration: none; }
        .recipe-site-header .logo-image { height: 50px; width: auto; }
        .recipe-site-header .logo-text {
            padding-left: 10px; color: #4dc9f7; font-size: 38px;
            font-weight: bold; margin: 0;
        }
        .recipe-header-nav { display: flex; align-items: center; }

        /* Hapus aturan margin-left umum yang sebelumnya mencakup semua */
        /* .recipe-header-nav .usermenu-greeting,
        .recipe-header-nav .btn,
        .recipe-header-nav form { margin-left: 15px; } */

        .recipe-header-nav .usermenu-greeting {
            font-weight:bold;
            color:#555;
            margin-right: 15px; /* Memberi jarak ke tombol di kanannya */
        }
        .recipe-header-nav .username {color:#4dc9f7;}
        .recipe-header-nav .btn, .recipe-header-nav .logoutbtn {
            padding: 8px 15px; font-size:0.9em; border-radius:8px; text-decoration:none; cursor:pointer;
            border: 1px solid #ccc; background-color: #e9e9e9; color: #333;
        }

        /* Atur margin-left untuk tombol .btn */
        .recipe-header-nav .btn {
            margin-left: 10px;
        }

        /* Atur margin-left khusus untuk tombol logout agar tidak menempel ke tombol sebelumnya */
        .recipe-header-nav .logoutbtn {
            margin-left: 15px; /* Sesuaikan nilai ini untuk jarak yang diinginkan */
        }

        .recipe-header-nav .btn.btnhov:hover, .recipe-header-nav .logoutbtn.btnhovel:hover {
            background-color: #d0d0d0;
        }
        .recipe-main-content-wrapper {
            flex-grow: 1;
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            padding: 30px 15px;
            box-sizing: border-box;
        }
        .recipe-content-box { 
            background-color: #fff;
            padding: 30px 40px;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            width: 95%;
            max-width: 1400px;
            box-sizing: border-box;
        }
        .recipe-content-box .form-title {
            text-align: center;
            color: #4dc9f7;
            font-size: 2em;
            margin-top: 0;
            margin-bottom: 25px;
        }
        .recipe-content-box #error {
            min-height: 20px;
            margin-bottom: 15px;
            color: #D8000C;
            background-color: #FFD2D2;
            padding: 8px;
            border-radius: 5px;
            font-size: 0.9em;
            text-align: center;
            display: none;
        }
        .recipe-content-box #error:not(:empty) { display: block; }


        .recipe-content-box form p { 
            margin-bottom: 15px;
            display: flex;
            flex-wrap: wrap;
            align-items: center;
        }
        .recipe-content-box form label, .recipe-content-box form p:first-line { 
            font-weight: 500;
            color: #333;
            margin-bottom: 5px; 
            display: block; 
        }
        .recipe-content-box .recipebox,
        .recipe-content-box .txtarea,
        .recipe-content-box select,
        .recipe-content-box input[type="file"] {
            width: 100%;
            padding: 10px 12px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 0.95em;
            font-family: "Montserrat", sans-serif;
            margin-top: 5px;
        }
        .recipe-content-box .txtarea {
            min-height: 100px;
            resize: vertical; 
        }
         .recipe-content-box input[type="file"] {
            padding: 7px; 
        }

        .recipe-content-box #ingr-counter,
        .recipe-content-box #instr-counter {
            font-size: 0.85em;
            color: #777;
            text-align: right;
            display: block;
            margin-top: 3px;
            margin-bottom: 15px;
        }

        .recipe-content-box .button-group { 
            margin-top: 30px;
            display: flex;
            justify-content: flex-end; 
            gap: 10px;
        }
        .recipe-content-box .postbtn,
        .recipe-content-box .cancelbtn {
            padding: 10px 25px;
            font-size: 1em;
            font-weight: bold;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: background-color 0.2s ease, transform 0.1s ease;
        }
        .recipe-content-box .postbtn.btnhov {
            background-color: #a3e6ff;
            color: #334752;
            border: 1px solid #cccccc; 
        }
        .recipe-content-box .postbtn.btnhov:hover {
            background-color: #4dc9f7;
            transform: translateY(-1px);
        }
        .recipe-content-box .cancelbtn.btnhovcl { 
            background-color: #e7e6e6;
            color: #333;
            border: 1px solid #cccccc;
        }
        .recipe-content-box .cancelbtn.btnhovcl:hover {
            background-color: #afafaf;
            transform: translateY(-1px);
        }

        /* SweetAlert Custom CSS */
        .swal-modal {border-radius: 10px;}
        .swal-footer { text-align: center !important; }
        .swal-button-container { margin: 0 8px !important; }

    </style>
</head>
<body class="recipe-page-body">

    <header class="recipe-site-header"> 
        <a href="dashboard.php" class="logo-link">
            <img class="logo-image" src="images/logo.png" alt="Happy Pot Logo">
            <h1 class="logo-text">Happy Pot</h1>
        </a>
        <div class="recipe-header-nav">
            <?php
            echo '<div class="usermenu-greeting">Welcome, <span class="username">' . $username_display . '!</span></div>';
            echo '<button class="btn btnhov" type="button" onClick="location.href=\'profile.php\'">Profile</button>';
            // Modified Logout button
            echo '<button class="logoutbtn btnhovel" type="button" id="logoutConfirmBtn">Log-out</button>';
            ?>
        </div>
    </header>

    <div class="recipe-main-content-wrapper"> 
        <div class="recipe-content-box">
            
            <h2 class="form-title">Post a new recipe!</h2> 
            <div id="error"></div> 
            
            <form id="recipeform" method="POST" action="recipe_upload.php" enctype="multipart/form-data">
                <p>
                    <label for="name">Recipe name:</label>
                    <input id="name" class="recipebox" type="text" name="name" required>
                </p>
                <p>
                    <label for="img">Recipe image:</label>
                    <input id="img" type="file" name="img" accept="image/*" required>
                </p>
                <p>
                    <label for="category">Category:</label>
                    <select id="category" name="category" size="1" required>
                        <option selected value="">Select Category</option>
                        <option value="food">Food</option>
                        <option value="drink">Drink</option>
                    </select>
                </p>
                <p>
                    <label for="time">Prep time:</label>
                    <select id="time" name="time" size="1" required>
                        <option selected value="">Pick Time</option> 
                        <option value="5">5 min</option>
                        <option value="10">10 min</option>
                        <option value="15">15 min</option>
                        <option value="30">30 min</option>
                        <option value="45">45 min</option>
                        <option value="60">60 min</option>
                        <option value="90">90 min</option>
                        <option value="120">120 min</option>
                    </select>
                </p>

                <p>
                    <label for="ingr">Ingredients:</label>
                    <textarea id="ingr" class="txtarea" name="ingredients" cols="100" rows="5" required></textarea>
                    <span id="ingr-counter"></span>
                </p>

                <p>
                    <label for="instr">Instructions:</label>
                    <textarea id="instr" class="txtarea" name="instructions" cols="100" rows="10" required></textarea>
                    <span id="instr-counter"></span>
                </p>

                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($_SESSION['user_id']); ?>">

                <div class="button-group">
                    <button class="cancelbtn btnhovcl" type="button" onClick="location.href='dashboard.php'" name="cancel">Cancel</button> 
                    <input id="recipost" class="postbtn btnhov" type="submit" name="post" value="Post Recipe">
                </div>
            </form>

        </div> 
    </div> 

<script>
    const ingrTextarea = document.getElementById("ingr");
    const instrTextarea = document.getElementById("instr");
    const ingrCharCount = document.getElementById("ingr-counter");
    const instrCharCount = document.getElementById("instr-counter");

    if (ingrTextarea && ingrCharCount) {
        ingrTextarea.addEventListener("input", function() {
            const maxLength = 500; 
            let currentLength = ingrTextarea.value.length;
            if (currentLength > maxLength) {
                ingrTextarea.value = ingrTextarea.value.slice(0, maxLength);
                currentLength = maxLength;
            }
            ingrCharCount.textContent = currentLength + "/" + maxLength;
        });
        if(ingrCharCount) ingrCharCount.textContent = ingrTextarea.value.length + "/" + 500;
    }


    if (instrTextarea && instrCharCount) {
        instrTextarea.addEventListener("input", function() {
            const maxLength = 1000;
            let currentLength = instrTextarea.value.length;
            if (currentLength > maxLength) {
                instrTextarea.value = instrTextarea.value.slice(0, maxLength);
                currentLength = maxLength;
            }
            instrCharCount.textContent = currentLength + "/" + maxLength;
        });
        if(instrCharCount) instrCharCount.textContent = instrTextarea.value.length + "/" + 1000;
    }

    const recipeForm = document.getElementById('recipeform');
    const errorElement = document.getElementById('error'); 

    if (recipeForm && errorElement) {
        recipeForm.addEventListener('submit', (e) => {
            let messages = [];
            const name = document.getElementById('name');
            const img = document.getElementById('img');
            const category = document.getElementById('category');
            const time = document.getElementById('time');
            const ingr = document.getElementById('ingr');
            const instr = document.getElementById('instr');

            if (!name.value.trim()) {
                messages.push('Recipe name is required');
            } else if (name.value.length > 20) { 
                messages.push('Recipe name can\'t be longer than 20 characters');
            }

            if (!img.value) {
                messages.push('Recipe image is required');
            }

            if (!category.value || category.value === '') {
                messages.push('Category is required');
            }

            if (!time.value || time.value === 'epilogi' || time.value === '') { 
                messages.push('Prep time is required');
            }

            if (!ingr.value.trim()) {
                messages.push('Ingredients are required');
            } else if (ingr.value.length > 500) {
                messages.push('Ingredients can\'t be longer than 500 characters');
            }


            if (!instr.value.trim()) {
                messages.push('Instructions are required');
            } else if (instr.value.length > 1000) {
                messages.push('Instructions can\'t be longer than 1000 characters');
            }

            if (messages.length > 0) {
                e.preventDefault();
                errorElement.innerText = messages.join(', ');
                errorElement.style.display = 'block'; 
            } else {
                errorElement.style.display = 'none'; 
            }
        });
    }

    // SweetAlert for Logout Confirmation
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('logoutConfirmBtn').addEventListener('click', function(e) {
            e.preventDefault(); // Prevent default button action

            swal({
                title: "Logout Confirmation",
                text: "Are you sure you want to log out?",
                icon: "warning",
                buttons: {
                    cancel: "Cancel",
                    confirm: {
                        text: "Yes, Logout",
                        value: true,
                        className: "swal-button--danger", // Style the confirm button
                    }
                },
                dangerMode: true,
            })
            .then((willLogout) => {
                if (willLogout) {
                    // Redirect directly to logout.php, which will then handle session destruction and redirect to index.php
                    window.location.href = 'logout.php'; 
                } else {
                    swal("Logout cancelled!", {
                        icon: "info",
                        button: "OK",
                    });
                }
            });
        });
    });
</script>
</body>
</html>
