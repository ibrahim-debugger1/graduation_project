<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Safe Community</title>
	<link rel="stylesheet" href="/cakephp/css/master.css">
	<link rel="stylesheet" href="/cakephp/css/normalize.css">
	<link rel="stylesheet" href="/cakephp/css/all.min.css">
	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">
</head>
<style>
	/* Start Variables */
:root {
  --error: "reqiured";
  --emailError: "HU domain!";
  /* Added by Yazan { */
  --section-padding: 100px;
  --main-color: #228e9e;
  --main-background-image: linear-gradient(to bottom, #228e9e, #134850);
  --main-border-radius: 5px;
  /* } */
}

/* End Variables */

/* Start Global Rules */
* {
  -webkit-box-sizing: border-box;
  -moz-box-sizing: border-box;
  box-sizing: border-box;
  padding: 0;
  margin: 0;
}

html {
  scroll-behavior: smooth;
}

body {
  font-family: "Open Sans", sans-serif;
  background-color: #f0f2f5;
}

ul {
  list-style: none;
}

.container {
  padding-left: 15px;
  padding-right: 15px;
  margin-left: auto;
  margin-right: auto;
}

/* Small */
@media (min-width: 768px) {
  .container {
    width: 750px;
  }
}

/* Medium */
@media (min-width: 992px) {
  .container {
    width: 970px;
  }
}

/* Large */
@media (min-width: 1200px) {
  .container {
    width: 1170px;
  }
}

/* End Global Rules */

.login-landing {
  padding-top: var(--section-padding);
  padding-bottom: var(--section-padding);
  min-height: 100vh;
}

.login-landing .container {
  height: 100%;

  display: flex;
  flex-wrap: wrap;
  justify-content: space-around;
  align-items: center;
}

.login-landing .content {
  width: 500px;
  padding: 15px;
  text-align: center;
}

.login-landing .content .logo {
  width: 300px;
}

.login-landing .content p {
  padding-left: 60px;
  font-size: 25px;
  line-height: 1.6;
}

@media (max-width: 767px) {
  .login-landing .content p {
    padding-left: 0;
  }

  .login-landing {
    padding-top: calc(var(--section-padding) / 2);
    padding-bottom: calc(var(--section-padding) / 2);
  }
}

.login-landing .login-section {
  background-color: white;
	transition: .3s;
  position: relative;
  width: 400px;
  padding: 15px;
  border-radius: 10px;
  box-shadow: 5px 5px 15px black;
}

.login-landing .content span {
  background-color: var(--main-color);
  color: white;
  padding: 2px 8px;
  transform: skew(10deg, 10deg);
  border-radius: var(--main-border-radius);
}

.login-landing .login-section form {
  display: flex;
  flex-direction: column;
  justify-content: space-around;
}

input[type="text"],
input[type="email"],
input[type="password"] {
  display: block;
  width: 100%;
  padding: 10px;
  margin-bottom: 20px;
  border-radius: var(--main-border-radius);
  border: 1px solid #ccc;
  font-size: 16px;
}

input[type="submit"] {
  display: block;
  width: 100%;
  background-color: #228e9e;
  background-image: var(--main-background-image);
  color: white;
  padding: 10px 20px;
  border: none;
  border-radius: var(--main-border-radius);
  font-size: 16px;
  cursor: pointer;
  margin: 20px auto 0;
}

button.register {
  display: block;
  width: 100%;
  background-color: #228e9e;
  background-image: var(--main-background-image);
  color: white;
  padding: 10px 20px;
  border: none;
  border-radius: var(--main-border-radius);
  font-size: 16px;
  cursor: pointer;
  margin: 20px auto 0;
}

.login-landing .login-section form span.error {
  display: block;

  background-color: red;
  color: white;

  height: 20px;
  width: 20px;
  border-radius: 50%;
  text-align: center;

  position: absolute;
  z-index: 2;
  right: 10px;
  top: 35%;

  transform: translateY(-50%);
  cursor: pointer;
}

.login-landing .login-section form span.error span.text {
  position: absolute;
  padding: 4px;
  border-radius: 6px;

  top: 25px;
  left: 50%;
  transform: translateX(-50%);
  background-color: #1d1d1d;
  color: white;
  border-radius: var(--main-border-radius);
  width: 200px;
  display: none;
}

.login-landing .login-section form span.error:hover span.text {
  display: block;
}
.login-landing .login-section .error-message{
	color: red;
	text-align: center;
	font-weight: 600;
	margin-top: 10px;
}
</style>


<div class="login-landing">
		<div class="container">
			<div class="content">
				<img src="/cakephp/app/webroot/img/logo.png" alt="" class="logo" />
				<p>
					Connect with your classmates and the Universities around you on
					<br />
					<span> University World </span>
				</p>
			</div>
			<div class="login-section">
				<form action="" method="post">
					<input type="text" name="username" id="username" placeholder="User Name" required>
					<input type="password" name="password" id="password" placeholder="Password" required>
					<input type="submit" value="Login">
				</form>
				<button class="register">Sign Up</button>
			</div>
		</div>
	</div>

	<script>
		document.querySelector('.login-landing .login-section .register').onclick = function () {
			window.location.href = 'register';
		}

let ok = false;
let count = 0;
document.querySelectorAll('.login-landing .login-section form input').forEach((el) => {
	el.addEventListener('blur', (e) => {
		if (e.target.name === 'username') {
			if (/[\s+<+>+&+\*+]/gi.test(e.target.value) && count === 0) {
				count++;
				createError(e, `Do not use * , & , | , or space`);
				ok = false;
			}
			else
				ok = true;
		}

	});
	el.addEventListener('click', (e) => {
		if (e.target.type === 'submit') {
			e.preventDefault();
			let fields = e.target.parentElement.querySelectorAll(`input:not([type="submit"])`);
			if ((fields[0].value === '' || fields[1].value === '') && count === 0) {
				count++;
				createError(e, `Fill All Fields`);
				ok = false;
			}
			else if (ok) {
				let data = new Object();
				document.querySelectorAll('.login-landing .login-section form input:not([type="submit"])').forEach((el)=>{
					data[`${el.name}`]= el.value;
				})
				let req = new XMLHttpRequest();
				req.open('POST','/cakephp/users/login');
				req.setRequestHeader("Content-Type", "application/json");
				req.onreadystatechange = function(){
					if(req.status===200 && req.readyState===4){
						if(req.responseText==='1')
						window.location.href = '/cakephp/Posts/index';
						else
						alert('error');
				}
				else{
					console.log(Error(req.status));
				}
				}
				req.send(JSON.stringify(data));
			}
		}
	});
});

function createError(e, message) {
	let error = document.createElement('div');
	error.classList.add('error-message');
	error.append(`${message}`);
	e.currentTarget.parentElement.parentElement.append(error);
	setTimeout(() => {
		error.remove();
		count = 0;
	}, 3000);
}



	</script>
</body>

</html>
