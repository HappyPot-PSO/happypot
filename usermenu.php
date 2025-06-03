<!-- Change Title -->
<?php $title = 'Welcome back!';?>

<!-- User Menu -->
<div class="usermenu">
	<h4>Welcome back <span class="username "><?php echo $_SESSION['username'] ;?> !</span></h4>
	<button class="btn btnhov" type="button" onClick="location.href='recipe.php'">Post a recipe</button>
</div>


