<?php
  include_once 'config.php';
  include_once 'token.php';
  include_once 'encrypt_decrypt.php';

  if(!isloggedin()){
    header("Location:sign-in?need_login=True");
  }

  $id = getUserId();
  $sql = mysql_query("SELECT Client.firstname, Client.lastname, Users.email, Users.create_date, Client.phone, Users.verified, Users.profile_img, Users.status, Users.two_factor, Client.billing_address FROM Client, Users WHERE Client.user_id = Users.id AND Users.id = '$id'")or die(mysql_error());
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
  $address = $row[9];
  $sql2 = mysql_query("SELECT date_time, status, ip FROM Login_Logging L WHERE user_id = '$id' ORDER BY date_time  DESC LIMIT 1,1 ");
  $row2 = mysql_fetch_array($sql2,MYSQL_NUM);
  $last_login_time = $row2[0];
  $last_login_status = "";
  $alt = "";
  $alt2 = "";
  if($row2[1]!="fail"){
    $last_login_status = "success";
  }
  else{
    $last_login_status = "fail";
  }

  if(isset($_GET["update"])&& $_GET["update"]=="profileimg"){
    header("Location:profile");
  }
  else if(isset($_GET["update"]) && $_GET["update"]=="profile"){
    $alt2 = "<div class='alert alert-success' role='alert'>Your profile has updated successfully.<button type='button' class='close' data-dismiss='alert' aria-label='Close'></div>";
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


  if(isset($_POST["change_profile_submit"])){
    if(!empty($_POST["passwordChange"])){
      $passwordChange = $_POST["passwordChange"];
      $inputfirstname = $_POST["firstname"];
      $inputlastname = $_POST["lastname"];
      $inputEmail = $_POST["email"];
      $inputPhone = $_POST["phone"];
      $address = $_POST["address"];
      $passwordChange = $_POST["passwordChange"];
      $id = isloggedin();
      $sql_profile = mysql_query("SELECT Users.password, Users.email, Client.firstname, Client.lastname, Client.phone, Client.billing_address FROM Users, Client WHERE Client.user_id = Users.id AND Users.id ='$id'")or die(mysql_error());
      $result_profile = mysql_fetch_array($sql_profile,MYSQL_NUM);
      $real_password = $result_profile[0];
      $real_email = $result_profile[1];
      $real_firstname = $result_profile[2];
      $real_lastname = $result_profile[3];
      $real_phone = $result_profile[4];
      $real_address = $result_profile[5];
      if($passwordChange == clean($passwordChange) && password_verify($passwordChange, $real_password)){
        if(!empty($inputfirstname) && !empty($inputlastname) && !empty($inputEmail) && !empty($inputPhone) && !empty($address)){
          if($inputfirstname == clean($inputfirstname) && $inputlastname == clean($inputlastname) && $inputEmail == clean($inputEmail) && $inputPhone == clean($inputPhone) && $address == clean($address)){
          if($inputfirstname!=$real_firstname){
            mysql_query("UPDATE Client SET firstname='$inputfirstname' WHERE user_id = '$id'") or die(mysql_error());
          }
          if($inputlastname!=$real_lastname){
            mysql_query("UPDATE Client SET lastname='$inputlastname' WHERE user_id = '$id'") or die(mysql_error());
          }
          if($real_email!=$inputEmail){
            $v_hash = md5(rand(0,1000));
            mysql_query("UPDATE Users SET email='$inputEmail', verified = 0, status = 'inactive', verify_hash = '$v_hash' WHERE id = '$id'") or die(mysql_error());
            $tmp = mysql_query("SELECT Client.firstname, Client.lastname, FROM Client WHERE Client.user_id='$id'")or die(mysql_error());
            $getf = mysql_fetch_array($tmp,MYSQL_NUM);
            $getfname = $getf[0];
            $getlname = $getf[1];
            send_email2($getfname,$getlname,$email,$v_hash);
            header("Location: account_issue");
          }
          if($inputPhone){
            mysql_query("UPDATE Client SET phone='$inputPhone' WHERE user_id = '$id'") or die(mysql_error());
          }
          if($address){
            mysql_query("UPDATE Client SET billing_address='$address' WHERE user_id = '$id'") or die(mysql_error());
          }
          header("Location:profile?update=profile");
        }
          else{
            $alt2 = "<div class='alert alert-danger' role='alert'>Wrong Input! Please try again..<button type='button' class='close' data-dismiss='alert' aria-label='Close'></div>";
          }
        }
        else{
          $alt2 = "<div class='alert alert-danger' role='alert'>Empty Input! Please try again..<button type='button' class='close' data-dismiss='alert' aria-label='Close'></div>";
        }
      }
      else{
        $alt2 = "<div class='alert alert-danger' role='alert'>Wrong Password! Please try again..<button type='button' class='close' data-dismiss='alert' aria-label='Close'></div>";
      }
    }
    else{
      $alt2 = "<div class='alert alert-danger' role='alert'>Empty Passsword! Please try again..<button type='button' class='close' data-dismiss='alert' aria-label='Close'></div>";
    }
  }

  if(isset($_POST["change_password_submit"])){
    require_once 'googleLib/GoogleAuthenticator.php';
    $sql_auth = mysql_query("SELECT Users.google_auth_code, Users.email, Users.two_factor, Users.password FROM Users WHERE Users.id='$id'") or die(mysql_error());
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

  function send_email2($fname,$lname,$email,$v_hash){
  	$to      = $email; // Send email to our user
  	$subject = ' Account Verification | Friend Pay'; // Give the email a subject
    $v_hash = encrypt($v_hash);
  	$message = "
  	Dear $fname $lname,

  	Thanks for signing up!
  	Your account email has been changed, you may now activate your account by pressing the url below.

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
	<title> My Wallet | Friend Pay</title>
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
                    <li class="active">
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
                <!--Wallet card-->
                <div class="col-xs-12 col-sm-3">
                  <div class="card card-about-me">
                      <div class="header">
                          <h2>My Wallet</h2>
                      </div>
                      <div class="body">
                          <ul>
                              <li>
                                  <div class="title">
                                      <i class="material-icons"> attach_money </i>
                                      Balance
                                  </div>
                                  <div class="content">
                                      0
                                  </div>
                              </li>
                              <li>
                                  <div class="title">
                                      <i class="material-icons">account_balance</i>
                                      Bank Account
                                  </div>
                                  <div class="content">
                                      ***882
                                  </div>
                              </li>
                              <li>
                                  <div class="title">
                                      <i class="material-icons">credit_card</i>
                                      Credit Card
                                  </div>
                                  <div class="content">
                                    ***7477
                                  </div>
                              </li>
                          </ul>
                      </div>
                  </div>
                </div>

                <div class="col-xs-12 col-sm-9">
                  <? echo $alt;?>
                  <? echo $alt2;?>
                </div>

                <div class="col-xs-12 col-sm-9">
                    <div class="card">
                        <div class="body">
                          <p class="font-bold col-blue-blue">Update Bank Account</p>

                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-9">
                    <div class="card">
                        <div class="body">
                          <p class="font-bold col-blue-blue">Update Credit Card</p>

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

      $("#passwordChange").change(function () {
        var p = $(this).val();
        $.ajax({
          url:"check.php",
          method:"POST",
          data:{checkPass:p},
          dataType:"text",
          success:function(response){
            if(response==1){
              $('#invalidPassword').css("color","green");
              $('#invalidPassword').css("display", "block");
              $('#invalidPassword').html("Correct Password");
              $('#passwordChange').removeClass( "is-invalid" ).addClass( "is-valid" );
            }
            else{
              $('#invalidPassword').css("color","red");
              $('#invalidPassword').css("display", "block");
              $('#invalidPassword').html("Wrong Password");
              $('#passwordChange').removeClass( "is-valid" ).addClass( "is-invalid" );
            }
            finalCheckChangePassword();
          },
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

      $('#inputfirstname').keyup(function(){
        var firstname = $(this).val();
        if(firstname.length>0){
          $('#invalidfirstname').css("color","green");
          $('#invalidfirstname').css("display", "block");
          $('#invalidfirstname').html("");
          $('#inputfirstname').removeClass( "is-invalid" ).addClass( "is-valid" );
        }
        else{
          $('#invalidfirstname').css("color","red");
          $('#invalidfirstname').css("display", "block");
          $('#invalidfirstname').html("Invalid first name!");
          $('#inputfirstname').removeClass( "is-valid" ).addClass( "is-invalid" );
        }
        finalCheckChangeProfile();
      });

      $('#inputlastname').keyup(function(){
        var lastname = $(this).val();
        if(lastname.length>0){
          $('#invalidlastname').css("color","green");
          $('#invalidlastname').css("display", "block");
          $('#invalidlastname').html("");
          $('#inputlastname').removeClass( "is-invalid" ).addClass( "is-valid" );
        }
        else{
          $('#invalidlastname').css("color","red");
          $('#invalidlastname').css("display", "block");
          $('#invalidlastname').html("Invalid last name!");
          $('#inputlastname').removeClass( "is-valid" ).addClass( "is-invalid" );
        }
        finalCheckChangeProfile();
      });


      $('#inputEmail').change(function(){
        var email = $(this).val();
        $.ajax({
          url:"check.php",
          method:"POST",
          data:{checkemail:email},
          dataType:"text",
          success:function(response){
            if(response==0&&checkEmail(email)){
              $('#invalidemail2').css("color","green");
              $('#invalidemail2').css("display", "block");
              $('#invalidemail2').html("Available email address");
              $('#inputEmail').removeClass( "is-invalid" ).addClass( "is-valid" );
            }
            else if(response==0&&!checkEmail(email)){
              $('#invalidemail2').css("color","red");
              $('#invalidemail2').css("display", "block");
              $('#invalidemail2').html("This email address is invalid");
              $('#inputEmail').removeClass( "is-valid" ).addClass( "is-invalid" );
            }
            else if(response==1&&!checkEmail(email)){
              $('#invalidemail2').css("color","red");
              $('#invalidemail2').css("display", "block");
              $('#invalidemail2').html("This email address is invalid");
              $('#inputEmail').removeClass( "is-valid" ).addClass( "is-invalid" );
            }
            else if(response==1&&checkEmail(email)){
              $('#invalidemail2').css("color","red");
              $('#invalidemail2').css("display", "block");
              $('#invalidemail2').html("This email address is already used");
              $('#inputEmail').removeClass( "is-valid" ).addClass( "is-invalid" );
            }
            finalCheckChangeProfile();
          },
        });
      });

      $('#inputPhone').change(function(){
        var phone = $(this).val();
        $.ajax({
          url:"check.php",
          method:"POST",
          data:{checkphone:phone},
          dataType:"text",
          success:function(response){
            if(response==0 && phone.length == 8 && checkPhone(phone)){
              $('#invalidphonenumber').css("color","green");
              $('#invalidphonenumber').css("display", "block");
              $('#invalidphonenumber').html("Available phone number");
              $('#inputPhone').removeClass( "is-invalid" ).addClass( "is-valid" );
            }
            else{
              $('#invalidphonenumber').css("color","red");
              $('#invalidphonenumber').css("display", "block");
              $('#invalidphonenumber').html("This phone number is invalid");
              $('#inputPhone').removeClass( "is-valid" ).addClass( "is-invalid" );
            }
            finalCheckChangeProfile();
          },
        });
      });

      $('#address').keyup(function(){
        var add = $(this).val();
        if(add.length>0){
          $('#invalidaddress').css("color","green");
          $('#invalidaddress').css("display", "block");
          $('#invalidaddress').html("");
          $('#address').removeClass( "is-invalid" ).addClass( "is-valid" );
        }
        else{
          $('#invalidaddress').css("color","red");
          $('#invalidaddress').css("display", "block");
          $('#invalidaddress').html("Invalid billing address!");
          $('#address').removeClass( "is-valid" ).addClass( "is-invalid" );
        }
        finalCheckChangeProfile();
      });

      $("#passwordChange").change(function () {
        var psc = $(this).val();
        $.ajax({
          url:"check.php",
          method:"POST",
          data:{checkPass:psc},
          dataType:"text",
          success:function(response){
            if(response==1){
              $('#invalidPasswordChange').css("color","green");
              $('#invalidPasswordChange').css("display", "block");
              $('#invalidPasswordChange').html("Correct Password");
              $('#passwordChange').removeClass( "is-invalid" ).addClass( "is-valid" );
            }
            else{
              $('#invalidPasswordChange').css("color","red");
              $('#invalidPasswordChange').css("display", "block");
              $('#invalidPasswordChange').html("Wrong Password");
              $('#passwordChange').removeClass( "is-valid" ).addClass( "is-invalid" );
            }
            finalCheckChangeProfile();
          },
        });
      });
    });

    function safeName(name){
        name.value = name.value.replace(/[^/ ,a-zA-Z-'\n\r.]+/g, '');
    }

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

    function safeName(name){
        name.value = name.value.replace(/[^/ ,a-zA-Z-'\n\r.]+/g, '');
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

    function finalCheckChangeProfile(){
      var inputfirstname = document.getElementById("inputfirstname").classList.contains('is-valid');
      var inputlastname = document.getElementById("inputlastname").classList.contains('is-valid');
      var inputEmail = document.getElementById("inputEmail").classList.contains('is-valid');
      var inputPhone = document.getElementById("inputPhone").classList.contains('is-valid');
      var address = document.getElementById("address").classList.contains('is-valid');
      var pass = document.getElementById("passwordChange").classList.contains('is-valid');
      console.log("see",inputfirstname && inputlastname && inputEmail && inputPhone && address);
      console.log(inputfirstname);
      if(inputfirstname && inputlastname && inputEmail && inputPhone && address && pass){
        document.getElementById("change_profile_submit").disabled = false;
      }
      else{
        document.getElementById("change_profile_submit").disabled = true;
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
