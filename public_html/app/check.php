<?php

include_once 'config.php';
include_once 'token.php';

if(isset($_POST["checkemail"])){
	$email = clean($_POST["checkemail"]);
	$sql2= mysql_query("SELECT * FROM Users WHERE email ='$email'") or die(mysql_error());
	echo mysql_num_rows($sql2);
}

if(isset($_POST["checkbalance"])){
	$id = isloggedin();
	$amount = clean($_POST["checkbalance"]);
	$sql2 = mysql_query("SELECT amount FROM Client WHERE user_id ='$id'") or die(mysql_error());
	$result = mysql_fetch_array($sql2,MYSQL_NUM);
	if(floatval($result[0]) < floatval($amount) ){
		echo 0;
	}
	else{
		echo 1;
	}
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

if(isset($_POST["getUser"])){
	$id = isloggedin();
	$s = clean($_POST["getUser"]);
	$find = mysql_query("SELECT Users.profile_img, Client.firstname, Client.lastname, Users.id FROM Users, Client WHERE Users.id <> $id AND Client.user_id = Users.id AND (Users.email LIKE '%$s%' OR Client.phone LIKE '$s')  ")or die(mysql_error());
	$count = 0;
	$result = "";
	while($arrayResult = mysql_fetch_array($find,MYSQL_NUM)){
		$count += 1;
		$img = $arrayResult[0];
		$fname	= $arrayResult[1]." ".$arrayResult[2];
		$ud = $arrayResult[3];
		$result .= "

		<div class='col-xs-12 col-sm-3'>
			<div class='card profile-card'>
				<div class='profile-header'>&nbsp;</div>
				<div class='profile-body'>
					<div class='image-area'>
						<img src='$img'  width='128' height='128' alt='Profile Image' />
					</div>
					<div class='content-area'>
						<h5><a href='member?id=$ud'>$fname</a></h5>
						<a href='pay?id=$ud' role='button' class='btn bg-yellow waves-effect m-b-15'>PAY</a>
						<a href='request?id=$ud' role='button' class='btn bg-blue waves-effect m-b-15'>REQUEST</a>
					</div>
				</div>
			</div>
		</div>
		";
	}

	if($count==0){
		echo  0;
	}
	else{
		echo $result;
	}

}


?>
