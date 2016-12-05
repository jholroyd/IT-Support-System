<?php

// include init file.
include('config.php');

// if form is submitted.
if(isset($_POST['generate'])) {
	$password = trim($_POST['password']);
	generate_password_hash($password);
}

?>
<h2>Generate Password Hash</h2>
<form method="post" action="">
<input type="password" name="password" placeholder="Password" />
<input type="submit" name="generate" value="Generate" />
</form>