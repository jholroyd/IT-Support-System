<?php

session_start();

$config = array(
    'host'		=> 'localhost',			// define the database host.
    'username'	=> 'root',				// define the database user.
    'password'	=> '',			// define the database password.
    'dbname'	=> 'support'		    // define the database name.
);

$db = new PDO('mysql:host=' . $config['host'] . ';dbname=' . $config['dbname'], $config['username'], $config['password']);
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

include('functions.php');
include('classes/bcrypt.php');

$bcrypt = new bcrypt();

?>