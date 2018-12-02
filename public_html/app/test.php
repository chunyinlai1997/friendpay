<?php
  include_once 'config.php';
  include_once 'token.php';
  include_once 'encrypt_decrypt.php';




  // decrypt($encrypted, 'wrong password') === null

  //echo clean('prologic338@gmail.com');
  /*
  require_once 'googleLib/GoogleAuthenticator.php';
  $ga = new GoogleAuthenticator();
  $secretGoogleAuth = $ga->createSecret(); //This function will create unique 16 digit secret key

  echo $secret;

  $sql = mysql_query("SELECT COUNT(Login_Logging.status) FROM Login_Logging, Users WHERE Login_Logging.user_id = Users.id AND Users.email = 'prologic338@gmail.com' AND Login_Logging.status = 'fail' AND Login_Logging.date_time > NOW() - INTERVAL 5 MINUTE  ");
  $row= mysql_fetch_array($sql,MYSQL_NUM);
  $failin5mins =  $row[0];
  echo $failin5mins;

  $sql = "SELECT * FROM Users";
  $r = mysql_query($sql);
  while ($row = mysql_fetch_row($r)) {
    print_r($row);
    echo "<br>";
  }
*/
  /*
  echo $_SERVER['HTTP_USER_AGENT'];

  echo php_uname();

  */
  //mysql_query("UPDATE Users SET email='prologic338@gmail.com' WHERE id = 2 ");
  //$id = isloggedin();
  //mysql_query("ALTER TABLE Users ADD COLUMN create_date DATETIME AFTER join_date");
  //$sql = "SHOW COLUMNS FROM Users";
  //$result = mysql_query($sql);
  //while($row = mysql_fetch_array($result)){
  //    echo $row['Field']."<br>";
  //}



  /*
  $sql2 = "SELECT verified FROM Users, Client WHERE Client.user_id = Users.id ";
  $r = mysql_query($sql2);
  while ($row = mysql_fetch_row($r)) {
    print_r($row);
  }

  <?php include 'head-info.php'; ?>
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
  console.log("See:",old,newP,newC,code);
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


  $id = isloggedin();
  $new_pass = clean("Willon97");
  $options = [
      'cost' => 9,
  ];
  $hashed_password = password_hash("$new_pass", PASSWORD_BCRYPT, $options);

  */
  //$id = isloggedin();
  //$tmp = mysql_query("SELECT Users.verified, Users.status FROM Users WHERE id='$id'")or die(mysql_error());
  //$getf = mysql_fetch_array($tmp,MYSQL_NUM);
  //echo $getf[0].$getf[1];
  //echo "UPDATE Users SET verified='0' WHERE id = '$id'";
  //mysql_query("UPDATE Users SET verified='0' WHERE id = '$id'");
?>
<div class='form-bottom'>
  		  <div class='card card-block' id='payform' style='display:block;margin-buttom:20px;'>
  			<!-- form card cc payment -->
  			<div class='card card-outline-secondary' style='padding:10px;'>
  				<div class='card-block'>
  					<div class='form-group text-center'>
  						<ul class='list-inline'>
  							<li class='list-inline-item'><i class='text-muted fa fa-cc-visa fa-2x'></i></li>
  							<li class='list-inline-item'><i class='fa fa-cc-mastercard fa-2x'></i></li>
  							<li class='list-inline-item'><i class='fa fa-cc-amex fa-2x'></i></li>
  							<li class='list-inline-item'><i class='fa fa-cc-discover fa-2x'></i></li>
  						</ul>
  					</div>
  					<hr>
  						<div class='form-group'>
  						<label class='col-md-12'>Payment Detail</label>
  						<input type='text' class='form-control' readonly value='Online Booking Service (Fully refund to consultantion fee)'>
  						</div>
  						<div class='form-group'>
  							<label for='cc_name'>Card Holder's Name</label>
  							<input type='text' class='form-control' id='cc_name' onkeyup='check_hname(this);'  name='cc_name' title='First and last name' >
  						</div>
  						<div id='vcname' class='invalid-feedback' style='display:none;'></div>
  						<div class='form-group'>
  							<label>Card Number</label>
  							<div class='input-group'>
  								<div class='input-group-addon' id='cardtype' style='dsiplay:none;'></div>
  								<input type='text' class='form-control' name='cardnum' autocomplete='off' maxlength='20' id='cardnum' onkeyup='CardNumber();' onchange='CardNumber();' title='Credit card number' >
  							</div>
  						</div>
  						<div id='vcnum' class='invalid-feedback' style='display:none;'></div>
  						<div class='form-group row'>
  							<label class='col-sm-10'>Card Exp. Date</label>
  							<div class='col-md-4'>
  								<select id='exMonth' name='cc_exp_mo' class='form-control' onchange='check_exp();' onkeyup='check_exp();' size='0'>
  									<option value='1'>01</option>
  									<option value='2'>02</option>
  									<option value='3'>03</option>
  									<option value='4'>04</option>
  									<option value='5'>05</option>
  									<option value='6'>06</option>
  									<option value='7'>07</option>
  									<option value='8'>08</option>
  									<option value='9'>09</option>
  									<option value='10'>10</option>
  									<option value='11'>11</option>
  									<option value='12'>12</option>
  								</select>
  							</div>
  							<div class='col-md-4'>
  								<select id='exYear' name='cc_exp_yr'  class='form-control' onchange='check_exp();' onkeyup='check_exp();'  size='0'>
  									<option value='2017'>2017</option>
  									<option value='2018'>2018</option>
  									<option value='2019'>2019</option>
  									<option value='2020'>2020</option>
  									<option value='2021'>2021</option>
  									<option value='2022'>2022</option>
  									<option value='2023'>2023</option>
  									<option value='2024'>2024</option>
  									<option value='2025'>2025</option>
  									<option value='2026'>2026</option>
  									<option value='2027'>2027</option>
  								</select>
  							</div>
  							<div class='col-md-4'>
  								<input type='text' class='form-control' autocomplete='off' maxlength='3' onkeyup='check_cvc(this);' id='cvc' pattern='\d{3}' title='Three digits at back of your card' placeholder='CVC'>
  							</div>
  						</div>
  						<div id='vdate' class='invalid-feedback' style='display:none;'></div>
  						<div id='vcvc' class='invalid-feedback' style='display:none;'></div>

  						<div class='row'>
  							<label class='col-md-12'>Amount</label>
  						</div>
  						<div class='form-inline'>
  							<div class='input-group'>
  								<div class='input-group-addon'>$</div>
  								<input type='text' class='form-control text-right' value='50' name='amount' readonly>
  								<div class='input-group-addon'>HKD</div>
  							</div>
  						</div>
  					</div>
  					<hr>
  					<div style='height:30px;'/>
  				</div>
  			</div>
