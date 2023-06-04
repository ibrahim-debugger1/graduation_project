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
	<div class="available-buttons">
		<div class="donate">
			Donate
		</div>
	</div>
	<div class="books-gallery">

	</div>
</body>
<script>
	//Variables:
	let booksData = <?php echo $books; ?>;
	let shopIconClass = "fa-solid fa-cart-plus";

		function generateBooks(){
		let booksGallery =document.querySelector('.books-gallery');
		booksData.forEach(el=>{
			let book =document.createElement('div');
			book.classList.add('book');
			book.dataset.id = el.Book.id;
			let bookFront =document.createElement('div');
			bookFront.classList.add('front');
			let availableImage =document.createElement('div');
			availableImage.classList.add('available-image');
			let img = document.createElement('img');
			img.src =`/cakephp/app/webroot/img/${el.Book.pic_path}`;
			img.alt = "book photo";
			availableImage.append(img);
			let name=document.createElement('div');
			name.classList.add('name');
			name.append(el.Book.name)
			bookFront.append(availableImage);
			bookFront.append(name);

			let bookBack =document.createElement('div');
			bookBack.classList.add('back');

			let shopIcon =document.createElement('i');
			shopIcon.classList.add(...shopIconClass.split(' '));
			let requestButton =document.createElement('div');
			requestButton.classList.add('request-book');
			requestButton.append('request');
			bookBack.append(shopIcon);
			bookBack.append(requestButton);


			book.append(bookFront);
			book.append(bookBack);


			booksGallery.append(book);
		})
		document.body.append(booksGallery);
	}

	generateBooks();

	//Elements Not Exist
	document.body.addEventListener('click',(e)=>{
		if(e.target.classList.contains('cancel-donate')){
			document.querySelector('.popup-overlay').remove();
			e.target.parentElement.remove();
		}
		else if(e.target.classList.contains('confirm-donate')){
			let form = new FormData();
			let fileInput = e.target.parentElement.querySelector('form input[type="file"]');
			form.append('file',fileInput.files[0]);
			form.append('name',e.target.parentElement.querySelector('form input[type="text"]').value);
			const xhr = new XMLHttpRequest();
				xhr.open('POST', '/cakephp/Books/add');
				xhr.onreadystatechange = function() {
				if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
					window.location.href = '/cakephp/Books/availablebooks';
				}
			}
			xhr.send(form);
		}
		else if(e.target.classList.contains('request-book')){
			let bookID = e.target.parentElement.parentElement.dataset.id;
			let obj = new Object();
			obj.id= bookID;
			let req = new XMLHttpRequest();
			req.open('POST','/cakephp/Books/makerequest');
			req.onreadystatechange = function(){
				if(this.readyState===4 && this.status===200){
					window.location.href = '/cakephp/Books/index';
				}
			}
			req.send(JSON.stringify(obj));
		}
	})
	if(document.querySelector('.books-gallery .book')){
		document.querySelectorAll('.books-gallery .book').forEach(el=>{
			el.addEventListener('click',(e)=>{
				e.currentTarget.classList.toggle('active');
			})
		})
	}

	document.querySelector('.available-buttons .donate').onclick = function () {
		alert('Handing the Book will be in IT College no.123');
		let popupOverlay = document.createElement('div');
		popupOverlay.classList.add('popup-overlay');
		let donateContent = document.createElement('div');
		let form =document.createElement('form');
		form.enctype='multipart/form-data';
		let imageInput =document.createElement('input');
		imageInput.type='file';
		imageInput.accept='image/jpg';
		imageInput.id='pic_path';
		imageInput.name='pic_path';
		let bookName=document.createElement('input');
		bookName.type = 'text';
		bookName.id= 'book_name';
		bookName.name= 'book_name';
		bookName.placeholder = "Book Name";
		form.append(imageInput);
		form.append(bookName);

		let confirmDonate =document.createElement('div');
		confirmDonate.classList.add('confirm-donate');
		confirmDonate.append('Confirm');
		let cancelDonate =document.createElement('div');
		cancelDonate.classList.add('cancel-donate');
		cancelDonate.append('Cancel');

		donateContent.append(form);
		donateContent.classList.add('donate-content');
		donateContent.append(cancelDonate);
		donateContent.append(confirmDonate);
		document.body.append(' ')
		document.body.append(popupOverlay);
		document.body.append(donateContent);

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
