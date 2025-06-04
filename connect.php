<?php

// Database connection
DEFINE ('DB_USER', 'root');
DEFINE ('DB_PASSWORD', '@<0zAacK_Guyj7{6');
DEFINE ('DB_HOST', '34.50.70.228');
DEFINE ('DB_NAME', 'recipedb');

$dbc = @mysqli_connect (DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, 3306) OR die ('Could not connect to MySQL: ' . mysqli_connect_error() );

?>