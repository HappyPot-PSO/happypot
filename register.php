<div class="aboutRegi">
<!-- About Us -->
	<div>
		<h2 class="abtusH2 underline" >About Us</h2>
		<p class="abtusP">Welcome to Happy Pot, your go-to website for delicious and easy to follow recipes!
		We are a a community of passionate foodies who believe that cooking should be fun and stress-free. 
		Here you can read and learn new recipes, and even post your own recipes too!</p>
	</div>
	
<!-- Register Form -->
	<div>
		<h2 class="regiH2 underline1">Not a member? Register here!</h2>
		<div id="error"></div>
		<form id="registerform" method="POST" action="registerdb.php">
			<input class="regibox" type="text" id="fname" name="fname" placeholder="first-name">
			<input class="regibox" type="text" id="lname" name="lname" placeholder="last-name"><br>
			<input class="regibox regimail" type="text" id="remail" name="remail" placeholder="e-mail"><br>
			<input class="regibox" type="password" id="rpassword" name="rpassword" placeholder="password">
			<input class="regibox" type="password" id="repassword" name="repassword" placeholder="repeat password"><br>
			<input class="regibtn btnhov" type="submit" id="registerbtn" name="register" value="Register">
		</form>
	</div>
</div>