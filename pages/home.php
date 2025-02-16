<?php
session_start();

// Check if the user is logged in, if
// not then redirect them to the login page
//if (!isset($_SESSION['user_data'])) {
//    header("Location: login.php");
//    exit();
//}
?>

<!DOCTYPE HTML>
<!--
	Alpha by HTML5 UP
	html5up.net | @ajlkn
	Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
-->
<html lang="en">
	<head>
		<title>Fish Findr-Home</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />

		<link rel="stylesheet" href="../assets/css/main.css" />
	</head>
	<body class="landing is-preload">
		<div id="page-wrapper">

			<!-- Header -->
			<header id="header">
				<h1><a href="home.php">Fish Findr</a> for PA</h1>
				<nav id="nav">
					<ul>
						
						<li><a href="home.php"><i class="fa fa-home"></i> Home</a></li>
						<li><a href="addgame.php"><i class="fa fa-plus-circle"></i> Add Game</a></li>
						<li><a href="leaderboard.php"><i class="fa fa-trophy"></i> Leaderboard </a></li>
						
						<li>
							<form method="post" action="search.php" style="display: flex; align-items: center;">
								<input type="text" placeholder="Search User" name="search" style="width: 150px; height: 25px; padding: 5px; margin-right: 5px; border-radius: 4px; border: 1px solid #ccc;">
								<button type="submit" style="padding: 5px 10px; border-radius: 4px; border: none; background-color:#bbb; color: white;">
									<i class="fa fa-search"></i>
								</button>
							</form>
						</li>
						<?php if (!isset($_SESSION['user_data'])): ?>
							<li><a href="login.php" class="button">Login</a></li>
							<li><a href="signup.php" class="button">Sign Up</a></li>
						<?php else: ?>
							<li><a href="logout.php" class="button">Logout</a></li>
							<li>
								<a href="changeprofile.php">
									<img src="<?= htmlspecialchars($_SESSION['user_data']['profilepic'] ?? '../assets/images/default.png'); ?>" 
										alt="Profile Picture" height="32" width="32" 
										style="vertical-align: middle; border-radius: 50%;">
								</a>
							</li>
						<?php endif; ?>
					</ul>
				</nav>
			</header>

			<!-- Banner -->
				<section id="banner">
					<h2>Fish Findr</h2>
					<p>The Unoffical PA Fish Leaderboards</p>
					<ul class="actions special">
						<li><a href="login.php" class="button primary">Sign Up</a></li>
						<li><a href="about.php" class="button">Learn More</a></li>
					</ul>
				</section>

			<!-- Main -->
				<section id="main" class="container">

					<section class="box special">
						<header class="major">
							<h2>Introducing the ultimate website
							<br />
							for fish rankings</h2>
							<p>Are you tired of meaningless debates of "Who is the best fish player?", "Are these teams fair?", and "Is Russell really that hot ?" (Yes). Well look no further cause this website is the solution to all your problems.</p>
						</header>
						<span class="image featured"><img src="images/pic01.jpg" alt="" /></span>
					</section>



					<div class="row">
						<div class="col-6 col-12-narrower">

							<section class="box special">
								<span class="image featured"><img src="images/pic02.jpg" alt="" /></span>
								<h3>Want to learn how to play or get some more tips</h3>
								<p>Look at this great resource made by EV Heck which will explain not just the rules, but how to become almost as good as me. </p>
								<ul class="actions special">
									<li><a href="https://docs.google.com/document/d/1bj6jxErp2NNFEwQ63zHbWmv8Pkha22qR7UI-ZRrCzcY/edit?usp=sharing" class="button alt">Rules/Tips and Tricks</a></li>
								</ul>
							</section>

						</div>
						<div class="col-6 col-12-narrower">

							<section class="box special">
								<span class="image featured"><img src="images/pic03.jpg" alt="" /></span>
								<h3>Game History</h3>
								<p>Want to see the past games played and who is playing on whose team. Look no further than here where you can see all past games played.</p>
								<ul class="actions special">
									<li><a href="history.php" class="button alt">Game History</a></li>
								</ul>
							</section>

						</div>
					</div>

				</section>

			<!-- CTA -->
				<section id="cta">

					<h2>Sign up</h2>
					<p>Sign up and start creating your account today</p>

					<form action="signup.php">
						<div class="row gtr-50 gtr-uniform">
							<div class="col-8 col-12-mobilep">
								<input type="email" name="email" id="email" placeholder="Email Address" />
							</div>
							<div class="col-4 col-12-mobilep">
								<input type="submit" value="Sign Up" class="fit" />
							</div>
						</div>
					</form>

				</section>

			<!-- Footer -->
			<footer id="footer">
					<ul class="icons">
						<li><a href="#" class="icon brands fa-twitter"><span class="label">Twitter</span></a></li>
						<li><a href="#" class="icon brands fa-facebook-f"><span class="label">Facebook</span></a></li>
						<li><a href="#" class="icon brands fa-instagram"><span class="label">Instagram</span></a></li>
						<li><a href="#" class="icon brands fa-github"><span class="label">Github</span></a></li>
						<li><a href="#" class="icon brands fa-dribbble"><span class="label">Dribbble</span></a></li>
						<li><a href="#" class="icon brands fa-google-plus"><span class="label">Google+</span></a></li>
					</ul>
					<ul class="icons">
						<li><a href="home.php" class="fa fa-home"> Home</a></li>
						<li><a href="addgame.php" class="fa fa-plus-circle"> Add Game</a></li>
						<li><a href="https://docs.google.com/document/d/1bj6jxErp2NNFEwQ63zHbWmv8Pkha22qR7UI-ZRrCzcY/edit?usp=sharing" class="fa fa-book"> Rules</a></li>
						<li><a href="profile.php" class="fa fa-user"> Profile</a></li>
						<li><a href="changeprofile.php" class="fa fa-edit"> Change Profile</a></li>
						<li><a href="leaderboard.php" class="fa fa-trophy"> Leaderboard</a></li>
						<li><a href="history.php" class="fa fa-history"> Past Games</a></li>
						<li><a href="about.php" class="fa fa-question-circle"> About</a></li>
					</ul>
					<ul class="copyright">
						<li>&copy; Untitled. All rights reserved.</li><li>Design: <a href="http://html5up.net">HTML5 UP</a></li><li><a href="termsandconditions.php">Terms and Conditions</a></li>
					</ul>
				</footer>

		</div>

		<!-- Scripts -->
			<script src="assets/js/jquery.min.js"></script>
			<script src="assets/js/jquery.dropotron.min.js"></script>
			<script src="assets/js/jquery.scrollex.min.js"></script>
			<script src="assets/js/browser.min.js"></script>
			<script src="assets/js/breakpoints.min.js"></script>
			<script src="assets/js/util.js"></script>
			<script src="assets/js/main.js"></script>

	</body>
</html>