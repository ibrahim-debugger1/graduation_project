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
	<div class="menu">
		<div class="side">
				<img src="/cakephp/app/webroot/img/logo.png" alt="">
				<h2>Safe Community</h2>
				<p>Book Exchange</p>
		</div>
		<div class="available">
			<i class="fa-solid fa-magnifying-glass"></i>
			<p>Available Books</p>
		</div>
	</div>
</body>
<script>
	let numberOfRequests = <?php echo $numberOfRequests ;?>;
	let indexButtons = document.createElement('div');
	let currentUser = <?php echo $this->session->read('User.id');?>;
	let currentRole = <?php echo $userRole; ?>;
	let pendingPosts = <?php echo $pendingPosts; ?>;
	let requestedBooks = <?php echo $requested; ?>;

	let currentUserRole = currentRole[0]['User']['role_id'];
	indexButtons.classList.add('index-buttons');
	if(currentUserRole==='2' && currentUser===126){
	let requests = document.createElement('div');
	requests.classList.add('requests');
	requests.append('Requests');
	if(numberOfRequests!==0){
	let notificationSpan =document.createElement('span');
	notificationSpan.append(numberOfRequests);
	requests.append(notificationSpan);
}
	indexButtons.append(requests);
	document.querySelector('.menu').before(indexButtons);
}

	document.querySelector('.menu .available').onclick = function(){window.location.href = '/cakephp/Books/availablebooks'};

	//Elements not exist:
	document.addEventListener('click',(e)=>{
		if(e.target.classList.contains('close-request')){
			document.querySelector('.popup-overlay').remove();
			e.target.parentElement.remove();
		}
		else if(e.target.classList.contains('accept-donate')){
			let bookID = e.target.parentElement.dataset.id;
			let obj = new Object();
			obj.id =bookID;
			let req = new XMLHttpRequest();
			req.open('POST','/cakephp/Books/addapproved');
			req.onreadystatechange =function(){
				if(this.readyState===4 && this.status===200){
					window.location.href = '/cakephp/Books/index';
				}
			}
			req.send(JSON.stringify(obj));
		}
		else if(e.target.classList.contains('reject-donate')){
		let bookID = e.target.parentElement.dataset.id;
			let obj = new Object();
			obj.id =bookID;
			let req = new XMLHttpRequest();
			req.open('POST','/cakephp/Books/adddeclined');
			req.onreadystatechange =function(){
				if(this.readyState===4 && this.status===200){
					window.location.href = '/cakephp/Books/index';
				}
			}
			req.send(JSON.stringify(obj));
		}
		else if(e.target.classList.contains('accept-borrow')){
			let bookID = e.target.parentElement.dataset.id;
			let obj = new Object();
			obj.id = bookID;
			let req = new XMLHttpRequest();
			req.open('POST','/cakephp/Books/adddeclined');
			req.onreadystatechange = function(){
				if(this.status===200 && this.readyState===4){
					window.location.href = '/cakephp/Books/index';
				}
			}
			req.send(JSON.stringify(obj));
		}
		else if(e.target.classList.contains('reject-borrow')){
			let bookID = e.target.parentElement.dataset.id;
			let obj = new Object();
			obj.id = bookID;
			let req = new XMLHttpRequest();
			req.open('POST','/cakephp/Books/addapproved');
			req.onreadystatechange = function(){
				if(this.status===200 && this.readyState===4){
					window.location.href = '/cakephp/Books/availablebooks';
				}
			}
			req.send(JSON.stringify(obj));
		}
	})

	if(document.querySelector('.index-buttons .requests')){
		document.querySelector('.index-buttons  .requests').addEventListener('click',(e)=>{
			generateRequests();
		})
	}
	function generateRequests(){
		let overlay = document.createElement('div');
			overlay.classList.add('popup-overlay');
			document.body.append(overlay);
			let requestsContent =document.createElement('div');
			requestsContent.classList.add('requests-content');
			let p1 =document.createElement('p');
			p1.append('Borrow Requests');
			p1.classList.add('borrowp');
			let p2 =document.createElement('p');
			p2.append('Donate Requests');
			p2.classList.add('donatep');

			let seperatorSpan =document.createElement('span');
			seperatorSpan.classList.add('requests-seperator');
			requestsContent.append(p1);
			requestsContent.append(seperatorSpan);
			requestsContent.append(p2);

			let closeSpan = document.createElement('span');
			closeSpan.classList.add('close-request');
			closeSpan.append('x');
			requestsContent.append(closeSpan);

			let borrowContainer =document.createElement('div');
			borrowContainer.classList.add('borrow-container');
		requestedBooks.forEach(el=>{
				let borrowed =document.createElement('div');
				borrowed.classList.add('borrowed-book');
				let image =document.createElement('img');
				image.src = `/cakephp/app/webroot/img/${el.Book.pic_path}`;
				let name =document.createElement('h3');
				name.append(el.Book.name);
				let username =document.createElement('p');
				let date =document.createElement('div');
				date.classList.add('request-date');
				date.append(el.Book.requested_date);
				username.append(`by: ${el.Book.username}`);
				borrowed.dataset.id = el.Book.id;
				borrowed.append(image);
				borrowed.append(name);
				borrowed.append(date);
				borrowed.append(username);
				let acceptButton =document.createElement('div');
				acceptButton.classList.add('accept-borrow');
				acceptButton.append('Accept');
				let rejectButton =document.createElement('div');
				rejectButton.classList.add('reject-borrow');
				rejectButton.append('Reject');
				borrowed.append(acceptButton);
				borrowed.append(rejectButton);
				borrowContainer.append(borrowed);
			})
			requestsContent.append(borrowContainer);

			let donateContainer =document.createElement('div');
			donateContainer.classList.add('donate-container');

			pendingPosts.forEach(el=>{
				let donated =document.createElement('div');
				donated.classList.add('donated-book');
				let image =document.createElement('img');
				image.src = `/cakephp/app/webroot/img/${el.Book.pic_path}`;
				let name =document.createElement('h3');
				name.append(el.Book.name);
				let username =document.createElement('p');
				username.append(`by: ${el.Book.username}`);
				donated.dataset.id = el.Book.id;
				donated.append(image);
				donated.append(name);
				donated.append(username);
				let acceptButton =document.createElement('div');
				acceptButton.classList.add('accept-donate');
				acceptButton.append('Accept');
				let rejectButton =document.createElement('div');
				rejectButton.classList.add('reject-donate');
				rejectButton.append('Reject');
				donated.append(acceptButton);
				donated.append(rejectButton);
				donateContainer.append(donated);
			})
			requestsContent.append(donateContainer);
			document.body.append(requestsContent);
	}


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
					localStorage.setItem('choosen-profile',sessionID);
					window.location.href = '/cakephp/users/profile';
				}
				else if (e.currentTarget.classList.contains('home')) {
					window.location.href = '/cakephp/Posts/index';
				}
			});
		});
</script>

</html>
