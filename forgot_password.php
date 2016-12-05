<?php

// include the init file.
include('includes/config.php');

// if the user is logged in, send them to the index.
login_protect();

?>
<!DOCTYPE html>
<html>
<head>

	<!-- title -->
	<title>Forgot Password - IT Support</title>
	
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
                    <div class="panel-title">Forgot Password</div>
                </div>     

                <div class="panel-body">
                            
                    <form class="form-horizontal" role="form" method="post" style="margin:0;">        
                        <div class="input-group">
                            <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
                            <input type="email" class="form-control" name="email" placeholder="Email Address">
                        </div>
                        <div style="margin:15px 0 0 0;" class="form-group">
                            <a href="login.php" class="btn btn-default">Back to Login</a>
                            <input type="submit" name="forgot_password" class="btn btn-success" value="Submit" />
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