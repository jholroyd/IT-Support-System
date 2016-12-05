<?php
include('includes/config.php');

// protect the page from guest users.
logout_protect();

// if logout request has been sent.
if(isset($_GET['logout'])) {
	logout();
}

if(isset($_POST['new_user'])){
    $user_first_name = $_POST['user_first_name'];
	$user_last_name = $_POST['user_last_name'];
	$user_username = $_POST['user_username'];
	$user_email = $_POST['user_email'];
	$user_role = $_POST['user_role'];
    $user_password = $_POST['user_password'];
    
    create_user($user_first_name, $user_last_name, $user_username, $user_email, $user_role, $user_password);
}

// delete user
if(isset($_POST['delete_user'])) {
	$id = $_POST['del_user_id'];
	
	delete_user($id);
}

// edit user
if(isset($_POST['edit_user'])) {
    
    $id = $_GET['edit'];
    $edit_first_name = $_POST['edit_first_name'];
	$edit_last_name = $_POST['edit_last_name'];
	$edit_username = $_POST['edit_username'];
	$edit_email = $_POST['edit_email'];
	$edit_role = $_POST['edit_role'];
    
    edit_user($id, $edit_first_name, $edit_last_name, $edit_username, $edit_email, $edit_role);
    
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
    <title><?php if(!$_SESSION['role'] == 0){ ?>Access Denied<?php } else { ?>Users<?php } ?> - Support</title>

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

        <?php if(!$_SESSION['role'] == 0){ ?>
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
                $query = $db->prepare("SELECT * FROM `users` WHERE `id` = ?");
                $query->bindValue(1, $id);
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
                    <h1 class="page-header">User: #<?php echo $result['0']; ?></h1>
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
                                <select class="form-control selectpicker" name="edit_role" data-size="5">
                                    <?php
                                    if($result['role'] == 0){
                                        echo '<option value="0" selected>Administrator</option>';
                                        echo '<option value="1">Staff</option>';
                                        echo '<option value="2">User</option>';
                                    } elseif($result['role'] == 1) {
                                        echo '<option value="1" selected>Staff</option>';
                                        echo '<option value="0">Administrator</option>';
                                        echo '<option value="2">User</option>';
                                    } elseif($result['role'] == 2) {
                                        echo '<option value="2" selected>User</option>';
                                        echo '<option value="0">Administrator</option>';
                                        echo '<option value="1">Staff</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Last Logged In</label>
                                <p class="form-control-static"><?php echo $result['last_logged_in']; ?></p>
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <a href="users.php" class="btn btn-default"><i class="fa fa-arrow-left"></i> Back</a>
                                <button type="submit" class="btn btn-success pull-right" name="edit_user">Save Changes</button>
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
                    <h1 class="page-header">User Doesn't Exist</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <p>Sorry, but the user you are trying to access doesn't exist. Use the button below to go back to the users page.</p>
                    <a href="users.php" class="btn btn-success"><i class="fa fa-arrow-left"></i> Go to Users</a>
                </div>
            </div>
            <?php } ?>
            <?php } else { ?>
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Users</h1>
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
                            <i class="fa fa-users fa-fw"></i> Users
                            <div class="pull-right">
                                <div class="btn-group">
                                    <button data-toggle="modal" data-target="#myModal" class="btn btn-default btn-xs">Create New User</button>
									<!-- Modal -->
									<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header">
													<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
													<h4 class="modal-title" id="myModalLabel">Create New User</h4>
												</div>
												<div class="modal-body">
													<form role="form" action="" method="post">
                                                            <div class="form-group">
																<label>First Name</label>
                                                                <input type="text" class="form-control" name="user_first_name" />
                                                            </div>
                                                            <div class="form-group">
																<label>Last Name</label>
                                                                <input type="text" class="form-control" name="user_last_name" />
                                                            </div>
                                                            <div class="form-group">
																<label>Username</label>
                                                                <input type="text" class="form-control" name="user_username" />
                                                            </div>
                                                            <div class="form-group">
																<label>Email Address</label>
                                                                <input type="email" class="form-control" name="user_email" />
                                                            </div>
                                                            <div class="form-group">
																<label>Password</label>
                                                                <input type="password" class="form-control" name="user_password" placeholder="Minimum 5 characters" />
                                                            </div>
                                                            <div class="form-group">
																<label>Role</label>
                                                                <select class="form-control selectpicker" name="user_role" data-size="5">
                                                                    <option value="0">Administrator</option>
                                                                    <option value="1">Staff</option>
                                                                    <option value="2" selected>User</option>
                                                                </select>
                                                            </div>
												</div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-success" name="new_user">Create User</button>
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
                                            <th>Role</th>
                                            <th>Last Logged In</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = $db->prepare('SELECT `id`, `first_name`, `last_name`, `email`, `username`, `role`, `last_logged_in` FROM `users`');
                                        $query->execute();
                                        foreach($query as $row){
                                            echo '<tr>';
                                                echo '<td>'.$row['first_name'].' '.$row['last_name'].'</td>';
                                                echo '<td>'.$row['username'].'</td>';
                                                echo '<td>'.$row['email'].'</td>';
                                                if($row['role'] == 0){
                                                    echo '<td>Administrator</td>';
                                                } elseif($row['role'] == 1) {
                                                    echo '<td>Staff</td>';
                                                } elseif($row['role'] == 2) {
                                                    echo '<td>User</td>';
                                                }
                                                echo '<td>'.$row['last_logged_in'].'</td>';
                                                if($row['id'] == 1){
                                                    echo '<td><a href="?edit='.$row['id'].'" class="btn btn-default btn-xs"><i class="fa fa-edit"></i> Edit</a></td>';
                                                } else {
                                                    echo "<td><a href='?edit=".$row['id']."' class='btn btn-default btn-xs'><i class='fa fa-edit'></i> Edit</a> <form role='form' action='' method='post' class='form-inline'><button type='submit' name='delete_user' class='btn btn-danger btn-xs' onclick=\"return confirm('Are you sure you want to delete this user?')\"><i class='fa fa-remove'></i> Delete</button><input type='hidden' name='del_user_id' value='".$row['id']."' /></form></td>";
                                                }
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