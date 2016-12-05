<?php

// construct_message
function construct_message($type, $text) {
	echo '<div class="alert '.$type.' alter-dismissable no-margin no-radius">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
            '.$text.'
        </div>';
}

// send_email
function send_email($from, $to, $subject, $body) {
	$headers = "From: $from\nReply-To: $from\nContent-Type: text/html";
	@mail($to,$subject,$body,$headers);
}

// login
function login($username, $password) {
	global $bcrypt;
	global $db;
		
	$query = $db->prepare("SELECT * FROM `users` WHERE `username` = ?");
	$query->bindValue(1, $username);
	
	if(empty($username)) {
        construct_message('alert-danger', 'You forgot to enter your login details.');
    } elseif(!username_exists($username)) {
		construct_message('alert-danger', 'Sorry, but that username does not exist.');
	} else {
		try {
			$query->execute();
			$data = $query->fetch();
			$stored_password = $data['password'];
				
			if ($bcrypt->verify($password, $stored_password) === true) {
				// logged in
				session_regenerate_id(true);
				unset($data['password']);
				$_SESSION['id'] = $data['id'];
				$_SESSION['first_name'] = $data['first_name'];
				$_SESSION['last_name'] = $data['last_name'];
                $_SESSION['username'] = $data['username'];
                $_SESSION['email'] = $data['email'];
                $_SESSION['role'] = $data['role'];
				$_SESSION['logged_in'] = 1;
				set_last_logged_in($data['id']);
				$_SESSION['last_logged_in'] = $data['last_logged_in'];
				header('Location: index.php');
				exit();
			} else {
				// not logged in
				construct_message('alert-danger', 'Sorry, wrong password.');
			}
		} catch (PDOException $e) {
			die($e->getMessage());
		}
	}
}

// logged_in
function logged_in() {
	return (isset($_SESSION['logged_in'])) ? true : false;
}

// login_protect
function login_protect() {
	if(logged_in() === true){
		header('Location: index.php');
		exit();
	}
}

// logout_protect
function logout_protect() {
	if(logged_in() === false){
		header('Location: login.php');
		exit();
	}
}

// generate_password_hash
function generate_password_hash($password) {
	global $bcrypt;
	$password_hash = $bcrypt->genHash($password);
	return $password_hash;
}

// logout
function logout() {
	$_SESSION = array();
	session_destroy();
	header('Location: login.php');
	exit();
}

// set_last_logged_in
function set_last_logged_in($id) {
	global $db;
	$time = date("Y-m-d H:i:s");
	
	$query = $db->prepare('UPDATE `users` SET `last_logged_in` = ? WHERE `id` = ?');
	$query->bindValue(1, $time);
	$query->bindValue(2, $id);
	
	try {
		$query->execute();
	} catch (PDOException $e) {
		die($e->getMessage());
	}
}

// status to name
function status_to_name($id){
    if($id == 0){
        return '<span class="label label-success">Open</span>';
    } elseif($id == 1){
        return '<span class="label label-warning">In Progress</span>';
    } elseif($id == 2){
        return '<span class="label label-danger">Closed</span>';
    }
}

function roleid_to_name($id){
    if($id == 0){
        echo 'Administrator';
    } elseif($id == 1){
        echo 'Staff';
    } elseif($id == 2){
        echo 'User';
    }
}

// change password
function change_password($id, $new_password){
    global $db;
    
    $query = $db->prepare('SELECT `id`, `username`, `password` FROM `users` WHERE `id` = ?');
    $query->bindValue(1, $id);
    $query->execute();
    $results = $query->fetch();
    
    $new_password2 = generate_password_hash($new_password);
    
    if(!user_id_exists($id)){
        construct_message('alert-danger', 'Sorry but that user does not exist.');
    } elseif(empty($new_password)){
        construct_message('alert-danger', 'You forgot to enter your new password.');
    } else {
        $query2 = $db->prepare('UPDATE `users` SET `password` = ? WHERE `id` = ?');
        $query2->bindValue(1, $new_password2);
        $query2->bindValue(2, $id);
        try {
            $query2->execute();
            construct_message('alert-success', 'You have successfully changed the password.');
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }
}

// import_department_csv
function import_department_csv($file, $handle) {
    global $db;
    $data = '';
    
    do {
        if ($data) { 
            $db->query("INSERT INTO `departments` (name) VALUES ('".addslashes($data[0])."')"); 
        } 
    } while ($data = fgetcsv($handle,1000,",","'"));
    construct_message('alert-success', 'You have successfully imported a csv.');
}

// import_categories_csv
function import_categories_csv($file, $handle) {
    global $db;
    $data = '';
    
    do {
        if ($data) { 
            $db->query("INSERT INTO `categories` (name) VALUES ('".addslashes($data[0])."')"); 
        } 
    } while ($data = fgetcsv($handle,1000,",","'"));
    construct_message('alert-success', 'You have successfully imported a csv.');
}

// import_rooms_csv
function import_rooms_csv($file, $handle) {
    global $db;
    $data = '';
    
    do {
        if ($data) { 
            $db->query("INSERT INTO `rooms` (name) VALUES ('".addslashes($data[0])."')"); 
        } 
    } while ($data = fgetcsv($handle,1000,",","'"));
    construct_message('alert-success', 'You have successfully imported a csv.');
}


// close_ticket
function close_ticket($id){
    global $db;
    
    $query = $db->prepare("UPDATE `tickets` SET `status` = ? WHERE `id` = ?");
    $query->bindValue(1, 2);
    $query->bindValue(2, $id);
    
    try {
        $query->execute();
        construct_message('alert-success', 'The ticket has been successfully closed.');
    } catch (PDOException $e) {
		die($e->getMessage());
	}
    
}

// re-open ticket
function reopen_ticket($id){
    global $db;
    
    $query = $db->prepare("UPDATE `tickets` SET `status` = ? WHERE `id` = ?");
    $query->bindValue(1, 0);
    $query->bindValue(2, $id);
    
    try {
        $query->execute();
        construct_message('alert-success', 'The ticket has now been re-opened.');
    } catch (PDOException $e) {
		die($e->getMessage());
	}
    
}

/*==========
COUNT FUNCTIONS
==========*/

// user count
function user_count() {
	global $db;
	
	$query = $db->prepare('SELECT count(*) FROM `users`');
	$query->execute();
	$rows = $query->fetch(PDO::FETCH_NUM);
	
	if($rows > 0){
		echo $rows[0];
	}
}

// staff count
function staff_count() {
	global $db;
	
	$query = $db->prepare('SELECT count(*) FROM `users` WHERE `role` = ?');
    $query->bindValue(1, 2);
	$query->execute();
	$rows = $query->fetch(PDO::FETCH_NUM);
	
	if($rows > 0){
		echo $rows[0];
	}
}

// department count
function department_count() {
	global $db;
	
	$query = $db->prepare('SELECT count(*) FROM `departments`');
	$query->execute();
	$rows = $query->fetch(PDO::FETCH_NUM);
	
	if($rows > 0){
		echo $rows[0];
	}
}

// departments count
function departments_count() {
	global $db;
	
	$query = $db->prepare('SELECT count(*) FROM `departments`');
    
	$query->execute();
	$rows = $query->fetch(PDO::FETCH_NUM);
	
    return $rows[0];
}

// room count
function room_count() {
	global $db;
	
	$query = $db->prepare('SELECT count(*) FROM `rooms`');
	$query->execute();
	$rows = $query->fetch(PDO::FETCH_NUM);
	
	if($rows > 0){
		echo $rows[0];
	}
}

// rooms count
function rooms_count() {
	global $db;
	
	$query = $db->prepare('SELECT count(*) FROM `rooms`');
    
	$query->execute();
	$rows = $query->fetch(PDO::FETCH_NUM);
	
    return $rows[0];
}

// categories index count
function categories_index_count() {
	global $db;
	
	$query = $db->prepare('SELECT count(*) FROM `categories`');
	$query->execute();
	$rows = $query->fetch(PDO::FETCH_NUM);
	
	if($rows > 0){
		echo $rows[0];
	}
}

// categories_count
function categories_count() {
	global $db;
	
	$query = $db->prepare('SELECT count(*) FROM `categories`');
    
	$query->execute();
	$rows = $query->fetch(PDO::FETCH_NUM);
	
    return $rows[0];
}

// open tickets count
function open_tickets_count() {
	global $db;
	
	$query = $db->prepare('SELECT count(*) FROM `tickets` WHERE `status` = ?');
    $query->bindValue(1, 0);
    
	$query->execute();
	$rows = $query->fetch(PDO::FETCH_NUM);
	
	if($rows > 0){
		echo $rows[0];
	}
}

// open tickets user count
function open_tickets_user_count() {
	global $db;
	
	$query = $db->prepare('SELECT count(*) FROM `tickets` WHERE `user_id` = ?');
    $query->bindValue(1, $_SESSION['id']);
    
	$query->execute();
	$rows = $query->fetch(PDO::FETCH_NUM);
	
	if($rows > 0){
		echo $rows[0];
	}
}

// in progress tickets count
function in_progress_tickets_count() {
	global $db;
	
	$query = $db->prepare('SELECT count(*) FROM `tickets` WHERE `status` = ?');
    $query->bindValue(1, 1);
    
	$query->execute();
	$rows = $query->fetch(PDO::FETCH_NUM);
	
	if($rows > 0){
		echo $rows[0];
	}
}

// closed tickets count
function closed_tickets_count() {
	global $db;
	
	$query = $db->prepare('SELECT count(*) FROM `tickets` WHERE `status` = ?');
    $query->bindValue(1, 2);
    
	$query->execute();
	$rows = $query->fetch(PDO::FETCH_NUM);
	
	if($rows > 0){
		echo $rows[0];
	}
}

// ticket replies count
function ticket_replies_count($ticket_id) {
	global $db;
	
	$query = $db->prepare('SELECT count(*) FROM `ticket_replies` WHERE `ticket_id` = ?');
    $query->bindValue(1, $ticket_id);
    
	$query->execute();
	$rows = $query->fetch(PDO::FETCH_NUM);
	
    return $rows[0];
}

// tickets count
function tickets_count() {
	global $db;
	
	$query = $db->prepare('SELECT count(*) FROM `tickets`');
    
	$query->execute();
	$rows = $query->fetch(PDO::FETCH_NUM);
	
    return $rows[0];
}

// closed_tickets_count_return
function closed_tickets_count_return() {
    global $db;
	
	$query = $db->prepare('SELECT count(*) FROM `tickets` WHERE `status` = ?');
    $query->bindValue(1, 2);
    
	$query->execute();
	$rows = $query->fetch(PDO::FETCH_NUM);
	
    return $rows[0];
}

// tickets user count
function tickets_user_count() {
	global $db;
	
	$query = $db->prepare('SELECT count(*) FROM `tickets` WHERE `user_id` = ?');
    $query->bindValue(1, $_SESSION['id']);
    
	$query->execute();
	$rows = $query->fetch(PDO::FETCH_NUM);
	
    return $rows[0];
}

/*==========
EDITING FUNCTIONS
==========*/

// edit room
function edit_room($id, $name){
    global $db;
    
    $query = $db->prepare("UPDATE `rooms` SET `name` = ? WHERE `id` = ?");
    $query->bindValue(1, $name);
    $query->bindValue(2, $id);
    
    try {
        $query->execute();
        construct_message('alert-success', 'The room has been successfully edited.');
    } catch (PDOException $e) {
		die($e->getMessage());
	}
    
}

// edit department
function edit_department($id, $name){
    global $db;
    
    $query = $db->prepare("UPDATE `departments` SET `name` = ? WHERE `id` = ?");
    $query->bindValue(1, $name);
    $query->bindValue(2, $id);
    
    try {
        $query->execute();
        construct_message('alert-success', 'The department has been successfully edited.');
    } catch (PDOException $e) {
		die($e->getMessage());
	}
    
}

// edit category
function edit_category($id, $name){
    global $db;
    
    $query = $db->prepare("UPDATE `categories` SET `name` = ? WHERE `id` = ?");
    $query->bindValue(1, $name);
    $query->bindValue(2, $id);
    
    try {
        $query->execute();
        construct_message('alert-success', 'The category has been successfully edited.');
    } catch (PDOException $e) {
		die($e->getMessage());
	}
    
}

// edit user
function edit_user($id, $edit_first_name, $edit_last_name, $edit_username, $edit_email, $edit_role){
    global $db;
    
    $query = $db->prepare("UPDATE `users` SET `first_name` = ?, `last_name` = ?, `username` = ?, `email` = ?, `role` = ? WHERE `id` = ?");
    $query->bindValue(1, $edit_first_name);
    $query->bindValue(2, $edit_last_name);
    $query->bindValue(3, $edit_username);
    $query->bindValue(4, $edit_email);
    $query->bindValue(5, $edit_role);
    $query->bindValue(6, $id);
    
    try {
        $query->execute();
        construct_message('alert-success', 'The user has been successfully edited.');
    } catch (PDOException $e) {
		die($e->getMessage());
	}
    
}

// edit staff
function edit_staff($id, $edit_first_name, $edit_last_name, $edit_username, $edit_email){
    global $db;
    
    $query = $db->prepare("UPDATE `users` SET `first_name` = ?, `last_name` = ?, `username` = ?, `email` = ? WHERE `id` = ?");
    $query->bindValue(1, $edit_first_name);
    $query->bindValue(2, $edit_last_name);
    $query->bindValue(3, $edit_username);
    $query->bindValue(4, $edit_email);
    $query->bindValue(5, $id);
    
    try {
        $query->execute();
        construct_message('alert-success', 'The staff member has been successfully edited.');
    } catch (PDOException $e) {
		die($e->getMessage());
	}
    
}

// edit ticket
function edit_ticket($id, $subject, $user_id, $department, $room, $message, $status, $priority, $category, $solution){
    global $db;
    
    $query = $db->prepare("UPDATE `tickets` SET `subject` = ?, `user_id` = ?, `department_id` = ?, `room_id` = ?, `message` = ?, `status` = ?, `priority` = ?, `category_id` = ?, `solution` = ? WHERE `id` = ?");
    $query->bindValue(1, $subject);
    $query->bindValue(2, $user_id);
    $query->bindValue(3, $department);
    $query->bindValue(4, $room);
    $query->bindValue(5, $message);
    $query->bindValue(6, $status);
    $query->bindValue(7, $priority);
    $query->bindValue(8, $category);
	$query->bindValue(9, $solution);
    $query->bindValue(10, $id);
    
    try {
        $query->execute();
        construct_message('alert-success', 'The current ticket has been successfully edited.');
    } catch (PDOException $e) {
		die($e->getMessage());
	}
    
}

// edit ticket user
function edit_ticket_user($id, $subject, $department, $room, $message, $priority, $category){
    global $db;
    
    $query = $db->prepare("UPDATE `tickets` SET `subject` = ?, `department_id` = ?, `room_id` = ?, `message` = ?, `priority` = ?, `category_id` = ? WHERE `id` = ?");
    $query->bindValue(1, $subject);
    $query->bindValue(2, $department);
    $query->bindValue(3, $room);
    $query->bindValue(4, $message);
    $query->bindValue(5, $priority);
    $query->bindValue(6, $category);
    $query->bindValue(7, $id);
    
    try {
        $query->execute();
        construct_message('alert-success', 'The current ticket has been successfully edited.');
    } catch (PDOException $e) {
		die($e->getMessage());
	}
    
}

// edit my account
function edit_my_account($id, $edit_first_name, $edit_last_name, $edit_username, $edit_email){
    global $db;
    
    $query = $db->prepare("UPDATE `users` SET `first_name` = ?, `last_name` = ?, `username` = ?, `email` = ? WHERE `id` = ?");
    $query->bindValue(1, $edit_first_name);
    $query->bindValue(2, $edit_last_name);
    $query->bindValue(3, $edit_username);
    $query->bindValue(4, $edit_email);
    $query->bindValue(5, $id);
    
    try {
        $query->execute();
        construct_message('alert-success', 'You have successfully updated your account.');
    } catch (PDOException $e) {
		die($e->getMessage());
	}
    
}

/*==========
IDS TO NAMES FUNCTIONS
==========*/

// departmentid_to_name
function departmentid_to_name($id) {
	global $db;
	
	$query = $db->prepare("SELECT `id`, `name` FROM `departments` WHERE `id` = ?");
	$query->bindValue(1, $id);
	
	try {
        $query->execute();
        $result = $query->fetch();
        return $result['name'];
    } catch (PDOException $e) {
        die($e->getMessage());
    }
}

// roomid_to_name
function roomid_to_name($id) {
	global $db;
	
	$query = $db->prepare("SELECT `id`, `name` FROM `rooms` WHERE `id` = ?");
	$query->bindValue(1, $id);
	
	try {
        $query->execute();
        $result = $query->fetch();
        return $result['name'];
    } catch (PDOException $e) {
        die($e->getMessage());
    }
}

// categoryid_to_name
function categoryid_to_name($id) {
	global $db;
	
	$query = $db->prepare("SELECT `id`, `name` FROM `categories` WHERE `id` = ?");
	$query->bindValue(1, $id);
	
	try {
        $query->execute();
        $result = $query->fetch();
        return $result['name'];
    } catch (PDOException $e) {
        die($e->getMessage());
    }
}

// userid_to_name
function userid_to_name($id) {
	global $db;
	
	$query = $db->prepare("SELECT `id`, `first_name`, `last_name` FROM `users` WHERE `id` = ?");
	$query->bindValue(1, $id);
	
	try {
        $query->execute();
        $result = $query->fetch();
        return $result['first_name'].' '.$result['last_name'];
    } catch (PDOException $e) {
        die($e->getMessage());
    }
}

// userid_to_firstname
function userid_to_firstname($id) {
	global $db;
	
	$query = $db->prepare("SELECT `id`, `first_name` FROM `users` WHERE `id` = ?");
	$query->bindValue(1, $id);
	
	try {
        $query->execute();
        $result = $query->fetch();
        return $result['first_name'];
    } catch (PDOException $e) {
        die($e->getMessage());
    }
}

// assigned_to_name
function assigned_to_name($id) {
	global $db;
	
	$query = $db->prepare("SELECT `id`, `first_name`, `last_name` FROM `users` WHERE `id` = ?");
	$query->bindValue(1, $id);
	
	try {
        $query->execute();
        $result = $query->fetch();
        return $result['first_name'].' '.$result['last_name'];
    } catch (PDOException $e) {
        die($e->getMessage());
    }
}

/*==========
DELETING FUNCTIONS
==========*/

// delete department
function delete_department($id){
	global $db;
	
	$query = $db->prepare("DELETE FROM `departments` WHERE `id` = ?");
	$query->bindValue(1, $id);
	
	try {
		$query->execute();
		construct_message('alert-success', 'The department has been successfully deleted.');
	} catch (PDOException $e) {
		die($e->getMessage());
	}
}

// delete category
function delete_category($id){
	global $db;
	
	$query = $db->prepare("DELETE FROM `categories` WHERE `id` = ?");
	$query->bindValue(1, $id);
	
	try {
		$query->execute();
		construct_message('alert-success', 'The category has been successfully deleted.');
	} catch (PDOException $e) {
		die($e->getMessage());
	}
}

// delete room
function delete_room($id){
	global $db;
	
	$query = $db->prepare("DELETE FROM `rooms` WHERE `id` = ?");
	$query->bindValue(1, $id);
	
	try {
		$query->execute();
		construct_message('alert-success', 'The room has been successfully deleted.');
	} catch (PDOException $e) {
		die($e->getMessage());
	}
}

// delete ticket
function delete_ticket($id){
	global $db;
	
	$query = $db->prepare("DELETE FROM `tickets` WHERE `id` = ?");
	$query->bindValue(1, $id);
	
	try {
		$query->execute();
        delete_old_ticket_replies($id);
		construct_message('alert-success', 'The ticket has been successfully deleted.');
	} catch (PDOException $e) {
		die($e->getMessage());
	}
}

// delete old ticket replies
function delete_old_ticket_replies($ticket_id){
    global $db;
    
    $query = $db->prepare("DELETE FROM `ticket_replies` WHERE `ticket_id` = ?");
    $query->bindValue(1, $ticket_id);
	
	try {
		$query->execute();
	} catch (PDOException $e) {
		die($e->getMessage());
	}
}

// delete ticket reply
function delete_ticket_reply($id){
	global $db;
	
	$query = $db->prepare("DELETE FROM `ticket_replies` WHERE `id` = ?");
	$query->bindValue(1, $id);
	
	try {
		$query->execute();
		construct_message('alert-success', 'The reply has been successfully deleted.');
	} catch (PDOException $e) {
		die($e->getMessage());
	}
}

// delete user
function delete_user($id){
	global $db;
	
	$query = $db->prepare("DELETE FROM `users` WHERE `id` = ?");
	$query->bindValue(1, $id);
	
	try {
		$query->execute();
		construct_message('alert-success', 'The user has been successfully deleted.');
	} catch (PDOException $e) {
		die($e->getMessage());
	}
}

// delete staff
function delete_staff($id){
	global $db;
	
	$query = $db->prepare("DELETE FROM `users` WHERE `id` = ?");
	$query->bindValue(1, $id);
	
	try {
		$query->execute();
		construct_message('alert-success', 'The staff member has been successfully deleted.');
	} catch (PDOException $e) {
		die($e->getMessage());
	}
}

/*==========
CREATING FUNCTIONS
==========*/

// create_user
function create_user($user_first_name, $user_last_name, $user_username, $user_email, $user_role, $password) {
	global $bcrypt;
	global $db;
	
	$password = $bcrypt->genHash($password);
		
	$query = $db->prepare("INSERT INTO `users` (`first_name`, `last_name`, `username`, `password`, `email`, `role`, `last_logged_in`) VALUES (?, ?, ?, ?, ?, ?, ?)");
	$query->bindValue(1, $user_first_name);
	$query->bindValue(2, $user_last_name);
	$query->bindValue(3, $user_username);
    $query->bindValue(4, $password);
    $query->bindValue(5, $user_email);
    $query->bindValue(6, $user_role);
    $query->bindValue(7, '0000-00-00 00:00:00');
	
	if(empty($user_first_name) or empty($user_last_name) or empty($user_username) or empty($password) or empty($user_email)) {
		construct_message('alert-danger', 'You forgot to enter all the required information.');
	} elseif(username_exists($user_username)) {
		construct_message('alert-danger', 'Sorry but that username already exists.');
	} elseif(email_exists($user_email)) {
        construct_message('alert-danger', 'Sorry but the email address you entered is in use.');
    } else {
		try {
			$query->execute();
            construct_message('alert-success', 'The user has been successfully created.');
		} catch (PDOException $e) {
			die($e->getMessage());
		}
	}
}

// create_staff
function create_staff($user_first_name, $user_last_name, $user_username, $user_email, $password) {
	global $bcrypt;
	global $db;
	
	$password = $bcrypt->genHash($password);
		
	$query = $db->prepare("INSERT INTO `users` (`first_name`, `last_name`, `username`, `password`, `email`, `role`, `last_logged_in`) VALUES (?, ?, ?, ?, ?, ?, ?)");
	$query->bindValue(1, $user_first_name);
	$query->bindValue(2, $user_last_name);
	$query->bindValue(3, $user_username);
    $query->bindValue(4, $password);
    $query->bindValue(5, $user_email);
    $query->bindValue(6, 2);
    $query->bindValue(7, '0000-00-00 00:00:00');
	
	if(empty($user_first_name) or empty($user_last_name) or empty($user_username) or empty($password) or empty($user_email)) {
		construct_message('alert-danger', 'You forgot to enter all the required information.');
	} elseif(username_exists($user_username)) {
		construct_message('alert-danger', 'Sorry but that username already exists.');
	} elseif(email_exists($user_email)) {
        construct_message('alert-danger', 'Sorry but the email address you entered is in use.');
    } else {
		try {
			$query->execute();
            construct_message('alert-success', 'The staff member has been successfully created.');
		} catch (PDOException $e) {
			die($e->getMessage());
		}
	}
}

// create ticket reply
function create_reply($user_id, $reply_content, $ticket_id) {
    global $db;
    $time = date("Y-m-d H:i:s");
    
    $query = $db->prepare("INSERT INTO `ticket_replies` (`content`, `ticket_id`, `datetime`, `user_id`) VALUES (?, ?, ?, ?)");
	$query->bindValue(1, $reply_content);
	$query->bindValue(2, $ticket_id);
	$query->bindValue(3, $time);
	$query->bindValue(4, $user_id);
	
	if(empty($reply_content)) {
		construct_message('alert-danger', 'You forgot to write a reply.');
	} else {
		try {
			$query->execute();
			construct_message('alert-success', 'You have successfully replied to the ticket.');
		} catch (PDOException $e) {
			die($e->getMessage());
		}
	}
}

// create ticket
function create_ticket($ticket_subject, $ticket_user_id, $ticket_department, $ticket_room, $ticket_message, $ticket_priority, $ticket_category) {
    global $db;
    $created = date("Y-m-d H:i:s");
    $status = 0;
    
    $query = $db->prepare("INSERT INTO `tickets` (`subject`, `created`, `status`, `user_id`, `department_id`, `room_id`, `message`, `priority`, `category_id`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
	$query->bindValue(1, $ticket_subject);
	$query->bindValue(2, $created);
	$query->bindValue(3, $status);
	$query->bindValue(4, $ticket_user_id);
    $query->bindValue(5, $ticket_department);
    $query->bindValue(6, $ticket_room);
    $query->bindValue(7, $ticket_message);
    $query->bindValue(8, $ticket_priority);
    $query->bindValue(9, $ticket_category);
	
	if(empty($ticket_subject) or empty($ticket_user_id) or empty($ticket_department) or empty($ticket_room) or empty($ticket_message) or empty($ticket_category)) {
		construct_message('alert-danger', 'You forgot to enter all the required information.');
	} else {
		try {
			$query->execute();
			construct_message('alert-success', 'You have successfully created a ticket.');
		} catch (PDOException $e) {
			die($e->getMessage());
		}
	}
}

// create ticket user
function create_ticket_user($ticket_subject, $ticket_user_id, $ticket_department, $ticket_room, $ticket_message, $ticket_priority, $ticket_category) {
    global $db;
    $created = date("Y-m-d H:i:s");
    $status = "0";
    
    $query = $db->prepare("INSERT INTO `tickets` (`subject`, `created`, `status`, `user_id`, `department_id`, `room_id`, `message`, `priority`, `category_id`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
	$query->bindValue(1, $ticket_subject);
	$query->bindValue(2, $created);
	$query->bindValue(3, $status);
	$query->bindValue(4, $ticket_user_id);
    $query->bindValue(5, $ticket_department);
    $query->bindValue(6, $ticket_room);
    $query->bindValue(7, $ticket_message);
    $query->bindValue(8, $ticket_priority);
    $query->bindValue(9, $ticket_category);
	
	if(empty($ticket_subject) or empty($ticket_user_id) or empty($ticket_department) or empty($ticket_room) or empty($ticket_message) or empty($ticket_priority) or empty($ticket_category)) {
		construct_message('alert-danger', 'You forgot to enter all the required information.');
	} else {
		try {
			$query->execute();
			construct_message('alert-success', 'You have successfully created your ticket.');
		} catch (PDOException $e) {
			die($e->getMessage());
		}
	}
}

// create room
function create_room($room_name) {
    global $db;
    
    $query = $db->prepare("INSERT INTO `rooms` (`name`) VALUES (?)");
	$query->bindValue(1, $room_name);
	
	if(empty($room_name)) {
		construct_message('alert-danger', 'You forgot to enter a room name.');
	} elseif(room_exists($room_name)) {
        construct_message('alert-danger', 'Sorry but that room already exists.');
    } else {
		try {
			$query->execute();
			construct_message('alert-success', 'The room has been successfully added.');
		} catch (PDOException $e) {
			die($e->getMessage());
		}
	}
}

// create department
function create_department($department_name) {
    global $db;
    
    $query = $db->prepare("INSERT INTO `departments` (`name`) VALUES (?)");
	$query->bindValue(1, $department_name);
	
	if(empty($department_name)) {
		construct_message('alert-danger', 'You forgot to enter a department name.');
	} elseif(department_exists($department_name)) {
        construct_message('alert-danger', 'Sorry but that department already exists.');
    } else {
		try {
			$query->execute();
			construct_message('alert-success', 'The department has been successfully added.');
		} catch (PDOException $e) {
			die($e->getMessage());
		}
	}
}

// create category type
function create_category($category_name) {
    global $db;
    
    $query = $db->prepare("INSERT INTO `categories` (`name`) VALUES (?)");
	$query->bindValue(1, $category_name);
	
	if(empty($category_name)) {
		construct_message('alert-danger', 'You forgot to enter a category.');
	} elseif(category_exists($category_name)) {
        construct_message('alert-danger', 'Sorry but that category already exists.');
    } else {
		try {
			$query->execute();
			construct_message('alert-success', 'The category has been successfully added.');
		} catch (PDOException $e) {
			die($e->getMessage());
		}
	}
}

/*==========
EXISTS FUNCTIONS
==========*/

// username_exists
function username_exists($username) {
	global $db;
	
	$query = $db->prepare("SELECT COUNT(`id`) FROM `users` WHERE `username` = ?");
	$query->bindValue(1, $username);
		
	try {
		$query->execute();
		$rows = $query->fetchColumn();
			
		if ($rows > 0) {
			return true;
		} else {
			return false;
		}
	} catch (PDOException $e) {
		die($e->getMessage());
	}
}
        
// user_id_exists
function user_id_exists($user_id) {
	global $db;
	
	$query = $db->prepare("SELECT COUNT(`id`) FROM `users` WHERE `id` = ?");
	$query->bindValue(1, $user_id);
		
	try {
		$query->execute();
		$rows = $query->fetchColumn();
			
		if ($rows > 0) {
			return true;
		} else {
			return false;
		}
	} catch (PDOException $e) {
		die($e->getMessage());
	}
}

// email_exists
function email_exists($email) {
	global $db;
	
	$query = $db->prepare("SELECT COUNT(`id`) FROM `users` WHERE `email` = ?");
	$query->bindValue(1, $email);
		
	try {
		$query->execute();
		$rows = $query->fetchColumn();
			
		if ($rows > 0) {
			return true;
		} else {
			return false;
		}
	} catch (PDOException $e) {
		die($e->getMessage());
	}
}

// department_exists
function department_exists($name) {
	global $db;
	
	$query = $db->prepare("SELECT COUNT(`id`) FROM `departments` WHERE `name` = ?");
	$query->bindValue(1, $name);
		
	try {
		$query->execute();
		$rows = $query->fetchColumn();
			
		if ($rows > 0) {
			return true;
		} else {
			return false;
		}
	} catch (PDOException $e) {
		die($e->getMessage());
	}
}

// category_exists
function category_exists($name) {
	global $db;
	
	$query = $db->prepare("SELECT COUNT(`id`) FROM `categories` WHERE `name` = ?");
	$query->bindValue(1, $name);
		
	try {
		$query->execute();
		$rows = $query->fetchColumn();
			
		if ($rows > 0) {
			return true;
		} else {
			return false;
		}
	} catch (PDOException $e) {
		die($e->getMessage());
	}
}

// room_exists
function room_exists($name) {
	global $db;
	
	$query = $db->prepare("SELECT COUNT(`id`) FROM `rooms` WHERE `name` = ?");
	$query->bindValue(1, $name);
		
	try {
		$query->execute();
		$rows = $query->fetchColumn();
			
		if ($rows > 0) {
			return true;
		} else {
			return false;
		}
	} catch (PDOException $e) {
		die($e->getMessage());
	}
}

/*==========
SELECT LIST FUNCTIONS
==========*/

// select list departments
function select_list_departments() {
    global $db;
	
	$query = $db->prepare("SELECT `id`, `name` FROM `departments` ORDER BY `name` ASC");
    
    try {
        $query->execute();
        $result = $query->fetchAll();
        foreach($result as $row){
            echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';
        }
    } catch (PDOException $e) {
        die($e->getMessage());
    }
}

// select list rooms
function select_list_rooms() {
    global $db;
	
	$query = $db->prepare("SELECT `id`, `name` FROM `rooms` ORDER BY `name` ASC");
    
    try {
        $query->execute();
        $result = $query->fetchAll();
        foreach($result as $row){
            echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';
        }
    } catch (PDOException $e) {
        die($e->getMessage());
    }
}

// select list rooms selected
function select_list_rooms_selected($id) {
    global $db;
	
	$query = $db->prepare("SELECT `id`, `name` FROM `rooms` ORDER BY `name` ASC");
    
    try {
        $query->execute();
        $result = $query->fetchAll();
        foreach($result as $row){
            echo '<option value="'.$row['id'].'"';
            if($row['id'] == $id){
                echo ' selected';
            }
            echo '>'.$row['name'].'</option>';
        }
    } catch (PDOException $e) {
        die($e->getMessage());
    }
}

// select list categories
function select_list_categories() {
    global $db;
	
	$query = $db->prepare("SELECT `id`, `name` FROM `categories` ORDER BY `name` ASC");
    
    try {
        $query->execute();
        $result = $query->fetchAll();
        foreach($result as $row){
            echo '<option value="'.$row['id'].'">'.$row['name'].'</option>';
        }
    } catch (PDOException $e) {
        die($e->getMessage());
    }
}

// select list categories selected
function select_list_categories_selected($id) {
    global $db;
	
	$query = $db->prepare("SELECT `id`, `name` FROM `categories` ORDER BY `name` ASC");
    
    try {
        $query->execute();
        $result = $query->fetchAll();
        foreach($result as $row){
            echo '<option value="'.$row['id'].'"';
            if($row['id'] == $id){
                echo ' selected';
            }
            echo '>'.$row['name'].'</option>';
        }
    } catch (PDOException $e) {
        die($e->getMessage());
    }
}

// select list staff members
function select_list_staff_members() {
    global $db;
	
	$query = $db->prepare("SELECT `id`, `first_name`, `last_name` FROM `users` WHERE `role` = ? ORDER BY `last_name` ASC");
    $query->bindValue(1, 2);
    
    try {
        $query->execute();
        $result = $query->fetchAll();
        foreach($result as $row){
            echo '<option value="'.$row['id'].'">'.$row['first_name'].' '.$row['last_name'].'</option>';
        }
    } catch (PDOException $e) {
        die($e->getMessage());
    }
}

// select list staff member selected
function select_list_staff_member_selected($id) {
    global $db;
	
	$query = $db->prepare("SELECT `id`, `first_name`, `last_name` FROM `users` WHERE `role` = ? ORDER BY `last_name` ASC");
    $query->bindValue(1, 2);
    
    try {
        $query->execute();
        $result = $query->fetchAll();
        foreach($result as $row){
            echo '<option value="'.$row['id'].'"';
            if($row['id'] == $id){
                echo ' selected';
            }
            echo '>'.$row['first_name'].' '.$row['last_name'].'</option>';
        }
    } catch (PDOException $e) {
        die($e->getMessage());
    }
}

// select list users
function select_list_users() {
    global $db;
	
	$query = $db->prepare("SELECT `id`, `first_name`, `last_name` FROM `users` WHERE `role` = ? OR `role` = ? ORDER BY `last_name` ASC");
    $query->bindValue(1, 0);
    $query->bindValue(2, 1);
    
    try {
        $query->execute();
        $result = $query->fetchAll();
        foreach($result as $row){
            echo '<option value="'.$row['id'].'">'.$row['first_name'].' '.$row['last_name'].'</option>';
        }
    } catch (PDOException $e) {
        die($e->getMessage());
    }
}

// select list users selected
function select_list_users_selected($id) {
    global $db;
	
	$query = $db->prepare("SELECT `id`, `first_name`, `last_name` FROM `users` WHERE `role` = ? OR `role` = ? ORDER BY `last_name` ASC");
    $query->bindValue(1, 0);
    $query->bindValue(2, 1);
    
    try {
        $query->execute();
        $result = $query->fetchAll();
        foreach($result as $row){
            echo '<option value="'.$row['id'].'"';
            if($row['id'] == $id){
                echo ' selected';
            }
            echo '>'.$row['first_name'].' '.$row['last_name'].'</option>';
        }
    } catch (PDOException $e) {
        die($e->getMessage());
    }
}

// select list departments selected
function select_list_departments_selected($id) {
    global $db;
	
	$query = $db->prepare("SELECT `id`, `name` FROM `departments` ORDER BY `name` ASC");
    
    try {
        $query->execute();
        $result = $query->fetchAll();
        foreach($result as $row){
            echo '<option value="'.$row['id'].'"';
            if($row['id'] == $id){
                echo ' selected';
            }
            echo '>'.$row['name'].'</option>';
        }
    } catch (PDOException $e) {
        die($e->getMessage());
    }
}

/*==========
LOADING FUNCTIONS
==========*/

function load_nav_menu(){
    $current_page = basename($_SERVER['PHP_SELF']);
    
    echo '<nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                <a class="navbar-brand" href="index.php">IT Support</a>
            </div>
            <!-- /.navbar-header -->';
    
    // top right nav.
    echo '<ul class="nav navbar-top-links navbar-right">
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-tasks fa-fw"></i> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li><a href="index.php"><i class="fa fa-dashboard fa-fw"></i> Dashboard</a></li>
                        <li class="divider"></li>
                        ';
    if($_SESSION['role'] == 2){
        echo '<li><a href="my_tickets.php"><i class="fa fa-ticket fa-fw"></i> My Tickets</a></li>';
    } else {
        echo '<li><a href="tickets.php"><i class="fa fa-ticket fa-fw"></i> Tickets</a></li>
              <li><a href="tasks.php"><i class="fa fa-tasks fa-fw"></i> Tasks</a></li>';
    }
    if($_SESSION['role'] == 0){
        echo '<li><a href="users.php"><i class="fa fa-users fa-fw"></i> Users</a></li>
              <li class="divider"></li>
              <li><a href="departments.php"><i class="fa fa-cogs fa-fw"></i> Departments</a></li>
              <li><a href="rooms.php"><i class="fa fa-cogs fa-fw"></i> Rooms</a></li>
              <li><a href="categories.php"><i class="fa fa-cogs fa-fw"></i> Categories</a></li>
              <li class="divider"></li>
              <li><a href="settings.php"><i class="fa fa-cogs fa-fw"></i> Settings</a></li>';
    } elseif($_SESSION['role'] == 1){
        echo '<li><a href="staff_members.php"><i class="fa fa-users fa-fw"></i> Staff Members</a></li>
              <li class="divider"></li>
              <li><a href="departments.php"><i class="fa fa-cogs fa-fw"></i> Departments</a></li>
              <li><a href="rooms.php"><i class="fa fa-cogs fa-fw"></i> Rooms</a></li>
              <li><a href="categories.php"><i class="fa fa-cogs fa-fw"></i> Categories</a></li>';
    }
    echo '          </ul>
                    <!-- /.dropdown-user -->
                </li>
                <!-- /.dropdown -->
                <li class="dropdown">
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li><a href="my_account.php"><i class="fa fa-user fa-fw"></i> My Account</a></li>
                        <li class="divider"></li>
                        <li><a href="?logout"><i class="fa fa-sign-out fa-fw"></i> Logout</a></li>
                    </ul>
                    <!-- /.dropdown-user -->
                </li>
                <!-- /.dropdown -->
            </ul>
            <!-- /.navbar-top-links -->';
    echo '</nav>';
}

?>