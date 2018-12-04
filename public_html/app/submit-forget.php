<?php
  include_once 'config.php';
  include_once 'token.php';
  include_once 'encrypt_decrypt.php';

  if(isloggedin()){
    header('Location:dashboard');
  }

  if(isset($_POST['submit']) && !empty($_POST['submit'])){
    if(isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])){
      $secret = '6Lf3gHwUAAAAALzCJ5Q61xmIcUNGivheJWBM9NYQ';
      $verifyResponse = file_get_contents('https://www.google.com/recaptcha/api/siteverify?secret='.$secret.'&response='.$_POST['g-recaptcha-response']);
      $responseData = json_decode($verifyResponse);
      if($responseData->success){
        if(isset($_POST['submit'])){
          generate();
        }
      	else{
      		header("forgot-password?re=NO_SUBMIT");
      	}
      }
      else{
        header('Location:forgot-password?re=FAIL_CAPTCHA');
      }
    }
    else{
      header('Location:forgot-password?re=NO_SUBMIT_CAPTCHA');
    }
  }
  else{
    header('Location:forgot-password?re=NO_SUBMIT');
  }

  function generate(){
		$email = clean($_POST['email']);
    $sql = mysql_query("SELECT * FROM Users WHERE email ='$email' ")or die(mysql_error());
    $match  = mysql_num_rows($sql);
    if($match>0){
      $v_hash = md5(rand(0,1000));
      mysql_query("UPDATE Users SET status='inactive', verified = 2, verify_hash = '$v_hash' WHERE email = '$email'") or die(mysql_error());
      $sql2 = mysql_query("SELECT Client.firstname, Client.lastname FROM Client, Users WHERE Users.email ='$email' AND Users.id = Client.user_id   ")or die(mysql_error());
      $getf = mysql_fetch_array($sql2,MYSQL_NUM);
      $firstname = $getf[0];
      $lastname = $getf[1];
      send_email($firstname,$lastname,$email,$v_hash);
    }
    else{
      header('Location:forgot-password?re=WRONG');
    }
  }

  function generateRandomString($length = 8) {
    return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
  }

  function send_email($fname,$lname,$email,$v_hash){
  	$to      = $email; // Send email to our user
  	$subject = ' Reset Password | Friend Pay'; // Give the email a subject
    $v_hash = encrypt($v_hash);
  	$message = "
  	Dear $fname $lname,

    We have reeceive your reset password request!
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

  $email = $_POST['email'];
?>
<html lang="en">
<head>
  <title> Requested password change | FriendPay </title>
  <?php include 'head-info.php';?>
</head>

<body class="fp-page">
    <div class="fp-box">
        <div class="logo">
          <a href="../" style="size:200%;">Friend<b>Pay</b></a>
          <small>Send money to friends and family.</small>
        </div>
        <div class="card">
            <div class="body">
              <div class="msg">
                  We have sent you a reset your password link to your email(<?php echo $_POST['email'] ?>).
              </div>
              <a href="../"><button class="btn btn-info waves-effect">Back To Home</button></a>
            </div>
        </div>
    </div>
</body>
</html>
