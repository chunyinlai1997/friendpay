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

  $fid = "";
  $friend_profileimg = "";
  $friend_name = "";
  if(!empty(isset($_GET["id"]))&&!empty(isset($_GET["rid"]))){
     $fid = $_GET["id"];
     $requestid = $_GET["rid"];
     $findf = mysql_query("SELECT COUNT(*) FROM Users WHERE id = '$fid' ") or die(mysql_error());
     $findResult = mysql_fetch_array($findf,MYSQL_NUM);
     if($findResult[0]==0){
       header("Location:dashboard");
       $findr = mysql_query("SELECT COUNT(*), status FROM Request WHERE id = '$requestid' ") or die(mysql_error());
       $findResult2 = mysql_fetch_array($findr,MYSQL_NUM);
       if($findResult2[0]==0){
         header("Location:dashboard");
       }
       if($findResult2[1]!="requested"){
         header("Location:dashboard");
       }
     }
     $getf = mysql_query("SELECT Users.profile_img, Client.firstname, Client.lastname FROM Client, Users WHERE Client.user_id = Users.id AND Users.id = '$fid' ") or die(mysql_error());
     $friendResult = mysql_fetch_array($getf,MYSQL_NUM);
     $friend_profileimg = $friendResult[0];
     $friend_name = $friendResult[1]." ".$friendResult[2];
     $rid = $requestid;
  }
  else{
    header("Location:dashboard");
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

  if(isset($_POST["transfer_submit"])){
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
        $t_amount = $_POST['amount'];
        $rid = $_POST['rid'];
        $friend_name = $_POST['friend_name'];
        $fid =  $_POST['fid'];
        $newamount =  $amount - $t_amount;
        //echo $amount." ".$newamount." ".$t_amount;
        mysql_query("UPDATE Client SET amount='$newamount' WHERE user_id = '$id'");
        $fdac = mysql_query("SELECT amount FROM Client WHERE user_id = '$fid' ");
        $result_fdac = mysql_fetch_array($fdac,MYSQL_NUM);
        $fdb = $result_fdac[0];
        $fdb += $t_amount;
        mysql_query("UPDATE Client SET amount='$fdb' WHERE user_id = '$fid'");
        mysql_query("UPDATE Request SET status='done' WHERE id='$rid'");
        $now = date("Y-m-d H:i:s");
        mysql_query("INSERT INTO Transaction(payer_id,payee_id,amount,date_time,status,remark) VAlUES('$id','$fid','$t_amount','$now','success','transfer')");
        $get_tid = mysql_query("SELECT tid FROM Transaction WHERE payer_id='$id' AND payee_id='$fid' AND date_time='$now' AND remark = 'transfer'  ")or die(mysql_error());
        $result_tid = mysql_fetch_array($get_tid,MYSQL_NUM);
        $tid = encrypt($result_tid[0]);
        send_email($firstname,$lastname,$email,$t_amount,$friend_name,$cardnum);
        $sentofd = mysql_query("SELECT Users.email, Client.firstname, Client.lastname FROM Client, Users WHERE Client.user_id = Users.id AND Users.id = '$fid' ");
        $result_sentofd  = mysql_fetch_array($sentofd,MYSQL_NUM);
        $refirstname = $result_sentofd[1];
        $relastname =  $result_sentofd[2];
        $reemail = $result_sentofd[0];
        $sender = $firstname." ".$lastname;
        send_email2($refirstname,$relastname,$reemail,$t_amount,$sender);
        $myid = isloggedin();
        $leftsql = mysql_query("SELECT count(*) FROM Friend WHERE user1 = '$myid' AND user2='$fid'");
        $rightsql = mysql_query("SELECT count(*)  FROM Friend WHERE user1 = '$fid' AND user2 = '$myid'");
        $leftc = mysql_fetch_array($leftsql,MYSQL_NUM);
        $rightc = mysql_fetch_array($rightsql,MYSQL_NUM);
        if($leftc[0]==1 || $rightc[0]==1 ){
          $myid = isloggedin();
        }
        else{
          mysql_query("INSERT INTO Friend(user1,user2,status) VAlUES('$myid','$fid','connected')");
        }
        header("Location:success?pay=$tid");
      }
      else{
        $alt = "<div class='alert alert-danger' role='alert'>Wrong code! Please try again.<button type='button' class='close' data-dismiss='alert' aria-label='Close'></div>";
      }
    }
    else{
      $alt = "<div class='alert alert-warning' role='alert'>Fail Submission! Please try again.<button type='button' class='close' data-dismiss='alert' aria-label='Close'></div>";
    }
  }



  function send_email($fname,$lname,$email,$t_amount,$friend_name,$cardnum){
  	$to      = $email; // Send email to our user
  	$subject = ' Transfer Made -- Transaction Record | Friend Pay'; // Give the email a subject
    $ipaddress = $_SERVER['REMOTE_ADDR'];
    $now = date("Y-m-d H:i:s");
  	$message = "
  	Dear $fname $lname,

  	You have just transfer $ $t_amount HKD to $friend_name with the credit card ends with $cardnum.
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

  function send_email2($fname,$lname,$email,$t_amount,$sender){
  	$to      = $email; // Send email to our user
  	$subject = ' Transfer Received -- Transaction Record | Friend Pay'; // Give the email a subject
    $now = date("Y-m-d H:i:s");
  	$message = "
  	Dear $fname $lname,

  	You have just recevied $ $t_amount HKD from $sender.
    The transaction was made in $now.

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
	<title> Complete Pay | Friend Pay</title>
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
                          <form action="complete_pay" method="POST" id="pay_form" class="form-horizontal">
                            <div class='image-area'>
                  						<img src='<?php echo $friend_profileimg;?>'  width='120' height='120' style="display: block; margin-left: auto; margin-right: auto; " alt='Profile Image' />
                  					</div>
                            <h2 style="text-align:center;">Transfer to <?php echo $friend_name;?></h2>
                            <h6 style="text-align:center;">You have <strong>$ <?php echo $amount?>HKD</strong> in your account</h6>
                            <div class="form-group">
                      			<label for="amount" class="col-sm-2 control-label">Amount</label>
                      			  <div class="col-sm-10">
                      			  <div class="form-line">
                      				<input type="text" id="amount" value="<?php $amountsql = mysql_query("SELECT amount FROM Request WHERE id='$rid'"); $resultamount = mysql_fetch_array($amountsql,MYSQL_NUM); echo $resultamount[0]; ?>" class="form-control" disabled required/>
                      			  </div>
                      			  <div id="invalid_amounts" class="invalid-feedback" style="display:none;">
                      			  </div>
                      			</div>
                      		  </div>

                            <div class='form-group'>
                              <label for="amount" class="col-sm-2 control-label">Authorization Code</label>
                      			  <div class="col-sm-10">
                                <div class="form-line">
                                <?php $amountsql = mysql_query("SELECT amount FROM Request WHERE id='$rid'"); $resultamount = mysql_fetch_array($amountsql,MYSQL_NUM);  ?>
                                <input type="hidden" value="<?php echo $resultamount[0];?>" name="amount" id="amount1"/>
                                <input type="hidden" value="<?php echo $fid; ?>" id="fid" name="fid"/>
                                <input type="hidden" value="<?php echo $friend_name; ?>" id="friend_name" name="friend_name"/>
                                <input type="hidden" value="<?php echo $rid; ?>" id="rid" name="rid"/>
                                <input autofocus type='text' id='inputGoogleAuthCode2' name='code2' minlength='6' maxlength='6' placeholder='Input 6-digit code here' class='form-control' onkeyup="checkcode()" onchange="checkcode()" autofocus required>
                                </div>
                                <div id='invalidGoogleAuthCode2' class='invalid-feedback' style='display:none;'>
                                </div>
                              </div>
                            </div>

                            <div class="form-group">
                              <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" id="transfer_submit" name="transfer_submit" value="transfer_submit" class="btn btn-danger" disabled>SUBMIT</button>
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

    $(document).ready(function(){
      var amount = $('#amount').val();
      $.ajax({
        url:"check.php",
        method:"POST",
        data:{checkbalance:amount},
        dataType:"text",
        success:function(response){
          if(safeNumber(amount)){
            console.log("res:",response);
            if(response==0){
              $('#invalid_amounts').css("color","red");
              $('#invalid_amounts').css("display", "block");
              $('#invalid_amounts').html("You do not have enough money in your balance.");
              $('#amount').removeClass( "is-valid" ).addClass( "is-invalid" );
            }
            else{
              $('#invalid_amounts').css("color","green");
              $('#invalid_amounts').css("display", "block");
              $('#invalid_amounts').html("");
              $('#amount').removeClass( "is-invalid" ).addClass( "is-valid" );
            }
          }
        },
      });

      $("#amount").keyup(function () {
        var amount = $(this).val();
        $.ajax({
          url:"check.php",
          method:"POST",
          data:{checkbalance:amount},
          dataType:"text",
          success:function(response){
            if(safeNumber(amount)){
              console.log("res:",response);
              if(response==0){
                $('#invalid_amounts').css("color","red");
                $('#invalid_amounts').css("display", "block");
                $('#invalid_amounts').html("You do not have enough money in your balance.");
                $('#amount').removeClass( "is-valid" ).addClass( "is-invalid" );
              }
              else{
                $('#invalid_amounts').css("color","green");
                $('#invalid_amounts').css("display", "block");
                $('#invalid_amounts').html("");
                $('#amount').removeClass( "is-invalid" ).addClass( "is-valid" );
              }
            }
            checkAmountsubmit();
          },
        });
      });
    });

    function safeNumber(amount){
      var patt1 = new RegExp("^[0-9]{1,5}[.][0-9]{1,2}$");
      var patt2 = new RegExp("^[0-9]{1,5}$");
      console.log(patt1.test(amount)||patt2.test(amount));
      if(patt1.test(amount)||patt2.test(amount)){
        if(parseFloat(amount) < 0.01){
          document.getElementById("amount").classList.add('is-invalid');
          document.getElementById("amount").classList.remove('is-valid');
          document.getElementById("invalid_amounts").innerHTML = "The minimum amount is $0.01HKD";
          document.getElementById("invalid_amounts").style.display = "block";
          document.getElementById("invalid_amounts").style.color = "red";
          return false;
        }
        if(parseFloat(amount) > 5000){
          document.getElementById("amount").classList.add('is-invalid');
          document.getElementById("amount").classList.remove('is-valid');
          document.getElementById("invalid_amounts").innerHTML = "The maximum amount is $5000HKD";
          document.getElementById("invalid_amounts").style.display = "block";
          document.getElementById("invalid_amounts").style.color = "red";
          return false;
        }
        else{
          document.getElementById("amount").classList.add('is-valid');
          document.getElementById("amount").classList.remove('is-invalid');
          document.getElementById("invalid_amounts").style.display = "none";
          return true;
        }
      }
      else{
        document.getElementById("amount").classList.add('is-invalid');
        document.getElementById("amount").classList.remove('is-valid');
        document.getElementById("invalid_amounts").innerHTML = "Please input a valid amount";
        document.getElementById("invalid_amounts").style.display = "block";
        document.getElementById("invalid_amounts").style.color = "red";
        return false;
      }
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
        document.getElementById("transfer_submit").disabled = false;
      }
      else{
        document.getElementById("transfer_submit").disabled = true;
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
