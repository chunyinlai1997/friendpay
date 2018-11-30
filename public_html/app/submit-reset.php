<?php
  include_once 'config.php';
  include_once 'token.php';

  if(isloggedin()){
    header('Location:dashboard');
  }

  if(isset($_POST['submit']) && !empty($_POST['submit'])){
    if(isset($_POST['enc']) && isset($_POST['uid'])){
      $uid = $_POST["uid"];
      $hash = $_POST["enc"];

      $sql2 = mysql_query("SELECT Users.verify_hash, Client.lastname, Client.firstname, Users.email, Users.two_factor, Users.verified FROM Users, Client WHERE Users.id ='$uid' and Users.id = Client.user_id ")or die(mysql_error());
      $result2 = mysql_fetch_array($sql2,MYSQL_NUM);
      $v_hash =  decrypt($result2[0]);
      $verified = $result2[5];
      $match  = mysql_num_rows($sql2);

      if($match==0){
        header("Location:reset?re=WRONG");
      }
      else if ($match==1){
        if( $verified == 2  || $verified == 4 ){
          if ($v_hash == $hash){
            $new_hash = md5(rand(0,1000));
            $password = clean($_POST['password1']);
            if($password!=$_POST['password1']){
              header("Location:reset?re=TIMEOUT");
            }
            $options = [
                'cost' => 9,
            ];
            $hashed_password = password_hash("$password", PASSWORD_BCRYPT, $options);
            $vcode = 1;
            $two_factor = $result2[4];
            if ($two_factor == "used"){
              $vcode = 3;
            }
            mysql_query("UPDATE Users SET password = '$hashed_password',  verify_hash = '$new_hash', status='inactive', verified = '$vcode' WHERE id ='$uid'")or die(mysql_error());
            send_email($result2[1],$result2[2],$result2[3]);
            //header("Location:sign-in?changed=True");
          }
        }
        else{
          header('Location:reset?re=NOT_APPLICABLE');
        }
      }

    }
    else{
      header('Location:reset?re=NO_SUBMIT');
    }
  }
  else{
    header('Location:reset?re=NO_SUBMIT');
  }


  function send_email($fname,$lname,$email){
  	$to      = $email; // Send email to our user
  	$subject = ' Password Changed | Friend Pay'; // Give the email a subject
  	$message = "
  	Dear $fname $lname,

  	Your account password has been changed through resetting passsowrd.

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
	<title> Completed Reset Password | Friend Pay</title>
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
                            Reset Password
                        </h2>
                    </div>
                    <div class="body">
                      <div class="row justify-content-center">
                        <div class="col-sm-11 col-md-8 col-6">
                           <h5>Completed!</h5>
                           <br>
                           Your account password has been changed.
                           <hr>
                           <a href="../"><button class="btn btn-info waves-effect">Back To Home</button></a>
                           <a href="sign-in"><button class="btn btn-success waves-effect">SIGN IN</button></a>
                        </div>
                      </div>
                    </div>
                </div>
            </div>
        </div>
        <?php include 'footer-info.php';?>
   </body>
   </html>
