<?php
session_start(); 
$title = 'Happy Pot Dashboard';

global $dbc; 
if (!isset($dbc)) {
    if (file_exists('connect.php')) {
        include_once 'connect.php';
    } else if (file_exists('../connect.php')) { 
        include_once '../connect.php';
    } else {
        die("Error: Database connection file not found.");
    }
}

$filterCategory = isset($_GET['category']) ? htmlspecialchars($_GET['category']) : 'all';

$allowedCategories = ['all', 'food', 'drink']; 
if (!in_array($filterCategory, $allowedCategories)) {
    $filterCategory = 'all'; 
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
        html, body.dashboard-page-body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        body.dashboard-page-body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #ccf1ff; 
            font-family: "Montserrat", sans-serif;
        }

        .dashboard-site-header { 
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

        .dashboard-site-header .logo-link {
            display: flex;
            align-items: center;
            text-decoration: none;
        }

        .dashboard-site-header .logo-image {
            height: 50px;
            width: auto;
        }

        .dashboard-site-header .logo-text {
            padding-left: 10px;
            color: #4dc9f7;
            font-size: 38px;
            font-weight: bold;
            margin: 0;
        }
        
        .dashboard-header-nav { 
            display: flex;
            align-items: center;
        }
        .dashboard-header-nav .usermenu-greeting {
            font-weight:bold;
            color:#555;
            margin-right: 15px; 
        }
        .dashboard-header-nav .username {
            color:#4dc9f7;
        }
        
        .dashboard-header-nav .btn, .dashboard-header-nav .logoutbtn {
            padding: 8px 15px;
            font-size:0.9em;
            border-radius:8px;
            text-decoration:none;
            cursor:pointer;
            border: 1px solid #ccc;
            background-color: #e9e9e9;
            color: #333;
        }

        .dashboard-header-nav .btn { 
            margin-left: 15px; 
        }
        
        .dashboard-header-nav button[onClick*='recipe.php'] {
            margin-right: 10px; 
        }

        .dashboard-header-nav .logoutbtn {
            /* No extra margin-left here */
        }
        
        .dashboard-header-nav .btn.btnhov:hover, .dashboard-header-nav .logoutbtn.btnhovel:hover {
            background-color: #d0d0d0;
        }


        .dashboard-main-content-wrapper {
            flex-grow: 1; 
            width: 100%;
            display: flex; 
            flex-direction: column; 
            justify-content: flex-start; 
            align-items: center; 
            padding: 30px 15px; 
            box-sizing: border-box;
        }

        .dashboard-content-box { 
            background-color: #fff;
            padding: 30px 25px; 
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            width: 95%; 
            max-width: 1400px; 
            box-sizing: border-box;
            /* Removed margin-top here as filters are now inside */
        }

        .dashboard-content-box .title { 
            text-align: center;
            color: #4dc9f7;
            font-size: 2.2em; 
            margin-top: 0;
            margin-bottom: 15px; /* Reduced margin to bring filters closer */
        }

        .dashboard-content-box main { 
            padding-top: 20px; /* Add padding to push table down from filters */
        }
        .dashboard-content-box table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed; 
        }
        .dashboard-content-box td {
            text-align: center; 
            padding: 15px 10px; 
            vertical-align: top;
            width: 25%; 
            box-sizing: border-box; 
        }
        .image-wrapper {
            position: relative; 
            width: 100%;
            padding-bottom: 75%; 
            overflow: hidden; 
            border: 2px solid #f0f0f0; 
            border-radius: 10px;
            margin: 0 auto 12px auto; 
        }

        .dashboard-content-box .postimg { 
            position: absolute; 
            top: 0;
            left: 0;
            width: 100%; 
            height: 100%; 
            object-fit: cover; 
            display: block; 
        }
        .dashboard-content-box .postitle { 
            font-size: 1.15em;
            font-weight: bold;
            color: #4dc9f7; 
            text-decoration: none;
            display: block;
            margin-bottom: 6px;
            line-height: 1.3; 
        }
        .dashboard-content-box .postitle strong { 
            font-weight: 600; 
        }
        .dashboard-content-box .postitle:hover {
            color: #4dc9f7; 
            text-decoration: none;
        }
        .dashboard-content-box .fa-clock {
            margin-right: 5px;
            color: #777; 
        }
        .dashboard-content-box td .recipe-detail { 
            display: block; 
            font-size: 0.9em;
            color: #666; 
            margin-bottom: 4px;
            line-height: 1.4;
        }
        .dashboard-content-box td .recipe-category {
            display: block;
            font-size: 0.85em;
            color: #555;
            margin-top: 5px;
            text-transform: capitalize;
            font-weight: bold;
        }


        .dashboard-content-box .norec { 
            text-align: center;
            font-size: 1.1em;
            color: #777;
            padding: 30px 0;
        }

        .swal-modal {border-radius: 10px;}

        .category-filters {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-bottom: 20px;
        }
        .category-filters .filter-btn {
            padding: 10px 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            text-decoration: none;
            color: #333;
            background-color: #f9f9f9;
            transition: background-color 0.2s ease, border-color 0.2s ease;
            font-weight: 500;
        }
        .category-filters .filter-btn:hover {
            background-color: #e0e0e0;
            border-color: #b0b0b0;
        }
        .category-filters .filter-btn.active {
            background-color: #4dc9f7;
            color: white;
            border-color: #4dc9f7;
            font-weight: bold;
        }
        .category-filters .filter-btn.active:hover {
            background-color: #36a2c9;
            border-color: #36a2c9;
        }

        @media (max-width: 1200px) {
            .dashboard-content-box td {
                width: 33.33%; 
            }
        }

        @media (max-width: 992px) {
            .dashboard-content-box td {
                width: 50%; 
            }
        }

        @media (max-width: 768px) {
            .dashboard-site-header {
                flex-direction: column;
                align-items: flex-start;
                padding: 15px 20px;
            }
            .dashboard-header-nav {
                flex-direction: column;
                align-items: flex-start;
                margin-top: 15px;
                width: 100%;
            }
            .dashboard-header-nav .usermenu-greeting {
                margin-right: 0;
                margin-bottom: 10px;
            }
            .dashboard-header-nav .btn, .dashboard-header-nav .logoutbtn {
                width: calc(100% - 20px); 
                margin-left: 0;
                margin-bottom: 10px;
                text-align: center;
            }
            .dashboard-header-nav button[onClick*='recipe.php'] {
                margin-right: 0; 
            }

            .dashboard-content-box td {
                width: 100%; 
            }
            .category-filters {
                flex-direction: column;
                gap: 10px;
            }
            .category-filters .filter-btn {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>
<body class="dashboard-page-body">
    <header class="dashboard-site-header">
        <a href="index.php" class="logo-link">
            <img class="logo-image" src="images/logo.png" alt="Happy Pot Logo">
            <h1 class="logo-text">Happy Pot</h1>
        </a>
        <div class="dashboard-header-nav">
            <?php
            if (isset($_SESSION['username'])) {
                echo '<div class="usermenu-greeting">Welcome, <span class="username">' . htmlspecialchars($_SESSION['username']) . '!</span></div>';
            }
            echo '<button class="btn btnhov" type="button" onClick="location.href=\'profile.php\'">Profile</button>';
            echo '<button class="btn btnhov post-recipe-btn" type="button" onClick="location.href=\'recipe.php\'">Post a recipe</button>'; 
            echo '<button class="logoutbtn btnhovel" type="button" id="logoutConfirmBtn">Log-out</button>';
            ?>
        </div>
    </header>

    <div class="dashboard-main-content-wrapper">
        <div class="dashboard-content-box">
            
            <h1 class="title">Recipes</h1>
            <div class="category-filters">
                <a href="?category=all" class="filter-btn <?php echo ($filterCategory == 'all' ? 'active' : ''); ?>">All</a>
                <a href="?category=food" class="filter-btn <?php echo ($filterCategory == 'food' ? 'active' : ''); ?>">Food</a>
                <a href="?category=drink" class="filter-btn <?php echo ($filterCategory == 'drink' ? 'active' : ''); ?>">Drink</a>
            </div>

            <main>
                <?php
                if (isset($_SESSION["post_success"]) && $_SESSION["post_success"]) {
                    echo '<script>
                                document.addEventListener("DOMContentLoaded", function() {
                                    if(typeof swal === "function") {
                                        swal("Post successful", "You can now view your post or make another one!", "success");
                                    }
                                });
                            </script>';
                    $_SESSION["post_success"] = false;
                }

                if (isset($dbc)) {
                    $query = "SELECT r.idrec, r.title, r.img, r.time, r.category, u.fname, u.lname 
                              FROM recipe r 
                              JOIN user u ON r.user_id = u.id";
                    
                    if ($filterCategory !== 'all') {
                        $query .= " WHERE r.category = ?";
                    }
                    
                    $query .= " ORDER BY r.idrec DESC"; 

                    $stmt = $dbc->prepare($query);

                    if ($stmt === false) {
                        echo '<p class="norec">Error preparing statement: ' . htmlspecialchars($dbc->error) . '</p>';
                    } else {
                        if ($filterCategory !== 'all') {
                            $stmt->bind_param("s", $filterCategory);
                        }
                        
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($result) {
                            if ($result->num_rows == 0) {
                                echo '<p class="norec">No recipes found in this category yet.</p>';
                            } else {
                                echo '<table>';
                                $count = 0;
                                while ($row = $result->fetch_assoc()) {
                                    if ($count % 4 == 0) { 
                                        if ($count > 0) { echo '</tr>';
                                        } 
                                        echo '<tr>'; 
                                    }
                                    echo '<td>';
                                    echo '<div class="image-wrapper">'; 
                                    echo '<a href="display.php?id=' . $row['idrec'] . '"><img class="postimg" src="' . htmlspecialchars($row['img']) . '" alt="' . htmlspecialchars($row['title']) . '"></a>';
                                    echo '</div>'; 
                                    echo '<a href="display.php?id=' . $row['idrec'] . '" class="postitle">' . htmlspecialchars($row['title']) . '</a>'; 
                                    echo '<span class="recipe-detail"><i class="fa-regular fa-clock"></i> ' . htmlspecialchars($row['time']) . ' mins</span>'; 
                                    echo '<span class="recipe-detail">By ' . htmlspecialchars($row['fname']) . ' ' . htmlspecialchars($row['lname']) . '</span>'; 
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
                            $result->free(); 
                        } else {
                            echo '<p class="norec">Error fetching recipes: ' . htmlspecialchars($dbc->error) . '</p>';
                        }
                        $stmt->close();
                    }
                } else {
                    echo "<p class='norec'>Database connection not available.</p>";
                }
                ?>
            </main>

        </div> 
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
if(isset($dbc)) { mysqli_close($dbc);
} 
?>
