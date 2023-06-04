<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<title>Profile</title>
	<link rel="stylesheet" href="/cakephp/css/normalize.css">
	<link rel="stylesheet" href="/cakephp/css/all.min.css">
	<link rel="stylesheet" href="/cakephp/css/profile.css">
</head>

<body>

	<div class="header">
		<div class="logo">
			<img src="/cakephp/app/webroot/img/logo.png" alt="logo">
			<h3>Safe Community</h3>
		</div>
		<div class="links">
			<ul class="nav-links">
				<li class="home"><i class="fa-solid fa-house"></i></i>Home</li>
				<li class="books"><i class="fa-solid fa-book-open "></i>Books</li>
				<li class="events"><i class="fa-solid fa-calendar "></i>Events</li>
				<li class="active profile"><i class="fa-solid fa-user "></i>Profile</li>
			</ul>
		</div>
		<!-- <div class="menu">
			<i class="fa-solid fa-bars"></i>
		</div>
		<div class="open-menu">
			Groups
			<i class="fa-solid fa-caret-down"></i>
		</div>
		<ul class="groups-list">
			<li><a href="" class="add-group">+ Add a group</a></li>
		</ul> -->
	</div>

	<div class="container">

		<div class="main-information">
			<div id="profile-image" class="profile-image">
				<!-- <img src="" alt=""> -->
			</div>
			<h2 id="username"></h2>
			<p id="birth-date"></p>
		</div>

		<div class="other-information">

			<div class="info major">
				<div class="title">Major</div>
				<p id="user-major" class="info"></p>
				<i class="fa-solid fa-plus"></i>
			</div>
			<div class="statistics info">
				<div class="title">Statistics</div>
				<p id="num-posts"></p>
				<p id="num-likes"></p>
				<p id="likes-ratio"></p>
			</div>
			<div id="groups-list" class="info">
				<div class="title">Group List</div>
			</div>
			<div id="skills-list" class="info">
				<div class="title">Skills</div>
				<i class="fa-solid fa-plus"></i>

			</div>

		</div>
		<div class="buttons">
			<div class="upload-photo">
				Edit Photo
			</div>
		</div>

		<script>
			let profileData;
			// Profile data
			let currentUser = <?php echo $this->session->read('User.id'); ?>;
			let choosenID = + localStorage['choosen-profile'];
			let obj = new Object();
			obj.id = choosenID;
				let req = new XMLHttpRequest();
				req.open('POST','/cakephp/users/profile');
				req.onreadystatechange =function(){
					if(this.readyState===4 && this.status===200){

						profileData =JSON.parse(this.responseText);
							//Generate profile elements
			document.querySelector(".main-information h2").textContent = profileData[3]['User']['username'];

			const profileImage = document.createElement("img");
			profileImage.src = `/cakephp/app/webroot/img/${profileData[3]['User']['pic_path']}`;
			profileImage.alt = "Profile Image";
			document.getElementById("profile-image").appendChild(profileImage);

			document.getElementById("user-major").textContent =
				profileData[3]['User']['major'];
			document.getElementById("birth-date").textContent =
				"email: " + (profileData[3]['User']['email'] || 'Not Found');
			document.getElementById("num-posts").textContent =
				"Number of Posts: " + (profileData[4] || 'No Posts Found');
			document.getElementById("num-likes").textContent =
				"Number of Likes: " + profileData[1];

			const likesRatio = profileData.numLikes / profileData.numPosts;
			document.getElementById("likes-ratio").textContent =
				"Likes Ratio: " + profileData[5];

			const groupsList = document.getElementById("groups-list");
			for(gr in profileData[2]){
				const listItem = document.createElement("p");
				listItem.textContent = profileData[2][gr];
				groupsList.appendChild(listItem);
			}

			const skillsList = document.getElementById("skills-list");
			if(profileData[0].length!==0)
			for(let i=0 ;i<profileData.length;i++){
				const listItem = document.createElement("p");
				listItem.textContent = profileData[0][i]['UserList']['skill_name'];
				skillsList.appendChild(listItem);
			}

			else
				skillsList.append('No skills found');
					}
			}
				req.send(JSON.stringify(obj));


			// const profileData = {
			// 	username: "John Doe",
			// 	profileImage: "28.jpg",
			// 	major: "Computer Science",
			// 	birthDate: "01/May/1990",
			// 	numPosts: 10,
			// 	numLikes: 100,
			// 	groups: ["Computer Science", "Software Engineering"],
			// 	skills: ["Web Development", "Soft Skills"],
			// };

			if(choosenID === currentUser){
				document.querySelector('.buttons .upload-photo').style.display ='block';
				document.querySelector('.buttons .upload-photo').addEventListener('click',(e)=>{
					let overlay = document.createElement('div');
					overlay.classList.add('popup-overlay');
					document.body.append(overlay);
					let formContent = document.createElement('div');
					formContent.classList.add('photo-content');
					document.body.append(formContent);
					let confirmUpload =document.createElement('div');
					confirmUpload.classList.add('confirm-uplaod');
					confirmUpload.append('Upload')
					let cancelUpload =document.createElement('div');
					cancelUpload.classList.add('cancel-uplaod');
					cancelUpload.append('Cancel');
					let buttonsCont =document.createElement('div');
					buttonsCont.classList.add('upload-buttons');
					buttonsCont.append(cancelUpload);
					buttonsCont.append(confirmUpload);

					let form =document.createElement('form');
					form.enctype = 'multipart/form-data';
					let input =document.createElement('input');
					input.type='file';
					input.accept = 'image/jpg,image/png,image/jpeg';
					input.name = 'pic_path';
					input.id = 'pic_path';

					form.append(input);
					formContent.append(form);
					formContent.append(buttonsCont);
				})
				document.querySelectorAll('.info i').forEach(el=>{
					el.style.display='block';
					el.addEventListener('click',(e)=>{

						if(e.target.parentElement.classList.contains('major')){
					let overlay = document.createElement('div');
					overlay.classList.add('popup-overlay');
					document.body.append(overlay);
					let majorContent = document.createElement('div');
					majorContent.classList.add('major-content');
					document.body.append(majorContent);
					let confirmMajor =document.createElement('div');
					confirmMajor.classList.add('confirm-major');
					confirmMajor.append('Upload')
					let cancelMajor =document.createElement('div');
					cancelMajor.classList.add('cancel-major');
					cancelMajor.append('Cancel');
					let buttonsCont =document.createElement('div');
					buttonsCont.classList.add('major-buttons');
					buttonsCont.append(cancelMajor);
					buttonsCont.append(confirmMajor);
					let input =document.createElement('input');
					input.type='text';
					input.placeholder = 'Major';
					majorContent.append(input);
					majorContent.append(cancelMajor);
					majorContent.append(confirmMajor);
						}
						else{
						let overlay = document.createElement('div');
					overlay.classList.add('popup-overlay');
					document.body.append(overlay);
					let skillContent = document.createElement('div');
					skillContent.classList.add('skill-content');
					document.body.append(skillContent);
					let confirmSkill =document.createElement('div');
					confirmSkill.classList.add('confirm-skill');
					confirmSkill.append('Upload')
					let cancelSkill =document.createElement('div');
					cancelSkill.classList.add('cancel-skill');
					cancelSkill.append('Cancel');
					let buttonsCont =document.createElement('div');
					buttonsCont.classList.add('skill-buttons');
					buttonsCont.append(cancelSkill);
					buttonsCont.append(confirmSkill);
					let input =document.createElement('input');
					input.type='text';
					input.placeholder = 'Skill';
					skillContent.append(input);
					skillContent.append(cancelSkill);
					skillContent.append(confirmSkill);
						}
					})
				})
			}

			//Elements Not Exist
			document.addEventListener('click',(e)=>{
				if(e.target.classList.contains('cancel-uplaod')){
					e.target.parentElement.parentElement.remove();
					document.querySelector('.popup-overlay').remove();
				}
				else if(e.target.classList.contains('confirm-uplaod')){
					let fileInput = e.target.parentElement.parentElement.querySelector('form input[type="file"]');
					let formData = new FormData();
					formData.append('file',fileInput.files[0]);
					let req = new XMLHttpRequest();
					req.open('POST','/cakephp/users/addpic');
					req.onreadystatechange = function(){
						if(this.readyState===4 && this.status===200){
							window.location.href = '/cakephp/users/profile';
						}
					}
					req.send(formData);
				}
				else if(e.target.classList.contains('cancel-major') || e.target.classList.contains('cancel-skill')){
					e.target.parentElement.remove();
					document.querySelector('.popup-overlay').remove();
				}
				else if(e.target.classList.contains('confirm-major')){
					let text = e.target.parentElement.querySelector('input[type="text"]').value;
					let obj = new Object();
					obj.content = text;
					let req = new XMLHttpRequest();
					req.open('POST','/cakephp/users/addmajor');
					req.onreadystatechange = function(){
						if(this.readyState===4 && this.status===200){
							window.location.href = '/cakephp/users/profile';
						}
					}
					req.send(JSON.stringify(obj));
				}
				else if(e.target.classList.contains('confirm-skill')){
					let text = e.target.parentElement.querySelector('input[type="text"]').value;
					let obj = new Object();
					obj.content = text;
					let req = new XMLHttpRequest();
					req.open('POST','/cakephp/users/addskill');
					req.onreadystatechange = function(){
						if(this.readyState===4 && this.status===200){
							window.location.href = '/cakephp/users/profile';
						}
					}
					req.send(JSON.stringify(obj));
				}
			})

		//Navigate:
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
					window.location.href = '/cakephp/Profiles/index';
				}
				else if (e.currentTarget.classList.contains('home')) {
					window.location.href = '/cakephp/Posts/index';
				}
			});
		});
		</script>
</body>

</html>
