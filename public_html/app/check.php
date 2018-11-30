<?php

include_once 'config.php';
include_once 'token.php';

if(isset($_POST["checkemail"])){
	$email = clean($_POST["checkemail"]);
	$sql2= mysql_query("SELECT * FROM Users WHERE email ='$email'") or die(mysql_error());
	echo mysql_num_rows($sql2);
}

if(isset($_POST["checkphone"])){
	$phone = clean($_POST["checkphone"]);
	$sql2 = mysql_query("SELECT * FROM Client WHERE phone ='$phone'") or die(mysql_error());
	echo mysql_num_rows($sql2);
}

if(isset($_POST["simplecheckemail"])){
	$email = clean($_POST["simplecheckemail"]);
	$id = isloggedin();
	$sql2= mysql_query("SELECT * FROM Users WHERE email ='$email' and id!='$id' ")or die(mysql_error());
	echo mysql_num_rows($sql2);
}

if(isset($_POST["checkPass"])){
	$pass = clean($_POST["checkPass"]);
	$id = isloggedin();
	$sql2= mysql_query("SELECT password FROM Users WHERE id ='$id'")or die(mysql_error());
	$result = mysql_fetch_array($sql2,MYSQL_NUM);
	$real = $result[0];
	if(password_verify($pass,$real)) {
		echo 1;
	}
	else{
		echo 0;
	}
}


?>
