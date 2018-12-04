<?php
  include_once 'config.php';
  include_once 'token.php';
  include_once 'encrypt_decrypt.php';

  function fuck($s){
    $find = mysql_query("SELECT Users.profile_img, Client.firstname, Client.lastname, Users.id FROM Users, Client WHERE Client.user_id = Users.id AND (Users.email LIKE '%$s%' OR Client.phone LIKE '$s')  ")or die(mysql_error());
    $count = 0;
    $result = "";
    while($arrayResult = mysql_fetch_array($find,MYSQL_NUM)){
      $count += 1;
      $img = $arrayResult[0];
      $fname	= $arrayResult[1]." ".$arrayResult[2];
      $ud = $arrayResult[3];
      $result .= "

      <div class='col-xs-12 col-sm-3'>
        <div class='card profile-card'>
          <div class='profile-header'>&nbsp;</div>
          <div class='profile-body'>
            <div class='image-area'>
              <img src='<?php echo $img; ?>'  width='128' height='128' alt='Profile Image' />
            </div>
            <div class='content-area'>
              <h3><?php echo $fname;?></h3>
              <a href='pay?id=$ud' role='button' class='btn bg-yellow waves-effect m-b-15'>PAY</a>
              <a href='request?id=$ud' role='button' class='btn bg-blue waves-effect m-b-15'>REQUEST</a>
            </div>
          </div>
        </div>
      </div>
      ";
    }

    if($count==0){
      return 0;
    }
    else{
      return $result;
    }
  }

  echo fuck("prologic338@gmail.com");

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
/*
  $id = isloggedin();
  $sqlf1 = mysql_query("(SELECT user2 as F FROM Friend WHERE user1 = 8) UNION (SELECT user1 as F FROM Friend WHERE user2 = 8)") or die(mysql_error());
  while($arrayResult = mysql_fetch_array($sqlf1,MYSQL_NUM)){
    $sqlf2 = mysql_query("SELECT Users.profile_img, Client.firstname, Client.lastname FROM Users, Client WHERE Users.id = Client.user_id AND Users.id='$arrayResult[0]'") or die(mysql_error());
    $frd = mysql_fetch_array($sqlf2,MYSQL_NUM);
    echo $frd[0];
  }

  echo "
  <div class='col-lg-2 col-md-3 col-sm-4 col-xs-6'>
    <div class='image-area'>
        <img src=".$frd[0]."  width='128' height='128' alt='Profile Image'></img>
        ".$frd[1]." ".$frd[2]."
    </div>
  </div>

  ";
  */

?>
