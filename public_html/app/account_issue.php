<?php
  include_once 'config.php';
  include_once 'token.php';

  if(!isloggedin()){
    header('Location:sign-in');
  }
  if(isVerified() && isActive()){
    header('Location:dashboard');
  }

  $verified = getVerfied();
  $status = getStatus();
  $isTwoFactor = isTwoFactor();
  $alt = "";
  $alt2 = "";
  $alt3 = "";
  $alt4 = "";
  $content = "";
  $form = "<form action='account_issue' method='post'>
  <div class='form-group'>
    <div class='input-group'>
    <input type='email' id='inputEmail' name='email' placeholder='Input a valid email address' class='form-control' required>
    </div>
    <div id='invalidEmailAlt' class='invalid-feedback' style='display:none;'>
    </div>
  </div>
  <div class='form-actions form-group'>
    <button type='submit' id='submit1' name='submit1' value='submit1' class='btn btn-primary btn-sm' disabled>Submit</button>
   </div>
  </form>";
  $give = "<button type='submit' id='submit2' name='submit2' value='submit2' class='btn bg-amber waves-effect'><i class='material-icons'>drafts</i>Request for New Verification Email</button>";
  $id = getUserId();
  $sql = mysql_query("SELECT Client.firstname, Client.lastname, Users.email FROM Client, Users WHERE Client.user_id = Users.id AND Users.id = '$id'")or die(mysql_error());
  $row = mysql_fetch_array($sql,MYSQL_NUM);
  $firstname = $row[0];
  $lastname = $row[1];
  $email = $row[2];

  if(isset($_POST['submit1'])){
    $id = isloggedin();
    $email = clean($_POST['email']);
  	$sql2 = mysql_query("SELECT Client.firstname, Client.lastname, Users.verify_hash, Users.verified FROM Users, Client WHERE Client.user_id = Users.id AND Users.id='$id'")or die(mysql_error());
  	$result = mysql_fetch_array($sql2,MYSQL_NUM);
  	if($result[3]==0){
      $fname = $result[0];
    	$lname = $result[1];
    	$v_hash = $result[2];
      $v_hash = md5(rand(0,1000));
      $join = date("Y-m-d H:i:s");
    	mysql_query("UPDATE Users SET join_date = '$join', verify_hash = '$v_hash', email='$email' WHERE id='$id'");
      send_email($fname,$lname,$email,$v_hash);
      $alt = "<div class='alert alert-success' role='alert'><strong>Well done!</strong> You have successfully requested a new activation email, please activate your account now.<button type='button' class='close' data-dismiss='alert' aria-label='Close'></div>";
      $form= "";
    }
    else{
      header("Location:dashboard");
    }
  }

  if(isset($_POST['submit2'])){
    $id = isloggedin();
  	$sql2 = mysql_query("SELECT Client.firstname, Client.lastname, Users.verify_hash, Users.email, Users.verified FROM Users, Client WHERE Client.user_id = Users.id AND Users.id='$id'")or die(mysql_error());
  	$result = mysql_fetch_array($sql2,MYSQL_NUM);
  	$fname = $result[0];
  	$lname = $result[1];
  	$v_hash = $result[2];
    $email = $result[3];
    if($result[4]==0){
      $v_hash = md5(rand(0,1000));
      $join = date("Y-m-d H:i:s");
      mysql_query("UPDATE Users SET join_date = '$join', verify_hash = '$v_hash' WHERE id='$id'");
    	send_email($fname,$lname,$email,$v_hash);
      $alt2 = "<div class='alert alert-success' role='alert'><strong>Well done!</strong> You have successfully requested a new activation email, please activate your account now.<button type='button' class='close' data-dismiss='alert' aria-label='Close'></div>";
      $give = "";
    }
    else{
      header("Location:dashboard");
    }
  }

  if(isset($_POST['submit3'])){
    $id = isloggedin();
    if(!empty($_POST['code1'])){
      require_once 'googleLib/GoogleAuthenticator.php';
      $sql_auth = mysql_query("SELECT Users.google_auth_code, Users.email, Users.two_factor FROM Users WHERE Users.id='$id'")or die(mysql_error());
      $result_auth = mysql_fetch_array($sql_auth,MYSQL_NUM);
      $google_auth_code = $result_auth[0];
      $ga = new GoogleAuthenticator();
      $code = $_POST['code1'];
      $checkResult = $ga->verifyCode($google_auth_code, $code, 2);
      if($checkResult){
        mysql_query("UPDATE Users SET verified = 3, status = 'active', two_factor='used' WHERE id='$id'");
        header("Location:dashboard?setup=True");
      }
      else{
        $alt3 = "<div class='alert alert-danger' role='alert'>Wrong code! Please try again.<button type='button' class='close' data-dismiss='alert' aria-label='Close'></div>";
      }
    }
    else{
      $alt3 = "<div class='alert alert-warning' role='alert'>Fail Submission! Please try again.<button type='button' class='close' data-dismiss='alert' aria-label='Close'></div>";
    }
  }

  if(isset($_POST['submit4'])){
    $id = isloggedin();
    if(!empty($_POST['code2'])){
      require_once 'googleLib/GoogleAuthenticator.php';
      $sql_auth = mysql_query("SELECT Users.google_auth_code, Users.email, Users.two_factor FROM Users WHERE Users.id='$id'")or die(mysql_error());
      $result_auth = mysql_fetch_array($sql_auth,MYSQL_NUM);
      $google_auth_code = $result_auth[0];
      $ga = new GoogleAuthenticator();
      $code = $_POST['code2'];
      $checkResult = $ga->verifyCode($google_auth_code, $code, 2);
      if($checkResult){
        mysql_query("UPDATE Users SET verified = 3, status = 'active', two_factor='used' WHERE id='$id'");
        header("Location:dashboard?setup=True");
      }
      else{
        $alt4 = "<div class='alert alert-danger' role='alert'>Wrong code! Please try again.<button type='button' class='close' data-dismiss='alert' aria-label='Close'></div>";
      }
    }
    else{
      $alt4 = "<div class='alert alert-warning' role='alert'>Fail Submission! Please try again.<button type='button' class='close' data-dismiss='alert' aria-label='Close'></div>";
    }
  }

  if($verified == 0 ){
    $content = goVerifyEmail($email, $alt, $alt2,$form, $give);
  }
  else if($verified == 1 ){
    require_once 'googleLib/GoogleAuthenticator.php';
    $sql_auth = mysql_query("SELECT Users.google_auth_code, Users.email, Users.two_factor FROM Users WHERE Users.id='$id'")or die(mysql_error());
    $result_auth = mysql_fetch_array($sql_auth,MYSQL_NUM);
    $google_auth_code = $result_auth[0];
    $ga = new GoogleAuthenticator();
    $auth_email = $result_auth[1];
    $qrCodeUrl = $ga->getQRCodeGoogleUrl($auth_email, $google_auth_code," Friend Pay | COMP3334 Project ($auth_email) ");
    $used_two_factor = $result_auth[2];
    if($used_two_factor == "used"){
      $content = "
      <div class='row clearfix'>
      <div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>
      <div class='card'>
      	<div class='header'>
      	<h2>Error</h2>
      	</div>
      	<div class='body'>
      	 <h5>For your account safety, please <a href='#'> contact us</a></h5>
      	</div>
      </div>
      </div>
      </div>
      ";
    }
    else {
      $content = "
      <div class='row clearfix'>
      <div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>
      <div class='card'>
      	<div class='header'>
      	<h2>Setup Two Factor Authentication</h2>
      	</div>
      	<div class='body'>
        ".$alt3."
      	 <h5>Please setup the two factor authentication to protect your account safety.</h5>
         <h6>Enter the verification code generated by Google Authenticator app on your phone.</h6>
         <div style='' class='media'>
        	<p>Get Google Authenticator on your phone</p>
          <a href='https://itunes.apple.com/us/app/google-authenticator/id388497605?mt=8' target='_blank'><img class='media-object' src='images/iphone.png' width='180' height='64'/></a>
          <a href='https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2&hl=en' target='_blank'><img class='media-object' src='images/android.png' width='180' height='64' /></a>
         </div>
         <h6>Scan the QR code below in Google Authenticator app.</h6>
         <img src='$qrCodeUrl' />
         <p>Alternatively, you can enter your secret key to Google Authenticator app: <strong>$google_auth_code</strong> [Warning: do not release it to third person] </p>
         <form action='account_issue' method='post'>
         <div class='form-group'>
           <div class='input-group'>
           Code:
           <input type='text' id='inputGoogleAuthCode' name='code1' minlength='6' maxlength='6' placeholder='Input 6-digit code here' class='form-control' required>
           <div id='invalidGoogleAuthCode' class='invalid-feedback' style='display:none;'>
           </div>
           </div>
         </div>
         <div class='form-actions form-group'>
           <button type='submit' id='submit3' name='submit3' value='submit3' class='btn btn-primary btn-sm' disabled>Submit</button>
          </div>
         </form>
      	</div>
      </div>
      </div>
      </div>
      ";
    }
    //$content = goTwoFactor();
  }
  else if($verified == 3){
    if($status == "inactive"){
      require_once 'googleLib/GoogleAuthenticator.php';
      $sql_auth = mysql_query("SELECT Users.google_auth_code, Users.email, Users.two_factor FROM Users WHERE Users.id='$id'")or die(mysql_error());
      $result_auth = mysql_fetch_array($sql_auth,MYSQL_NUM);
      $google_auth_code = $result_auth[0];
      $ga = new GoogleAuthenticator();
      $auth_email = $result_auth[1];
      $qrCodeUrl = $ga->getQRCodeGoogleUrl($auth_email, $google_auth_code," Friend Pay | COMP3334 Project ($auth_email) ");
      $used_two_factor = $result_auth[2];
      $content = "
      <div class='row clearfix'>
      <div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>
      <div class='card'>
      	<div class='header'>
      	<h2>Inactive Account</h2>
      	</div>
      	<div class='body'>
        ".$alt4."
      	<h5>Your account is inactive. Please activate your acount again.</h5>
        <form action='account_issue' method='post'>
        <div class='form-group'>
          <div class='input-group'>
          Input 6-digit authentication code in Google Authenticator app:
          <input type='text' id='inputGoogleAuthCode2' name='code2' minlength='6' maxlength='6' placeholder='Input 6-digit code here' class='form-control' required>
          <div id='invalidGoogleAuthCode2' class='invalid-feedback' style='display:none;'>
          </div>
          </div>
        </div>
        <div class='form-actions form-group'>
          <button type='submit' id='submit4' name='submit4' value='submit4' class='btn btn-primary btn-sm' disabled>Submit</button>
         </div>
        </form>
      	</div>
      </div>
      </div>
      </div>
          ";
    }
    else if($status == "blocked"){
      $content = leaveBlocked();
    }
  }
  else{
    header("Location:error");
  }

  function leaveBlocked(){
    return "
    <div class='row clearfix'>
      <div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>
        <div class='card'>
          <div class='header'>
            <h2>Blocked Account</h2>
          </div>
          <div class='body'>
            <h5>Your account is blocked. Please <a href='#'>Contact Us</a></h5>
          </div>
        </div>
      </div>
      </div>
    ";
  }

  function goVerifyEmail($email,$alt,$alt2,$form,$give){
      $content = "

      <div class='row clearfix'>
	<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>
		<div class='card'>
			<div class='header'>
				<h2>Incomplete Email Verification</h2>
			</div>
			<div class='body'>
				".$alt2."
				<h5>You have not verify your email yet. Please check your email(".$email.") and click on the activation link.</h5>
				<form action='account_issue' method='post'>
					<div class='form-group'>
					<div class='input-group'>
					".$give."
					</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</div>

      <div class='row clearfix' id='resend_vemail'>
      	<div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>
      		<div class='card'>
      			<div class='header'>
      			  <h2>Reset Account Email</h2>
      			</div>
      			<div class='body'>
      			  ".$alt."
      			  ".$form."
      			</div>
      		</div>
      	</div>
      </div>

      ";

      return $content;
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
	<title> Account Issue | Friend Pay</title>
	<?php include 'head-info.php'; ?>
  <!--<link href="https://fonts.googleapis.com/css?family=Roboto:400,700&subset=latin,cyrillic-ext" rel="stylesheet" type="text/css">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" type="text/css">
  <link href="plugins/bootstrap/css/bootstrap.css" rel="stylesheet">
  <link href="plugins/node-waves/waves.css" rel="stylesheet" />
  <link href="plugins/animate-css/animate.css" rel="stylesheet" />
  <link href="css/style.css" rel="stylesheet">
  <link href="css/themes/all-themes.css" rel="stylesheet" />-->
</head>

<body class="theme-green">
    <div class="page-loader-wrapper">
        <div class="loader">
            <div class="preloader">
                <div class="spinner-layer pl-red">
                    <div class="circle-clipper left">
                        <div class="circle"></div>
                    </div>
                    <div class="circle-clipper right">
                        <div class="circle"></div>
                    </div>
                </div>
            </div>
            <p>Please wait...</p>
        </div>
    </div>
    <div class="overlay"></div>

    <nav class="navbar">
        <div class="container-fluid">
            <div class="navbar-header">
                <a href="javascript:void(0);" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse" aria-expanded="false"></a>
                <a href="javascript:void(0);" class="bars"></a>
                <a class="navbar-brand" href="dashboard">Friend Pay</a>
            </div>
            <div class="collapse navbar-collapse" id="navbar-collapse">
                <ul class="nav navbar-nav navbar-right">
                    <li class="dropdown">
                        <?php
                          $cook =  $_COOKIE['SNID_'];
                          $link = "sign-out?logout=".$cook;
                        ?>
                        <a href="<?php echo $link;?>" role="button">
                            <i class="material-icons">logout</i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <section>
        <aside id="leftsidebar" class="sidebar">
            <div class="user-info">
                <div class="image">
                    <img src="images/user.png" width="48" height="48" alt="User" />
                </div>
                <div class="info-container">
                    <div class="name" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo $firstname." ".$lastname; ?></div>
                </div>
            </div>

            <div class="menu">
                <ul class="list">
                    <li class="header">Menu</li>
                    <li class="active">
                        <a href="dashboard">
                            <i class="material-icons">error</i>
                            <span>Account Issue</span>
                        </a>
                    </li>
                    <li>
                        <a href="profile">
                            <i class="material-icons">person</i>
                            <span>Profile</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="legal">
                <div class="copyright">
                    &copy; 2018 <a href="#"> Friend Pay | COMP3334 Project</a>.
                </div>
                <div class="version">
                    <b>Version: </b> 1.0.0
                </div>
            </div>

        </aside>

    </section>

    <section class="content">
        <div class="container-fluid">
            <div class="block-header">
                <h2>Account Issue</h2>
            </div>

            <?php echo $content; ?>

        </div>
    </section>
    <script>

    $(document).ready(function () {
      $(window).keydown(function(event){
          if(event.keyCode == 13) {
              e.preventDefault(); // Disable the " Entry " key
              return false;
          }
      });

      $('#inputGoogleAuthCode').change(function(){
        var code = $(this).val().length;
        var num = $(this).val();
        if(code==6&&checkNumber(num)){
          $('#invalidGoogleAuthCode').css("color","green");
          $('#invalidGoogleAuthCode').css("display", "block");
          $('#invalidGoogleAuthCode').html("");
          $('#inputGoogleAuthCode').removeClass( "is-invalid" ).addClass( "is-valid" );
        }
        else{
          $('#invalidGoogleAuthCode').css("color","red");
          $('#invalidGoogleAuthCode').css("display", "block");
          $('#invalidGoogleAuthCode').html("Please input 6-digit code!");
          $('#inputGoogleAuthCode').removeClass( "is-valid" ).addClass( "is-invalid" );
        }
        finalCheck2();
      });

      $('#inputGoogleAuthCode2').change(function(){
        var code = $(this).val().length;
        var num = $(this).val();
        if(code==6&&checkNumber(num)){
          $('#invalidGoogleAuthCode2').css("color","green");
          $('#invalidGoogleAuthCode2').css("display", "block");
          $('#invalidGoogleAuthCode2').html("");
          $('#inputGoogleAuthCode2').removeClass( "is-invalid" ).addClass( "is-valid" );
        }
        else{
          $('#invalidGoogleAuthCode2').css("color","red");
          $('#invalidGoogleAuthCode2').css("display", "block");
          $('#invalidGoogleAuthCode2').html("Please input 6-digit code!");
          $('#inputGoogleAuthCode2').removeClass( "is-valid" ).addClass( "is-invalid" );
        }
        finalCheck3();
      });

        $('#inputEmail').change(function(){
          var email = $(this).val();
          $.ajax({
            url:"check.php",
            method:"POST",
            data:{simplecheckemail:email},
            dataType:"text",
            success:function(response){

              if(response==0&&checkEmail(email)){
                $('#invalidEmailAlt').css("color","green");
                $('#invalidEmailAlt').css("display", "block");
                $('#invalidEmailAlt').html("Valid email address");
                $('#inputEmail').removeClass( "is-invalid" ).addClass( "is-valid" );
              }
              else if(response==0&&!checkEmail(email)){
                $('#invalidEmailAlt').css("color","red");
                $('#invalidEmailAlt').css("display", "block");
                $('#invalidEmailAlt').html("This email address is invalid");
                $('#inputEmail').removeClass( "is-valid" ).addClass( "is-invalid" );
              }
              else if(response==1&&!checkEmail(email)){
                $('#invalidEmailAlt').css("color","red");
                $('#invalidEmailAlt').css("display", "block");
                $('#invalidEmailAlt').html("This email address is invalid");
                $('#inputEmail').removeClass( "is-valid" ).addClass( "is-invalid" );
              }
              else if(response==1&&checkEmail(email)){
                $('#invalidEmailAlt').css("color","red");
                $('#invalidEmailAlt').css("display", "block");
                $('#invalidEmailAlt').html("This email address is already used by other user");
                $('#inputEmail').removeClass( "is-valid" ).addClass( "is-invalid" );
              }
              finalCheck();
            }
          });
        });

        function checkNumber(n){
          var regNumber = /^([0-9]{1,6})$/;
          if(regNumber.test(n)){
              return true;
            }
          else{
            return false;
          }
        }

        function checkEmail(email){
          var regMail = /^([_a-zA-Z0-9-]+)(\.[_a-zA-Z0-9-]+)*@([a-zA-Z0-9-]+\.)+([a-zA-Z]{2,3})$/;
          if(regMail.test(email)){
              return true;
            }
          else{
            return false;
          }
        }
    });

      function finalCheck(){
        var email =  document.getElementById("inputEmail").classList.contains('is-valid');
        if(email){
          document.getElementById("submit1").disabled = false;
        }
        else{
          document.getElementById("submit1").disabled = true;
        }
      }

      function finalCheck2(){
        var code =  document.getElementById("inputGoogleAuthCode").classList.contains('is-valid');
        if(code){
          document.getElementById("submit3").disabled = false;
        }
        else{
          document.getElementById("submit3").disabled = true;
        }
      }

      function finalCheck3(){
        var code =  document.getElementById("inputGoogleAuthCode2").classList.contains('is-valid');
        if(code){
          document.getElementById("submit4").disabled = false;
        }
        else{
          document.getElementById("submit4").disabled = true;
        }
      }

    </script>
    <script src="plugins/jquery/jquery.min.js"></script>
    <script src="plugins/bootstrap/js/bootstrap.js"></script>
    <script src="plugins/bootstrap-select/js/bootstrap-select.js"></script>
    <script src="plugins/jquery-slimscroll/jquery.slimscroll.js"></script>
    <script src="plugins/node-waves/waves.js"></script>
    <script src="plugins/jquery-countto/jquery.countTo.js"></script>
    <script src="plugins/raphael/raphael.min.js"></script>
    <script src="plugins/morrisjs/morris.js"></script>
    <script src="plugins/chartjs/Chart.bundle.js"></script>
    <script src="plugins/flot-charts/jquery.flot.js"></script>
    <script src="plugins/flot-charts/jquery.flot.resize.js"></script>
    <script src="plugins/flot-charts/jquery.flot.pie.js"></script>
    <script src="plugins/flot-charts/jquery.flot.categories.js"></script>
    <script src="plugins/flot-charts/jquery.flot.time.js"></script>
    <script src="plugins/jquery-sparkline/jquery.sparkline.js"></script>
    <script src="js/admin.js"></script>
    <script src="js/pages/index.js"></script>
    <script src="js/demo.js"></script>

  </body>
</html>
