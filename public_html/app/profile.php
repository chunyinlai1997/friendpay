<?php
  include_once 'config.php';
  include_once 'token.php';
  include_once 'encrypt_decrypt.php';

  if(!isloggedin()){
    header("Location:sign-in?need_login=True");
  }

  $id = getUserId();
  $sql = mysql_query("SELECT Client.firstname, Client.lastname, Users.email, Users.create_date, Client.phone, Users.verified, Users.profile_img, Users.status, Users.two_factor FROM Client, Users WHERE Client.user_id = Users.id AND Users.id = '$id'")or die(mysql_error());
  $row = mysql_fetch_array($sql,MYSQL_NUM);
  $firstname = $row[0];
  $lastname = $row[1];
  $email = $row[2];
  $create_date = $row[3];
  $phone = $row[4];
  $verified = $row[5];
  $profile_img = $row[6];
  $status = $row[7];
  $two_factor = $row[8];
  $sql2 = mysql_query("SELECT date_time, status, ip FROM Login_Logging L WHERE user_id = '$id' ORDER BY date_time  DESC LIMIT 1,1 ");
  $row2 = mysql_fetch_array($sql2,MYSQL_NUM);
  $last_login_time = $row2[0];
  $last_login_status = "";
  $alt = "";

  if($row2[1]!="fail"){
    $last_login_status = "success";
  }
  else{
    $last_login_status = "fail";
  }

  if(isset($_GET["update"])&&$_GET["update"]=="profileimg"){
    header("Location:profile");
  }

  if(isset($_POST['uploadprofile'])){
    	$image = base64_encode(file_get_contents($_FILES['profileimg']['tmp_name']));
    	$options = array('http'=>array(
    		'method'=>"POST",
    		'header'=>"Authorization: Bearer 9d80a5579bea50b9dbdaad0528ee66d08da6ecca\n"."Content-Type: application/x-ww-form-urlencoded",
    		'content'=>$image
    	));

    	$context = stream_context_create($options);
    	$imgurURL = "https://api.imgur.com/3/image";
    	$response = file_get_contents($imgurURL, false, $context);
    	$res = json_decode($response);
    	$imagelink = $res->data->link;
    	mysql_query("UPDATE Users SET profile_img='$imagelink' WHERE id = '$id' ");
      header("Location:profile?update=profileimg");
    }

    if(isset($_POST["change_password_submit"])){
      require_once 'googleLib/GoogleAuthenticator.php';
      $sql_auth = mysql_query("SELECT Users.google_auth_code, Users.email, Users.two_factor, Users.password FROM Users WHERE Users.id='$id'")or die(mysql_error());
      $result_auth = mysql_fetch_array($sql_auth,MYSQL_NUM);
      $google_auth_code = decrypt($result_auth[0]);
      $ga = new GoogleAuthenticator();
      $code = $_POST["inputGoogleAuthCode"];
      $checkResult = $ga->verifyCode($google_auth_code, $code, 2);
      if($checkResult){
        if(!empty($_POST["OldPassword"])){
          $OldPassword = clean($_POST["OldPassword"]);
          if(password_verify($OldPassword, $result_auth[3])){
            $password = clean($_POST['NewPassword']);
            if($password!=$_POST['NewPassword']){
             $alt =  "<div class='alert alert-danger' role='alert'>Timeout! Please try again.<button type='button' class='close' data-dismiss='alert' aria-label='Close'></div>";
            }
            else{
              $options = [
                  'cost' => 9,
              ];
              $hashed_password = password_hash("$password", PASSWORD_BCRYPT, $options);
              mysql_query("UPDATE Users SET password='$hashed_password' WHERE id = '$id'");
              send_email($firstname,$lastname,$email);
              $alt = "<div class='alert alert-success' role='alert'>Your password has changed successfully.<button type='button' class='close' data-dismiss='alert' aria-label='Close'></div>";
            }
          }
          else {
            $alt =  "<div class='alert alert-danger' role='alert'>Wrong Password! Please try again.<button type='button' class='close' data-dismiss='alert' aria-label='Close'></div>";
          }
        }
        else {
          $alt =  "<div class='alert alert-danger' role='alert'>Empty Password Change Submission! Please try again.<button type='button' class='close' data-dismiss='alert' aria-label='Close'></div>";
        }
      }
      else{
        $alt =  "<div class='alert alert-danger' role='alert'>Wrong code! Please try again.<button type='button' class='close' data-dismiss='alert' aria-label='Close'></div>";
      }
    }

    function send_email($fname,$lname,$email){
    	$to      = $email; // Send email to our user
    	$subject = ' Password Changed | Friend Pay'; // Give the email a subject
    	$message = "
    	Dear $fname $lname,

    	Your account password has been changed through passsowrd setting.

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
	<title> Profile | Friend Pay</title>
	<?php include 'head-info.php'; ?>
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
                      <a href="profile" role="button">
                          <i class="material-icons">person</i>
                      </a>
                    </li>

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
                    <li class="header">Menu</li>
                    <li>
                        <a href="dashboard">
                            <i class="material-icons">home</i>
                            <span>Home</span>
                        </a>
                    </li>
                    <li>
                        <a href="activity">
                            <i class="material-icons">history</i>
                            <span>Activity</span>
                        </a>
                    </li>
                    <li>
                        <a href="send_request">
                            <i class="material-icons">cached</i>
                            <span>Send and Request</span>
                        </a>
                    </li>
                    <li>
                        <a href="wallet">
                            <i class="material-icons">account_balance_wallet</i>
                            <span>Wallet</span>
                        </a>
                    </li>
                    <li>
                        <a href="#">
                            <i class="material-icons">help</i>
                            <span>Help</span>
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
            <div class="row clearfix">
                <div class="col-xs-12 col-sm-3">
                    <div class="card profile-card">
                        <div class="profile-header">&nbsp;</div>
                        <div class="profile-body">
                            <div class="image-area">
                                <img src="<?php echo $profile_img; ?>"  width="128" height="128" alt="Profile Image" />
                            </div>
                            <div class="content-area">
                                <a class="btn bg-pink waves-effect m-b-15 collapsed" role="button" data-toggle="collapse" href="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                                    Change Icon
                                </a>
                                <div class="collapse" id="collapseExample">
                                    <div class="well">
                                      <form action="profile" method="POST" enctype="multipart/form-data">
                                      <label for="file">Choose an image to upload</label>
                                      <input id="profileimg" name="profileimg" type="file">
                                      </input>
                                      <input type="submit" class="btn btn-primary" id="uploadprofile" name="uploadprofile" value="Upload" disabled>
                                      </form>
                                    </div>
                                </div>
                                <h3><?php echo $firstname." ".$lastname;?></h3>
                                <p></p>
                                <p>Since <? echo date("d-m-Y", strtotime($create_date)); ?></p>
                            </div>
                        </div>
                        <div class="profile-footer">
                          <ul>
                            <li>
                                <span>Account Status</span>
                                <span><?php echo $status;?></span>
                            </li>
                            <li>
                                <span>Two Factor Authentication</span>
                                <span><?php echo $two_factor;?></span>
                            </li>
                            <li>
                                <span>Last Login</span>
                                <span><small><?php echo $last_login_time."(".$last_login_status.")";?></small></span>

                            </li>

                          </ul>
                        </div>
                    </div>

                    <div class="card card-about-me">
                        <div class="header">
                            <h2>My Info</h2>
                        </div>
                        <div class="body">
                            <ul>
                                <li>
                                    <div class="title">
                                        <i class="material-icons">email</i>
                                        Email
                                    </div>
                                    <div class="content">
                                        <? echo $email." ";
                                           if($verified!=0){
                                             echo "<span class='label bg-green'>Verified</span>";
                                           }
                                           else{
                                             echo "<span class='label bg-green'>Not Verified</span>";
                                           }
                                        ?>
                                    </div>
                                </li>
                                <li>
                                    <div class="title">
                                        <i class="material-icons">phone</i>
                                        Phone
                                    </div>
                                    <div class="content">
                                        <? echo $phone." ";?><span class="label bg-green">Verified</span>
                                    </div>
                                </li>
                                <li>
                                    <div class="title">
                                        <i class="material-icons">location_city</i>
                                        Billing Address
                                    </div>
                                    <div class="content">
                                        <?php
                                        if($address!=""){
                                          echo $address;
                                        }
                                        else{
                                          echo "No billing address yet";
                                        }
                                         ?>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-9">
                  <? echo $alt;?>
                </div>

                <div class="col-xs-12 col-sm-9">
                    <div class="card">
                        <div class="body">
                            <div>
                                <ul class="nav nav-tabs" role="tablist">
                                    <li role="presentation" class="active"><a href="#friend" aria-controls="friend" role="tab" data-toggle="tab">Friends</a></li>
                                    <li role="presentation"><a href="#profile_settings" aria-controls="settings" role="tab" data-toggle="tab">Profile Settings</a></li>
                                    <li role="presentation"><a href="#change_password_settings" aria-controls="settings" role="tab" data-toggle="tab">Change Password</a></li>
                                </ul>

                                <div class="tab-content">
                                    <div role="tabpanel" class="tab-pane fade in active" id="friend">
                                      <div class="list-unstyled row clearfix">
                                        <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6">
                                          <div class="image-area">
                                              <img src="<?php echo $profile_img; ?>"  width="128" height="128" alt="Profile Image" />
                                          </div>
                                        </div>


                                      </div>
                                    </div>


                                    <div role="tabpanel" class="tab-pane fade in" id="profile_settings">
                                        <form action="profile" method="POST" class="form-horizontal">
                                            <div class="form-group">
                                                <label for="firstname" class="col-sm-2 control-label">First Name</label>
                                                <div class="col-sm-10">
                                                    <div class="form-line">
                                                      <input autocomplete="off" type="text" name="firstname" id="inputfirstname" class="form-control" onkeyup="safeName(this)" placeholder="Your first name" autofocus inlength="1" maxlength="255" required/>
                                                      <div id="invalidfirstname" class="invalid-feedback" style="display:none;">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="lastname" class="col-sm-2 control-label">Last Name</label>
                                                <div class="col-sm-10">
                                                    <div class="form-line">
                                                      <input autocomplete="off" type="text" name="lastname" id="inputlastname" class="form-control" onkeyup="safeName(this)" placeholder="Your last name" minlength="1" maxlength="255" required/>
                                                      <div id="invalidlastname" class="invalid-feedback" style="display:none;">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="email" class="col-sm-2 control-label">Email</label>
                                                <div class="col-sm-10">
                                                    <div class="form-line">
                                                      <input autocomplete="off" type="email" name="email" id="inputEmail" class="form-control" placeholder="Your email address" required />
                                                      <div id="invalidemail" class="invalid-feedback" style="display:none;">
                                                        Please provide a valid email address
                                                      </div>
                                                      <div id="invalidemail2" class="invalid-feedback" style="display:none;">
                                                      </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="phone" class="col-sm-2 control-label">Last Name</label>
                                                <div class="col-sm-10">
                                                    <div class="form-line">
                                                      <input autocomplete="off" type="phone" min-length="8" max-length="8" name="phone" id="inputPhone" class="form-control" placeholder="Your phone number" required />
                                                      <div id="invalidphonenumber" class="invalid-feedback" style="">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="address" class="col-sm-2 control-label">Billing Address</label>
                                                <div class="col-sm-10">
                                                    <div class="form-line">
                                                        <input autocomplete="off" type="text" name="address" id="address" class="form-control" onkeyup="safeName(this)" placeholder="Your last name" minlength="1" maxlength="255" required/>
                                                        <div id="invalidaddress" class="invalid-feedback" style="display:none;">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="OldPassword" class="col-sm-3 control-label">Current Password</label>
                                                <div class="col-sm-9">
                                                    <div class="form-line">
                                                        <input autofocus type="password" class="form-control" id="OldPassword" name="OldPassword" placeholder="Current Password" required>
                                                    </div>
                                                    <div id="invalidOldPassword" class="invalid-feedback" style="display:none;">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-sm-offset-2 col-sm-10">
                                                    <button type="submit" id="change_profile_submit" name="change_profile_submit" value="change_profile_submit" class="btn btn-danger">SUBMIT</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>

                                    <div role="tabpanel" class="tab-pane fade in" id="change_password_settings">
                                        <form action="profile.php" method="POST" class="form-horizontal">
                                            <div class="form-group">
                                                <label for="OldPassword" class="col-sm-3 control-label">Current Password</label>
                                                <div class="col-sm-9">
                                                    <div class="form-line">
                                                        <input autofocus type="password" class="form-control" id="OldPassword" name="OldPassword" placeholder="Current Password" required>
                                                    </div>
                                                    <div id="invalidOldPassword" class="invalid-feedback" style="display:none;">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="NewPassword" class="col-sm-3 control-label">New Password</label>
                                                <div class="col-sm-9">
                                                    <div class="form-line">
                                                        <input type="password" class="form-control" id="NewPassword" onkeyup="validatePass(this.value)" minlength="8" name="NewPassword" placeholder="New Password" required>
                                                    </div>
                                                    <div id="invalidNewPassword" class="invalid-feedback" style="display:none;">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="NewPasswordConfirm" class="col-sm-3 control-label">Confirm New Password</label>
                                                <div class="col-sm-9">
                                                    <div class="form-line">
                                                        <input type="password" class="form-control" id="NewPasswordConfirm" name="NewPasswordConfirm" onkeyup="checkPass(this)" minlength="8" placeholder="Confirm New Password" required>
                                                    </div>
                                                    <div id="invalidNewPasswordConfirm" class="invalid-feedback" style="display:none;">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="inputGoogleAuthCode" class="col-sm-3 control-label">Google Authenticator Code</label>
                                                <div class="col-sm-9">
                                                    <div class="form-line">
                                                        <input type="text" class="form-control" id="inputGoogleAuthCode" name="inputGoogleAuthCode" placeholder="6-digit 2 factor authentication code" required>
                                                    </div>
                                                    <div id="invalidGoogleAuthCode" class="invalid-feedback" style="display:none;">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-sm-offset-3 col-sm-9">
                                                    <button type="submit" name="change_password_submit" id="change_password_submit" value="change_password_submit" class="btn btn-danger" disabled>SUBMIT</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-9">
                    <div class="card">
                        <div class="body">
                            <div>
                                <ul class="nav nav-tabs" role="tablist">
                                    <li role="presentation" class="active"><a href="#payment_method" aria-controls="payment_method" role="tab" data-toggle="tab">Payment Methods</a></li>
                                    <li role="presentation"><a href="#bank_account" aria-controls="bank_account" role="tab" data-toggle="tab">Bank Account</a></li>
                                    <li role="presentation"><a href="#credit_card" aria-controls="credit_card" role="tab" data-toggle="tab">Credit Card</a></li>
                                </ul>

                                <div class="tab-content">
                                    <div role="tabpanel" class="tab-pane fade in active" id="payment_method">

                                    </div>


                                    <div role="tabpanel" class="tab-pane fade in" id="bank_account">

                                    </div>

                                    <div role="tabpanel" class="tab-pane fade in" id="credit_card">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <script>
    $(document).ready(function(){
      $("#profileimg").change(function () {
          var fileExtension = ['jpeg', 'jpg', 'png', 'gif', 'bmp'];
          if ($.inArray($(this).val().split('.').pop().toLowerCase(), fileExtension) == -1) {
              alert("Only formats are allowed : "+ fileExtension.join(', '));
              document.getElementById("profileimg").classList.add('is-invalid');
              document.getElementById("profileimg").classList.remove('is-valid');
              $('#uploadprofile').attr("disabled",true);
          }
          else{
              document.getElementById("profileimg").classList.add('is-valid');
              document.getElementById("profileimg").classList.remove('is-invalid');
              $('#uploadprofile').attr("disabled",false);

          }
      });

      $("#OldPassword").change(function () {
        var old = $(this).val();
        $.ajax({
          url:"check.php",
          method:"POST",
          data:{checkPass:old},
          dataType:"text",
          success:function(response){
            if(response==1){
              $('#invalidOldPassword').css("color","green");
              $('#invalidOldPassword').css("display", "block");
              $('#invalidOldPassword').html("Correct Password");
              $('#OldPassword').removeClass( "is-invalid" ).addClass( "is-valid" );
            }
            else{
              $('#invalidOldPassword').css("color","red");
              $('#invalidOldPassword').css("display", "block");
              $('#invalidOldPassword').html("Wrong Password");
              $('#OldPassword').removeClass( "is-valid" ).addClass( "is-invalid" );
            }
            finalCheckChangePassword();
          },
        });
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
        finalCheckChangePassword();
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

    });

    function validatePass(pass){
      if (pass.search(/[a-z]/) < 0) {
        document.getElementById("NewPassword").classList.add('is-invalid');
        document.getElementById("NewPassword").classList.remove('is-valid');
        document.getElementById("invalidNewPassword").style.display = "block";
        document.getElementById("invalidNewPassword").style.color = "red";
        document.getElementById("invalidNewPassword").innerHTML = "Your password must contain a lower case letter";
      }
      else if(pass.search(/[A-Z]/) < 0) {
        document.getElementById("NewPassword").classList.add('is-invalid');
        document.getElementById("NewPassword").classList.remove('is-valid');
        document.getElementById("invalidNewPassword").style.display = "block";
        document.getElementById("invalidNewPassword").style.color = "red";
        document.getElementById("invalidNewPassword").innerHTML = "Your password must contain an upper case letter";
      }
      else  if (pass.search(/[0-9]/) < 0) {
        document.getElementById("NewPassword").classList.add('is-invalid');
        document.getElementById("NewPassword").classList.remove('is-valid');
        document.getElementById("invalidNewPassword").style.display = "block";
        document.getElementById("invalidNewPassword").style.color = "red";
        document.getElementById("invalidNewPassword").innerHTML = "Your password must contain a number";
      }
      else  if (pass.length < 8) {
        document.getElementById("NewPassword").classList.add('is-invalid');
        document.getElementById("NewPassword").classList.remove('is-valid');
        document.getElementById("invalidNewPassword").style.display = "block";
        document.getElementById("invalidNewPassword").style.color = "red";
        document.getElementById("invalidNewPassword").innerHTML = "Your password is too short";
      }
      else{
        document.getElementById("NewPassword").classList.remove('is-invalid');
        document.getElementById("NewPassword").classList.add('is-valid');
        document.getElementById("invalidNewPassword").style.display = "block";
        document.getElementById("invalidNewPassword").innerHTML = "Valid password";
        document.getElementById("invalidNewPassword").style.color = "green";
      }
      finalCheckChangePassword();
    }

    function checkPass(){

      var pass1 = document.getElementById('NewPassword');
      var pass2 = document.getElementById('NewPasswordConfirm');
      if(pass1.value != pass2.value){
        document.getElementById("NewPasswordConfirm").classList.add('is-invalid');
        document.getElementById("NewPasswordConfirm").classList.remove('is-valid');
        document.getElementById("invalidNewPasswordConfirm").style.display = "block";
        document.getElementById("invalidNewPasswordConfirm").style.color = "red";
        document.getElementById("invalidNewPasswordConfirm").innerHTML = "Password not match";
      }
      else
      {
        document.getElementById("NewPasswordConfirm").classList.remove('is-invalid');
        document.getElementById("NewPasswordConfirm").classList.add('is-valid');
        document.getElementById("invalidNewPasswordConfirm").style.display = "block";
        document.getElementById("invalidNewPasswordConfirm").innerHTML = "Password match";
        document.getElementById("invalidNewPasswordConfirm").style.color = "green";
      }
      finalCheckChangePassword();
    }

    function finalCheckChangePassword(){
      var old = document.getElementById("OldPassword").classList.contains('is-valid');
      var newP = document.getElementById("NewPassword").classList.contains('is-valid');
      var newC = document.getElementById("NewPasswordConfirm").classList.contains('is-valid');
      var code = document.getElementById("inputGoogleAuthCode").classList.contains('is-valid');
      if(old && newP && newC && code){
        document.getElementById("change_password_submit").disabled = false;
      }
      else{
        document.getElementById("change_password_submit").disabled = true;
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
