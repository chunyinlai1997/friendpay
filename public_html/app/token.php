<?php
	include_once 'config.php';
	function isloggedin(){
		if(isset($_COOKIE['SNID'])){
			$d_token = sha1($_COOKIE['SNID']);
			if(mysql_query("SELECT user_id FROM Token WHERE token = '$d_token'")){
				$sql = mysql_query("SELECT user_id FROM Token WHERE token = '$d_token'");
				$result = mysql_fetch_array($sql,MYSQL_NUM);
				$user_id = $result[0];
				if(isset($_COOKIE['SNID_'])){
					return $user_id;
				}
				else{
					$cstrong = True;
					$token = bin2hex(openssl_random_pseudo_bytes(64,$cstrong));
					$h_token = sha1($token);
					$h_snid = sha1($_COOKIE['SNID']);
					$ipaddress = $_SERVER['REMOTE_ADDR'];
					mysql_query("INSERT INTO Token(token,user_id,ip_address) VALUES('$h_token ','$user_id','$ipaddress')");
					mysql_query("DELETE FROM Token WHERE token = '$h_snid'");
					return $user_id;
				}
			}
			return false;
		}
		else{
			return false;
		}
	}
	/*
	function isAuth(){
		if(isset($_COOKIE['SNID2'])){
			$d_token = sha1($_COOKIE['SNID2']);
			if(mysql_query("SELECT user_id FROM Token2 WHERE token = '$d_token'")){
				$sql = mysql_query("SELECT user_id FROM Token2 WHERE token = '$d_token'");
				$result = mysql_fetch_array($sql,MYSQL_NUM);
				$user_id = $result[0];
				if(isset($_COOKIE['SNID2_'])){
					return $user_id;
				}
				else{
					$cstrong = True;
					$token = bin2hex(openssl_random_pseudo_bytes(64,$cstrong));
					$h_token = sha1($token);
					$h_snid = sha1($_COOKIE['SNID2']);
					$resultrole = mysql_fetch_array($sql,MYSQL_NUM);
					$role = $resultrole[0];
					mysql_query("INSERT INTO Token(token,user_id) VALUES('$h_token ','$user_id')");
					mysql_query("DELETE FROM Token WHERE token = '$h_snid'");
					return $user_id;
				}
			}
			return false;
		}
		else{
			return false;
		}
	}
	*/
	function getLoginStatus(){
		$status = isloggedin();
		if($status != false){
			return true;
		}
		else{
			return false;
		}
	}

	function getUserId(){
		return isloggedin();
	}

	function getStatus(){
		$id = isloggedin();
		if($id!=false){
			$sql5 = mysql_query("SELECT status FROM Users WHERE id = '$id'");
			$status = mysql_fetch_array($sql5,MYSQL_NUM);
			return $status[0];
		}
		return false;
	}

	function getVerfied(){
		$id = isloggedin();
		if($id!=false){
			$sql5 = mysql_query("SELECT verified FROM Users WHERE id = '$id'");
			$verified = mysql_fetch_array($sql5,MYSQL_NUM);
			return $verified[0];
		}
		return false;
	}

	function isVerified(){
		$id = isloggedin();
		if($id!=false){
			$sql5 = mysql_query("SELECT verified FROM Users WHERE id = '$id'");
			$result5 = mysql_fetch_array($sql5,MYSQL_NUM);
			$f = $result5[0];
			if($f!=3){
				return false;
			}
			else{
				return true;
			}
		}
		else{
			return false;
		}
	}

	function isActive(){
		$id = isloggedin();
		if($id!=false){
			$sql5 = mysql_query("SELECT status FROM Users WHERE id = '$id'");
			$result5 = mysql_fetch_array($sql5,MYSQL_NUM);
			$s = $result5[0];
			if($s!="active"){
				return false;
			}
			else{
				return true;
			}
		}
		else{
			return false;
		}
	}

	function isTwoFactor(){
		$id = isloggedin();
		if($id!=false){
			$sql5 = mysql_query("SELECT two_factor FROM Users WHERE id = '$id'");
			$result5 = mysql_fetch_array($sql5,MYSQL_NUM);
			$two_factor = $result5[0];
			if($two_factor!="used"){
				return true;
			}
		}
		return false;
	}

	function clean($string) {
	   $string = str_replace('  ', ' ', $string); // Replaces all spaces with hyphens.
     $string = preg_replace('/[^ A-Za-z0-9.@-_*\-]/', '', $string);
     return preg_replace('/-+/', '-', $string); // Replaces multiple hyphens with single one.
	}
?>
