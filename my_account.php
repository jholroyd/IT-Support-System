<?php
include('includes/config.php');

// protect the page from guest users.
logout_protect();

// if logout request has been sent.
if(isset($_GET['logout'])) {
	logout();
}

// edit user
if(isset($_POST['edit_user'])) {
    
    $id = $_SESSION['id'];
    $edit_first_name = $_POST['edit_first_name'];
	$edit_last_name = $_POST['edit_last_name'];
	$edit_username = $_POST['edit_username'];
	$edit_email = $_POST['edit_email'];
    
    edit_my_account($id, $edit_first_name, $edit_last_name, $edit_username, $edit_email);
    
}

// change password
if(isset($_POST['change_password'])) {
    
    $id = $_SESSION['id'];
    $new_password = $_POST['new_password'];
    
    change_password($id, $new_password);
}

?>
<!DOCTYPE html>
<html>
<head>
    
    <!-- meta -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- title -->
    <title>My Account - Support</title>

    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="libraries/bootstrap-select/css/bootstrap-select.min.css" rel="stylesheet">
    <link href="assets/css/sb-admin-2.css" rel="stylesheet">
    <link href="libraries/font-awesome/css/font-awesome.min.css" rel="stylesheet">

    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
    
</head>
<body>
    
    <div id="wrapper">

        <?php load_nav_menu(); ?>

        <div id="page-wrapper">
            <?php
                $id = $_SESSION['id'];
                global $db;
                $query = $db->prepare("SELECT * FROM `users` WHERE `id` = ?");
                $query->bindValue(1, $id);
                try {
                    $query->execute();
                    $result = $query->fetch();
                } catch (PDOException $e) {
                    die($e->getMessage());
                }
            ?>
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">My Account</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <div class="row">
                
                <!-- 6 columns area -->
                <div class="col-lg-6">
                    
                    <div class="well">
                        <form role="form" method="post" action="" class="no-margin">
                            <div class="form-group">
                                <label>ID</label>
                                <p class="form-control-static"><?php echo $result['id']; ?></p>
                            </div>
                            <div class="form-group">
                                <label>First Name</label>
                                <input type="text" class="form-control" name="edit_first_name" value="<?php echo $result['first_name']; ?>" />
                            </div>
                            <div class="form-group">
                                <label>Last Name</label>
                                <input type="text" class="form-control" name="edit_last_name" value="<?php echo $result['last_name']; ?>" />
                            </div>
                            <div class="form-group">
                                <label>Username</label>
                                <input type="text" class="form-control" name="edit_username" value="<?php echo $result['username']; ?>" />
                            </div>
                            <div class="form-group">
                                <label>Email</label>
                                <input type="text" class="form-control" name="edit_email" value="<?php echo $result['email']; ?>" />
                            </div>
                            <div class="form-group">
                                <label>Role</label>
                                <p class="form-control-static"><?php roleid_to_name($result['role']); ?></p>
                            </div>
                            <div class="form-group">
                                <label>Last Logged In</label>
                                <p class="form-control-static"><?php echo $result['last_logged_in']; ?></p>
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <button type="submit" class="btn btn-success" name="edit_user">Save Changes</button>
                            </div>
                        </form>
                    </div>
            
                </div>
                <!-- // 6 columns area -->
                
                <!-- 6 columns area -->
                <div class="col-lg-6">
                    
                    <div class="panel panel-default">
                        <!-- panel heading -->
                        <div class="panel-heading">Change Password</div>
                        <!-- panel body -->
                        <div class="panel-body">
                            <form role="form" method="post" action="">
                                <div class="form-group">
                                    <label>New Password</label>
                                    <input type="password" class="form-control" name="new_password" placeholder="Minimum 5 characters" />
                                </div>
                                <button type="submit" class="btn btn-success" name="change_password">Change Password</button>
                            </form>
                        </div>
                        
                    </div>
            
                </div>
                <!-- // 6 columns area -->
                
            </div>
            <!-- /.row -->
            
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

<?php include('includes/footer.php'); ?>