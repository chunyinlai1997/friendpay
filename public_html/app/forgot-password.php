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
      return "Fail Submission or Timeout! Try Again.";
    }
		else if($_GET["re"]=="WRONG"){
      return "There is some problem with your account, please try again or contact us 12345678.";
    }
  }

?>
<html lang="en">
<head>
  <title> Forget Password | FriendPay </title>
  <?php include 'head-info.php';?>
  <script src='https://www.google.com/recaptcha/api.js'></script>
</head>

<body class="fp-page">
    <div class="fp-box">
        <div class="logo">
          <a href="../" style="size:200%;">Friend<b>Pay</b></a>
          <small>Send money to friends and family.</small>
        </div>
        <div class="card">
            <div class="body">
              <?php
                $error1 = "<div class='alert alert-danger'><a class='close'data-dismiss='alert' href='#'>×</a>";
                $error2 = "</div>";
                if(isset($_GET["re"])){
                  $msg = re_fail();
                  echo $error1.$msg.$error2;
                }
              ?>
                <form id="forgot_password" action="submit-forget" method="POST">
                    <div class="msg">
                        Enter your email address that you used to register. We'll send you an email a
                        link to reset your password.
                    </div>
                    <div class="form-group">
                      <label>Email</label>
                      <input autocomplete="off" type="email" name="email" id="inputEmail" class="form-control" placeholder="Your email address" required />
                      <div id="invalidemail" class="invalid-feedback" style="display:none;">
                        Please provide a valid email address
                      </div>
                      <div id="invalidemail2" class="invalid-feedback" style="display:none;">
                      </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div style="size:50%;" class="g-recaptcha" data-sitekey="6Lf3gHwUAAAAALtbWbs_7kEYPbtAFOCPdKY8BRxN"></div>
                        </div>

                    </div>
    								<div class="row">
    									<div class="col-md-6">
    											<button class="btn btn-block bg-pink waves-effect" id="submit" name="submit" type="submit" value="submit" >RESET</button>
    									</div>
    								</div>

                    <div class="row m-t-20 m-b--5 align-center">
                        <a href="sign-in">Sign In!</a>
                    </div>
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

        $('#inputEmail').change(function(){
					var email = $(this).val();
					$.ajax({
						url:"check.php",
						method:"POST",
						data:{checkemail:email},
						dataType:"text",
						success:function(response){
							if(response==1){
								$('#invalidemail2').css("color","green");
								$('#invalidemail2').css("display", "block");
								$('#invalidemail2').html("Valid email address");
								$('#inputEmail').removeClass( "is-invalid" ).addClass( "is-valid" );
							}
							else if(response==0){
								$('#invalidemail2').css("color","red");
								$('#invalidemail2').css("display", "block");
								$('#invalidemail2').html("Invalid email address");
								$('#inputEmail').removeClass( "is-valid" ).addClass( "is-invalid" );
							}
              finalCheck()
						},
					});
				});
    });

    function finalCheck(){
      var email =  document.getElementById("inputEmail").classList.contains('is-valid');
      if(email){
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
