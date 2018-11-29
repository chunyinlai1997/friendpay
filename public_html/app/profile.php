<?php
  include_once 'config.php';
  include_once 'token.php';

  if(!isloggedin()){
    header("Location:sign-in?need_login=True");
  }


  $id = getUserId();
  $sql = mysql_query("SELECT Client.firstname, Client.lastname, Users.email, Users.create_date, Client.phone, Users.verified, Users.profile_img, Users.status, Users.two_factor FROM Client, Users WHERE Client.user_id = Users.id AND Users.id = '$id'")or die(mysql_error());
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
  $sql2 = mysql_query("SELECT date_time, status, ip FROM Login_Logging L WHERE user_id = '$id' ORDER BY date_time  DESC LIMIT 1,1 ");
  $row2 = mysql_fetch_array($sql2,MYSQL_NUM);
  $last_login_time = $row2[0];
  $last_login_status = "";
  if($row2[1]!="fail"){
    $last_login_status = "success";
  }
  else{
    $last_login_status = "fail";
  }

  //https://imgur.com/#access_token=9d80a5579bea50b9dbdaad0528ee66d08da6ecca&expires_in=315360000&token_type=bearer&refresh_token=587cb7de7f31ccb9b20ab18356dc84928fe30bf3&account_username=chunyinlai1997&account_id=75370421
  // imgur ClientID: 7424eb4ea028890
  // imgur Client secret:	ab16c127c11e69bd00cac7fd20e475bbd6a640bf
  if(isset($_GET["update"])&&$_GET["update"]=="profileimg"){
    header("Location:profile");
  }

  if(isset($_POST['uploadprofile'])){

  	$image = base64_encode(file_get_contents($_FILES['profileimg']['tmp_name']));

  	$options = array('http'=>array(
  		'method'=>"POST",
  		'header'=>"Authorization: Bearer 9d80a5579bea50b9dbdaad0528ee66d08da6ecca\n"."Content-Type: application/x-ww-form-urlencoded",
  		'content'=>$image
  	));

  	$context = stream_context_create($options);

  	$imgurURL = "https://api.imgur.com/3/image";

  	$response = file_get_contents($imgurURL, false, $context);

  	$res = json_decode($response);

  	$imagelink = $res->data->link;

  	mysql_query("UPDATE Users SET profile_img='$imagelink' WHERE id = '$id' ");

    header("Location:profile?update=profileimg");
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
                    <img src="<?php echo $profile_img; ?>" width="48" height="48" alt="User" />
                </div>
                <div class="info-container">
                    <div class="name" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><?php echo $firstname." ".$lastname; ?></div>
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
                                <img src="<?php echo $profile_img; ?>"  width="128" height="128" alt="Profile Image" />
                            </div>
                            <div class="content-area">
                                <a class="btn bg-pink waves-effect m-b-15 collapsed" role="button" data-toggle="collapse" href="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                                    Change Icon
                                </a>
                                <div class="collapse" id="collapseExample">
                                    <div class="well">
                                      <form action="profile" method="POST" enctype="multipart/form-data">
                                      <label for="file">Choose an image to upload</label>
                                      <input id="profileimg" name="profileimg" type="file">
                                      </input>
                                      <input type="submit" class="btn btn-primary" id="uploadprofile" name="uploadprofile" value="Upload" disabled>
                                      </form>
                                    </div>
                                </div>
                                <h3><?php echo $firstname." ".$lastname;?></h3>
                                <p></p>
                                <p>Since <? echo date("d-m-Y", strtotime($create_date)); ?></p>
                            </div>
                        </div>
                        <div class="profile-footer">
                          <ul>
                            <li>
                                <span>Account Status</span>
                                <span><?php echo $status;?></span>
                            </li>
                            <li>
                                <span>Two Factor Authentication</span>
                                <span><?php echo $two_factor;?></span>
                            </li>
                            <li>
                                <span>Last Login</span>
                                <span><?php echo $last_login_time;?></span>

                            </li>
                            <li>
                              <span></span>
                              <span><?php echo $last_login_status;?></span>
                            </li>
                          </ul>
                        </div>
                    </div>

                    <div class="card card-about-me">
                        <div class="header">
                            <h2>My Info</h2>
                        </div>
                        <div class="body">
                            <ul>
                                <li>
                                    <div class="title">
                                        <i class="material-icons">email</i>
                                        Email
                                    </div>
                                    <div class="content">
                                        <? echo $email." ";
                                           if($verified!=0){
                                             echo "<span class='label bg-green'>Verified</span>";
                                           }
                                           else{
                                             echo "<span class='label bg-green'>Not Verified</span>";
                                           }
                                        ?>
                                    </div>
                                </li>
                                <li>
                                    <div class="title">
                                        <i class="material-icons">phone</i>
                                        Phone
                                    </div>
                                    <div class="content">
                                        <? echo $phone." ";?><span class="label bg-green">Verified</span>
                                    </div>
                                </li>
                                <li>
                                    <div class="title">
                                        <i class="material-icons">location_city</i>
                                        Billing Address
                                    </div>
                                    <div class="content">
                                        <?php echo "address"; ?>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-9">
                    <div class="card">
                        <div class="body">
                            <div>
                                <ul class="nav nav-tabs" role="tablist">
                                    <li role="presentation" class="active"><a href="#friend" aria-controls="friend" role="tab" data-toggle="tab">Friends</a></li>
                                    <li role="presentation"><a href="#profile_settings" aria-controls="settings" role="tab" data-toggle="tab">Profile Settings</a></li>
                                    <li role="presentation"><a href="#change_password_settings" aria-controls="settings" role="tab" data-toggle="tab">Change Password</a></li>
                                </ul>

                                <div class="tab-content">
                                    <div role="tabpanel" class="tab-pane fade in active" id="friend">

                                    </div>


                                    <div role="tabpanel" class="tab-pane fade in" id="profile_settings">
                                        <form class="form-horizontal">
                                            <div class="form-group">
                                                <label for="NameSurname" class="col-sm-2 control-label">Name Surname</label>
                                                <div class="col-sm-10">
                                                    <div class="form-line">
                                                        <input type="text" class="form-control" id="NameSurname" name="NameSurname" placeholder="Name Surname" value="Marc K. Hammond" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="Email" class="col-sm-2 control-label">Email</label>
                                                <div class="col-sm-10">
                                                    <div class="form-line">
                                                        <input type="email" class="form-control" id="Email" name="Email" placeholder="Email" value="example@example.com" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="InputExperience" class="col-sm-2 control-label">Experience</label>

                                                <div class="col-sm-10">
                                                    <div class="form-line">
                                                        <textarea class="form-control" id="InputExperience" name="InputExperience" rows="3" placeholder="Experience"></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="InputSkills" class="col-sm-2 control-label">Skills</label>

                                                <div class="col-sm-10">
                                                    <div class="form-line">
                                                        <input type="text" class="form-control" id="InputSkills" name="InputSkills" placeholder="Skills">
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="col-sm-offset-2 col-sm-10">
                                                    <input type="checkbox" id="terms_condition_check" class="chk-col-red filled-in" />
                                                    <label for="terms_condition_check">I agree to the <a href="#">terms and conditions</a></label>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class="col-sm-offset-2 col-sm-10">
                                                    <button type="submit" class="btn btn-danger">SUBMIT</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div role="tabpanel" class="tab-pane fade in" id="change_password_settings">
                                        <form class="form-horizontal">
                                            <div class="form-group">
                                                <label for="OldPassword" class="col-sm-3 control-label">Old Password</label>
                                                <div class="col-sm-9">
                                                    <div class="form-line">
                                                        <input type="password" class="form-control" id="OldPassword" name="OldPassword" placeholder="Old Password" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="NewPassword" class="col-sm-3 control-label">New Password</label>
                                                <div class="col-sm-9">
                                                    <div class="form-line">
                                                        <input type="password" class="form-control" id="NewPassword" name="NewPassword" placeholder="New Password" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label for="NewPasswordConfirm" class="col-sm-3 control-label">New Password (Confirm)</label>
                                                <div class="col-sm-9">
                                                    <div class="form-line">
                                                        <input type="password" class="form-control" id="NewPasswordConfirm" name="NewPasswordConfirm" placeholder="New Password (Confirm)" required>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="form-group">
                                                <div class="col-sm-offset-3 col-sm-9">
                                                    <button type="submit" class="btn btn-danger">SUBMIT</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-9">
                    <div class="card">
                        <div class="body">
                            <div>
                                <ul class="nav nav-tabs" role="tablist">
                                    <li role="presentation" class="active"><a href="#payment_method" aria-controls="payment_method" role="tab" data-toggle="tab">Payment Methods</a></li>
                                    <li role="presentation"><a href="#bank_account" aria-controls="bank_account" role="tab" data-toggle="tab">Bank Account</a></li>
                                    <li role="presentation"><a href="#credit_card" aria-controls="credit_card" role="tab" data-toggle="tab">Credit Card</a></li>
                                </ul>

                                <div class="tab-content">
                                    <div role="tabpanel" class="tab-pane fade in active" id="payment_method">

                                    </div>


                                    <div role="tabpanel" class="tab-pane fade in" id="bank_account">

                                    </div>

                                    <div role="tabpanel" class="tab-pane fade in" id="credit_card">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
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
    });
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
