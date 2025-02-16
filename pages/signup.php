<?php
include '../database/connect.php';
function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$message = "";
$toastClass = "";
if ($_SERVER["REQUEST_METHOD"] == "POST"){ 
    $fName = test_input($_POST['fName']);
    $lName = test_input($_POST['lName']);
    $username = test_input($_POST['username']);
    $email = test_input($_POST['email']);
    $userpassword =  password_hash(test_input($_POST['password']), PASSWORD_DEFAULT);
    $bio = isset($_POST['bio']) ? test_input($_POST['bio']) : ""; // Set to null if not provided
    $filename ="../images/users/" . "base_user.png";

    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $target_dir = "../images/users/"; // Ensure this folder exists
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
        // Allow certain file formats
        $allowedTypes = ["jpg", "jpeg", "png", "gif"];
        if (in_array($imageFileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $filename = $target_file; // Store file path in the database
            }
        }
    }
    $checkEmailStmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
    $checkEmailStmt->bind_param("s", $email);
    $checkEmailStmt->execute();
    $checkEmailStmt->store_result();
    
    $checkUsernameStmt = $conn->prepare("SELECT username FROM users WHERE username = ?");
    $checkUsernameStmt->bind_param("s", $username);
    $checkUsernameStmt->execute();
    $checkUsernameStmt->store_result();
    if ($checkEmailStmt->num_rows > 0) {
        $message = "Email ID already exists";
        $toastClass = "#007bff";
    } elseif ($checkUsernameStmt->num_rows > 0) {
        $message = "Username already exists";
        $toastClass = "#007bff";
    } else {
        $insertStmt = $conn->prepare("INSERT INTO users (fName, lName, email, username, password, profilepic, bio) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $insertStmt->bind_param("sssssss", $fName, $lName, $email, $username, $userpassword, $filename, $bio);        
        if ($insertStmt->execute()) {
            $message = "Sign Up Successful! Redirecting...";
            $toastClass = "#28a745"; // Success color
            // Start the session and redirect to the dashboard or home page
            session_start();
            // Fetch the entire row of user data
            $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            $user_data = $result->fetch_assoc();
            // Store user data in session
            $_SESSION['user_data'] = $user_data;
            header("Location: home.php");
            exit();
        } else {
            $message = "Error: " . $conn->error;
            $toastClass = "#dc3545"; // Danger color
        }
        
        $insertStmt->close();
    }
    $checkEmailStmt->close();
    $checkUsernameStmt->close();
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

            
            <section id="main" class="container medium">
                <header>
                    <h2>Sign Up</h2>
                    <p>Sign up to use this website</p>
                </header>
                <div id="message"></div>
                <div class="box">
                    <form method="post" action="" enctype="multipart/form-data">
                        <div class="row gtr-50 gtr-uniform">
                            <div>
                                <input type="file" name="image" id="image" accept=".png, .jpg, .jpeg, .webp" placeholder="Profile Picture" />
                            </div>
                            <div class="col-12">
                                <input type="text" name="fName" id="fName" value="" placeholder="First Name*" required/>
                            </div>
                            <div class="col-12">
                                <input type="text" name="lName" id="lName" value="" placeholder="Last Name*" required/>
                            </div>
                            <div class="col-12">
                                <input type="email" name="email" id="email" value="" placeholder="Email*" required/>
                            </div>
                            <div class="col-12">
                                <input type="text" name="username" id="username" value="" placeholder="Username*" minlength="5" required/>
                            </div>
                            <div class="col-12">
                                <input type="password" name="password" id="password" value="" placeholder="Password*" minlength="5" required/>
                            </div>
                            <div class="col-12">
                                <textarea type="text" name="bio" id="bio" placeholder="Enter your bio" rows="4" maxlength=255></textarea>
                            </div>
                            <div class="col-12">
                                <a href="login.php">Already have an account? =></a>
                            </div>
                            <div class="col-12">
                                <input type="checkbox" id="terms" name="terms" required>
                                <label for="terms">I agree to the <a href="#">terms and conditions</a></label>
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
                                    <li><input type="submit" id="submit" class="button" /></li>
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
                document.getElementById('submit').addEventListener('submit', function(event) {
                    const file = document.getElementById('image').files:inlineRefs{references="&#91;&#123;&quot;type&quot;&#58;&quot;inline_reference&quot;,&quot;start_index&quot;&#58;883,&quot;end_index&quot;&#58;886,&quot;number&quot;&#58;0,&quot;url&quot;&#58;&quot;https&#58;//www.geeksforgeeks.org/what-is-the-limit-file-format-when-using-input-typefile/&quot;,&quot;favicon&quot;&#58;&quot;https&#58;//imgs.search.brave.com/bGE5KD5Za34la_MeOAt7584d1aXRWEQopsXEQyAALPw/rs&#58;fit&#58;32&#58;32&#58;1&#58;0/g&#58;ce/aHR0cDovL2Zhdmlj/b25zLnNlYXJjaC5i/cmF2ZS5jb20vaWNv/bnMvYjBhOGQ3MmNi/ZWE5N2EwMmZjYzA1/ZTI0ZTFhMGUyMTE0/MGM0ZTBmMWZlM2Y2/Yzk2ODMxZTRhYTBi/NDdjYTE0OS93d3cu/Z2Vla3Nmb3JnZWVr/cy5vcmcv&quot;,&quot;snippet&quot;&#58;&quot;We&#32;can&#32;conclude&#32;that&#32;limiting&#32;the&#32;file&#32;formats&#32;in&#32;HTML&#32;requires&#32;only&#32;adding&#32;an&#32;&#32;tag&#32;and&#32;specifying&#32;the&#32;file&#32;format&#32;in&#32;the&#32;accept&#32;attribute&#32;(that&#32;restricts&#32;the&#32;users&#32;from&#32;browsing&#32;and&#32;selecting&#32;the&#32;files)…&quot;&#125;&#93;"};
                    const maxSize = 20 * 1024 * 1024; // 20MB
                    if (file && file.size > maxSize) {
                        alert('File is too large. Please select a file smaller than 20MB.');
                        event.preventDefault(); // Prevent form submission
                    }
                });
            </script>
            <script>
                let toastElList = [].slice.call(document.querySelectorAll('.toast'))
                let toastList = toastElList.map(function (toastEl) {
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