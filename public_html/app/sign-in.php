<?php
  include 'config.php';
  include_once 'token.php';

  if(isloggedin()){
    header('Location:dashboard');
  }

	function re_fail(){
		if($_GET["re"]=="FAIL_CAPTCHA"){
			return "Fail Captcha Verification! Try Again.";
		}
		else if($_GET["re"]=="NO_SUBMIT_CAPTCHA"){
			return "No Captcha Submission! Try Again.";
		}
		else if($_GET["re"]=="NO_SUBMIT"){
			return "Fail Login Submission or Timeout! Try Again.";
		}
    else if($_GET["re"]=="RESET"){
			return "You haven't reset password yet, please go to Forget Password or check your email again.";
    }
  }

	function ac_fail(){
		if($_GET["ac"]=="WRONG"){
			return "Wrong email or password! Try Again!";
		}
		else if($_GET["ac"]=="NO_SUBMIT"){
			return "Empty login submission! Try Again.";
		}
    else if($_GET["ac"]=="LIMIT"){
      return "Your account is deactiviated due to too many failed login attempts. Please go to Forget Password or check your email again.  ";
    }
	}

	function need_login(){
		if($_GET["need_login"]=="True"){
			return "Login to continue.";
		}
	}

  function changed(){
		if($_GET["changed"]=="True"){
			return "Your password has sucessfully changed.";
		}
	}

 ?>
<html lang="en">
<head>
  <title> Sign In | FriendPay </title>
  <?php include 'head-info.php';?>
  <style>
  #video-background {
  /*  making the video fullscreen  */
    position: fixed;
    right: 0;
    bottom: 0;
    min-width: 100%;
    min-height: 100%;
    width: auto;
    height: auto;
    z-index: -100;
  }
  </style>
  <script src='https://www.google.com/recaptcha/api.js'></script>
</head>

<body class="login-page">
    <video autoplay loop id="video-background" muted plays-inline>
      <source src="images/promo.mp4" type="video/mp4">
    </video>
    <div class="login-box">
        <div class="logo">
            <a href="../" style="size:200%;">Friend<b>Pay</b></a>
            <small>Send money to friends and family.</small>
        </div>
        <div class="card">
            <div class="body">
                  <?php
    							$error1 = "<div class='alert alert-danger'><a class='close'data-dismiss='alert' href='#'>×</a>";
                  $success1 = "<div class='alert alert-success'><a class='close'data-dismiss='alert' href='#'>×</a>";
    							$error2 = "</div>";
    							if(isset($_GET["re"])){
    								$msg = re_fail();
    								echo $error1.$msg.$error2;
    							}
    							else if(isset($_GET["ac"])){
    								$msg = ac_fail();
    								echo $error1.$msg.$error2;
    							}
    							else if(isset($_GET["need_login"])){
    								$msg = need_login();
    								echo $error1.$msg.$error2;
    							}
                  else if(isset($_GET["changed"])){
    								$msg = changed();
    								echo $success1.$msg.$error2;
    							}
    						?>
                <form id="sign_in" action="submit-signin" method="POST">
                    <div class="msg">Sign in to start your transaction</div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">person</i>
                        </span>
                        <div class="form-line">
                            <input type="email" id="inputEmail" class="form-control" name="email" placeholder="Email" required autofocus>
                        </div>
                    </div>
                    <div class="input-group">
                        <span class="input-group-addon">
                            <i class="material-icons">lock</i>
                        </span>
                        <div class="form-line">
                            <input type="password" id="password" class="form-control" name="password" placeholder="Password" minlength='8' maxlength='24' required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="g-recaptcha" data-sitekey="6Lf3gHwUAAAAALtbWbs_7kEYPbtAFOCPdKY8BRxN"></div>
                        </div>

                    </div>
    								<div class="row">
    									<div class="col-md-6">
    											<button class="btn btn-block bg-blue waves-effect" id="submit" name="submit" type="submit" value="submit" >SIGN IN</button>
    									</div>
    								</div>
                    <div class="row m-t-15 m-b--20">
                        <div class="col-xs-6">
                            <a href="sign-up">Register Now!</a>
                        </div>
                        <div class="col-xs-6 align-right">
                            <a href="forgot-password">Forgot Password?</a>
                        </div>
                    </div>
                    <p class="text-center"><small>We assume you agree the use <strong>cookies</strong> to ensure that we give you the best experience on our website by clicking the Sign In button.</small></p>
                </form>
            </div>
        </div>
    </div>

    <script>
	$(document).ready(function () {
		$("#submit" ).click(function(e) {
			if(grecaptcha.getResponse() == "") {
				e.preventDefault();
				alert("Captcha is Missing!");
			}
		});
	});

	function checkEmail(email){
		var regMail = /^([_a-zA-Z0-9-]+)(\.[_a-zA-Z0-9-]+)*@([a-zA-Z0-9-]+\.)+([a-zA-Z]{2,3})$/;
		if(regMail.test(email)){
			$('#invalidemail2').css("color","green");
			$('#invalidemail2').css("display", "block");
			$('#invalidemail2').html("");
			$('#inputEmail').removeClass( "is-invalid" ).addClass( "is-valid" );
		}
		else{
			$('#invalidemail2').css("color","red");
			$('#invalidemail2').css("display", "block");
			$('#invalidemail2').html("Wrong email format");
			$('#inputEmail').removeClass( "is-valid" ).addClass( "is-invalid" );
		}
		finalCheck();
	}

	function checkPass(pass){
		if(pass.length > 5 ){
			$('#invalidpass').css("color","green");
			$('#invalidpass').css("display", "block");
			$('#invalidpass').html("");
			$('#password').removeClass( "is-invalid" ).addClass( "is-valid" );
		}
		else{
			$('#invalidpass').css("color","red");
			$('#invalidpass').css("display", "block");
			$('#invalidpass').html("Wrong password format");
			$('#password').removeClass( "is-valid" ).addClass( "is-invalid" );
		}
		finalCheck();
	}

	function finalCheck(){
		var email =  document.getElementById("inputEmail").classList.contains('is-valid');
		var pass1 =  document.getElementById("password").classList.contains('is-valid');
		if(pass1 && email){
			document.getElementById("submit").disabled = false;
		}
		else{
			document.getElementById("submit").disabled = true;
		}
	}

	</script>
  <?php include 'footer-info.php';?>
</body>
</html>
