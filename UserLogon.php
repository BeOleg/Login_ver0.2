<?php
require_once('dbLink.php');



	class userLogon //extends user
	{

		private $_uName;
		private $_uPass;
		private $_id;
		private $_loggedIn;
		private $_cookie;
		
		public function __construct()
		{
	    	session_start();
			
			if(isset($_SESSION['uName'], $_SESSION['uPass']))
			{
				$uName = $_SESSION['uName'];
				$uPass = $_SESSION['uPass'];
                $this->validateSession();
			    $cookie = isset($_COOKIE['uName']) ? 1 : 0;
			    $this->_loggedIn = $this->authUser($uName, $uPass, $cookie, false);
			}
			else if(isset($_COOKIE['uName'], $_COOKIE['uPass']))
			{
				
			    $uName = $_COOKIE['uName'];
				$uPass = $_COOKIE['uPass'];
				$this->_loggedIn = $this->authUser($uName, $uPass, 1, false);
				$this->validateCookies();
				if($this->isLoggedIn())
			   $this->startSession();
			}
			else
			{
				$this->_uName = null;
				$this->_uPass = null;
				$this->_id = 0;
		        $this->_loggedIn = 0;
		        $this->_cookie = 0;
			}
			
		}			
		
		private function authUser($un, $pass, $cookie, $overrideCookie)
		{
		$link = new dbLink(); 
		
			$query = "SELECT id, cookie FROM users WHERE MD5(uName) = '" .md5($un). "'" . "AND uPass = '" . $pass . "' LIMIT 1";//SZANITIZE USER
			$qres = mysql_query($query, $link->getlink());
			if(mysql_num_rows($qres) == 0)
				return 0;
			
				
			$row = mysql_fetch_assoc($qres);
			$this->_id = $row['id'];
			$this->_cookie = $row['cookie'];
			$this->_uName = $un;
			$this->_uPass = $pass;
			$cookie = $cookie ? 1 : 0;
			if(($cookie != $this->_cookie && $overrideCookie == true) || ($cookie == 0 && $this->_cookie == 1))
				{
					$query = "UPDATE users SET cookie = '" . $cookie . "' WHERE id = '" . $this->_id . "' LIMIT 1";
					mysql_query($query, $link->getlink());
					$this->cookie = $cookie;

				}
           
			$this->_uName = $un;
			$this->_uPass = $pass;
			return 1;
		mysql_free($link->getLink());
		}
		
		private function startSession()
		{
		    $_SESSION['uName'] = $this->_uName;
			$_SESSION['uPass'] = $this->_uPass;
			$_SESSION['count'] = 0;
			$_SESSION['uAgent'] = $_SERVER['HTTP_USER_AGENT'];
			$_SESSION['uAddr'] = $_SERVER['REMOTE_ADDR'];
		}
		
		private function endSession()
		{
			session_unset();
			session_destroy();
		}
		
		private function  validateSession()
		{
			if($_SERVER['HTTP_USER_AGENT'] != $_SESSION['uAgent'] and $_SERVER['REMOTE_ADDR'] != $_SESSION['uAddr'])
			{
			  $this->logOut();
			  return false; 
			}
			if($_SESSION['count'] >= 5)
			   {
					session_regenerate_id();
				    $_SESSION['count'] = 0;
			   }	
            else
                $_SESSION['count'] += 1;			
			
			return true;
		}
			
		private function makeCookies()
		{
			setcookie('uName', $this->_uName, time() + (3600 * 24), "/");
			setcookie('uPass', $this->_uPass, time() + (3600 * 24), "/");
			setcookie('uAddr', $_SERVER['REMOTE_ADDR'], time() + (3600 * 24), "/");
			setcookie('uAgent', $_SERVER['HTTP_USER_AGENT'], time() + (3600 * 24), "/");
			setcookie('count', 0, time() + (3600 * 24), "/");

		}
		
		private function killCookies()
		{
		    setcookie('uName', '', time() - 3600, "/");
		    setcookie('uPass', '', time() - 3600, "/");
			setcookie('uName', '', time() - 3600, "/");
			setcookie('uPass', '', time() - 3600, "/");
			setcookie('uAgent', '', time() - 3600, "/");
			setcookie('uAddr', '', time() - 3600, "/");
			setcookie('count', '', time() - 3600, "/");
		}
		private function  validateCookies()
		{
			if($_SERVER['HTTP_USER_AGENT'] != $_COOKIE['uAgent'] and $_SERVER['REMOTE_ADDR'] != $_COOKIE['uAddr'] or $this->_cookie == 0 )
			{
					$this->logOut();
					return false; 
			}
				//I might add an exception, to tell the user in an other way that the session has been compremised, using a seperate object
				
			if($_COOKIE['count'] >= 5)
			{
					$this->logOut();
					return false; 
			}
            else
                setcookie('count', $_COOKIE['count'] + 1, time() + (3600 * 24), "/"); //I must consider revising this to match the expiry of the other cookies from my site		
				return true;
	
		}
		

		
		public function login($uName, $uPass, $cookie)
		{		
			
			$this->_loggedIn = $this->authUser($uName, $uPass, $cookie, true);
			if($this->_loggedIn == 0)
			   return false;
			if($cookie == 1)
				$this->makeCookies();			

			$this->startSession();
			
			return true;
		}
		
		public function logOut()
		{

		 if(isset($_SESSION))
				$this->endSession(); 
			if(isset($_COOKIE))
				$this->killCookies();
		
			$this->_loggedIn = 0;	

      //should I call an empty constructor here to erase all user data?			
		}
		public function isLoggedIn()		
		{
		//return $this->_loggedIn ? true : false;
		if($this->_loggedIn == 1)
		   return true;
		return false; //Will consider changing the db attribute 'cookie' at logout also, and not only when the user is logging in
		}
		
		public function getUserName()
		{
			return $this->_uName;
		}
	}
?>