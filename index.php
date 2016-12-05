<?php
include('includes/config.php');

// protect the page from guest users.
logout_protect();

// if logout request has been sent.
if(isset($_GET['logout'])) {
	logout();
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
    <title>Dashboard - Support</title>

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
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Dashboard</h1>
                </div>
                <!-- /.col-lg-12 -->
            </div>
            <div class="row">
                
                <!-- 6 columns area -->
                <div class="col-lg-8">
                    
                    <!-- summary -->
                    <div class="panel panel-default">
                        
                        <!-- panel heading -->
                        <div class="panel-heading"><i class="fa fa-support fa-fw"></i> Ticket Summary</div>
                        
                        <?php if($_SESSION['role'] == 0){ ?>
                        <div class="panel-body">
                            <div class="list-group no-margin">
                                <div class="list-group-item"><span class="badge badge-success"><?php open_tickets_count(); ?></span> Open Tickets</div>
                                <div class="list-group-item"><span class="badge badge-warning"><?php in_progress_tickets_count(); ?></span> In Progress Tickets</div>
                                <div class="list-group-item"><span class="badge badge-danger"><?php closed_tickets_count(); ?></span> Closed Tickets</div>
                            </div>
                        </div>
                        <?php } elseif($_SESSION['role'] == 1){ ?>
                        <div class="panel-body">
                            <div class="list-group no-margin">
                                <div class="list-group-item"><span class="badge badge-success"><?php open_tickets_count(); ?></span> Open Tickets</div>
                                <div class="list-group-item"><span class="badge badge-warning"><?php in_progress_tickets_count(); ?></span> In Progress Tickets</div>
                                <div class="list-group-item"><span class="badge badge-danger"><?php closed_tickets_count(); ?></span> Closed Tickets</div>
                            </div>
                        </div>
                        <?php } elseif($_SESSION['role'] == 2){ ?>
                        <div class="panel-body">
                            <div class="list-group no-margin">
                                <div class="list-group-item"><span class="badge"><?php open_tickets_user_count(); ?></span> Tickets created by you</div>
                            </div>
                        </div>
                        <?php } ?>
                        
                    </div>
                    <!-- // summary -->
                    
                    <?php if($_SESSION['role'] == 2){ ?>
    
                    <?php } else { ?>
                    <!-- summary -->
                    <div class="panel panel-default">
                        
                        <!-- panel heading -->
                        <div class="panel-heading"><i class="fa fa-cogs fa-fw"></i> System Info</div>
                        
                        <?php if($_SESSION['role'] == 0){ ?>
                        <div class="panel-body">
                            <div class="list-group no-margin">
                                <div class="list-group-item"><span class="badge"><?php user_count(); ?></span> Users</div>
                                <div class="list-group-item"><span class="badge"><?php department_count(); ?></span> Departments</div>
                                <div class="list-group-item"><span class="badge"><?php room_count(); ?></span> Rooms</div>
                                <div class="list-group-item"><span class="badge"><?php categories_index_count(); ?></span> Categories</div>
                            </div>
                        </div>
                        <?php } elseif($_SESSION['role'] == 1){ ?>
                        <div class="panel-body">
                            <div class="list-group no-margin">
                                <div class="list-group-item"><span class="badge"><?php staff_count(); ?></span> Staff Members</div>
                                <div class="list-group-item"><span class="badge"><?php department_count(); ?></span> Departments</div>
                                <div class="list-group-item"><span class="badge"><?php room_count(); ?></span> Rooms</div>
                                <div class="list-group-item"><span class="badge"><?php categories_index_count(); ?></span> Categories</div>
                            </div>
                        </div>
                        <?php } elseif($_SESSION['role'] == 2){ ?>
                        <div class="panel-body">
                            <div class="list-group no-margin">
                                <div class="list-group-item"><span class="badge"><?php open_tickets_user_count(); ?></span> Tickets created by you</div>
                            </div>
                        </div>
                        <?php } ?>
                        
                    </div>
                    <!-- // summary -->
                    <?php } ?>
                    
                </div>
                <!-- // 6 columns area -->
                
                <!-- 6 columns area -->
                <div class="col-lg-4">
                    
                    <?php if($_SESSION['role'] == 2){ ?>
                    <!-- summary -->
                    <div class="panel panel-default">
                        
                        <!-- panel heading -->
                        <div class="panel-heading"><i class="fa fa-comments fa-fw"></i> Create A Ticket</div>
                        
                        <div class="panel-body">
                            <form role="form" action="" method="post">
														<div class="form-group">
                                                            <label>Subject</label>
															<input type="text" class="form-control" name="ticket_subject" />
														</div>
                                                        <div class="form-group">
                                                            <label>Message</label>
                                                            <textarea class="form-control selectpicker" rows="5" name="ticket_message"></textarea>
                                                        </div>
                                                        <div class="form-group">
                                                            <label>Priority</label>
															<div class="radio"><label><input type="radio" name="ticket_priority" id="ticket_priority" value="0" checked> Low</label></div>
			                                                <div class="radio"><label><input type="radio" name="ticket_priority" id="ticket_priority" value="1"> Medium</label></div>
			                                                <div class="radio"><label><input type="radio" name="ticket_priority" id="ticket_priority" value="2"> High</label></div>
														</div>
                                                        <div class="form-group">
                                                            <label>Department</label>
															<select class="form-control selectpicker" name="ticket_department" data-live-search="true" data-size="5">
                                                                <?php select_list_departments(); ?>
                                                            </select>
														</div>
                                                        <div class="form-group">
                                                            <label>Room</label>
															<select class="form-control selectpicker" name="ticket_room" data-live-search="true" data-size="5">
                                                                <?php select_list_rooms(); ?>
                                                            </select>
														</div>
                                                        <div class="form-group">
                                                            <label>Category</label>
															<select class="form-control selectpicker" name="ticket_category" data-live-search="true" data-size="5">
                                                                <?php select_list_categories(); ?>
                                                            </select>
														</div>
												</div>
                                                <div class="modal-footer">
                                                    <input type="hidden" name="ticket_user_id" value="<?php echo $_SESSION['id']; ?>" />
                                                    <button type="submit" class="btn btn-success" name="new_ticket">Create Ticket</button>
                                                    </form>
                        </div>
                        
                    </div>
                    <!-- // summary -->
                    <?php } else { ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <i class="fa fa-tasks fa-fw"></i> Tasks
                        </div>
                        <!-- /.panel-heading -->
                        <div class="panel-body">
                            <div class="list-group">
                                <a href="#" class="list-group-item">Task One
                                    <span class="pull-right text-muted small"><em>4 minutes ago</em></span>
                                </a>
                                <a href="#" class="list-group-item">Task Two
                                    <span class="pull-right text-muted small"><em>12 minutes ago</em></span>
                                </a>
                                <a href="#" class="list-group-item">Task Three
                                    <span class="pull-right text-muted small"><em>27 minutes ago</em></span>
                                </a>
                                <a href="#" class="list-group-item">Task Four
                                    <span class="pull-right text-muted small"><em>43 minutes ago</em></span>
                                </a>
                                <a href="#" class="list-group-item">Task Five
                                    <span class="pull-right text-muted small"><em>11:32 AM</em></span>
                                </a>
                            </div>
                            <!-- /.list-group -->
                            <a href="tasks.php" class="btn btn-default btn-block">View All Tasks</a>
                        </div>
                        <!-- /.panel-body -->
                    </div>
                    <?php } ?>
                    
                </div>
                <!-- // 6 columns area -->
            
            </div>
            <!-- /.row -->
            
        </div>
        <!-- /#page-wrapper -->

    </div>
    <!-- /#wrapper -->

<?php include('includes/footer.php'); ?>