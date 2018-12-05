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

  $id = isloggedin();
  $sql = mysql_query("SELECT Client.firstname, Client.lastname, Users.email, Users.profile_img FROM Client, Users WHERE Client.user_id = Users.id AND Users.id = '$id'") or die(mysql_error());
  $row = mysql_fetch_array($sql,MYSQL_NUM);
  $firstname = $row[0];
  $lastname = $row[1];
  $email = $row[2];
  $profile_img = $row[3];
  $fid = "";
  $alt = "";
  $alt2 = "";

  if(isset($_GET["id"])&&!empty(isset($_GET["id"]))){
    $fid = $_GET["id"];

    $findf = mysql_query("SELECT COUNT(*) FROM Users WHERE id = '$fid' ") or die(mysql_error());
    $findResult = mysql_fetch_array($findf,MYSQL_NUM);
    if($findResult[0]==0){
      header("Location:dashboard");
    }

    $getf = mysql_query("SELECT Users.profile_img, Client.firstname, Client.lastname, Users.create_date FROM Client, Users WHERE Client.user_id = Users.id AND Users.id = '$fid' ") or die(mysql_error());
    $friendResult = mysql_fetch_array($getf,MYSQL_NUM);

    $friend_profileimg = $friendResult[0];
    $friend_name = $friendResult[1]." ".$friendResult[2];
    $friend_firstname = $friendResult[1];
    $friend_joindate = date("d-m-Y", strtotime($friendResult[3]));

  }
  else{
    header("Location:profile");
  }

?>
<html lang="en">
<head>
	<title> Profile | Friend Pay</title>
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
                <div class="col-xs-12 col-sm-3">
                    <div class="card profile-card">
                        <div class="profile-header">&nbsp;</div>
                        <div class="profile-body">
                            <div class="image-area">
                                <img src="<?php echo $friend_profileimg; ?>"  width="128" height="128" alt="Profile Image" />
                            </div>
                            <div class="content-area">
                                <?php
                                $leftsql = mysql_query("SELECT count(*) FROM Friend WHERE user1 = '$id' AND user2='$fid'");
                                $rightsql = mysql_query("SELECT count(*)  FROM Friend WHERE user1 = '$fid' AND user2 = '$id'");
                                $leftc = mysql_fetch_array($leftsql,MYSQL_NUM);
                                $rightc = mysql_fetch_array($rightsql,MYSQL_NUM);
                                if($leftc[0]==1 || $rightc[0]==1 ){
                                  echo "<a id='#' class='btn bg-pink waves-effect m-b-15' role='button' disabled>Friend</a>";
                                }
                                else{
                                  echo "<a id='friend_btn' href='add_friend?id=$fid' class='btn bg-pink waves-effect m-b-15' role='button'>Add Friend</a>";
                                }
                                ?>
                                <h3><?php echo $friend_name;?></h3>
                                <p>Since <? echo $friend_joindate; ?></p>
                            </div>
                        </div>
                    </div>

                    <a href="pay?id=<?php echo $fid;?>">
                    <div class="card profile-card">
                          <div class="demo-color-box bg-amber">
                              <div class="color-code"></div>
                              <div class="color-name">PAY</div>
                          </div>
                      </div></a>

                      <a href="request?id=<?php echo $fid;?>">
                      <div class="card profile-card">

                          <div class="demo-color-box bg-indigo">
                              <div class="color-code"></div>
                              <div class="color-name">REQUEST</div>
                          </div>
                    </div></a>
                </div>

                <div class="col-xs-12 col-sm-9">
                  <? echo $alt;?>
                  <? echo $alt2;?>
                </div>

                <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                            <h2>
                                Our Interactions
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
                                  $sqlf1 = mysql_query("(SELECT remark, payer_id, payee_id, amount, date_time, status FROM Transaction WHERE payer_id = '$id' AND payee_id = '$fid' ) UNION (SELECT remark, payer_id, payee_id, amount, date_time, status FROM Transaction WHERE payer_id = '$fid' AND payee_id = '$id') ORDER BY date_time DESC ") or die(mysql_error());
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
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>
    <script>

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
</body>

</html>
