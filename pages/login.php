<?php
include '../database/connect.php';

$message = "";
$toastClass = "";
function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email_username = test_input($_POST['email_username']);
    $password = test_input($_POST['password']);

    // Prepare and execute
    $stmt = $conn->prepare("SELECT password FROM users WHERE email = ? OR username = ?");
    $stmt->bind_param("ss", $email_username, $email_username); // Bind the parameter twice
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows >0) {
        $stmt->bind_result($db_password);
        $stmt->fetch();
        
        if (password_verify($password, $db_password) || $db_password == $password) {
            $message = "Login successful";
            $toastClass = "bg-success";
            // Start the session and redirect to the dashboard or home page
            session_start();
            // Fetch the entire row of user data
            $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? OR username = ?");
            $stmt->bind_param("ss", $email_username, $email_username);
            $stmt->execute();
            $result = $stmt->get_result();
            $user_data = $result->fetch_assoc();

            // Store user data in session
            $_SESSION['user_data'] = $user_data;
            
            header("Location: home.php");
            exit();
        } else {
            $message = "Wrong Password";
            $toastClass = "bg-danger";
        }
    } else {
        $message = "Email/Username not found";
        $toastClass = "bg-warning";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE HTML>
<!--
	Alpha by HTML5 UP
	html5up.net | @ajlkn
	Free for personal and commercial use under the CCA 3.0 license (html5up.net/license)
-->
<html lang="en">
	<head>
		<title>Phillips Academy Unoffical Fish Companion</title>
		<meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<link rel="stylesheet" href="../assets/css/main.css" />
	</head>
	<body class="is-preload">
        <div>
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

            <section id="main" class="container medium">
                <header>
                    <h2>Login</h2>
                    <p>Login to use this website</p>
                </header>
                <div class="box">
                    <form method="post">
                        <div class="row gtr-50 gtr-uniform">
                            <div class="col-12">
                                <input type="text" name="email_username" id="email_username" value="" placeholder="Email or Username" required/>
                            </div>
                            <div class="col-12">
                                <input type="password" name="password" id="password" value="" placeholder="Password*" required/>
                            </div>
                            <div>
                                <a href="emailresetpassword.php">Forgot password? =></a>
                            </div>
                            <div>
                                <a href="signup.php">Don't have an account? =></a>
                            </div>
                            <?php if ($message): ?>
                                <div class="toast align-items-center border-0 col-12" role="alert" aria-live="assertive" aria-atomic="true">
                                    <div class="d-flex col-12">
                                        <div class="toast-body col-12" style="color: <?php echo $toastClass; ?>;">
                                            <?php echo $message; ?>
                                        </div>
                                        <button type="button" class="btn-close me-2 m-auto" 
                                            data-bs-dismiss="toast"
                                            aria-label="Close"></button>
                                    </div>
                                </div>
                            <?php endif; ?>
                            <div class="col-12">
                                <ul class="actions special">
                                    <li><input type="reset"  class="button alt"/></li>
                                    <li><input type="submit" class="button"/></li>
                                </ul>
                            </div>
                        </div>
                    </form>
                </div>
            </section>

        </div>
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
		<!-- Scripts -->
            <script>
                var toastElList = [].slice.call(document.querySelectorAll('.toast'))
                var toastList = toastElList.map(function (toastEl) {
                    return new bootstrap.Toast(toastEl, { delay: 3000 });
                });
                toastList.forEach(toast => toast.show());
            </script>
			<script src="assets/js/jquery.min.js"></script>
			<script src="assets/js/jquery.dropotron.min.js"></script>
			<script src="assets/js/jquery.scrollex.min.js"></script>
			<script src="assets/js/browser.min.js"></script>
			<script src="assets/js/breakpoints.min.js"></script>
			<script src="assets/js/util.js"></script>
			<script src="assets/js/main.js"></script>

	</body>
</html>