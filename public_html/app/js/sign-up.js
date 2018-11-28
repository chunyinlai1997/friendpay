
	$(document).ready(function () {
		$("#submit" ).click(function(e) {
			if(grecaptcha.getResponse() == "") {
				e.preventDefault();
				alert("Captcha is Missing!");
			}
		});

		$('#inputfirstname').keyup(function(){
			var firstname = $(this).val();
			if(firstname.length>0){
				$('#invalidfirstname').css("color","green");
				$('#invalidfirstname').css("display", "block");
				$('#invalidfirstname').html("");
				$('#inputfirstname').removeClass( "is-invalid" ).addClass( "is-valid" );
			}
			else{
				$('#invalidfirstname').css("color","red");
				$('#invalidfirstname').css("display", "block");
				$('#invalidfirstname').html("Invalid first name!");
				$('#inputfirstname').removeClass( "is-valid" ).addClass( "is-invalid" );
			}
			finalCheck();
		});

		$('#inputlastname').keyup(function(){
			var lastname = $(this).val();
			if(lastname.length>0){
				$('#invalidlastname').css("color","green");
				$('#invalidlastname').css("display", "block");
				$('#invalidlastname').html("");
				$('#inputlastname').removeClass( "is-invalid" ).addClass( "is-valid" );
			}
			else{
				$('#invalidlastname').css("color","red");
				$('#invalidlastname').css("display", "block");
				$('#invalidlastname').html("Invalid last name!");
				$('#inputlastname').removeClass( "is-valid" ).addClass( "is-invalid" );
			}
			finalCheck();
		});

		$('#inputEmail').change(function(){
			var email = $(this).val();
			$.ajax({
				url:"check.php",
				method:"POST",
				data:{checkemail:email},
				dataType:"text",
				success:function(response){
					if(response==0&&checkEmail(email)){
						$('#invalidemail2').css("color","green");
						$('#invalidemail2').css("display", "block");
						$('#invalidemail2').html("Available email address");
						$('#inputEmail').removeClass( "is-invalid" ).addClass( "is-valid" );
					}
					else if(response==0&&!checkEmail(email)){
						$('#invalidemail2').css("color","red");
						$('#invalidemail2').css("display", "block");
						$('#invalidemail2').html("This email address is invalid");
						$('#inputEmail').removeClass( "is-valid" ).addClass( "is-invalid" );
					}
					else if(response==1&&!checkEmail(email)){
						$('#invalidemail2').css("color","red");
						$('#invalidemail2').css("display", "block");
						$('#invalidemail2').html("This email address is invalid");
						$('#inputEmail').removeClass( "is-valid" ).addClass( "is-invalid" );
					}
					else if(response==1&&checkEmail(email)){
						$('#invalidemail2').css("color","red");
						$('#invalidemail2').css("display", "block");
						$('#invalidemail2').html("This email address is already used");
						$('#inputEmail').removeClass( "is-valid" ).addClass( "is-invalid" );
					}
					finalCheck();
				}
			});
		});

		function checkEmail(email){
			var regMail = /^([_a-zA-Z0-9-]+)(\.[_a-zA-Z0-9-]+)*@([a-zA-Z0-9-]+\.)+([a-zA-Z]{2,3})$/;
			if(regMail.test(email)){
					return true;
				}
			else{
				return false;
			}
		}
	});

	function safeName(name){
		name.value = name.value.replace(/[^/ ,a-zA-Z-'\n\r.]+/g, '');
	}

	function validatePass(pass){
		if (pass.search(/[a-z]/) < 0) {
		  document.getElementById("password1").classList.add('is-invalid');
			document.getElementById("password1").classList.remove('is-valid');
			document.getElementById("invalidPassword1").style.display = "block";
			document.getElementById("invalidPassword1").style.color = "red";
			document.getElementById("invalidPassword1").innerHTML = "Your password must contain a lower case letter";
		}
		else if(pass.search(/[A-Z]/) < 0) {
		  document.getElementById("password1").classList.add('is-invalid');
			document.getElementById("password1").classList.remove('is-valid');
			document.getElementById("invalidPassword1").style.display = "block";
			document.getElementById("invalidPassword1").style.color = "red";
			document.getElementById("invalidPassword1").innerHTML = "Your password must contain an upper case letter";
		}
		else  if (pass.search(/[0-9]/) < 0) {
			document.getElementById("password1").classList.add('is-invalid');
			document.getElementById("password1").classList.remove('is-valid');
			document.getElementById("invalidPassword1").style.display = "block";
			document.getElementById("invalidPassword1").style.color = "red";
			document.getElementById("invalidPassword1").innerHTML = "Your password must contain a number";
		}
		else  if (pass.length < 6) {
			document.getElementById("password1").classList.add('is-invalid');
			document.getElementById("password1").classList.remove('is-valid');
			document.getElementById("invalidPassword1").style.display = "block";
			document.getElementById("invalidPassword1").style.color = "red";
			document.getElementById("invalidPassword1").innerHTML = "Your password is too short";
		}
		else{
			document.getElementById("password1").classList.remove('is-invalid');
			document.getElementById("password1").classList.add('is-valid');
			document.getElementById("invalidPassword1").style.display = "block";
			document.getElementById("invalidPassword1").innerHTML = "Valid password";
			document.getElementById("invalidPassword1").style.color = "green";
		}
		finalCheck();
	}

	function checkPass()
	{
		var pass1 = document.getElementById('password1');
	  var pass2 = document.getElementById('password2');
		if(pass1.value != pass2.value){
			document.getElementById("password2").classList.add('is-invalid');
			document.getElementById("password2").classList.remove('is-valid');
			document.getElementById("invalidPassword2").style.display = "block";
			document.getElementById("invalidPassword2").style.color = "red";
			document.getElementById("invalidPassword2").innerHTML = "Password not match";
		}
		else
		{
			document.getElementById("password2").classList.remove('is-invalid');
			document.getElementById("password2").classList.add('is-valid');
			document.getElementById("invalidPassword2").style.display = "block";
			document.getElementById("invalidPassword2").innerHTML = "Password match";
			document.getElementById("invalidPassword2").style.color = "green";
		}
		finalCheck();
	}

	function finalCheck(){
		var first = document.getElementById("inputfirstname").classList.contains('is-valid');
		var last = document.getElementById("inputlastname").classList.contains('is-valid');
		var email =  document.getElementById("inputEmail").classList.contains('is-valid');
		var pass1 =  document.getElementById("password1").classList.contains('is-valid');
		var pass2 =  document.getElementById("password2").classList.contains('is-valid');
		if( first && last && email && pass1 && pass2){
			document.getElementById("submit").disabled = false;
		}
		else{
			document.getElementById("submit").disabled = true;
		}
	}
