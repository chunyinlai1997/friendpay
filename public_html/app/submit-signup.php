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
        create();
      }
      else{
        header('Location:sign-up?re=FAIL_CAPTCHA');
      }
    }
    else{
      header('Location:sign-up?re=NO_SUBMIT_CAPTCHA');
    }
  }
  else{
    header('Location:sign-up?re=NO_SUBMIT');
  }

  function create(){
  	if(isset($_POST['submit'])){
  		$password = clean($_POST['password1']);
      if($password != $_POST['password1']){
        header('Location:sign-up?re=PASSWORD_PROBLEM');
      }
  		else{
        $firstname = clean($_POST['firstname']);
    		$lastname = clean($_POST['lastname']);
        $email = clean($_POST['email']);
        $phone = clean($_POST['phone']);
    		$join = date("Y-m-d H:i:s");
        $options = [
            'cost' => 9,
        ];
        $hashed_password = password_hash("$password", PASSWORD_BCRYPT, $options);
    		$v_hash = md5(rand(0,1000));
        $v = 0;

        require_once 'googleLib/GoogleAuthenticator.php';
        $ga = new GoogleAuthenticator();
        $secretGoogleAuth = $ga -> createSecret();
        $secretGoogleAuth = encrypt($secretGoogleAuth);
    		mysql_query("INSERT INTO Users(email,password,join_date,create_date,verified,verify_hash,role,google_auth_code) VALUES('$email','$hashed_password','$join','$join','$v','$v_hash','client','$secretGoogleAuth')")or die(mysql_error());
    		$sql = mysql_query("SELECT id FROM Users WHERE email ='$email'")or die(mysql_error());
        $result = mysql_fetch_array($sql,MYSQL_NUM);
    		$mid =  $result[0];

        mysql_query("INSERT INTO Client(user_id,lastname,firstname,phone) VALUES('$mid','$lastname','$firstname','$phone')")or die(mysql_error());
    		send_email($firstname,$lastname,$email,$v_hash);
      }
  	}
  	else{
  		header("sign-up?re=NO_SUBMIT");
  	}
  }



  function send_email($fname,$lname,$email,$v_hash){
  	$to      = $email; // Send email to our user
  	$subject = ' Account Verification | Friend Pay'; // Give the email a subject
    $v_hash = encrypt($v_hash);
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
	<title>Register Successful | Friend Pay</title>
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
                            Sign Up Completed
                        </h2>
                    </div>
                    <div class="body">
                      <div class="row justify-content-center">
                        <div class="col-sm-11 col-md-8 col-6">
                           <h5><font style="color:black; font-size:1em;">Thank you for the registration!</h5>
                           A verification email has sent to your email adress (<?php echo $_POST['email'] ?>), you have to activate your account with the verification link in 24 hours.</font>
                           <br>
                           <hr>
                           <a href="../"><button class="btn btn-info waves-effect">Back To Home</button></a>
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
