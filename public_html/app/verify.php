<?php
	include_once 'config.php';
	include_once 'token.php';

  $msg="";
	$flag="Verification Fail";
	if(isset($_GET['v']) && !empty($_GET['v']) AND isset($_GET['e']) && !empty($_GET['e']) AND isset($_GET['h']) && !empty($_GET['h'])){
		if($_GET['v']=="activate"){
			$email = mysql_escape_string($_GET['e']);
			$hash = mysql_escape_string($_GET['h']);
			$sql = mysql_query("SELECT id, email, verified, verify_hash FROM Users WHERE email='$email' AND verify_hash='$hash'") or die(mysql_error());
			$row = mysql_fetch_array($sql,MYSQL_NUM);
			$match  = mysql_num_rows($sql);
      $tryid = $row[0];

			if($match==0 && $email == $row[1]){
				$msg="This link has been activated.";
			}
			else if($match==0 && $email != $row[1]){
				$msg="There is a verification problem, please <a href='#'>contact us</a>.";
			}
			else if($match==1){
				$sql2 = mysql_query("SELECT HOUR(TIMEDIFF(join_date, now())) FROM Users WHERE id = '$tryid' ");
	      $intime = mysql_fetch_array($sql2,MYSQL_NUM);
				if($intime[0] >= 24 ){
	        $msg="Your verification link has expired! We have send you a new verification link to your email address. If you have any diffculties, please <a href=''>Contact Us</a> for help. ";
	        $v_hash = md5(rand(0,1000));
					$join = date("Y-m-d H:i:s");
	        mysql_query("UPDATE Users SET join_date = '$join', verify_hash = '$v_hash' WHERE email = '$email'");
	        $sql2 = mysql_query("SELECT Client.firstname, Client.lastname FROM Users,Client WHERE Users.email='$email' AND Users.id = Client.user_id ") or die(mysql_error());
	        $row2 = mysql_fetch_array($sql2,MYSQL_NUM);
	        $firstname = $row[0];
	        $lastname = $row[1];
	        send_email($firstname,$lastname,$email,$v_hash);
      	}
				else{
					if($row[3]==$hash){
						$msg="Verification Success! Your account has activated now!";
						$flag="Verification Success";
						$new_hash = md5(rand(0,1000));
		        $join = date("Y-m-d H:i:s");
						mysql_query("UPDATE Users SET verified = 1, verify_hash = '$new_hash', join_date = '$join' WHERE email = '$email'") or die(mysql_error());
					}
					else{
						$msg = "Verification Error! Please sign in to request for a new verfication email.";
					}
				}
			}
		}
		else{
			$msg = "Thank you for coming to FriendPay, but you are not applicable for the account verification, you can <a href='#'>sign in</a>.";
		}
	}
	else{
		$msg = "Thank you for coming to FriendPay, but you are not applicable for the account verification, you can <a href='#'>sign in</a>.";
	}

  function send_email($fname,$lname,$email,$v_hash){
  	$to      = $email; // Send email to our user
  	$subject = ' Account Verification | Friend Pay'; // Give the email a subject
  	$message = "
  	Dear $fname $lname,

  	Thanks for signing up!
  	Your account has been created, you can activate your account by pressing the url below.

  	---------------------------------------------------------------------------------------

  	Please click this link to activate your account within 24 hours:
  	https://www2.comp.polyu.edu.hk/~15088378d/app/verify?v=activate&e=$email&h=$v_hash

		This is a system-generated email.  Please do not reply.
    If you did not use our service, please ignore this email.

  	Best Regards,

  	Friend Pay Team
  	";

  	$headers = 'From:noreply@friendpay.com' . "\r\n";
  	mail($to, $subject, $message, $headers);
  }
?>
<html lang="en">
<head>
	<title> <?php echo $flag; ?> | Friend Pay</title>
	<?php include 'head-info.php';?>
</head>
<body class="theme-green">
	<nav class="navbar">
    <div class="navbar-header">
        <a href="javascript:void(0);" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" aria-expanded="false"></a>
        <a href="javascript:void(0);" class="bars"></a>
        <a class="navbar-brand" href="../">Friend Pay</a>
    </div>
  </nav>
    <section class="content">
        <div class="container-fluid">
            <div class="col-lg-12">
                <div class="card">
                    <div class="header">
                        <h2>
                            Account Verification
                        </h2>
                    </div>
                    <div class="body">
                      <div class="row justify-content-center">
                        <div class="col-sm-11 col-md-8 col-6">
													 <?php echo $flag; ?>
                           <h5><?php echo $msg; ?></h5>
                           <br>
                           <hr>
                           <a href="../"><button class="btn btn-info waves-effect">Back To Home</button></a>
                           <a href="sign-in"><button class="btn btn-info waves-effect">Sign In</button></a>
                        </div>
                      </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

	   <?php include 'footer-info.php';?>
</body>
</html>
