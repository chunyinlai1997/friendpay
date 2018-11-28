<?php
  include_once 'config.php';
  include_once 'token.php';

  if(isloggedin()){
    header('Location:dashboard');
  }
  //include_once 'session.php';

  function re_fail(){
    if($_GET["re"]=="NO_SUBMIT"){
      return "Fail Submission! Try Again.";
    }
    else if($_GET["re"]=="TIMEOUT"){
      return "There is some problem with your password, please do not use any invalid chracters.";
    }
    else if($_GET["re"]=="WRONG"){
      return "Fail Submission! Try Again.";
    }
    else if($_GET["re"]=="NOT_APPLICABLE"){
      return "Not Applicable!";
    }
  }

  $msg="";
	$flag="Reset Fail";
	if(isset($_GET['v']) && !empty($_GET['v']) AND isset($_GET['e']) && !empty($_GET['e']) AND isset($_GET['h']) && !empty($_GET['h'])){
		if($_GET['v']=="reset"){
			$email = mysql_escape_string($_GET['e']);
			$hash = mysql_escape_string($_GET['h']);
			$sql = mysql_query("SELECT id, email, verified, verify_hash, password FROM Users WHERE email='$email' AND verify_hash='$hash'") or die(mysql_error());
			$row = mysql_fetch_array($sql,MYSQL_NUM);

      $verified = $row[2];
      if($verified == 2 || $verified == 4){
        if($row[3]==$hash){
          $target = $row[3];
          $uid = $row[0];

					$msg="
            <form action='submit-reset' method='POST'>
            	<div class='form-group'>
            	  <label>Password</label>
                <input type='hidden' name='uid' value='$row[0]' id='uid' />
                <input type='hidden' name='enc' value='$hash' id='enc' />
                <input autocomplete='off' type='password' name='password1' class='form-control' id='password1' onkeyup='validatePass(this.value)' placeholder='Your password' minlength='8' maxlength='24' required autofocus />
            	  <div id='invalidPassword1' class='invalid-feedback' style='display:none;'></div>
            	</div>
            	<div class='form-group'>
            	  <label>Confirm password</label>
            	  <input autocomplete='off' type='password' name='password2' class='form-control' id='password2' onkeyup='checkPass(this)' placeholder='Confirm password' minlength='8' maxlength='24' required/>
            	  <div id='invalidPassword2' class='invalid-feedback' style='display:none;'></div>
            	</div>
              <div class='row'>
                <div class='col-md-6'>
                    <button class='btn btn-block bg-pink waves-effect' id='submit' name='submit' type='submit' value='submit' >RESET</button>
                </div>
              </div>
            </form>
          ";
				}
				else{
					$msg="This link has been used or invalid.";
				}
      }
			else{
        $msg = "You are not applicable to reset password, please go to <a href='forget-password'>Forget Password</a>.";
			}
		}
		else{
			$msg = "Not Applicable";
		}
	}
	else{
		$msg = "Not Applicable....";
	}


?>
<html lang="en">
<head>
	<title> Reset Password | Friend Pay</title>
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
                            <?php
              								$error1 = "<div class='alert alert-danger'><a class='close'data-dismiss='alert' href='#'>×</a>";
              								$error2 = "</div>";
              								if(isset($_GET["re"])){
              									$mse = re_fail();
              									echo $error1.$mse.$error2;
              								}
              							?>
                           <h5><?php echo $msg; ?></h5>
                           <br>
                           <hr>
                           <!--<a href="../"><button class="btn btn-info waves-effect">Back To Home</button></a>-->
                        </div>
                      </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script>
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
      var pass1 =  document.getElementById("password1").classList.contains('is-valid');
      var pass2 =  document.getElementById("password2").classList.contains('is-valid');
      if( pass1 && pass2){
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
