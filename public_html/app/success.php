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


  if(isset($_GET["pay"])){
    $tid = decrypt($_GET["pay"]);
    $findtid = mysql_query("SELECT count(*) FROM Transaction WHERE tid = '$tid'");
    $gettid = mysql_fetch_array($findtid,MYSQL_NUM);
    if($gettid[0]==1){
      $get = mysql_query("SELECT amount, payee_id FROM Transaction WHERE tid = '$tid' ")or die(mysql_error());
      $geta =  mysql_fetch_array($get,MYSQL_NUM);
      $print_amount = $geta[0];
      $payeeid = $geta[1];
      $getapyee = mysql_query("SELECT Users.profile_img, Client.firstname, Client.lastname FROM Client, Users  WHERE Client.user_id = Users.id AND Users.id = '$payeeid' ")or die(mysql_error());
      $py =  mysql_fetch_array($getapyee,MYSQL_NUM);
      $friend_name = $py[1]." ".$py[2];
      $friend_profileimg = $py[0];
    }
    else{
      //echo "here1";
      header("Location:activity");
    }
  }

  if(isset($_GET["request"])){
    $rid = decrypt($_GET["request"]);
    $findtid = mysql_query("SELECT count(*) FROM Request WHERE id = '$rid'");
    $gettid = mysql_fetch_array($findtid,MYSQL_NUM);
    if($gettid[0]==1){
      $get = mysql_query("SELECT amount, payer_id FROM Request WHERE id = '$rid' ")or die(mysql_error());
      $geta =  mysql_fetch_array($get,MYSQL_NUM);
      $print_amount = $geta[0];
      $payerid = $geta[1];
      $getapyee = mysql_query("SELECT Users.profile_img, Client.firstname, Client.lastname FROM Client, Users  WHERE Client.user_id = Users.id AND Users.id = '$payerid' ")or die(mysql_error());
      $py =  mysql_fetch_array($getapyee,MYSQL_NUM);
      $friend_name = $py[1]." ".$py[2];
      $friend_profileimg = $py[0];
    }
    else{
      //echo "here1";
      header("Location:activity");
    }
  }
  else{
    //echo "here1";
    header("Location:activity");
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
  $cardnum = substr(decrypt($row[7]),-4);
  $print_amount = "";
  $friend_profileimg = "";
  $friend_name = "";

?>
<html lang="en">
<head>
	<title> Success | Friend Pay</title>
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
                    <div class="card">
                        <div class="body">
                          <form action="pay" method="POST" id="pay_form" class="form-horizontal">
                            <div class='image-area'>
                  						<img src='<?php echo $py[0];?>'  width='120' height='120' style="display: block; margin-left: auto; margin-right: auto; " alt='Profile Image' />
                  					</div>
                            <?php
                            if(isset($_GET["pay"])){
                              echo "<h2 style='text-align:center;'>You have ransfered $$geta[0] HKD to $py[1] $py[2], using your credit card ends with $cardnum.</h2>";
                            }
                            else if(isset($_GET["request"])){
                              echo "<h2 style='text-align:center;'>You have request for $$geta[0] HKD from $py[1] $py[2].</h2>";
                            }
                            ?>
                            <a href='activity' role='button' class='btn bg-yellow waves-effect m-b-15'>View My Transaction</a>
                          </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

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
