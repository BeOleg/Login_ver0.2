<?php
require_once('UserLogon.php');
$ul = new userLogon();




$is_ajax = $_REQUEST['is_ajax'];

	if(isset($is_ajax) && $is_ajax)
	{
		$username = $_REQUEST['username'];
		$password = $_REQUEST['password'];
		$cookie =   $_REQUEST['cookie'];
		
		if($ul->login($username, md5($password), $cookie))
			echo true;
		else
			echo false;
	}

	
   if(isset($_GET['act']) and $_GET['act'] == "lg")
   {
		$ul->logOut();
		header('Location: login.php');
   }  
?>