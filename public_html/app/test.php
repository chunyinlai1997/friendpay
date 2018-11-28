<?php
  include_once 'config.php';
  include_once 'token.php';


  echo $_POST["enc"]." ".$_POST["uid"];










  //echo clean('prologic338@gmail.com');
  /*
  require_once 'googleLib/GoogleAuthenticator.php';
  $ga = new GoogleAuthenticator();
  $secretGoogleAuth = $ga->createSecret(); //This function will create unique 16 digit secret key

  echo $secret;

  $sql = mysql_query("SELECT COUNT(Login_Logging.status) FROM Login_Logging, Users WHERE Login_Logging.user_id = Users.id AND Users.email = 'prologic338@gmail.com' AND Login_Logging.status = 'fail' AND Login_Logging.date_time > NOW() - INTERVAL 5 MINUTE  ");
  $row= mysql_fetch_array($sql,MYSQL_NUM);
  $failin5mins =  $row[0];
  echo $failin5mins;

  $sql = "SELECT * FROM Users";
  $r = mysql_query($sql);
  while ($row = mysql_fetch_row($r)) {
    print_r($row);
    echo "<br>";
  }
*/
  /*
  echo $_SERVER['HTTP_USER_AGENT'];

  echo php_uname();

  */
  //mysql_query("UPDATE Users SET email='prologic338@gmail.com' WHERE id = 2 ");
  //$id = isloggedin();
  //mysql_query("ALTER TABLE Users ADD COLUMN create_date DATETIME AFTER join_date");
  //$sql = "SHOW COLUMNS FROM Users";
  //$result = mysql_query($sql);
  //while($row = mysql_fetch_array($result)){
  //    echo $row['Field']."<br>";
  //}



  /*
  $sql2 = "SELECT verified FROM Users, Client WHERE Client.user_id = Users.id ";
  $r = mysql_query($sql2);
  while ($row = mysql_fetch_row($r)) {
    print_r($row);
  }
  */

?>
