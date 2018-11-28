<?php
  include_once 'config.php';
  include_once 'token.php';

  if(!isloggedin()){
    header("Location:sign-in");
  }
  if(!isVerified() || !isActive()){
    header("Location:account_issue");
  }

?>
<html lang="en">
<head>
	<title> Reset Password | Friend Pay</title>
	<?php include 'head-info.php';?>
</head>
<?php
  $cook =  $_COOKIE['SNID_'];
  $link = "sign-out?logout=".$cook;
?>

<html lang="en">
<head>
	<title> Reset Password | Friend Pay</title>
	<?php include 'head-info.php';?>
</head>
<a href="<?php echo $link;?>"><button class="btn btn-success waves-effect">SIGN OUT</button></a>
