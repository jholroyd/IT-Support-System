<?php
include('includes/config.php');

// protect the page from guest users.
logout_protect();

// if logout request has been sent.
if(isset($_GET['logout'])) {
	logout();
}

if(isset($_POST['new_staff'])){
    $staff_first_name = $_POST['staff_first_name'];
	$staff_last_name = $_POST['staff_last_name'];
	$staff_username = $_POST['staff_username'];
	$staff_email = $_POST['staff_email'];
    $staff_password = $_POST['staff_password'];
    
    create_staff($staff_first_name, $staff_last_name, $staff_username, $staff_email, $staff_password);
}

// delete staff
if(isset($_POST['delete_staff'])) {
	$id = $_POST['del_staff_id'];
	
	delete_staff($id);
}

// edit staff
if(isset($_POST['edit_staff'])) {
    
    $id = $_GET['edit'];
    $edit_first_name = $_POST['edit_first_name'];
	$edit_last_name = $_POST['edit_last_name'];
	$edit_username = $_POST['edit_username'];
	$edit_email = $_POST['edit_email'];
    
    edit_staff($id, $edit_first_name, $edit_last_name, $edit_username, $edit_email);
    
}

// change password
if(isset($_POST['change_password'])) {
    
    $id = $_GET['edit'];
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
    <title><?php if(!$_SESSION['role'] == 1){ ?>Access Denied<?php } else { ?>Staff Members<?php } ?> - Support</title>

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

        <?php if(!$_SESSION['role'] == 1){ ?>
        <div id="page-wrapper">
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Access Denied</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <p>Sorry, but you are not allowed to access this page. Use the button below to go to the dashboard.</p>
                    <a href="index.php" class="btn btn-success"><i class="fa fa-arrow-left"></i> Go to Dashboard</a>
                </div>
            </div>
        </div>
        <?php } else { ?>
        <div id="page-wrapper">
            <?php if(array_key_exists('edit', $_GET)){ ?>
            <?php
                $id = $_GET['edit'];
                global $db;
                $query = $db->prepare("SELECT * FROM `users` WHERE `id` = ? AND `role` = ?");
                $query->bindValue(1, $id);
                $query->bindValue(2, 2);
                try {
                    $query->execute();
                    $result = $query->fetch();
                } catch (PDOException $e) {
                    die($e->getMessage());
                }
    
                if($query->rowCount() > 0){
            ?>
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Staff Member: #<?php echo $result['0']; ?></h1>
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
                                <label>Last Logged In</label>
                                <p class="form-control-static"><?php echo $result['last_logged_in']; ?></p>
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <a href="staff_members.php" class="btn btn-default"><i class="fa fa-arrow-left"></i> Back</a>
                                <button type="submit" class="btn btn-success pull-right" name="edit_staff">Save Changes</button>
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
            <?php } else { ?>
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Staff Member Doesn't Exist</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <p>Sorry, but the staff member you are trying to access doesn't exist. Use the button below to go back to the staff members page.</p>
                    <a href="staff_members.php" class="btn btn-success"><i class="fa fa-arrow-left"></i> Go to Staff Members</a>
                </div>
            </div>
            <?php } ?>
            <?php } else { ?>
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Staff Members</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <div class="row">
                
                <!-- 12 columns area -->
                <div class="col-lg-12">
                    
                    <!-- summary -->
                    <div class="panel panel-default">
                        
                        <!-- panel heading -->
                        <div class="panel-heading">
                            Staff Members
                            <div class="pull-right">
                                <div class="btn-group">
                                    <button data-toggle="modal" data-target="#myModal" class="btn btn-default btn-xs">Create New Staff Member</button>
									<!-- Modal -->
									<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header">
													<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
													<h4 class="modal-title" id="myModalLabel">Create New Staff Member</h4>
												</div>
												<div class="modal-body">
													<form role="form" action="" method="post">
                                                            <div class="form-group">
																<label>First Name</label>
                                                                <input type="text" class="form-control" name="staff_first_name" />
                                                            </div>
                                                            <div class="form-group">
																<label>Last Name</label>
                                                                <input type="text" class="form-control" name="staff_last_name" />
                                                            </div>
                                                            <div class="form-group">
																<label>Username</label>
                                                                <input type="text" class="form-control" name="staff_username" />
                                                            </div>
                                                            <div class="form-group">
																<label>Email Address</label>
                                                                <input type="email" class="form-control" name="staff_email" />
                                                            </div>
                                                            <div class="form-group">
																<label>Password</label>
                                                                <input type="password" class="form-control" name="staff_password" placeholder="Minimum 5 characters" />
                                                            </div>
												</div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-success" name="new_staff">Create Staff Member</button>
													</form>
                                                </div>
											</div>
											<!-- /.modal-content -->
										</div>
										<!-- /.modal-dialog -->
									</div>
									<!-- /.modal -->
                                </div>
                            </div>
                        </div>
                        
                        <!-- panel body -->
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table no-margin">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Username</th>
                                            <th>Email</th>
                                            <th>Last Logged In</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = $db->prepare('SELECT `id`, `first_name`, `last_name`, `email`, `username`, `role`, `last_logged_in` FROM `users` WHERE `role` = ?');
                                        $query->bindValue(1, 2);
                                        $query->execute();
                                        foreach($query as $row){
                                            echo '<tr>';
                                                echo '<td>'.$row['first_name'].' '.$row['last_name'].'</td>';
                                                echo '<td>'.$row['username'].'</td>';
                                                echo '<td>'.$row['email'].'</td>';
                                                echo '<td>'.$row['last_logged_in'].'</td>';
                                                echo "<td><a href='?edit=".$row['id']."' class='btn btn-default btn-xs'><i class='fa fa-edit'></i> Edit</a> <form role='form' action='' method='post' class='form-inline'><button type='submit' name='delete_staff' class='btn btn-danger btn-xs' onclick=\"return confirm('Are you sure you want to delete this staff member?')\"><i class='fa fa-remove'></i> Delete</button><input type='hidden' name='del_staff_id' value='".$row['id']."' /></form></td>";
                                            echo '</tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <!-- /.table-responsive -->
                        </div>
                        
                    </div>
                    <!-- // summary -->
                    
                </div>
                <!-- // 12 columns area -->
                
            </div>
            <!-- /.row -->
            
            <?php } ?>
            
        </div>
        <!-- /#page-wrapper -->
        <?php } ?>

    </div>
    <!-- /#wrapper -->

<?php include('includes/footer.php'); ?>