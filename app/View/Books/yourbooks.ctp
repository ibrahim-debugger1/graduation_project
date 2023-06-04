<!DOCTYPE html>
<html>

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Safe Community</title>
	<link rel="stylesheet" href="/cakephp/css/normalize.css">
	<link rel="stylesheet" href="/cakephp/css/all.min.css">
	<link rel="stylesheet" href="/cakephp/css/book.css">

	<link rel="preconnect" href="https://fonts.googleapis.com">
	<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
	<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@300;400;600;800&display=swap" rel="stylesheet">
</head>
<style>
</style>

<body>
	<div class="header">
		<div class="logo">
			<img src="/cakephp/app/webroot/img/logo.png" alt="logo">
			<h3>Safe Community</h3>
		</div>
		<div class="links">
			<ul class="nav-links">
				<li class="home"><i class="fa-solid fa-house"></i></i>Home</li>
				<li class="active books"><i class="fa-solid fa-book-open "></i>Books</li>
				<li class="events"><i class="fa-solid fa-calendar "></i>Events</li>
				<li class="profile"><i class="fa-solid fa-user "></i>Profile</li>
			</ul>
		</div>
	</div>
</body>
<script>


	//Navigate
	document.querySelectorAll('.header .links .nav-links li').forEach(el => {
		el.addEventListener('click', (e) => {
			console.log(e.target);
			if (e.currentTarget.classList.contains('events')) {
				window.location.href = '/cakephp/Events/index';
			}
			else if (e.currentTarget.classList.contains('books')) {
				window.location.href = '/cakephp/Books/index';
			}
			else if (e.currentTarget.classList.contains('profile')) {
				window.location.href = '/cakephp/Profile/index';
			}
			else if (e.currentTarget.classList.contains('home')) {
				window.location.href = '/cakephp/Posts/index';
			}
		});
	});
</script>

</html>