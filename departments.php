<?php
include('includes/config.php');

// protect the page from guest users.
logout_protect();

// if logout request has been sent.
if(isset($_GET['logout'])) {
	logout();
}

if(isset($_POST['new_department'])){
    $department_name = $_POST['department_name'];
    create_department($department_name);
}

// delete department
if(isset($_POST['delete_department'])) {
	$id = $_POST['del_department_id'];
	
	delete_department($id);
}

// edit department
if(isset($_POST['edit_department'])) {
    
    $id = $_GET['edit'];
    $edit_department_name = $_POST['edit_department_name'];
    
    edit_department($id, $edit_department_name);
    
}

// import csv
if(isset($_POST['import_csv'])) {
    if($_FILES['csv']['size'] > 0) {
        $file = $_FILES['csv']['tmp_name']; 
        $handle = fopen($file,"r");
        
        import_department_csv($file, $handle);
    } else {
        construct_message('alert-danger', 'You need to choose a file to upload.');
    }
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
    <title><?php if($_SESSION['role'] == 2){ ?>Access Denied<?php } else { ?>Departments<?php } ?> - Support</title>

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

        <?php if($_SESSION['role'] == 2){ ?>
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
                $query = $db->prepare("SELECT * FROM `departments` WHERE `id` = ?");
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
                    <h1 class="page-header">Department: #<?php echo $result['0']; ?></h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <div class="row">
                
                <!-- 12 columns area -->
                <div class="col-lg-12">
                    
                    <div class="well">
                        <form role="form" method="post" action="" class="no-margin">
                            <div class="form-group">
                                <label>ID</label>
                                <p class="form-control-static"><?php echo $result['id']; ?></p>
                            </div>
                            <div class="form-group" style="margin-bottom:30px;">
                                <label>Name</label>
                                <input type="text" class="form-control" name="edit_department_name" value="<?php echo $result['name']; ?>" />
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <a href="departments.php" class="btn btn-default"><i class="fa fa-arrow-left"></i> Back</a>
                                <button type="submit" class="btn btn-success pull-right" name="edit_department">Save Changes</button>
                            </div>
                        </form>
                    </div>
            
                </div>
                <!-- // 12 columns area -->
                
            </div>
            <!-- /.row -->
            <?php } else { ?>
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Department Doesn't Exist</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <p>Sorry, but the department you are trying to access doesn't exist. Use the button below to go back to the departments page.</p>
                    <a href="departments.php" class="btn btn-success"><i class="fa fa-arrow-left"></i> Go to Departments</a>
                </div>
            </div>
            <?php } ?>
            <?php } else { ?>
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Departments</h1>
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
                            <i class="fa fa-cogs fa-fw"></i> Departments
                            <div class="pull-right">
                                <div class="btn-group">
                                    <button type="button" data-toggle="modal" data-target="#myModal" class="btn btn-default btn-xs">Create New Department</button>
									<!-- Modal -->
									<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header">
													<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
													<h4 class="modal-title" id="myModalLabel">Create New Department</h4>
												</div>
												<div class="modal-body">
													<form role="form" action="" method="post">
														<div class="form-group">
                                                            <input type="text" class="form-control" name="department_name" placeholder="Enter a department name here ..." />
														</div>
												</div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-success" name="new_department">Create Department</button>
                                                    </form>
                                                </div>
											</div>
											<!-- /.modal-content -->
										</div>
										<!-- /.modal-dialog -->
									</div>
									<!-- /.modal -->
                                </div>
                                <div class="btn-group">
                                    <button type="button" data-toggle="modal" data-target="#importCSV" class="btn btn-default btn-xs">Import CSV</button>
                                    <!-- Modal -->
									<div class="modal fade" id="importCSV" tabindex="-1" role="dialog" aria-labelledby="importCSVLabel" aria-hidden="true">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header">
													<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
													<h4 class="modal-title" id="importCSVLabel">Import CSV</h4>
												</div>
												<div class="modal-body">
													<form role="form" action="" method="post" enctype="multipart/form-data">
														<div class="form-group">
                                                            <label>Choose a file to import</label>
															<input type="file" name="csv" id="csv" />
														</div>
												</div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-success" name="import_csv">Import CSV</button>
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
                            <?php if(departments_count() > 0){ ?>
                            <div class="table-responsive">
                                <table class="table no-margin">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = $db->prepare('SELECT `id`, `name` FROM `departments`');
                                        $query->execute();
                                        foreach($query as $row){
                                            echo '<tr>';
                                                echo '<td>'.$row['name'].'</td>';
                                                echo "<td><a href='?edit=".$row['id']."' class='btn btn-default btn-xs'><i class='fa fa-edit'></i> Edit</a> <form method='post' action='' class='form-inline'><button type='submit' name='delete_department' class='btn btn-danger btn-xs' onclick=\"return confirm('Are you sure you want to delete this department?')\"><i class='fa fa-remove'></i> Delete</button><input type='hidden' name='del_department_id' value='".$row['id']."' /></form></td>";
                                            echo '</tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php } else { ?>
                            <p>There have been no departments created yet.</p>
                            <?php } ?>
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