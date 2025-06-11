<?php
session_start(); 

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php"); 
    exit();
}

require_once('connect.php'); 

$username_display = isset($_SESSION['username']) ? htmlspecialchars($_SESSION['username']) : 'User';
$title = 'My Profile - Happy Pot';
$user_id_session = $_SESSION['user_id'];

$query_user_details = "SELECT email, fname, lname FROM user WHERE id = ?";
$stmt_user_details = mysqli_prepare($dbc, $query_user_details);
mysqli_stmt_bind_param($stmt_user_details, "i", $user_id_session);
mysqli_stmt_execute($stmt_user_details);
$result_user_details = mysqli_stmt_get_result($stmt_user_details);
$user_data = mysqli_fetch_assoc($result_user_details);
mysqli_stmt_close($stmt_user_details);

$query_user_recipes = "SELECT idrec, title, img, time FROM recipe WHERE user_id = ? ORDER BY idrec DESC";
$stmt_user_recipes = mysqli_prepare($dbc, $query_user_recipes);
mysqli_stmt_bind_param($stmt_user_recipes, "i", $user_id_session);
mysqli_stmt_execute($stmt_user_recipes);
$result_user_recipes = mysqli_stmt_get_result($stmt_user_recipes);

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
        /* CSS yang sudah ada */
        html, body.profile-page-body { height: 100%; margin: 0; padding: 0; }
        body.profile-page-body {
            display: flex; flex-direction: column; min-height: 100vh;
            background-color: #ccf1ff; font-family: "Montserrat", sans-serif;
        }
        .profile-site-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 15px 30px; background-color: #fff;
            border-bottom: 1px solid #cccccc; width: 100%;
            box-sizing: border-box; flex-shrink: 0;
        }
        .profile-site-header .logo-link { display: flex; align-items: center; text-decoration: none; }
        .profile-site-header .logo-image { height: 50px; width: auto; }
        .profile-site-header .logo-text {
            padding-left: 10px; color: #4dc9f7; font-size: 38px;
            font-weight: bold; margin: 0;
        }
        .profile-header-nav { display: flex; align-items: center; }

        .profile-header-nav .usermenu-greeting {
            font-weight:bold;
            color:#555;
            margin-right: 15px; /* Memberi jarak ke tombol di kanannya */
        }
        .profile-header-nav .username {color:#4dc9f7;}
        .profile-header-nav .btn, .profile-header-nav .logoutbtn {
            padding: 8px 15px; font-size:0.9em; border-radius:8px; text-decoration:none; cursor:pointer;
            border: 1px solid #ccc; background-color: #e9e9e9; color: #333;
        }
        /* Atur margin-left untuk semua tombol .btn */
        .profile-header-nav .btn {
            margin-left: 15px;
        }
        /* Atur margin-right spesifik untuk tombol "Post a recipe" agar ada jarak dengan tombol logout */
        .profile-header-nav button[onClick*='recipe.php'] {
            margin-right: 10px; /* Jarak tambahan ke tombol logout */
        }
        /* Tombol logout tidak perlu margin-left tambahan jika sudah ada margin-right dari tombol sebelumnya */
        .profile-header-nav .btn.btnhov:hover, .profile-header-nav .logoutbtn.btnhovel:hover {
            background-color: #d0d0d0;
        }

        .profile-main-content-wrapper {
            flex-grow: 1; width: 100%; display: flex;
            justify-content: center; align-items: flex-start; 
            padding: 30px 15px; box-sizing: border-box;
        }
        .profile-content-box { 
            background-color: #fff; padding: 30px 40px; border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1); width: 95%; /* Diperluas agar 4 kolom pas */
            max-width: 1400px; /* Diperluas agar 4 kolom pas */
            box-sizing: border-box;
        }
        .profile-details-section {
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        .profile-details-section .content-title { 
            font-size: 1.8em; color: #333; margin-top: 0;
            margin-bottom: 20px; font-weight: 600; text-align: center;
        }
        .profile-info-item {
            margin-bottom: 12px; font-size: 1.05em; color: #333;
        }
        .profile-info-item strong {
            display: inline-block; width: 130px; 
            color: #555; font-weight: 600;
        }
        .user-recipes-section .content-title { 
            font-size: 1.8em; color: #333; margin-top: 0;
            margin-bottom: 25px; font-weight: 600; text-align: center;
        }
        .profile-content-box table {
            width: 100%; border-collapse: collapse; table-layout: fixed; /* Penting untuk lebar kolom yang konsisten */
        }
        .profile-content-box td {
            text-align: center; 
            padding: 15px 10px; 
            vertical-align: top; 
            width: 25%; /* Untuk 4 kolom per baris */
            box-sizing: border-box;
        }

        /* --- CSS BARU UNTUK GAMBAR SERAGAM (SALIN DARI dashboard.php) --- */
        .image-wrapper {
            position: relative; 
            width: 100%;
            padding-bottom: 75%; /* Rasio aspek 4:3 (height is 75% of width). Sesuaikan ini! */
            overflow: hidden; 
            border: 2px solid #f0f0f0; 
            border-radius: 10px;
            margin: 0 auto 12px auto; 
        }

        .profile-content-box .postimg { /* Sesuaikan selector agar menargetkan gambar di profile */
            position: absolute; 
            top: 0;
            left: 0;
            width: 100%; 
            height: 100%; 
            object-fit: cover; 
            display: block; 
        }
        /* --- AKHIR CSS GAMBAR SERAGAM --- */

        .profile-content-box .postitle {
            font-size: 1.1em; font-weight: bold; color: #007bff;
            text-decoration: none; display: block; margin-bottom: 5px; line-height: 1.3;
        }
        .profile-content-box .postitle:hover { text-decoration: underline; color: #0056b3; }
        .profile-content-box .fa-clock { margin-right: 5px; }
        .profile-content-box td .recipe-detail-profile { 
            display: block; font-size: 0.9em; color: #666;
            margin-bottom: 4px; line-height: 1.4;
        }
        .profile-content-box .norec {
            text-align: center; font-size: 1.1em; color: #777; padding: 20px 0;
        }
        .recipe-actions {
            margin-top: 10px;
            display: flex;
            justify-content: center;
            gap: 10px;
        }
        /* Gaya untuk tombol Edit (Kuning) */
        .recipe-actions .btn-edit {
            background-color: #ffc107; 
            color: #212529; 
            border-color: #ffc107;
            padding: 6px 12px; 
            font-size: 0.85em; 
            font-weight: 500;
            border-radius: 6px;
            border: 1px solid transparent;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.2s ease, color 0.2s ease;
            display: inline-flex; 
            align-items: center;
            justify-content: center;
            min-width: 30px; 
            min-height: 30px;
        }
        .recipe-actions .btn-edit:hover {
            background-color: #e0a800;
            border-color: #d39e00;
        }

        /* Gaya untuk tombol Delete (Merah) - DISESUAIKAN */
        .recipe-actions .btn-delete-sweetalert { 
            background-color: #dc3545; 
            color: white; 
            border-color: #dc3545;
            padding: 6px 12px; 
            font-size: 0.85em; 
            font-weight: 500;
            border-radius: 6px;
            border: 1px solid transparent;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.2s ease, color 0.2s ease;
            display: inline-flex; 
            align-items: center;
            justify-content: center;
            min-width: 30px; 
            min-height: 30px;
        }
        .recipe-actions .btn-delete-sweetalert:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }
        /* Pastikan ikon di dalamnya juga putih */
        .recipe-actions .btn-delete-sweetalert i {
            color: white; 
            font-size: 0.9em; 
        }


        .profile-page-actions {
            text-align: center;
            margin-top: 30px;
        }
        .profile-page-actions .btn-action.primary { 
            padding: 10px 25px; font-size: 1em; font-weight: bold;
            border-radius: 8px; border: 1px solid #4dc9f7; cursor: pointer;
            text-decoration: none; background-color: #4dc9f7; color: white;
            transition: background-color 0.2s ease;
        }
        .profile-page-actions .btn-action.primary:hover {
            background-color: #36a2c9;
        }
        .recipe-actions .btn-action-recipe i { 
            font-size: 0.9em; 
        }
        .swal-footer {
            text-align: center !important; 
        }
        .swal-button-container {
            margin: 0 8px !important;
        }

        .profile-edit-actions .btn-edit-profile {
            background-color: #4dc9f7; 
            color: white; 
            border-color: #4dc9f7;
            padding: 8px 20px; 
            font-size: 1em; 
            font-weight: 500;
            border-radius: 8px; 
            border: 1px solid transparent;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.2s ease, border-color 0.2s ease, color 0.2s ease;
            display: inline-flex; 
            align-items: center;
            justify-content: center;
            gap: 8px; 
        }

        .profile-edit-actions .btn-edit-profile:hover {
            background-color: #4dc9f7; 
            border-color: #4dc9f7;
        }

        .profile-edit-actions .btn-edit-profile i {
            font-size: 0.9em; 
            color: white; 
        }

        /* Media queries for responsiveness (agar sesuai dengan dashboard.php) */
        @media (max-width: 1200px) {
            .profile-content-box td {
                width: 33.33%; /* 3 kolom per baris */
            }
            .profile-content-box {
                max-width: 1000px; /* Menyesuaikan max-width container utama */
            }
        }

        @media (max-width: 992px) {
            .profile-content-box td {
                width: 50%; /* 2 kolom per baris */
            }
            .profile-content-box {
                max-width: 700px; 
            }
        }

        @media (max-width: 768px) {
            .profile-content-box td {
                width: 100%; /* 1 kolom per baris */
            }
            .profile-content-box {
                max-width: 400px; 
            }
        }
    </style>
</head>
<body class="profile-page-body">

    <header class="profile-site-header">
        <a href="index.php" class="logo-link">
            <img class="logo-image" src="images/logo.png" alt="Happy Pot Logo">
            <h1 class="logo-text">Happy Pot</h1>
        </a>
        <div class="profile-header-nav">
            <?php 
            echo '<div class="usermenu-greeting">Welcome, <span class="username">' . $username_display . '!</span></div>';
            echo '<button class="btn btnhov" type="button" onClick="location.href=\'recipe.php\'">Post a recipe</button>';
            echo '<button class="logoutbtn btnhovel" type="button" id="logoutConfirmBtn">Log-out</button>';
            ?>
        </div>
    </header>

    <div class="profile-main-content-wrapper">
        <div class="profile-content-box">
            <?php
            // Logic untuk menampilkan SweetAlert status setelah redirect (Sukses/Error)
            if (isset($_SESSION['recipe_action_status']) && isset($_SESSION['recipe_action_type'])) {
                $status_message = htmlspecialchars($_SESSION['recipe_action_status']);
                $status_type = htmlspecialchars($_SESSION['recipe_action_type']);
                
                echo "<script>
                        document.addEventListener('DOMContentLoaded', function() {
                            swal({
                                title: '" . ucfirst($status_type) . "!',
                                text: '" . addslashes($status_message) . "',
                                icon: '" . $status_type . "',
                                button: 'OK'
                            }).then(function() {
                                // Opsional: Bersihkan URL dari parameter status
                                if (window.history.replaceState) {
                                    const cleanUrl = window.location.protocol + '//' + window.location.host + window.location.pathname;
                                    window.history.replaceState({ path: cleanUrl }, '', cleanUrl);
                                }
                            });
                        });
                      </script>";
                unset($_SESSION['recipe_action_status']);
                unset($_SESSION['recipe_action_type']);
            }
            ?>

            <div class="profile-details-section">
                <h2 class="content-title">Detail Profil</h2>
                <?php if ($user_data): ?>
                    <div class="profile-info-item">
                        <strong>Email:</strong> <?php echo htmlspecialchars($user_data['email']); ?>
                    </div>
                    <div class="profile-info-item">
                        <strong>Nama Depan:</strong> <?php echo htmlspecialchars($user_data['fname']); ?>
                    </div>
                    <div class="profile-info-item">
                        <strong>Nama Belakang:</strong> <?php echo htmlspecialchars($user_data['lname']); ?>
                    </div>
                <?php else: ?>
                    <p class="error-message" style="text-align:center; color:red;">Data pengguna tidak ditemukan.</p>
                <?php endif; ?>
                <div class="profile-edit-actions" style="text-align: center; margin-top: 20px;">
                    <a href="edit_profile.php" class="btn-action btn-edit-profile" title="Edit Profile">
                        <i class="fa-solid fa-pen-to-square"></i> Edit Profile
                    </a>
                </div>
            </div>

            <div class="user-recipes-section">
                <h2 class="content-title">Your Recipes</h2>
                <?php
                if ($result_user_recipes) {
                    if (mysqli_num_rows($result_user_recipes) == 0) {
                        echo '<p class="norec">You haven\'t posted any recipes yet. <a href="recipe.php">Post your first recipe!</a></p>';
                    } else {
                        echo '<table>';
                        $count = 0;
                        while ($recipe_row = mysqli_fetch_assoc($result_user_recipes)) {
                            if ($count % 4 == 0) { 
                                if ($count > 0) echo '</tr>';
                                echo '<tr>';
                            }
                            echo '<td>';
                            // STRUKTUR HTML BARU UNTUK GAMBAR (SALIN DARI dashboard.php)
                            echo '<div class="image-wrapper">'; 
                            echo '<a href="display.php?id=' . $recipe_row['idrec'] . '"><img class="postimg" src="' . htmlspecialchars($recipe_row['img']) . '" alt="' . htmlspecialchars($recipe_row['title']) . '"></a>';
                            echo '</div>'; // Tutup image-wrapper
                            echo '<a href="display.php?id=' . $recipe_row['idrec'] . '" class="postitle">' . htmlspecialchars($recipe_row['title']) . '</a>';
                            echo '<span class="recipe-detail-profile"><i class="fa-regular fa-clock"></i> ' . htmlspecialchars($recipe_row['time']) . ' mins</span>';
                            echo '<div class="recipe-actions">';
                            echo '<a href="edit_recipe.php?id=' . $recipe_row['idrec'] . '" class="btn-action-recipe btn-edit" title="Edit Recipe"><i class="fa-solid fa-pen-to-square"></i></a>';
                            // HTML untuk tombol delete (KOTAK MERAH)
                            echo '<a href="#" class="btn-action-recipe btn-delete-sweetalert" title="Delete Recipe" data-recipe-id="' . $recipe_row['idrec'] . '" data-recipe-title="' . htmlspecialchars(addslashes($recipe_row['title'])) . '"><i class="fa-solid fa-trash-can"></i></a>';
                            echo '</div>';
                            echo '</td>';
                            $count++;
                        }
                        if ($count % 4 != 0) {
                            while ($count % 4 != 0) {
                                echo '<td></td>'; 
                                $count++;
                            }
                        }
                        echo '</tr>'; 
                        echo '</table>';
                    }
                } else {
                    echo '<p class="norec">Error fetching your recipes: ' . (isset($dbc) ? mysqli_error($dbc) : 'Database connection error') . '</p>';
                }
                if(isset($stmt_user_recipes)) mysqli_stmt_close($stmt_user_recipes);
                ?>
            </div>
            
            <div class="profile-page-actions">
                <a href="dashboard.php" class="btn-action primary">Kembali ke Beranda</a>
            </div>

        </div> 
    </div> 

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var deleteButtons = document.querySelectorAll('.btn-delete-sweetalert'); 
            deleteButtons.forEach(function(button) {
                button.addEventListener('click', function(e) {
                    e.preventDefault(); 

                    var recipeId = this.dataset.recipeId;
                    var recipeTitle = this.dataset.recipeTitle;

                    swal({
                        title: "Are you sure?",
                        text: "Once deleted, you will not be able to recover recipe: \"" + recipeTitle + "\"!",
                        icon: "warning", 
                        buttons: {
                            cancel: "Cancel", 
                            delete: {
                                text: "Delete", 
                                value: true,
                                className: "swal-button--danger", 
                            }
                        },
                        dangerMode: true, 
                    })
                    .then((willDelete) => {
                        if (willDelete) {
                            window.location.href = 'delete_recipe.php?id=' + recipeId;
                        } else {
                            swal("Your recipe is safe!");
                        }
                    });
                });
            });

            // SweetAlert for Logout Confirmation
            document.getElementById('logoutConfirmBtn').addEventListener('click', function(e) {
                e.preventDefault(); 

                swal({
                    title: "Logout Confirmation",
                    text: "Are you sure you want to log out?",
                    icon: "warning",
                    buttons: {
                        cancel: "Cancel",
                        confirm: {
                            text: "Yes, Logout",
                            value: true,
                            className: "swal-button--danger", 
                        }
                    },
                    dangerMode: true,
                })
                .then((willLogout) => {
                    if (willLogout) {
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
<?php
if(isset($dbc)) mysqli_close($dbc); 
?>