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
    <title><?php if($_SESSION['role'] == 2){ ?>Access Denied<?php } else { ?>Tasks<?php } ?> - Support</title>

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
                $query = $db->prepare("SELECT * FROM `tickets` WHERE `id` = ?");
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
                    <h1 class="page-header">Ticket: #<?php echo $result['0']; ?></h1>
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
                                <label>Created</label>
                                <p class="form-control-static"><?php echo $result['created']; ?></p>
                            </div>
                            
                            <div class="form-group">
                                <label>Subject</label>
                                <input type="text" class="form-control" name="edit_subject" value="<?php echo $result['subject']; ?>" />
                            </div>
                            <div class="form-group">
                                <label>Message</label>
                                <textarea class="form-control" rows="10" name="edit_message"><?php echo $result['message']; ?></textarea>
                            </div>
                            <div class="form-group">
                                <label>Status</label>
                                <select class="form-control selectpicker" name="edit_status" data-size="5">
                                    <?php
                                    if($result['status'] == 0){
                                        echo '<option value="0" selected>Open</option>';
                                        echo '<option value="1">In Progress</option>';
                                        echo '<option value="2">Closed</option>';
                                    } elseif($result['status'] == 1) {
                                        echo '<option value="1" selected>In Progress</option>';
                                        echo '<option value="0">Open</option>';
                                        echo '<option value="2">Closed</option>';
                                    } elseif($result['status'] == 2) {
                                        echo '<option value="2" selected>Closed</option>';
                                        echo '<option value="0">Open</option>';
                                        echo '<option value="1">In Progress</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Staff Member</label>
				                <select class="form-control selectpicker" name="edit_user_id" data-live-search="true" data-size="5">
                                    <?php select_list_staff_member_selected($result['user_id']); ?>
                                </select>
				            </div>
                            <div class="form-group">
                                <label>Priority</label>
                                <?php
                                    if($result['priority'] == 0){
                                        echo '<div class="radio"><label><input type="radio" name="edit_priority" value="0" checked> Low</label></div>';
                                        echo '<div class="radio"><label><input type="radio" name="edit_priority" value="1"> Medium</label></div>';
                                        echo '<div class="radio"><label><input type="radio" name="edit_priority" value="2"> High</label></div>';
                                    } elseif($result['priority'] == 1) {
                                        echo '<div class="radio"><label><input type="radio" name="edit_priority" value="0"> Low</label></div>';
                                        echo '<div class="radio"><label><input type="radio" name="edit_priority" value="1" checked> Medium</label></div>';
                                        echo '<div class="radio"><label><input type="radio" name="edit_priority" value="2"> High</label></div>';
                                    } elseif($result['priority'] == 2) {
                                        echo '<div class="radio"><label><input type="radio" name="edit_priority" value="0"> Low</label></div>';
                                        echo '<div class="radio"><label><input type="radio" name="edit_priority" value="1"> Medium</label></div>';
                                        echo '<div class="radio"><label><input type="radio" name="edit_priority" value="2" checked> High</label></div>';
                                    }
                                ?>
                            </div>
                            <div class="form-group">
                                <label>Department</label>
				                <select class="form-control selectpicker" name="edit_department" data-live-search="true" data-size="5">
                                    <?php select_list_departments_selected($result['department_id']); ?>
                                </select>
				            </div>
                            <div class="form-group">
                                <label>Room</label>
				                <select class="form-control selectpicker" name="edit_room" data-live-search="true" data-size="5">
                                    <?php select_list_rooms_selected($result['room_id']); ?>
                                </select>
				            </div>
                            <div class="form-group">
                                <label>Device</label>
				                <select class="form-control selectpicker" name="edit_device" data-live-search="true" data-size="5">
                                    <?php select_list_devices_selected($result['device_id']); ?>
                                </select>
				            </div>
							<div class="form-group">
                                <label>Solution</label>
                                <textarea class="form-control" rows="10" name="edit_solution"><?php echo $result['solution']; ?></textarea>
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <a href="tickets.php" class="btn btn-default"><i class="fa fa-arrow-left"></i> Back</a>
                                <button type="submit" class="btn btn-success pull-right" name="edit_ticket">Save Changes</button>
                            </div>
                        </form>
                    </div>
                    
                </div>
                <!-- // 6 columns -->
                    
                <div class="col-lg-6">
                    
                    <!-- summary -->
                    <div class="panel panel-default">
                        
                        <!-- panel heading -->
                        <div class="panel-heading">
                            Replies
                            <div class="pull-right">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#myModal">Create New Reply</button>
                                    <!-- Modal -->
									<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header">
													<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
													<h4 class="modal-title" id="myModalLabel">Create New Reply</h4>
												</div>
												<div class="modal-body">
													<form role="form" action="" method="post">
                                                        <div class="form-group">
                                                            <textarea class="form-control selectpicker" rows="10" name="reply_content" placeholder="Write your reply here ..."></textarea>
                                                        </div>
												</div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-success" name="new_reply">Create Reply</button>
                                                    <input type="hidden" name="reply_ticket_id" value="<?php echo $_GET['edit']; ?>" />
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
                            <?php if(ticket_replies_count($_GET['edit']) > 0){ ?>
                            <div class="table-responsive">
                                <table class="table no-margin">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Date</th>
                                            <th>Content</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = $db->prepare('SELECT * FROM `ticket_replies` WHERE `ticket_id` = ? ORDER BY `datetime` ASC');
                                        $query->bindValue(1, $_GET['edit']);

                                        $query->execute();
                                        foreach($query as $row){
                                            echo '<tr>';
                                                echo '<td>'.userid_to_name($row['user_id']).'</td>';
                                                echo '<td>'.$row['datetime'].'</td>';
                                                echo '<td>'.$row['content'].'</td>';
                                                echo "<td><form role='form' action='' method='post' class='form-inline'><button type='submit' name='delete_ticket_reply' class='btn btn-danger btn-xs' onclick=\"return confirm('Are you sure you want to delete this reply?')\"><i class='fa fa-remove'></i> Delete</button><input type='hidden' name='ticket_reply_id' value='".$row['id']."' /></form></td>";
                                            echo '</tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php } else { ?>
                            <p>There are no replies for this ticket.</p>
                            <?php } ?>
                        </div>
                    
                    </div>
                    
                </div>
                <!-- // 6 columns area -->
                
            </div>
            <!-- /.row -->
            <?php } else { ?>
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Task Doesn't Exist</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <p>Sorry, but the task you are trying to access doesn't exist. Use the button below to go back to the tasks page.</p>
                    <a href="tasks.php" class="btn btn-success"><i class="fa fa-arrow-left"></i> Go to Tasks</a>
                </div>
            </div>
            <?php } ?>
            <?php } else { ?>
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">Tasks</h1>
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
                            <i class="fa fa-tasks fa-fw"></i> Tasks
                            <div class="pull-right">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#myModal">Create New Task</button>
                                    <!-- Modal -->
									<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header">
													<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
													<h4 class="modal-title" id="myModalLabel">Create New Task</h4>
												</div>
												<div class="modal-body">
													<form role="form" action="" method="post">
                                                        <div class="row">
                                                            <div class="col-lg-6">
                                                                <div class="form-group">
                                                                    <label>Subject</label>
                                                                    <input type="text" class="form-control" placeholder="What's your problem about?" name="ticket_subject" />
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Message</label>
                                                                    <textarea class="form-control selectpicker" placeholder="Explain your problem here..." rows="10" name="ticket_message"></textarea>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Staff Member</label>
                                                                    <select class="form-control selectpicker" name="ticket_user_id" data-live-search="true" data-size="5">
                                                                        <?php select_list_staff_members(); ?>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="col-lg-6">
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
                                                                    <label>Device</label>
                                                                    <select class="form-control selectpicker" name="ticket_device" data-live-search="true" data-size="5">
                                                                        <?php select_list_devices(); ?>
                                                                    </select>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label>Priority</label>
                                                                    <div class="radio"><label><input type="radio" name="ticket_priority" id="ticket_priority" value="0" checked> Low</label></div>
                                                                    <div class="radio"><label><input type="radio" name="ticket_priority" id="ticket_priority" value="1"> Medium</label></div>
                                                                    <div class="radio"><label><input type="radio" name="ticket_priority" id="ticket_priority" value="2"> High</label></div>
                                                                </div>
                                                            </div>
                                                        </div>
												</div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                    <button type="submit" class="btn btn-success" name="new_ticket">Create Ticket</button>
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
                            <?php if(tickets_count() > 0){ ?>
                            <div class="table-responsive">
                                <table class="table no-margin table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Subject</th>
                                            <th>Staff Member</th>
                                            <th>Room</th>
											<th>Device</th>
                                            <th>Status</th>
                                            <th>Replies</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
										$query = $db->prepare('SELECT `id`, `subject`, `device_id`, `user_id`, `room_id`, `status` FROM `tickets` ORDER BY `status`');
										
                                        $query->execute();
                                        foreach($query as $row){
                                            echo '<tr>';
                                                echo '<td>'.$row['subject'].'</td>';
                                                echo '<td>'.userid_to_name($row['user_id']).'</td>';
                                                echo '<td>'.roomid_to_name($row['room_id']).'</td>';
												echo '<td>'.deviceid_to_name($row['device_id']).'</td>';
                                                echo '<td>'.status_to_name($row['status']).'</td>';
                                                echo '<td>'.ticket_replies_count($row['id']).'</td>';
                                                if($row['status'] == 2){
                                                    if($_SESSION['role'] == 0){
                                                        echo "<td><form role='form' action='' method='post' class='form-inline'><button type='submit' name='reopen_ticket' class='btn btn-default btn-xs' onclick=\"return confirm('Are you sure you want to re-open this ticket?')\"><i class='fa fa-plus'></i> Re-Open</button> <button type='submit' name='delete_ticket' class='btn btn-danger btn-xs' onclick=\"return confirm('Are you sure you want to permanently delete this ticket?')\"><i class='fa fa-remove'></i> Delete</button><input type='hidden' name='delete_ticket_id' value='".$row['id']."' /><input type='hidden' name='reopen_ticket_id' value='".$row['id']."' /></form></td>";
                                                    } else {
                                                        echo "<td><form role='form' action='' method='post' class='form-inline'><button type='submit' name='reopen_ticket' class='btn btn-default btn-xs' onclick=\"return confirm('Are you sure you want to re-open this ticket?')\"><i class='fa fa-plus'></i> Re-Open</button><input type='hidden' name='reopen_ticket_id' value='".$row['id']."' /></form></td>";
                                                    }
                                                } else {
                                                echo "<td><a href='?edit=".$row['id']."' class='btn btn-default btn-xs'><i class='fa fa-edit'></i> Edit</a> <form role='form' action='' method='post' class='form-inline'><button type='submit' name='close_ticket' class='btn btn-danger btn-xs' onclick=\"return confirm('Are you sure you want to close this ticket?')\"><i class='fa fa-remove'></i> Close</button><input type='hidden' name='close_ticket_id' value='".$row['id']."' /></form></td>";
                                                }
                                            echo '</tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php } else { ?>
                            <p>There have been no tickets created yet.</p>
                            <?php } ?>
                        </div>
                    
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