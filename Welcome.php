<?php

require_once('UserLogon.php');

$ul = new userLogon();
//$ul->endSession(); // testing the cookies
if($ul->isLoggedIn())
	echo "Welcome, " . $ul->getUserName() . "! <a href=doLogin.php?act=lg>Log-out</a>";
else
	echo "Welcome, guest! <a href=login.php>Log-in</a>";
	
	
	//For internal use only
	print("<br />1.");
	print_r($_SESSION);
	print("<br />");print("<br />");
	print("2.");
	print_r($_COOKIE);
	print("<br />");
?>