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

  if(isset($_GET["id"])&&!empty(isset($_GET["id"]))){
    $fid = $_GET["id"];
    $leftsql = mysql_query("SELECT count(*) FROM Friend WHERE user1 = '$id' AND user2='$fid'");
    $rightsql = mysql_query("SELECT count(*)  FROM Friend WHERE user1 = '$fid' AND user2 = '$id'");
    $leftc = mysql_fetch_array($leftsql,MYSQL_NUM);
    $rightc = mysql_fetch_array($rightsql,MYSQL_NUM);
    if($leftc[0]==1 || $rightc[0]==1 ){
      header("Location:member?id=$fid");
    }
    else{
      mysql_query("INSERT INTO Friend(user1,user2,status) VAlUES('$id','$fid','connected')");
      header("Location:member?id=$fid");
    }
  }
  else{
    header("Location:500");
  }
?>
