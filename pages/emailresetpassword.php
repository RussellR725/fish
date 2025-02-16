<?php
include '../database/connect.php';


?>


<!DOCTYPE HTML>
<html lang="en">
<head>
    <title>Leaderboard - Alpha</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body class="is-preload">
    <div id="page-wrapper">
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


        <section id="main" class="container">
            <header>
                <h2>Reset password</h2>
                <p>Doesn't work just email me or Max and ill send temp password</p>
            </header>
            <div class="row">
                <div class="col-12">
					<form method="post">
						<div class="row gtr-50 gtr-uniform">
							<div class="col-12">
								<input type="text" name="email" id="email" value="" placeholder="Email" required/>
							</div>
							<div class="col-12">
								<ul class="actions">
									<li><input type="submit" value="Submit" class="primary" /></li>
								</ul>
							</div>
						</div>
					</form>
				</div>
				<div class="col-12">
                    <?php
                    if ($_SERVER["REQUEST_METHOD"] == "POST") {
                        $email = $_POST['email'];
                        $check = mysqli_query($conn, "SELECT * FROM users WHERE email = '$email'");
                        $checkrows = mysqli_num_rows($check); 
                        if($checkrows > 0){ 
                        // we found an account! 
                            $row=mysqli_fetch_assoc($check);
                            $str=$row['password'];
                            $message = "To reset your password, please use this temporary password to change your account: \ntemp password:" . $str; 
                            if(mail($email, 'Reset Password', $message, 'From:'.'pafishcompanion@gmail.com\r\n')){ 
                                echo "<p> A password reset link has been sent to $email </p>";
                            }
                            else{
                                echo "<p>Failed to send email</p>";
                            }
                        } 
                        else {
                            echo "<p>No account found for $email</p>";
                        }
                    }
                    ?>
				</div>
            </div>
        </section>

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
