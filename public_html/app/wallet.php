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
  $bank_name = $row[10];
  $cardnum = "****".substr(decrypt($row[7]),-4);
  $bankac = "****".substr(decrypt($row[8]),-6);
  $amount = $row[11];
  $alt = "";
  $alt2 = "";

  if(isset($_GET["update"])&&$_GET["update"]=="cc"){
    $alt = "<div class='alert alert-success' role='alert'>Your credit card information has updated successfully.<button type='button' class='close' data-dismiss='alert' aria-label='Close'></div>";
  }
  if(isset($_GET["update"])&&$_GET["update"]=="bank"){
    $alt2 = "<div class='alert alert-success' role='alert'>Your bank account information has updated successfully.<button type='button' class='close' data-dismiss='alert' aria-label='Close'></div>";
  }
  if(isset($_GET["need"])&&$_GET["need"]=="cc"){
    $alt = "<div class='alert alert-warning' role='alert'>Please add your credit card information.<button type='button' class='close' data-dismiss='alert' aria-label='Close'></div>";
  }
  if(isset($_GET["need"])&&$_GET["need"]=="bank"){
    $alt2 = "<div class='alert alert-warning' role='alert'>Please add your bank account information.<button type='button' class='close' data-dismiss='alert' aria-label='Close'></div>";
  }

  if(isset($_POST["cc_submit"])){
    $ccnum = clean($_POST["cc_num"]);
    $exMonth = clean($_POST["exMonth"]);
    $exYear = clean($_POST["exYear"]);
    $cvv =  clean($_POST["cvv"]);
    if($ccnum!=$_POST["cc_num"] || $exMonth!=$_POST["exMonth"] || $exYear!=$_POST["exYear"] || $cvv!=$_POST["cvv"]  ){
      $alt = "<div class='alert alert-danger' role='alert'>Wrong Credit Card Input! Please try again..<button type='button' class='close' data-dismiss='alert' aria-label='Close'></div>";
    }
    else{
      $type = "";
      $visaPattern = "/^(?:4[0-9]{12}(?:[0-9]{3})?)$/";
      $mastPattern = "/^(?:5[1-5][0-9]{14})$/";
      $amexPattern = "/^(?:3[47][0-9]{13})$/";
      $discPattern = "/^(?:6(?:011|5[0-9][0-9])[0-9]{12})$/";
      if(preg_match($visaPattern,$ccnum)){
        $type = "visa";
      }
      if(preg_match($mastPattern,$ccnum)){
        $type = "mastercard";
      }
      if(preg_match($amexPattern,$ccnum)){
        $type = "amercian_express";
      }
      if(preg_match($discPattern,$ccnum)){
        $type = "discover";
      }

      $ccnum = encrypt($ccnum);
      $exMonth = encrypt($exMonth);
      $exYear = encrypt($exYear);
      $cvv =  encrypt($cvv);
      mysql_query("UPDATE Client SET credit_card_type = '$type', credit_card_number = '$ccnum', cc_exp_mo = '$exMonth', cc_exp_yr = '$exMonth', cvv = '$cvv' WHERE user_id = '$id' ");
      header("Location:wallet?update=cc");
    }
  }

  if(isset($_POST["bank_submit"])){
    $bank_ac = clean($_POST["bank_ac"]);
    if($bank_ac!=$_POST["bank_ac"]){
      $alt2 = "<div class='alert alert-danger' role='alert'> Wrong Bank Account Input! Please try again..<button type='button' class='close' data-dismiss='alert' aria-label='Close'></div>";
    }
    else{
      $bank_ac = encrypt($bank_ac);
      $bank_name = $_POST["bank_name"];
      mysql_query("UPDATE Client SET bank_name = '$bank_name', bank_account_number = '$bank_ac' WHERE user_id = '$id' ");
      header("Location:wallet?update=bank");
    }
  }

?>
<html lang="en">
<head>
	<title> My Wallet | Friend Pay</title>
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
                    <li class="active">
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
                  <? echo $alt2;?>
                </div>
                <!--Wallet card-->
                <div class="col-xs-12 col-sm-12">
                  <div class="card card-about-me">
                      <div class="header">
                          <h2>My Wallet</h2>
                      </div>
                      <div class="body">
                          <ul>
                              <li>
                                  <div class="title">
                                      <i class="material-icons"> attach_money </i>
                                      Balance
                                  </div>
                                  <div class="content">
                                      <?php echo $amount." HKD"; ?>
                                  </div>
                              </li>
                              <li>
                                  <div class="title">
                                      <i class="material-icons">account_balance</i>
                                      Bank Account
                                  </div>
                                  <div class="content">
                                    <?php
                                    echo $bank_name." ";
                                    if($bankac!="****"){
                                      echo $bankac;
                                    }
                                    else{
                                      echo "No bank account yet";
                                    }
                                     ?>
                                  </div>
                              </li>
                              <li>
                                  <div class="title">
                                      <i class="material-icons">credit_card</i>
                                      Credit Card
                                  </div>
                                  <div class="content">
                                    <?php
                                    if($cardtype=="visa"){
                                      echo "<i class='fa fa-cc-visa fa-2x'></i> ";
                                    }
                                    else if ($cardtype=="mastercard") {
                                      echo "<i class='fa fa-cc-mastercard fa-2x'></i> ";
                                    }
                                    else if ($cardtype=="amercian_express") {
                                      echo "<i class='fa fa-cc-amex fa-2x'></i> ";
                                    }
                                    else if ($cardtype=="discover") {
                                      echo "<i class='fa fa-cc-discover fa-2x'></i> ";
                                    }

                                    if($cardnum!="****"){
                                      echo $cardnum;
                                    }
                                    else{
                                      echo "No credit card yet";
                                    }
                                     ?>
                                  </div>
                              </li>
                          </ul>
                      </div>
                  </div>
                </div>

                <a href="topup"><div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="demo-color-box bg-indigo">
                        <div class="color-code"></div>
                        <div class="color-name">TOP-UP</div>
                    </div>
                </div></a>

                <a href="cashout"><div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                    <div class="demo-color-box bg-teal">
                        <div class="color-code"></div>
                        <div class="color-name">CASHOUT</div>
                    </div>
                </div></a>
                </div>

                <div class="col-xs-12 col-sm-12">
                    <div class="card">
                        <div class="body">
                          <p class="font-bold col-blue-blue">Update Bank Account</p>
                          <small>This will replace your stored bank account information.</small>
                          <form action="wallet" method="POST" id="cc_form" class="form-horizontal">
                            <div class="form-group">
                      			<label for="bank_name" class="col-sm-2 control-label">Bank Name</label>
                      			  <div class="col-sm-10">
                      			  <div class="form-line">
                      				<!--<input autocomplete="off" type="text" name="bank_name" id="bank_name" value="" class="form-control is-valid" onkeyup="safeName(this)" placeholder="Your first name" autofocus inlength="1" maxlength="255" required/>-->
                              <select id='bank_name' name='bank_name' class='form-control' onchange='checkBank();' onkeyup='checkBank();' >
                                <option value='Bank of China (Hong Kong)'>Bank of China (Hong Kong)</option>
                                <option value='The Bank of East Asia, Limited	'>The Bank of East Asia, Limited	</option>
                                <option value='Chong Hing Bank'>Chong Hing Bank</option>
                                <option value='Citibank (Hong Kong)'>Citibank (Hong Kong)</option>
                                <option value='Dah Sing Bank'>Dah Sing Bank</option>
                                <option value='DBS Bank (Hong Kong)'>DBS Bank (Hong Kong)</option>
                                <option value='Fubon Bank (Hong Kong)'>Fubon Bank (Hong Kong)</option>
                                <option value='Hang Seng Bank'>Hang Seng Bank</option>
                                <option value='Hongkong and Shanghai Banking Corporation'>Hongkong and Shanghai Banking Corporation</option>
                                <option value='Industrial and Commercial Bank of China (Asia)'>Industrial and Commercial Bank of China (Asia)</option>
                                <option value='Nanyang Commercial Bank'>Nanyang Commercial Bank</option>
                                <option value='OCBC Wing Hang Bank'>OCBC Wing Hang Bank</option>
                                <option value='Shanghai Commercial Bank'>Shanghai Commercial Bank</option>
                                <option value='Standard Chartered Hong Kong'>Standard Chartered Hong Kong</option>
                              </select>
                      			  </div>
                      			  <div id="invalid_bank_name" class="invalid-feedback " style="display:none;">
                      			  </div>
                      			</div>
                      		  </div>

                            <div class="form-group">
                            <label for="cvv" class="col-sm-2 control-label">Bank Account Number</label>
                            <div class="col-sm-10">
                              <div class="form-line">
                                <input type="text" name="bank_ac" id="bank_ac" class="form-control" placeholder="Account Number" maxlength='24' onkeyup='BankNumber();' onchange='BankNumber();'  autocomplete="off">
                              </div>
                              <div id="invalid_bank_ac" class="invalid-feedback " style="display:none;">
                              </div>
                            </div>
                            </div>

                            <div class="form-group">
                              <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" id="bank_submit" name="bank_submit" value="bank_submit" class="btn btn-danger" disabled>SUBMIT</button>
                              </div>
                            </div>

                          </form>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12 col-sm-12">
                    <div class="card">
                        <div class="body">
                          <p class="font-bold col-blue-blue">Update Credit Card</p>
                          <small>This will replace your stored credit card information.</small>
                          <form action="wallet" method="POST" id="cc_form" class="form-horizontal">
                            <div class="form-group">
                      			<label for="cc_holder_name" class="col-sm-2 control-label">Holder's Name</label>
                      			  <div class="col-sm-10">
                      			  <div class="form-line">
                      				<input autocomplete="off" type="text" name="cc_holder_name" id="cc_holder_name" value="<?php echo $firstname; ?>" class="form-control is-valid" onkeyup="safeName(this)" placeholder="Your first name" inlength="1" maxlength="255" required/>
                      			  </div>
                      			  <div id="invalid_cc_holder_name" class="invalid-feedback " style="display:none;">
                      			  </div>
                      			</div>
                      		  </div>

                            <div class="form-group">
                      			<label for="cc_num" class="col-sm-2 control-label">Credit Card Number</label>
                      			<div class="col-sm-10">
                      			  <div class="form-line">
                      				  <input type="text" name="cc_num" id="cc_num" class="form-control" placeholder="Ex: 0000 0000 0000 0000" maxlength='20' onkeyup='CardNumber();' onchange='CardNumber();'  autocomplete="off">
                      			  </div>
                      			  <div id="invalid_ccnum" class="invalid-feedback " style="display:none;">
                      			  </div>
                      			</div>
                            <div class="col-sm-2">
                                <div id="cardtype">
                                  <i class='fa fa-cc-visa'></i>
                                  <i class='fa fa-cc-mastercard'></i>
                                  <i class='fa fa-cc-amex'></i>
                                  <i class='fa fa-cc-discover'></i>
                                </div>
                            </div>
                      		  </div>

                            <div class="form-group">
                            <label for="cc_num" class="col-sm-2 control-label">Expire Date</label>
                            <div class="row clearfix">
                              <div class='col-md-4'>
                    						<select id='exMonth' name='exMonth' class='form-control' onchange='check_exp();' onkeyup='check_exp();'>
                    							<option value='1'>01</option>
                    							<option value='2'>02</option>
                    							<option value='3'>03</option>
                    							<option value='4'>04</option>
                    							<option value='5'>05</option>
                    							<option value='6'>06</option>
                    							<option value='7'>07</option>
                    							<option value='8'>08</option>
                    							<option value='9'>09</option>
                    							<option value='10'>10</option>
                    							<option value='11'>11</option>
                    							<option value='12'>12</option>
                    						</select>
                    					</div>
                    					<div class='col-md-4'>
                    						<select id='exYear' name='exYear'  class='form-control' onchange='check_exp();' onkeyup='check_exp();' >
                    							<option value='2018'>2018</option>
                    							<option value='2019'>2019</option>
                    							<option value='2020'>2020</option>
                    							<option value='2021'>2021</option>
                    							<option value='2022'>2022</option>
                    							<option value='2023'>2023</option>
                    							<option value='2024'>2024</option>
                    							<option value='2025'>2025</option>
                    							<option value='2026'>2026</option>
                    							<option value='2027'>2027</option>
                    						</select>
                    					</div>

                            </div>
                            <div class="col-sm-10">
                            <div id="vdate" class="invalid-feedback " style="display:none;">
                            </div>
                            </div>
                          </div>

                          <div class="form-group">
                          <label for="cvv" class="col-sm-2 control-label">CVV</label>
                          <div class="col-sm-10">
                            <div class="form-line">
                              <input type="text" name="cvv" id="cvv" class="form-control" placeholder="CVV" maxlength='3' pattern='\d{3}' onkeyup='checkcvv();' onchange='checkcvv();'  autocomplete="off">
                            </div>
                            <div id="invalid_cvv" class="invalid-feedback " style="display:none;">
                            </div>
                          </div>
                          </div>

                          <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                              <button type="submit" id="cc_submit" name="cc_submit" value="cc_submit" class="btn btn-danger" disabled>SUBMIT</button>
                            </div>
                          </div>

                          </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>
    <script>
    $(document).ready(function(){
      $('#cc_holder_name').keyup(function(){
        var cc_holder_name = $(this).val();
        if(cc_holder_name.length>0){
          $('#invalid_cc_holder_name').css("color","green");
          $('#invalid_cc_holder_name').css("display", "block");
          $('#invalid_cc_holder_name').html("");
          $('#cc_holder_name').removeClass( "is-invalid" ).addClass( "is-valid" );
        }
        else{
          $('#invalid_cc_holder_name').css("color","red");
          $('#invalid_cc_holder_name').css("display", "block");
          $('#invalid_cc_holder_name').html("Invalid first name!");
          $('#cc_holder_name').removeClass( "is-valid" ).addClass( "is-invalid" );
        }
        checkCCsubmit();
      });

    });

    var cvvP = /^([0-9]{3})$/;
    function checkcvv(){
      var cvv  = document.getElementById("cvv").value;
      var isCVV = cvvP.test( cvv ) === true;
      if( cvv.length == 3  && isCVV){
        document.getElementById("cvv").classList.add('is-valid');
        document.getElementById("cvv").classList.remove('is-invalid');
        document.getElementById("invalid_cvv").style.display = "none";
      }
      else{
        document.getElementById("cvv").classList.add('is-invalid');
        document.getElementById("cvv").classList.remove('is-valid');
        document.getElementById("invalid_cvv").innerHTML = "Please input a valid cvv";
        document.getElementById("invalid_cvv").style.display = "block";
        document.getElementById("invalid_cvv").style.color = "red";
      }
      checkCCsubmit();
    }

    var bankP = /^([0-9]{12,24})$/;
    function BankNumber(){
      var bank_ac  = document.getElementById("bank_ac").value;
      var isBankAC = bankP.test( bank_ac ) === true;
      if(isBankAC &&  cvv.length!=0){
        document.getElementById("bank_ac").classList.add('is-valid');
        document.getElementById("bank_ac").classList.remove('is-invalid');
        document.getElementById("invalid_bank_ac").style.display = "none";
      }
      else{
        document.getElementById("bank_ac").classList.add('is-invalid');
        document.getElementById("bank_ac").classList.remove('is-valid');
        document.getElementById("invalid_bank_ac").innerHTML = "Please input a valid account number";
        document.getElementById("invalid_bank_ac").style.display = "block";
        document.getElementById("invalid_bank_ac").style.color = "red";
      }
      checkBanksubmit();
    }

    var visaPattern = /^(?:4[0-9]{12}(?:[0-9]{3})?)$/;
    var mastPattern = /^(?:5[1-5][0-9]{14})$/;
    var amexPattern = /^(?:3[47][0-9]{13})$/;
    var discPattern = /^(?:6(?:011|5[0-9][0-9])[0-9]{12})$/;

    function checkBank(){
      var bank_name = document.getElementById("bank_name").options[e1.selectedIndex].value;
      if(bank_name.length>0){
        document.getElementById("bank_name").classList.add('is-valid');
        document.getElementById("bank_name").classList.remove('is-invalid');
        document.getElementById("invalid_bank_name").style.display = "none";;
      }
      else{
        document.getElementById("bank_name").classList.add('is-invalid');
        document.getElementById("bank_name").classList.remove('is-valid');
        document.getElementById("invalid_bank_name").innerHTML = "Please select a bank name";
        document.getElementById("invalid_bank_name").style.display = "block";
        document.getElementById("invalid_bank_name").style.color = "red";
      }
      checkBanksubmit();
    }

    function CardNumber() {
      var ccNum  = document.getElementById("cc_num").value;
      var isVisa = visaPattern.test( ccNum ) === true;
      var isMast = mastPattern.test( ccNum ) === true;
      var isAmex = amexPattern.test( ccNum ) === true;
      var isDisc = discPattern.test( ccNum ) === true;
      console.log("here");
      if( ccNum.length >=15 && (isVisa || isMast || isAmex || isDisc) ) {
        document.getElementById("cardtype").style.display = "block";
        document.getElementById("invalid_ccnum").style.display = "none";
        document.getElementById("cc_num").classList.add('is-valid');
        document.getElementById("cc_num").classList.remove('is-invalid');
        if( isVisa ) {
          console.log("visa");
          document.getElementById("cardtype").innerHTML = "<i class='fa fa-cc-visa fa-2x'></i>";
        }
        else if( isMast ) {
          console.log("Master");
           document.getElementById("cardtype").innerHTML = "<i class='fa fa-cc-mastercard fa-2x'></i>";
        }
        else if( isAmex ) {
          console.log("Amex");
          document.getElementById("cardtype").innerHTML = "<i class='fa fa-cc-amex fa-2x'></i>";
        }
        else if( isDisc ) {
          console.log("Disc");
          document.getElementById("cardtype").innerHTML = "<i class='fa fa-cc-discover fa-2x'></i>";
        }
        else{
          console.log("no");
          document.getElementById("invalid_ccnum").style.color = "red";
          document.getElementById("invalid_ccnum").style.display = "block";
          document.getElementById("invalid_ccnum").innerHTML = "Invalid credit card type";
          document.getElementById("cardtype").innerHTML = "<i class='fa fa-cc-visa'></i><i class='fa fa-cc-mastercard'></i><i class='fa fa-cc-amex'></i><i class='fa fa-cc-discover'></i>";
        }
      }
      else{
        console.log("no");
        document.getElementById("invalid_ccnum").style.color = "red";
        document.getElementById("invalid_ccnum").style.display = "block";
        document.getElementById("invalid_ccnum").innerHTML = "Invalid credit card number";
        document.getElementById("cardtype").innerHTML = "<i class='fa fa-cc-visa'></i><i class='fa fa-cc-mastercard'></i><i class='fa fa-cc-amex'></i><i class='fa fa-cc-discover'></i>";
      }
      checkCCsubmit();
    }

    function check_exp(){
      var e1 = document.getElementById("exMonth");
      var e2 = document.getElementById("exYear");
      var exM = e1.options[e1.selectedIndex].value;
      var exY = e2.options[e2.selectedIndex].value;
      var today = new Date();
      var someday = new Date();
      var d = new Date();
      var now_month = d.getMonth();
      var now_Year = d.getFullYear();
      document.getElementById("vdate").style.display = "block";
      if ((now_Year>exY)||(now_Year==exY && now_month+1 > exM)) {
        document.getElementById("exMonth").classList.add('is-invalid');
        document.getElementById("exMonth").classList.remove('is-valid');
        document.getElementById("exYear").classList.add('is-invalid');
        document.getElementById("exYear").classList.remove('is-valid');
        document.getElementById("vdate").innerHTML = "Please select a valid expiry date";
        document.getElementById("vdate").style.display = "block";
        document.getElementById("vdate").style.color = "red";
      }
      else{
        document.getElementById("exMonth").classList.add('is-valid');
        document.getElementById("exMonth").classList.remove('is-invalid');
        document.getElementById("exYear").classList.add('is-valid');
        document.getElementById("exYear").classList.remove('is-invalid');
        document.getElementById("vdate").style.display = "none";
      }
      checkCCsubmit();
    }

    function checkCCsubmit(){
      var holdername = document.getElementById("cc_holder_name").classList.contains('is-valid');
      var ccnum = document.getElementById("cc_num").classList.contains('is-valid');
      var exMonth = document.getElementById("exMonth").classList.contains('is-valid');
      var exYear = document.getElementById("exYear").classList.contains('is-valid');
      var cvv = document.getElementById("cvv").classList.contains('is-valid');
      console.log("this",holdername!=false&&ccnum!=false&&exMonth!=false&&exYear!=false&&cvv!=false);
      if(holdername!=false&&ccnum!=false&&exMonth!=false&&exYear!=false&&cvv!=false){
        document.getElementById("cc_submit").disabled = false;
      }
      else{
        document.getElementById("cc_submit").disabled = true;
      }
    }

    function checkBanksubmit(){
      var bankac = document.getElementById("bank_ac").classList.contains('is-valid');
      if(bankac){
        document.getElementById("bank_submit").disabled = false;
      }
      else{
        document.getElementById("bank_submit").disabled = true;
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
