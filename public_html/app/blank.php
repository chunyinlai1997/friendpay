<html lang="en">
<head>
	<title>Register Successful | Friend Pay</title>
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
                            Sign Up Completed
                        </h2>
                    </div>
                    <div class="body">
                      <div class="row justify-content-center">
                        <div class="col-sm-11 col-md-8 col-6">
                           <h5><font style="color:black; font-size:1em;">Thank you for the registration!</h5>
                           A verification email has sent to your email adress (<?php echo $_POST['email'] ?>), you have to activate your account with the verification link in 24 hours.</font>
                           <br>
                           <hr>
                           <a href="../"><button class="btn btn-info waves-effect">Back To Home</button></a>
                        </div>
                      </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

	   <?php include 'footer-info.php';?>
</body>
</html>
