<?php

// include the init file.
include('includes/config.php');

// if the user is logged in, send them to the index.
login_protect();

// if login form is submitted.
if(isset($_POST['login'])) {
	$username = trim($_POST['username']);
	$password = trim($_POST['password']);
	login($username, $password);
}

?>
<!DOCTYPE html>
<html>
<head>

	<!-- title -->
	<title>Login - IT Support</title>
	
	<!-- meta -->
	<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
	
	<!-- stylesheets -->
	<link href="assets/css/bootstrap.min.css" rel="stylesheet">
    
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>
<body>
    
    <div class="container">
        
        <div id="loginbox" style="margin-top:40px;" class="mainbox col-md-6 col-md-offset-3 col-sm-8 col-sm-offset-2">                    
            
            <div class="panel panel-default">
                
                <div class="panel-heading">
                    <div class="panel-title">Login</div>
                </div>     

                <div class="panel-body">
                            
                    <form class="form-horizontal" role="form" method="post" style="margin:0;">        
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                            <input type="text" class="form-control" name="username" placeholder="Username">
                        </div>    
                        <div class="input-group" style="margin-top:15px;">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                            <input type="password" class="form-control" name="password" placeholder="Password">
                        </div>
                        <div style="margin:15px 0 0 0;" class="form-group">
                            <a href="forgot_password.php" name="forgot-password" class="btn btn-default">Forogt Password?</a>
                            <input type="submit" name="login" class="btn btn-success" value="Login" />
                        </div>
                    </form>     
                
                </div>                     
                    
            </div>
        
        </div>
        
    </div>
    
    <!-- javascript -->
    <script src="assets/js/jquery-2.1.1.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
	
</body>
</html>