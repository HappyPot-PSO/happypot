<?php
if (isset($_POST['logout'])) {
    // Destroy session - Log out
    session_destroy();
    header("Location: index.php");
    exit();
}?>

<!-- Log-out -->
    <form method="POST" action="">
        <button class="logoutbtn btnhovcl" type="submit" name="logout">Log-out</button>
    </form>
</header>