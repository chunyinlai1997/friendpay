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
  if(!authorized()){
    header("Location:authorize");
  }

  $id = getUserId();
  $sql = mysql_query("SELECT Client.firstname, Client.lastname, Users.email, Users.create_date, Client.phone, Users.verified, Users.profile_img, Client.credit_card_number, Client.bank_account_number, Client.credit_card_type, Client.bank_name, Client.amount FROM Client, Users WHERE Client.user_id = Users.id AND Users.id = '$id'")or die(mysql_error());
  $row = mysql_fetch_array($sql,MYSQL_NUM);
  $firstname = $row[0];
  $lastname = $row[1];
  $email = $row[2];
  $create_date = $row[3];
  $phone = $row[4];
  $verified = $row[5];
  $profile_img = $row[6];
  $cardtype = $row[9];
  $cardnum = $row[7];
  $amount = $row[11];
  if($cardnum==""){
    header("Location:wallet?need=cc");
  }
  $cardnum = substr(decrypt($row[7]),-4);
  $alt = "";
  $alt2 = "";

  if(isset($_GET["topup"])&&$_GET["topup"]=="success"){
    $get = mysql_query("SELECT amount FROM Transaction WHERE payee_id='$id' AND payer_id='$id' AND remark='topup' ORDER BY tid DESC LIMIT 1 ")or die(mysql_error());
    $geta =  mysql_fetch_array($get,MYSQL_NUM);
    $print_amount = $geta[0];
    $alt = "<div class='alert alert-success' role='alert'>Transaction completed, you have top up $".$print_amount." HKD to your FriendPay account.<button type='button' class='close' data-dismiss='alert' aria-label='Close'></div>";
  }
  else if(isset($_GET["balance"])&&$_GET["balance"]=="fail"){
    $alt = "<div class='alert alert-danger' role='alert'>You do not have enough money for transaction, please top up first.<button type='button' class='close' data-dismiss='alert' aria-label='Close'></div>";
  }

  if(isset($_POST["topup_submit"])){
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
        $top_amount = floatval($_POST["amount"]);
        $newamount = $top_amount + $amount;
        mysql_query("UPDATE Client SET amount='$newamount' WHERE user_id = '$id'");
        $now = date("Y-m-d H:i:s");
        mysql_query("INSERT INTO Transaction(payer_id,payee_id,amount,date_time,status,remark) VAlUES('$id','$id','$top_amount','$now','success','topup')");
        send_email($firstname,$lastname,$email,$top_amount,$cardnum);
        header("Location: topup?topup=success");
      }
      else{
        $alt = "<div class='alert alert-danger' role='alert'>Wrong code! Please try again.<button type='button' class='close' data-dismiss='alert' aria-label='Close'></div>";
      }
    }
    else{
      $alt = "<div class='alert alert-warning' role='alert'>Fail Submission! Please try again.<button type='button' class='close' data-dismiss='alert' aria-label='Close'></div>";
    }
  }

  function send_email($fname,$lname,$email,$top_amount,$cardnum){
  	$to      = $email; // Send email to our user
  	$subject = ' Top Up -- Transaction Record | Friend Pay'; // Give the email a subject
    $ipaddress = $_SERVER['REMOTE_ADDR'];
    $now = date("Y-m-d H:i:s");
  	$message = "
  	Dear $fname $lname,

  	You have just top up $ $top_amount HKD to your account with the credit card ends with $cardnum.
    The transaction was made in $now with the device IP: $ipaddress.

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
	<title> Top Up | Friend Pay</title>
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
                <div class="col-xs-12 col-sm-12">
                  <? echo $alt;?>
                </div>

                <div class="col-xs-12 col-sm-12">
                    <div class="card">
                        <div class="body">
                          <p class="font-bold col-blue-blue">Top Up</p>
                          <small>You can top up from your credit card</small>
                          <form action="topup" method="POST" id="topup_form" class="form-horizontal">
                            <div class="form-group">
                      			<label for="amount" class="col-sm-2 control-label">Amount</label>
                      			  <div class="col-sm-10">
                      			  <div class="form-line">
                      				<input autocomplete="off" type="text" name="amount" id="amount" class="form-control" onkeyup="safeNumber(this)" placeholder="topup amount in HKD" autofocus required/>
                      			  </div>
                      			  <div id="invalid_amounts" class="invalid-feedback " style="display:none;">
                      			  </div>
                      			</div>
                      		  </div>

                            <div class='form-group'>
                              <label for="amount" class="col-sm-2 control-label">Authorization Code</label>
                      			  <div class="col-sm-10">
                                <div class="form-line">
                                <input autofocus type='text' id='inputGoogleAuthCode2' name='code2' minlength='6' maxlength='6' placeholder='Input 6-digit code here' class='form-control' onkeyup="checkcode()" onchange="checkcode()" autofocus required>
                                </div>
                                <div id='invalidGoogleAuthCode2' class='invalid-feedback' style='display:none;'>
                                </div>
                              </div>
                            </div>

                            <div class="form-group">
                              <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" id="topup_submit" name="topup_submit" value="topup_submit" class="btn btn-danger" disabled>SUBMIT</button>
                              </div>
                            </div>

                            <div class="form-group"><div class="col-sm-offset-2 col-sm-10"><h6>Using your credit card ends with <?php echo $cardnum; ?>.</h6></div></div>
                          </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <script>
    var patt1 = new RegExp("^[0-9]{1,5}[.][0-9]{1,2}$");
    var patt2 = new RegExp("^[0-9]{1,5}$");
    function safeNumber(amount){
      if(patt1.test(amount.value)||patt2.test(amount.value)){
        if(parseFloat(amount.value)<0.01){
          document.getElementById("amount").classList.add('is-invalid');
          document.getElementById("amount").classList.remove('is-valid');
          document.getElementById("invalid_amounts").innerHTML = "The minimum amount is $0.01HKD";
          document.getElementById("invalid_amounts").style.display = "block";
          document.getElementById("invalid_amounts").style.color = "red";
        }
        else if(parseFloat(amount.value)>5000){
          document.getElementById("amount").classList.add('is-invalid');
          document.getElementById("amount").classList.remove('is-valid');
          document.getElementById("invalid_amounts").innerHTML = "The maximum amount is $5000HKD";
          document.getElementById("invalid_amounts").style.display = "block";
          document.getElementById("invalid_amounts").style.color = "red";
        }
        else{
          document.getElementById("amount").classList.add('is-valid');
          document.getElementById("amount").classList.remove('is-invalid');
          document.getElementById("invalid_amounts").style.display = "none";
        }
      }
      else{
        document.getElementById("amount").classList.add('is-invalid');
        document.getElementById("amount").classList.remove('is-valid');
        document.getElementById("invalid_amounts").innerHTML = "Please input a valid amount";
        document.getElementById("invalid_amounts").style.display = "block";
        document.getElementById("invalid_amounts").style.color = "red";
      }
      checkAmountsubmit();
    }

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
      checkAmountsubmit();
    }

    function checkAmountsubmit(){
      var amount =  document.getElementById("amount").classList.contains('is-valid');
      var code =  document.getElementById("inputGoogleAuthCode2").classList.contains('is-valid');
      console.log(amount,code);
      if(code&&amount){
        document.getElementById("topup_submit").disabled = false;
      }
      else{
        document.getElementById("topup_submit").disabled = true;
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
    <!--<script src="plugins/bootstrap-tagsinput/bootstrap-tagsinput.js"></script>
    <script src="js/pages/forms/advanced-form-elements.js"></script>
    <script src="js/pages/forms/basic-form-elements.js"></script>1111-->
    <script src="js/admin.js"></script>
    <script src="js/pages/ui/dialogs.js"></script>
    <script src="js/pages/index.js"></script>
    <script src="js/demo.js"></script>

</body>

</html>
