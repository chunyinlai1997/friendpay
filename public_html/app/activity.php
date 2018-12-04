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
  $sql = mysql_query("SELECT Client.firstname, Client.lastname, Users.email, Users.create_date, Client.phone, Users.verified, Users.profile_img, Users.status, Users.two_factor, Client.billing_address FROM Client, Users WHERE Client.user_id = Users.id AND Users.id = '$id'")or die(mysql_error());
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
  $address = $row[9];
  $sql2 = mysql_query("SELECT date_time, status, ip FROM Login_Logging L WHERE user_id = '$id' ORDER BY date_time  DESC LIMIT 1,1 ");
  $row2 = mysql_fetch_array($sql2,MYSQL_NUM);
  $last_login_time = $row2[0];
  $last_login_status = "";

?>
<html lang="en">
<head>
	<title> Activity | Friend Pay</title>
	<?php include 'head-info.php'; ?>
  <link href="plugins/jquery-datatable/skin/bootstrap/css/dataTables.bootstrap.css" rel="stylesheet">
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
                    <li class="active">
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
                <!--<h2>
                    Transaction Record
                </h2>-->
            </div>
            <!-- Basic Examples -->
            <div class="row clearfix">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="card">
                        <div class="header">
                            <h2>
                                Transaction Record
                            </h2>
                        </div>
                        <div class="body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover dataTable js-exportable">
                                  <!--js-basic-example-->
                                    <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th>Payer</th>
                                            <th>Receiver</th>
                                            <th>Amount (HKD)</th>
                                            <th>DateTime</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tfoot>
                                        <tr>
                                            <th>Type</th>
                                            <th>Payer</th>
                                            <th>Receiver</th>
                                            <th>Amount (HKD)</th>
                                            <th>DateTime</th>
                                            <th>Status</th>
                                        </tr>
                                    </tfoot>
                                    <tbody>
<!--<tr> <td>Tiger Nixon</td> <td>System Architect</td> <td>Edinburgh</td> <td>61</td> <td>2011/04/25</td> <td>$320,800</td> </tr>-->
                                      <?php
                                      $id = isloggedin();
                                      $sqlf1 = mysql_query("(SELECT remark, payer_id, payee_id, amount, date_time, status FROM Transaction WHERE payer_id = '$id') UNION (SELECT remark, payer_id, payee_id, amount, date_time, status FROM Transaction WHERE payee_id = '$id') ORDER BY date_time DESC ") or die(mysql_error());
                                      while($arrayResult = mysql_fetch_array($sqlf1,MYSQL_NUM)){
                                        $type = $arrayResult[0];
                                        $payer_id = $arrayResult[1];
                                        $payee_id = $arrayResult[2];
                                        $amount = $arrayResult[3];
                                        $dt = $arrayResult[4];
                                        $st = $arrayResult[5];
                                        if($payer_id==$id && $payee_id==$id && $type=="topup"){
                                          echo "
                                          <tr>
                                              <td>TOPUP</td>
                                              <td>ME</td>
                                              <td>ME</td>
                                              <td>$amount</td>
                                              <td>$dt</td>
                                              <td>$st</td>
                                          </tr>
                                          ";
                                        }
                                        else if($payer_id==$id && $payee_id==$id && $type=="cashout"){
                                          echo "
                                          <tr>
                                              <td>CASHOUT</td>
                                              <td>ME</td>
                                              <td>ME</td>
                                              <td>$amount</td>
                                              <td>$dt</td>
                                              <td>$st</td>
                                          </tr>
                                          ";
                                        }
                                        else if($payer_id==$id && $type=="transfer"){
                                          $find = mysql_query("SELECT firstname, lastname FROM Client WHERE user_id = '$payee_id'");
                                          $getfind = mysql_fetch_array($find,MYSQL_NUM);
                                          $name = $getfind[0]." ".$getfind[1];
                                          echo "
                                          <tr>
                                              <td>RECEIVED</td>
                                              <td>ME</td>
                                              <td><a href='member?id=$payee_id'>$name</a></td>
                                              <td>$amount</td>
                                              <td>$dt</td>
                                              <td>$st</td>
                                          </tr>
                                          ";
                                        }
                                        else if($payee_id==$id && $type=="transfer"){
                                          $find = mysql_query("SELECT firstname, lastname FROM Client WHERE user_id = '$payer_id'");
                                          $getfind = mysql_fetch_array($find,MYSQL_NUM);
                                          $name = $getfind[0]." ".$getfind[1];
                                          echo "
                                          <tr>
                                              <td>PAY</td>
                                              <td><a href='member?id=$payer_id'>$name</a></td>
                                              <td>ME</td>
                                              <td>$amount</td>
                                              <td>$dt</td>
                                              <td>$st</td>
                                          </tr>
                                          ";
                                        }
                                      }
                                      ?>
                                    </tbody>
                                </table>
                            </div>
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
    <script src="plugins/jquery-datatable/jquery.dataTables.js"></script>
    <script src="plugins/jquery-datatable/skin/bootstrap/js/dataTables.bootstrap.js"></script>
    <script src="plugins/jquery-datatable/extensions/export/dataTables.buttons.min.js"></script>
    <script src="plugins/jquery-datatable/extensions/export/buttons.flash.min.js"></script>
    <script src="plugins/jquery-datatable/extensions/export/jszip.min.js"></script>
    <script src="plugins/jquery-datatable/extensions/export/pdfmake.min.js"></script>
    <script src="plugins/jquery-datatable/extensions/export/vfs_fonts.js"></script>
    <script src="plugins/jquery-datatable/extensions/export/buttons.html5.min.js"></script>
    <script src="plugins/jquery-datatable/extensions/export/buttons.print.min.js"></script>
    <script src="js/admin.js"></script>
    <script src="js/pages/tables/jquery-datatable.js"></script>
    <script src="js/demo.js"></script>
</body>

</html>
