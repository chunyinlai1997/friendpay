<?php
  include_once 'config.php';
  include_once 'token.php';

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
  $sql = mysql_query("SELECT Client.firstname, Client.lastname, Users.email, Users.create_date, Client.phone, Users.verified, Users.profile_img, Users.status, Users.two_factor, Client.amount FROM Client, Users WHERE Client.user_id = Users.id AND Users.id = '$id'")or die(mysql_error());
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
  $amount = $row[9];
?>

<html lang="en">
<head>
	<title> Dashboard | Friend Pay</title>
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
                    <li class="active">
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
            <div class="block-header">
                <?php
                if(isset($_GET['setup'])){
                  if($_GET['setup']==True){
                    echo "<div class='alert alert-success' role='alert'>Well Done! You have activated your acccount.<button type='button' class='close' data-dismiss='alert' aria-label='Close'></div>";
                  }
                }
                ?>
                <h2><?php
                $str = "";
                $time = date("H");
                $timezone = date("e");
                if ($time < "12") {
                    $str =  "Good morning";
                }
                else if ($time >= "12" && $time < "17") {
                    $str = "Good afternoon";
                }
                else if ($time >= "17" && $time < "19") {
                    $str =  "Good evening";
                } else if ($time >= "19") {
                    $str =  "Good night";
                }

                echo $str.", ".$firstname;


                ?></h2>
            </div>

            <!-- Widgets -->
            <a href="wallet"><div class="row clearfix">
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="demo-color-box bg-pink">
                        <div class="color-code"></div>
                        <div class="color-name">Balance: $<?php echo $amount; ?>HKD</div>
                    </div>
                </div></a>

                <a href="send_request">
                <div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="demo-color-box bg-amber">
                        <div class="color-code"></div>
                        <div class="color-name">PAY</div>
                    </div>
                </div></a>

                <a href="send_request"><div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="demo-color-box bg-indigo">
                        <div class="color-code"></div>
                        <div class="color-name">REQUEST</div>
                    </div>
                </div></a>

                <a href="profile"><div class="col-lg-3 col-md-3 col-sm-6 col-xs-12">
                    <div class="demo-color-box bg-teal">
                        <div class="color-code"></div>
                        <div class="color-name">Profile</div>
                    </div>
                </div></a>


            </div>

              <div class="row clearfix">
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                            <h2>
                                Recent History
                            </h2>
                        </div>
                        <div class="body table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date Time</th>
                                        <th>Transaction</th>
                                    </tr>
                                </thead>
                                <tbody>
                                  <?php
                                  $id = isloggedin();
                                  $sqlf1 = mysql_query("(SELECT remark, payer_id, payee_id, amount, date_time, status FROM Transaction WHERE payer_id = '$id' ) UNION (SELECT remark, payer_id, payee_id, amount, date_time, status FROM Transaction WHERE payee_id = '$id') ORDER BY date_time DESC LIMIT 10 ") or die(mysql_error());
                                  $count = 0;
                                  $result = "";
                                  while($arrayResult = mysql_fetch_array($sqlf1,MYSQL_NUM)){
                                    $count = $count + 1 ;
                                    $type = $arrayResult[0];
                                    $payer_id = $arrayResult[1];
                                    $payee_id = $arrayResult[2];
                                    $amount = $arrayResult[3];
                                    $dt = $arrayResult[4];
                                    $st = $arrayResult[5];

                                    if($payer_id==$id && $type=="transfer"){
                                      $find = mysql_query("SELECT firstname, lastname FROM Client WHERE user_id = '$payee_id'");
                                      $getfind = mysql_fetch_array($find,MYSQL_NUM);
                                      $name = $getfind[0];
                                      $result .= "
                                      <tr>
                                          <th scope='row'>$dt</th>
                                          <td>You have transfered $$amount HKD to $name. <span class='label bg-green'>$st</span></td>
                                      </tr>
                                      ";
                                    }
                                    else if($payee_id==$id && $type=="transfer"){
                                      $find = mysql_query("SELECT firstname, lastname FROM Client WHERE user_id = '$payer_id'");
                                      $getfind = mysql_fetch_array($find,MYSQL_NUM);
                                      $name = $getfind[0]." ".$getfind[1];
                                      $result .= "
                                      <tr>
                                          <th scope='row'>$dt</th>
                                          <td>$name havs transfered $$amount HKD to you.  <span class='label bg-green'>$st</span></td>
                                      </tr>
                                      ";
                                    }
                                  }

                                  echo $result;
                                  ?>
                                </tbody>
                            </table>
                            <a href="activity"><button type="button" class="btn btn-warning waves-effect">More</button></a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                            <h2>
                                Pending Request
                            </h2>
                        </div>
                        <div class="body table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Date Time</th>
                                        <th>Transaction</th>
                                    </tr>
                                </thead>
                                <tbody>
                                  <?php
                                  $id = isloggedin();
                                  $sqlf1 = mysql_query("(SELECT remark, payer_id, payee_id, amount, date_time, status FROM Request WHERE payer_id = '$id' ) UNION (SELECT remark, payer_id, payee_id, amount, date_time, status FROM Request WHERE payee_id = '$id') ORDER BY date_time DESC LIMIT 10 ") or die(mysql_error());
                                  $count = 0;
                                  $result = "";
                                  while($arrayResult = mysql_fetch_array($sqlf1,MYSQL_NUM)){
                                    $count = $count + 1 ;
                                    $type = $arrayResult[0];
                                    $payer_id = $arrayResult[1];
                                    $payee_id = $arrayResult[2];
                                    $amount = $arrayResult[3];
                                    $dt = $arrayResult[4];
                                    $st = $arrayResult[5];

                                    if($payer_id==$id && $type=="transfer_request"){
                                      $find = mysql_query("SELECT firstname, lastname FROM Client WHERE user_id = '$payee_id'");
                                      $getfind = mysql_fetch_array($find,MYSQL_NUM);
                                      $name = $getfind[0];
                                      $result .= "
                                      <tr>
                                          <th scope='row'>$dt</th>
                                          <td>$name have request for $$amount HKD from you. <span class='label bg-orange'>$st</span></td>
                                      </tr>
                                      ";
                                    }
                                    else if($payee_id==$id && $type=="transfer_request"){
                                      $find = mysql_query("SELECT firstname, lastname FROM Client WHERE user_id = '$payer_id'");
                                      $getfind = mysql_fetch_array($find,MYSQL_NUM);
                                      $name = $getfind[0]." ".$getfind[1];
                                      $result .= "
                                      <tr>
                                          <th scope='row'>$dt</th>
                                          <td>$You have request for $amount from $name.  <span class='label bg-orange'>$st</span></td>
                                      </tr>
                                      ";
                                    }
                                  }

                                  echo $result;
                                  ?>
                                </tbody>
                            </table>
                            <a href="#"><button type="button" class="btn btn-warning waves-effect">More</button></a>
                        </div>
                    </div>
                </div>
              </div>

    </div>
    </div>

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
