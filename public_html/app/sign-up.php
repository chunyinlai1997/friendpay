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
		else if($_GET["re"]=="PASSWORD_PROBLEM"){
      return "There is some problem with your password, please do not use any invalid chracters.";
    }
  }

?>

<head>
    <title> Sign Up | FriendPay </title>
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

<body class="signup-page">
    <video autoplay loop id="video-background" muted plays-inline>
      <source src="images/promo.mp4" type="video/mp4">
    </video>
    <div class="signup-box">
        <div class="logo">
          <a href="../">Friend<b>Pay</b></a>
          <small>Send money to friends and family.</small>
        </div>
        <div class="card">
          <div class="body">
            <div class="box_form">
							<?php
								$error1 = "<div class='alert alert-danger'><a class='close'data-dismiss='alert' href='#'>×</a>";
								$error2 = "</div>";
								if(isset($_GET["re"])){
									$msg = re_fail();
									echo $error1.$msg.$error2;
								}
							?>
							<form action="submit-signup" method="POST">
                <div class="form-group">
                  <label>Frist Name</label>
                  <input autocomplete="off" type="text" name="firstname" id="inputfirstname" class="form-control" onkeyup="safeName(this)" placeholder="Your first name" autofocus inlength="1" maxlength="255" required/>
                  <div id="invalidfirstname" class="invalid-feedback" style="display:none;">
                 </div>
                </div>
                <div class="form-group">
                  <label>Last name</label>
                  <input autocomplete="off" type="text" name="lastname" id="inputlastname" class="form-control" onkeyup="safeName(this)" placeholder="Your last name" minlength="1" maxlength="255" required/>
                  <div id="invalidlastname" class="invalid-feedback" style="display:none;">
                  </div>

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
								<div class="form-group">
                  <label>Phone</label>
                  <input autocomplete="off" type="phone" min-length="8" max-length="8" name="phone" id="inputPhone" class="form-control" placeholder="Your phone number" required />
                  <div id="invalidphonenumber" class="invalid-feedback" style="">
									</div>
                </div>
                <div class="form-group">
                  <label>Password</label>
                  <input autocomplete="off" type="password" name="password1" class="form-control" id="password1" onkeyup="validatePass(this.value)" placeholder="Your password" minlength="8" maxlength="24" required />
                  <div id="invalidPassword1" class="invalid-feedback" style="display:none;"></div>
                </div>
                <div class="form-group">
                  <label>Confirm password</label>
                  <input autocomplete="off" type="password" name="password2" class="form-control" id="password2" onkeyup="checkPass(this)" placeholder="Confirm password" minlength="8" maxlength="24" required/>
                  <div id="invalidPassword2" class="invalid-feedback" style="display:none;"></div>
                </div>
                <div id="pass-info" class="clearfix"></div>
                <!--<div class="checkbox-holder text-left">
                  <div class="checkbox_2">
                    <input type="checkbox" value="accept_2" id="check_2" name="check_2" checked="" />
                    <label for="check_2"><span></strong></span></label>
                  </div>
                </div>-->
                <div class="row">
                    <div class="col-md-12">
                        <div style="size:50%;" class="g-recaptcha" data-sitekey="6Lf3gHwUAAAAALtbWbs_7kEYPbtAFOCPdKY8BRxN"></div>
                    </div>

                </div>
								<div class="row">
									<div class="col-md-6">
											<button class="btn btn-block bg-pink waves-effect" id="submit" name="submit" type="submit" value="submit" >SIGN UP</button>
									</div>
								</div>
							</form>
								<p class="text-center link_bright"><a href="sign-in"><strong>I have an account already</strong></a></p>
								<p class="text-center"><small>I Agree to the <strong>Terms &amp; Conditions</strong> by clicking the Sign Up button.</small></p>
              </div>
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
							finalCheck();
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
							finalCheck();
						},
					});
				});

				function checkEmail(email){
					var regMail = /^([_a-zA-Z0-9-]+)(\.[_a-zA-Z0-9-]+)*@([a-zA-Z0-9-]+\.)+([a-zA-Z]{2,3})$/;
					if(regMail.test(email)){
							return true;
						}
					else{
						return false;
					}
				}

				function checkPhone(phone){
					var regexPhone = /^[0-9]{1,8}$/;
					if(regexPhone.test(phone)){
							return true;
						}
					else{
						return false;
					}
				}
				
			});

			function safeName(name){
					name.value = name.value.replace(/[^/ ,a-zA-Z-'\n\r.]+/g, '');
			}



			function validatePass(pass){
				if (pass.search(/[a-z]/) < 0) {
				  document.getElementById("password1").classList.add('is-invalid');
					document.getElementById("password1").classList.remove('is-valid');
					document.getElementById("invalidPassword1").style.display = "block";
					document.getElementById("invalidPassword1").style.color = "red";
					document.getElementById("invalidPassword1").innerHTML = "Your password must contain a lower case letter";
				}
				else if(pass.search(/[A-Z]/) < 0) {
				  document.getElementById("password1").classList.add('is-invalid');
					document.getElementById("password1").classList.remove('is-valid');
					document.getElementById("invalidPassword1").style.display = "block";
					document.getElementById("invalidPassword1").style.color = "red";
					document.getElementById("invalidPassword1").innerHTML = "Your password must contain an upper case letter";
				}
				else  if (pass.search(/[0-9]/) < 0) {
					document.getElementById("password1").classList.add('is-invalid');
					document.getElementById("password1").classList.remove('is-valid');
					document.getElementById("invalidPassword1").style.display = "block";
					document.getElementById("invalidPassword1").style.color = "red";
					document.getElementById("invalidPassword1").innerHTML = "Your password must contain a number";
				}
				else  if (pass.length < 8) {
					document.getElementById("password1").classList.add('is-invalid');
					document.getElementById("password1").classList.remove('is-valid');
					document.getElementById("invalidPassword1").style.display = "block";
					document.getElementById("invalidPassword1").style.color = "red";
					document.getElementById("invalidPassword1").innerHTML = "Your password is too short";
				}
				else{
					document.getElementById("password1").classList.remove('is-invalid');
					document.getElementById("password1").classList.add('is-valid');
					document.getElementById("invalidPassword1").style.display = "block";
					document.getElementById("invalidPassword1").innerHTML = "Valid password";
					document.getElementById("invalidPassword1").style.color = "green";
				}
				finalCheck();
			}

			function checkPass()
			{
				var pass1 = document.getElementById('password1');
			  var pass2 = document.getElementById('password2');
				if(pass1.value != pass2.value){
					document.getElementById("password2").classList.add('is-invalid');
					document.getElementById("password2").classList.remove('is-valid');
					document.getElementById("invalidPassword2").style.display = "block";
					document.getElementById("invalidPassword2").style.color = "red";
					document.getElementById("invalidPassword2").innerHTML = "Password not match";
				}
				else
				{
					document.getElementById("password2").classList.remove('is-invalid');
					document.getElementById("password2").classList.add('is-valid');
					document.getElementById("invalidPassword2").style.display = "block";
					document.getElementById("invalidPassword2").innerHTML = "Password match";
					document.getElementById("invalidPassword2").style.color = "green";
				}
				finalCheck();
			}

			function finalCheck(){
				var first = document.getElementById("inputfirstname").classList.contains('is-valid');
				var last = document.getElementById("inputlastname").classList.contains('is-valid');
				var email =  document.getElementById("inputEmail").classList.contains('is-valid');
				var pass1 =  document.getElementById("password1").classList.contains('is-valid');
				var pass2 =  document.getElementById("password2").classList.contains('is-valid');
				var phone =  document.getElementById("inputPhone").classList.contains('is-valid');
				if( first && last && email && pass1 && pass2 && phone){
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
