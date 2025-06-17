<?php
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

require_once('connect.php'); // Pastikan file koneksi database Anda ada

$user_id_session = $_SESSION['user_id'];
$title = 'Edit Profile - Happy Pot';
$error_message = '';
$success_message = '';

// Ambil data profil pengguna saat ini, termasuk password (hashed) untuk validasi
$query_user_details = "SELECT email, fname, lname, password FROM user WHERE id = ?";
$stmt_user_details = mysqli_prepare($dbc, $query_user_details);
mysqli_stmt_bind_param($stmt_user_details, "i", $user_id_session);
mysqli_stmt_execute($stmt_user_details);
$result_user_details = mysqli_stmt_get_result($stmt_user_details);
$user_data = mysqli_fetch_assoc($result_user_details);
mysqli_stmt_close($stmt_user_details);

// Redirect jika data pengguna tidak ditemukan (harusnya tidak terjadi jika sudah login)
if (!$user_data) {
    $_SESSION['profile_action_status'] = 'Error';
    $_SESSION['profile_action_type'] = 'error';
    $_SESSION['profile_action_message'] = 'User data not found. Please log in again.';
    header("Location: profile.php"); // Redirect ke profile.php untuk pesan error ini
    exit();
}

// Simpan hashed password dari database untuk validasi
$stored_hashed_password = $user_data['password'];

// Proses form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $fname = trim($_POST['fname']);
    $lname = trim($_POST['lname']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];

    // --- Start Validation ---

    // Basic validation for essential fields
    if (empty($email) || empty($fname) || empty($lname)) {
        $error_message = "All fields (Email, First Name, Last Name) are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    }

    // If no error so far, check email uniqueness
    if (empty($error_message)) {
        $query_check_email = "SELECT id FROM user WHERE email = ? AND id != ?";
        $stmt_check_email = mysqli_prepare($dbc, $query_check_email);
        mysqli_stmt_bind_param($stmt_check_email, "si", $email, $user_id_session);
        mysqli_stmt_execute($stmt_check_email);
        mysqli_stmt_store_result($stmt_check_email);

        if (mysqli_stmt_num_rows($stmt_check_email) > 0) {
            $error_message = "Email already exists for another user.";
        }
        mysqli_stmt_close($stmt_check_email);
    }

    // If no error so far, proceed with password change validation (if new password is provided)
    $update_password = false;
    if (empty($error_message) && !empty($new_password)) { // Only proceed if no existing error and new password is provided
        // 1. Validate Current Password
        if (empty($current_password)) {
            $error_message = "To change your password, you must enter your current password.";
        } elseif (!password_verify($current_password, $stored_hashed_password)) {
            $error_message = "Current password is incorrect.";
        }
        // 2. Validate New Password length
        elseif (strlen($new_password) < 8) {
            $error_message = "New password must be at least 8 characters long.";
        }
        // 3. Validate New Password vs Confirm New Password
        elseif ($new_password !== $confirm_new_password) {
            $error_message = "New password and confirm new password do not match.";
        } else {
            $update_password = true; // Mark to update password
        }
    }

    // --- End Validation ---

    // Only proceed with database update if no error messages were set
    if (empty($error_message)) {
        // Build the UPDATE query
        $update_fields = [];
        $bind_types = '';
        $bind_params = [];

        $update_fields[] = "email = ?";
        $bind_types .= "s";
        $bind_params[] = &$email;

        $update_fields[] = "fname = ?";
        $bind_types .= "s";
        $bind_params[] = &$fname;

        $update_fields[] = "lname = ?";
        $bind_types .= "s";
        $bind_params[] = &$lname;

        // If password update is approved, add it to the query
        if ($update_password) {
            $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_fields[] = "password = ?";
            $bind_types .= "s";
            $bind_params[] = &$hashed_new_password;
        }

        $query_update_profile = "UPDATE user SET " . implode(", ", $update_fields) . " WHERE id = ?";
        $bind_types .= "i"; // For user_id
        $bind_params[] = &$user_id_session;

        $stmt_update_profile = mysqli_prepare($dbc, $query_update_profile);

        // Need to use call_user_func_array with references for bind_param
        $tmp_bind_params = [];
        foreach ($bind_params as $key => $value) {
            $tmp_bind_params[$key] = &$bind_params[$key];
        }

        // Check if bind_param was successful before executing
        if (call_user_func_array('mysqli_stmt_bind_param', array_merge([$stmt_update_profile, $bind_types], $tmp_bind_params))) {
            if (mysqli_stmt_execute($stmt_update_profile)) {
                // Update session username if first name changed
                $_SESSION['username'] = $fname;
                $success_message = 'Profile updated successfully!'; // Set success message for pop-up

                // Re-fetch updated user data for form pre-fill (important if password changed)
                $query_user_details_after_update = "SELECT email, fname, lname, password FROM user WHERE id = ?";
                $stmt_user_details_after_update = mysqli_prepare($dbc, $query_user_details_after_update);
                mysqli_stmt_bind_param($stmt_user_details_after_update, "i", $user_id_session);
                mysqli_stmt_execute($stmt_user_details_after_update);
                $result_user_details_after_update = mysqli_stmt_get_result($stmt_user_details_after_update);
                $user_data = mysqli_fetch_assoc($result_user_details_after_update); // Update $user_data
                $stored_hashed_password = $user_data['password']; // Update stored hashed password
                mysqli_stmt_close($stmt_user_details_after_update);

            } else {
                $error_message = "Error updating profile: " . mysqli_error($dbc);
            }
        } else {
            $error_message = "Error binding parameters for update: " . mysqli_error($dbc);
        }
        mysqli_stmt_close($stmt_update_profile);
    }
}
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
        /* Re-use most of your profile.php CSS or link a common CSS file */
        html, body { height: 100%; margin: 0; padding: 0; }
        body {
            display: flex; flex-direction: column; min-height: 100vh;
            background-color: #ccf1ff; font-family: "Montserrat", sans-serif;
        }
        .site-header {
            display: flex; align-items: center; justify-content: space-between;
            padding: 15px 30px; background-color: #fff;
            border-bottom: 1px solid #cccccc; width: 100%;
            box-sizing: border-box; flex-shrink: 0;
        }
        .site-header .logo-link { display: flex; align-items: center; text-decoration: none; }
        .site-header .logo-image { height: 50px; width: auto; }
        .site-header .logo-text {
            padding-left: 10px; color: #4dc9f7; font-size: 38px;
            font-weight: bold; margin: 0;
        }
        .header-nav { display: flex; align-items: center; }

        .header-nav .usermenu-greeting {
            font-weight:bold;
            color:#555;
            margin-right: 15px; 
        }
        .header-nav .username {color:#4dc9f7;}
        .header-nav .btn, .header-nav .logoutbtn {
            padding: 8px 15px; font-size:0.9em; border-radius:8px; text-decoration:none; cursor:pointer;
            border: 1px solid #ccc; background-color: #e9e9e9; color: #333;
        }
        .header-nav .btn {
            margin-left: 15px;
        }
        .header-nav button[onClick*='recipe.php'] {
            margin-right: 10px; 
        }
        .header-nav .btn.btnhov:hover, .header-nav .logoutbtn.btnhovel:hover {
            background-color: #d0d0d0;
        }
        .main-content-wrapper {
            flex-grow: 1; width: 100%; display: flex;
            justify-content: center; align-items: flex-start;
            padding: 30px 15px; box-sizing: border-box;
        }
        .content-box {
            background-color: #fff; padding: 30px 40px; border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1); width: 90%;
            max-width: 700px;
            box-sizing: border-box;
        }
        .content-box .content-title {
            font-size: 1.8em; color: #333; margin-top: 0;
            margin-bottom: 25px; font-weight: 600; text-align: center;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }
        .form-group input[type="email"],
        .form-group input[type="text"],
        .form-group input[type="password"] {
            width: calc(100% - 20px);
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 1em;
            box-sizing: border-box;
        }
        .form-group input[type="email"]:focus,
        .form-group input[type="text"]:focus,
        .form-group input[type="password"]:focus {
            border-color: #4dc9f7;
            outline: none;
            box-shadow: 0 0 5px rgba(77, 201, 247, 0.5);
        }
        .form-actions {
            text-align: center;
            margin-top: 30px;
        }
        .form-actions button {
            padding: 12px 30px;
            font-size: 1.1em;
            font-weight: bold;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: background-color 0.2s ease;
        }
        .form-actions button.primary {
            background-color: #4dc9f7;
            color: white;
        }
        .form-actions button.primary:hover {
            background-color: #36a2c9;
        }
        .form-actions button.secondary {
            background-color: #e9e9e9;
            color: #333;
            border: 1px solid #ccc;
            margin-left: 15px;
        }
        .form-actions button.secondary:hover {
            background-color: #d0d0d0;
        }
        .message { /* Generic class for messages */
            text-align: center;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-weight: bold;
        }
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .success-message {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .swal-footer {
            text-align: center !important;
        }
        .swal-button-container {
            margin: 0 8px !important;
        }
    </style>
</head>
<body>

    <header class="site-header">
        <a href="index.php" class="logo-link">
            <img class="logo-image" src="images/logo.png" alt="Happy Pot Logo">
            <h1 class="logo-text">Happy Pot</h1>
        </a>
        <div class="header-nav">
            <?php
            echo '<div class="usermenu-greeting">Welcome, <span class="username">' . htmlspecialchars($_SESSION['username']) . '!</span></div>';
            echo '<button class="btn btnhov" type="button" onClick="location.href=\'recipe.php\'">Post a recipe</button>';
            echo '<button class="logoutbtn btnhovel" type="button" id="logoutConfirmBtn">Log-out</button>';
            ?>
        </div>
    </header>

    <div class="main-content-wrapper">
        <div class="content-box">
            <h2 class="content-title">Edit Your Profile</h2>

            <?php if ($error_message): ?>
                <div class="message error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <form action="edit_profile.php" method="POST">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="fname">First Name:</label>
                    <input type="text" id="fname" name="fname" value="<?php echo htmlspecialchars($user_data['fname']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="lname">Last Name:</label>
                    <input type="text" id="lname" name="lname" value="<?php echo htmlspecialchars($user_data['lname']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="current_password">Current Password (required to change password):</label>
                    <input type="password" id="current_password" name="current_password">
                </div>

                <div class="form-group">
                    <label for="new_password">New Password (min. 8 characters, leave blank if not changing):</label>
                    <input type="password" id="new_password" name="new_password">
                </div>
                <div class="form-group">
                    <label for="confirm_new_password">Confirm New Password:</label>
                    <input type="password" id="confirm_new_password" name="confirm_new_password">
                </div>
                <div class="form-actions">
                    <button type="submit" class="primary">Update Profile</button>
                    <button type="button" class="secondary" onclick="window.location.href='profile.php'">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // SweetAlert for Logout Confirmation (re-used from profile.php)
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

            // SweetAlert for Success Message (MODIFIED FOR REDIRECT)
            <?php if (!empty($success_message)): ?>
                swal({
                    title: "Success!",
                    text: "<?php echo addslashes($success_message); ?>",
                    icon: "success",
                    button: "OK"
                }).then((value) => {
                    if (value) {
                        window.location.href = 'profile.php';
                    }
                });
            <?php endif; ?>
        });
    </script>
</body>
</html>

<?php
if(isset($dbc)) mysqli_close($dbc);
?>