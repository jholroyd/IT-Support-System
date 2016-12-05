<?php
include('includes/config.php');

// protect the page from guest users.
logout_protect();

// if logout request has been sent.
if(isset($_GET['logout'])) {
	logout();
}

// create a ticket reply.
if(isset($_POST['new_reply'])){
    $user_id = $_SESSION['id'];
    $reply_content = $_POST['reply_content'];
	$ticket_id = $_POST['reply_ticket_id'];
    
    create_reply($user_id, $reply_content, $ticket_id);
}

// create a new ticket.
if(isset($_POST['new_ticket'])){
    $ticket_subject = $_POST['ticket_subject'];
	$ticket_user_id = $_SESSION['id'];
	$ticket_department = $_POST['ticket_department'];
	$ticket_room = $_POST['ticket_room'];
	$ticket_message = $_POST['ticket_message'];
    $ticket_priority = $_POST['ticket_priority'];
    $ticket_category = $_POST['ticket_category'];
    
    create_ticket_user($ticket_subject, $ticket_user_id, $ticket_department, $ticket_room, $ticket_message, $ticket_priority, $ticket_category);
}

// edit existing ticket.
if(isset($_POST['edit_ticket'])) {
    
    $edit_id = $_GET['edit'];
    $edit_subject = $_POST['edit_subject'];
	$edit_department = $_POST['edit_department'];
	$edit_room = $_POST['edit_room'];
	$edit_message = $_POST['edit_message'];
    $edit_priority = $_POST['edit_priority'];
    $edit_category = $_POST['edit_category'];
    
    edit_ticket_user($edit_id, $edit_subject, $edit_department, $edit_room, $edit_message, $edit_priority, $edit_category);
    
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
    <title><?php if(!$_SESSION['role'] == 2){ ?>Access Denied<?php } else { ?>My Tickets<?php } ?> - Support</title>

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
        
        <?php if(!$_SESSION['role'] == 2){ ?>
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
                $query = $db->prepare("SELECT * FROM `tickets` WHERE `id` = ? AND `user_id` = ?");
                $query->bindValue(1, $id);
                $query->bindValue(2, $_SESSION['id']);
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
                                <textarea class="form-control selectpicker" rows="10" name="edit_message"><?php echo $result['message']; ?></textarea>
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
                                <label>Category</label>
				                <select class="form-control selectpicker" name="edit_category" data-live-search="true" data-size="5">
                                    <?php select_list_categories_selected($result['category_id']); ?>
                                </select>
				            </div>
							<div class="form-group">
                                <label>Solution</label>
                                <p class="form-control-static">
								<?php 
								if($result['solution'] == ""){
									echo 'None available.';
								} else {
									echo $result['solution'];
								}
								?>
								</p>
                            </div>
                            <div class="form-group" style="margin-bottom:0;">
                                <a href="my_tickets.php" class="btn btn-default"><i class="fa fa-arrow-left"></i> Back</a>
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
                    <h1 class="page-header">Ticket Doesn't Exist</h1>
                </div>
            </div>
            <div class="row">
                <div class="col-lg-12">
                    <p>Sorry, but the ticket you are trying to access doesn't exist. Use the button below to go back to your tickets page.</p>
                    <a href="my_tickets.php" class="btn btn-success"><i class="fa fa-arrow-left"></i> Go to My Tickets</a>
                </div>
            </div>
            <?php } ?>
            <?php } else { ?>
            <div class="row">
                <div class="col-lg-12">
                    <h1 class="page-header">My Tickets</h1>
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
                            My Tickets
                            <div class="pull-right">
                                <div class="btn-group">
                                    <button type="button" class="btn btn-default btn-xs" data-toggle="modal" data-target="#myModal">Create New Ticket</button>
                                    <!-- Modal -->
									<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
										<div class="modal-dialog">
											<div class="modal-content">
												<div class="modal-header">
													<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
													<h4 class="modal-title" id="myModalLabel">Create New Ticket</h4>
												</div>
												<div class="modal-body">
													<form role="form" action="" method="post">
														<div class="form-group">
                                                            <label>Subject</label>
															<input type="text" class="form-control" name="ticket_subject" />
														</div>
                                                        <div class="form-group">
                                                            <label>Message</label>
                                                            <textarea class="form-control selectpicker" rows="10" name="ticket_message"></textarea>
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
                            <?php if(tickets_user_count() > 0){ ?>
                            <div class="table-responsive">
                                <table class="table no-margin">
                                    <thead>
                                        <tr>
                                            <th>Subject</th>
                                            <th>Room</th>
											<th>Category</th>
                                            <th>Status</th>
                                            <th>Replies</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $query = $db->prepare('SELECT `id`, `subject`, `category_id`, `user_id`, `room_id`, `status` FROM `tickets` WHERE `user_id` = ? ORDER BY `status`');
                                        $query->bindValue(1, $_SESSION['id']);

                                        $query->execute();
                                        foreach($query as $row){
                                            echo '<tr>';
                                                echo '<td>'.$row['subject'].'</td>';
                                                echo '<td>'.roomid_to_name($row['room_id']).'</td>';
												echo '<td>'.categoryid_to_name($row['category_id']).'</td>';
                                                echo '<td>'.status_to_name($row['status']).'</td>';
                                                echo '<td>'.ticket_replies_count($row['id']).'</td>';
                                                if($row['status'] == 2){
                                                    echo "<td>N/A</td>";
                                                } else {
                                                    echo "<td><a href='?edit=".$row['id']."' class='btn btn-default btn-xs'><i class='fa fa-edit'></i> Edit</a></td>";
                                                }
                                            echo '</tr>';
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php } else { ?>
                            <p>You haven't created any tickets yet.</p>
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