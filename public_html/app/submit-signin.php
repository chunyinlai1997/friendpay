<?php
	include_once 'config.php';
	include_once 'token.php';
	include_once 'encrypt_decrypt.php';

	if(isloggedin()){
    header('Location:dashboard');
  }
	else{
		checker();
	}

	function checker(){
		if(isset($_POST['submit']) && !empty($_POST['submit'])):
		    if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])):
		        //your site secret key
		        $secret = '6Lf3gHwUAAAAALzCJ5Q61xmIcUNGivheJWBM9NYQ';
		        //get verify response data
		        $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['g-recaptcha-response']);
		        $responseData = json_decode($verifyResponse);
		        if($responseData->success):
		           validate();
		        else:
		            header('Location:sign-in?re=FAIL_CAPTCHA');
		        endif;
		    else:
		        header('Location:sign-in?re=NO_SUBMIT_CAPTCHA');
		    endif;
		else:
			header('Location:sign-in?re=NO_SUBMIT');
		endif;
	}

	function validate(){
		if(isset($_POST['submit'])){
			$ipaddress = $_SERVER['REMOTE_ADDR'];
			$email = clean($_POST['email']);
			$pass = clean($_POST['password']);
			$sql= mysql_query("SELECT id, email, password, status, verified, role FROM Users WHERE email = '$email'")or die(mysql_error());
			$row= mysql_fetch_array($sql,MYSQL_NUM);
			$hp = $row[2];
	    $user_id = $row[0];
			$status = $row[3];
			$role =  $row[5];
	    $date = date("Y-m-d H:i:s");
			$verified = $row[4];
			if($verified==2){
				header("Location:sign-in?re=RESET");
			}
			else if($verified == 4){
				header("Location:sign-in?ac=LIMIT");
			}
			else{
				$sqlc = mysql_query("SELECT COUNT(Login_Logging.status) FROM Login_Logging, Users WHERE Login_Logging.user_id = Users.id AND Users.email = '$email' AND Login_Logging.status = 'fail' AND Login_Logging.date_time > NOW() - INTERVAL 5 MINUTE  ");
			  $rowc = mysql_fetch_array($sqlc,MYSQL_NUM);
			  $failin5mins =  $rowc[0];
				if($failin5mins >= 5 || ($status=="inactive" && $verified == 4 )){
					$sql2 = mysql_query("SELECT Client.firstname, Client.lastname FROM Client, Users WHERE Users.email ='$email' AND Users.id = Client.user_id   ")or die(mysql_error());
		      $getf = mysql_fetch_array($sql2,MYSQL_NUM);
		      $firstname = $getf[0];
		      $lastname = $getf[1];
					$v_hash = md5(rand(0,1000));
			    $join = date("Y-m-d H:i:s");
			  	mysql_query("UPDATE Users SET verified=4, status='inactive', join_date = '$join', verify_hash = '$v_hash'  WHERE email='$email' ");
		      send_email($firstname,$lastname,$email,$v_hash);
					header("Location:sign-in?ac=LIMIT");
				}

				if(password_verify($pass,$hp)) {
					$cstrong = True;
					$token = bin2hex(openssl_random_pseudo_bytes(64,$cstrong));
					$h_token = sha1($token);

					//temp direct to index
					//header("Location:index");
					mysql_query("INSERT INTO Token(token,user_id,ip_address) VALUES('$h_token ','$user_id','$ipaddress')");
					setcookie("SNID",$token,time()+60*60*1,'/',NULL,NULL,TRUE);	//first login token will expire after 1 hours
					setcookie("SNID_",'1',time()+60*60*0.5,'/',NULL,NULL,TRUE);	//second login token will expire after 30 minutes
					if($role =="client"){
						if($verified == 0){
							$logstatus = "success with account issue (not verified)";
							mysql_query("INSERT INTO Login_Logging(user_id,date_time,status,ip) VALUES('$user_id','$date','sucess with account issue','$ipaddress')");
							header("Location:account_issue");
						}
						else if($verified == 1){
							if($status=="inactive"){
								$logstatus = "success with account issue (inactive)";
								mysql_query("INSERT INTO Login_Logging(user_id,date_time,status,ip) VALUES('$user_id','$date','sucess with account issue','$ipaddress')");
								header("Location:account_issue");
							}
							else if($status=="blocked"){
								$logstatus = "success with account issue (blocked)";
								mysql_query("INSERT INTO Login_Logging(user_id,date_time,status,ip) VALUES('$user_id','$date','$logstatus','$ipaddress')");
								header("Location:account_issue");
							}
							else if($status=="active"){
								mysql_query("UPDATE Users SET status = 'inactive' WHERE email = '$email' ");
								$logstatus = "success with account issue (inactive)";
								mysql_query("INSERT INTO Login_Logging(user_id,date_time,status,ip) VALUES('$user_id','$date','$logstatus','$ipaddress')");
								header("Location:account_issue");
							}
						}
						else if($verified==3){
							if($status=="inactive"){
								$logstatus = "success with account issue (inactive)";
								mysql_query("INSERT INTO Login_Logging(user_id,date_time,status,ip) VALUES('$user_id','$date','sucess with account issue','$ipaddress')");
								header("Location:account_issue");
							}
							else if($status=="blocked"){
								$logstatus = "success with account issue (blocked)";
								mysql_query("INSERT INTO Login_Logging(user_id,date_time,status,ip) VALUES('$user_id','$date','$logstatus','$ipaddress')");
								header("Location:account_issue");
							}
							else{
								$logstatus = "success";
								mysql_query("INSERT INTO Login_Logging(user_id,date_time,status,ip) VALUES('$user_id','$date','$logstatus','$ipaddress')");
								header("Location:dashboard");
							}
						}
						else{
							echo "error! Please contact us";
						}
					}
					else{
						echo "welcome admin";
					}
				}
				else{
					$logstatus = "fail";
					mysql_query("INSERT INTO Login_Logging(user_id,date_time,status,ip) VALUES('$user_id','$date','$logstatus','$ipaddress')");
					header("Location:sign-in?ac=WRONG");
				}
			}
		}
		else{
			header("Location:sign-in?ac=NO_SUBMIT");
		}
	}

	function send_email($fname,$lname,$email,$v_hash){
  	$to      = $email; // Send email to our user
  	$subject = ' Reset Password | Friend Pay'; // Give the email a subject
		$v_hash = encrypt($v_hash);
  	$message = "
  	Dear $fname $lname,

    You have reached the limit of sign in attempts and we have deactivated your account right now.
    -----------------------------------------------------------
  	Please click this link to activate your account:

  	https://www2.comp.polyu.edu.hk/~15088378d/app/reset?v=reset&e=$email&h=$v_hash

    This is a system-generated email.  Please do not reply.
    If you did not use our service, please ignore this email.

  	Best Regards,

  	Friend Pay Team
  	";

  	$headers = 'From:noreply@friendpay.com' . "\r\n";
  	mail($to, $subject, $message, $headers);
  }

?>
