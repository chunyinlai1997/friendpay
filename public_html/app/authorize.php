<?php
  include_once 'config.php';
  include_once 'token.php';
  include_once 'encrypt_decrypt.php';

  if(!isloggedin()){
    header("Location:sign-in?need_login=True");
  }
  if(!isVerified() || !isActive()){
    header("Location:account_issue");
  }
  if(authorized()){
    header("Location:dashboard");
  }

  $id = getUserId();
  $sql = mysql_query("SELECT Client.firstname, Client.lastname, Users.email, Users.profile_img FROM Client, Users WHERE Client.user_id = Users.id AND Users.id = '$id' ")or die(mysql_error());
  $row = mysql_fetch_array($sql,MYSQL_NUM);
  $firstname = $row[0];
  $lastname = $row[1];
  $email = $row[2];
  $profile_img = $row[3];
  $alt = "";

  if(isset($_POST['submit4'])){
    $id = isloggedin();
    if(!empty($_POST['code2'])){
      require_once 'googleLib/GoogleAuthenticator.php';
      $sql_auth = mysql_query("SELECT Users.google_auth_code, Users.email, Users.two_factor FROM Users WHERE Users.id='$id'")or die(mysql_error());
      $result_auth = mysql_fetch_array($sql_auth,MYSQL_NUM);
      $google_auth_code = decrypt($result_auth[0]);
      $ga = new GoogleAuthenticator();
      $code = $_POST['code2'];
      $checkResult = $ga->verifyCode($google_auth_code, $code, 2);
      if($checkResult){
        $d_token = sha1($_COOKIE['SNID']);
        mysql_query("UPDATE Token SET authorized='Yes' WHERE token='$d_token'");
        send_email($firstname,$lastname,$email);
        header("Location: dashboard");
      }
      else{
        $alt = "<div class='alert alert-danger' role='alert'>Wrong code! Please try again.<button type='button' class='close' data-dismiss='alert' aria-label='Close'></div>";
      }
    }
    else{
      $alt = "<div class='alert alert-warning' role='alert'>Fail Submission! Please try again.<button type='button' class='close' data-dismiss='alert' aria-label='Close'></div>";
    }
  }

  function send_email($fname,$lname,$email){
  	$to      = $email; // Send email to our user
  	$subject = ' Login Alert -- Account Safety | Friend Pay'; // Give the email a subject
    $ipaddress = $_SERVER['REMOTE_ADDR'];
    $now = date("Y-m-d H:i:s");
  	$message = "
  	Dear $fname $lname,

    Is that you?
  	You have just login to your accunt with passing the two factor authentication.
    IP: $ipaddress
    Time: $now
    If it is not you, please contact us immediately to protect your account safety.

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
	<title> Authorize User | Friend Pay</title>
	<?php include 'head-info.php'; ?>
  <link href='https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css' rel='stylesheet' />
  <link href="plugins/sweetalert/sweetalert.css" rel="stylesheet">
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
                    <a href="profile"><img src="<?php echo $profile_img; ?>" width="48" height="48" alt="User" /></a>
                </div>
                <div class="info-container">
                    <div class="name" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo $firstname." ".$lastname; ?></div>
                    <div class="email" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo $email; ?></div>
                </div>
            </div>

            <div class="menu">
                <ul class="list">

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
            <div class="row clearfix">
              <div class='row clearfix'>

              <div class='col-lg-12 col-md-12 col-sm-12 col-xs-12'>
              <div class='card'>
                <div class='header'>
                <h2>Login Authorization</h2>
                </div>
                <div class='body'>
                <? echo $alt;?>
                <h5>Please type in your authentication code to prove your identity.</h5>
                <small>In case you lost the Google Authenticator key, please go to "forget password".</small>
                <hr/>
                <form action='authorize' method='post'>
                <div class='form-group'>
                  <div class='input-group'>
                  Input 6-digit authentication code in Google Authenticator app:
                  <input autofocus type='text' id='inputGoogleAuthCode2' name='code2' minlength='6' maxlength='6' placeholder='Input 6-digit code here' class='form-control' onkeyup="checkcode()" onchange="checkcode()" autofocus required>
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
            </div>
        </div>
    </section>
    <script>
    var codeP = /^([0-9]{6})$/;
    function checkcode(){
      var code  = document.getElementById("inputGoogleAuthCode2").value;
      var checking = codeP.test( code ) === true;
      if(checking){
        document.getElementById("inputGoogleAuthCode2").classList.add('is-valid');
        document.getElementById("inputGoogleAuthCode2").classList.remove('is-invalid');
        document.getElementById("invalidGoogleAuthCode2").style.display = "none";
      }
      else{
        document.getElementById("inputGoogleAuthCode2").classList.add('is-invalid');
        document.getElementById("inputGoogleAuthCode2").classList.remove('is-valid');
        document.getElementById("invalidGoogleAuthCode2").innerHTML = "Please input a valid code";
        document.getElementById("invalidGoogleAuthCode2").style.display = "block";
        document.getElementById("invalidGoogleAuthCode2").style.color = "red";
      }
      finalCheck3();
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
    <script src="plugins/sweetalert/sweetalert.min.js"></script>
    <script src="js/admin.js"></script>
    <script src="js/pages/ui/dialogs.js"></script>
    <script src="js/pages/index.js"></script>
    <script src="js/demo.js"></script>

</body>

</html>
