<?php
session_start();
include '../database/connect.php';
function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
// Check if the user is logged in, if not then redirect them to the login page
if (!isset($_SESSION['user_data'])) {
    header("Location: login.php");
    exit();
}

// Check if connection is successful
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Corrected SQL query
$result = $conn->query("SELECT id, fName, lName, username, trophies FROM users ORDER BY lastplayed DESC");
$players = [];
while ($row = $result->fetch_assoc()) {
    $players[] = $row;
}

if ($_SERVER["REQUEST_METHOD"] == "POST"){ 
    $team1score = (int) $_POST['team1score'];
    $team2score = (int) $_POST['team2score'];
    $notes = $_POST['notes'] ? test_input($_POST['notes']) : "";
    $player11 = $_POST['1player1'];
    $player12 = $_POST['1player2'];
    $player13 = $_POST['1player3'];
    $player21 = $_POST['2player1'];
    $player22 = $_POST['2player2'];
    $player23 = $_POST['2player3'];
    if ((($team1score+$team2score)!==9)||(count(array_unique([$player11, $player12, $player13, $player21, $player22, $player23]))!==6)){
        header("Location: addGame.php");
        exit();
    }

    $trophy = $conn->prepare("SELECT id, trophies, wins, losses FROM users WHERE id IN (?, ?, ?, ?, ?, ?)");
    $trophy->bind_param("iiiiii", $player11, $player12, $player13, $player21, $player22, $player23);
    $trophy->execute();
    $result = $trophy->get_result();
    $playersreal = [];
    while ($row = $result->fetch_assoc()) {
        $playersreal[$row['id']] = $row;
    }
    $players=[$player11, $player12, $player13, $player21, $player22, $player23];
    $playersR=[];
    foreach($players as $player){
        $playersR[]=$playersreal[$player];
    }

    
    $filename = "";
    $target_dir = "../images/games/"; // Ensure this folder exists
    $target_file = $target_dir . basename($_FILES["image"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Allow certain file formats
    $allowedTypes = ["jpg", "jpeg", "png", "webp"];
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        $filename = $target_file; // Store file path in the database
    }
    
    $insertStmt = $conn->prepare("INSERT INTO games (recorder, image, notes, 1score, 1player1, 1player1T, 1player2, 1player2T, 1player3, 1player3T, 2score, 2player1, 2player1T, 2player2, 2player2T, 2player3, 2player3T) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $insertStmt->bind_param("isiiiiiiiiiiiiii", $_SESSION['user_data']['id'], $filename, $notes,$team1score, $player11, $playersreal[$player11]['trophies'], $player12, $playersreal[$player12]['trophies'], $player13, $playersreal[$player13]['trophies'], $team2score, $player21, $playersreal[$player21]['trophies'], $player22, $playersreal[$player22]['trophies'], $player23, $playersreal[$player23]['trophies']);        
    if ($insertStmt->execute()) {
        $message = "Sign Up Successful! Redirecting...";
        
        $winTeam=($team1score>$team2score)?0:1;
        $lossTeam=($team1score<$team2score)?0:1;
        $winTeamElo=[];
        for ($i=$winTeam*3; $i<$winTeam*3+3; $i++){
            $winTeamElo[]=$playersR[$i]['trophies'];
        }
        $lossTeamElo=[];
        for ($i=$lossTeam*3; $i<$lossTeam*3+3; $i++){
            $lossTeamElo[]=$playersR[$i]['trophies'];
        }

        $trophychange=(int)(((abs($team1score-$team2score)+1)/4)*((40/pi()*atan((array_sum($lossTeamElo)-array_sum($winTeamElo))/300)+20)));
        for($i=$winTeam*3; $i<$winTeam*3+3; $i++){
            $updateStmt = $conn->prepare("UPDATE users SET trophies = ?, wins = ?, lastplayed = ? WHERE id = ?");
            $newTrophy = $playersR[$i]['trophies'] + $trophychange;
            $newWins = $playersR[$i]['wins'] + 1;
            $playerId = $players[$i];
            $currentDate = date('Y-m-d H:i:s');
            
            $updateStmt->bind_param("iisi", $newTrophy, $newWins, $currentDate, $playerId);
            $updateStmt->execute();
            $updateStmt->close();
            
        }
        for($j=$lossTeam*3; $j<$lossTeam*3+3; $j++){
            $updateStmt = $conn->prepare("UPDATE users SET trophies = ? , losses = ?, lastplayed = ? WHERE id = ?");
            $newTrophy = $playersR[$j]['trophies'] - $trophychange;
            $newLosses = $playersR[$j]['losses'] + 1;
            $playerId = $players[$j];
            $currentDate = date('Y-m-d H:i:s');
            $updateStmt->bind_param("iisi", $newTrophy, $newLosses,$currentDate, $playerId);
            $updateStmt->execute();
            $updateStmt->close();
        }
        header("Location: home.php?" .  $winTeam . $lossTeam . 'oop' . $trophychange);
        exit();
    } else {   
        
    }
    
    $insertStmt->close();    
}

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
                <h2>Add Game</h2>
                <p>Make sure that sets add up to 9 and each player is different or it will not be uploaded</p>
            </header>
            <div class="row">
                <div class="col-12">
                    <section class="box">
                        <form  enctype="multipart/form-data" method="POST">
                            <div class="row gtr-50 gtr-uniform">
                                <div class="col-6 col-12-mobilep">
                                    <h4>Game Image: You must take a picture of the sets and people who played the game for the game to be valid</h4>
                                </div>
                                <div class="col-6 col-12-mobilep">
                                    <input type="file" name="image" id="image" accept=".png, .jpg, .jpeg, .webp" placeholder="Picture of game" capture="environment" required/>
                                </div>
                                <div class="col-6 col-12-mobilep">
                                    <h2>Team 1</h2>
                                </div>
                                <div class="col-6 col-12-mobilep">
                                    <h2>Team 2</h2>
                                </div>
                                <div class="col-6 col-12-mobilep" style="display: flex; align-items: center;">
                                    <h4 style="margin-right: 10px;">Team 1 Sets</h4>
                                    <input type="number" name="team1score" id="team1score" min="0" max="9" style="flex: 1; width: 40%;">
                                </div>
                                <div class="col-6 col-12-mobilep" style="display: flex; align-items: center;">
                                    <h4 style="margin-right: 10px;">Team 2 Sets</h4>
                                    <input type="number" name="team2score" id="team2score" min="0" max="9" style="flex: 1; width: 40%;">
                                </div>
                                <div class="col-6 col-12-mobilep" style="display: flex; align-items: center;">
                                    <h4 style="margin-right: 10px;">Player 1 </h4>
                                    <div>
                                        <select name="1player1" id="1player1" required>
                                            <?php foreach ($players as $row): ?>
                                            <option  value="<?= htmlspecialchars($row["id"])?>"><?= htmlspecialchars($row["username"])."  :  " . htmlspecialchars($row["fName"])." " . htmlspecialchars($row["lName"]) ?> </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 col-12-mobilep" style="display: flex; align-items: center;">
                                    <h4 style="margin-right: 10px;">Player 1 </h4>
                                    <div>
                                        <select name="2player1" id="2player1" required>
                                            <?php foreach ($players as $row): ?>
                                            <option  value="<?= htmlspecialchars($row["id"])?>"><?= htmlspecialchars($row["username"])."  :  " . htmlspecialchars($row["fName"])." " . htmlspecialchars($row["lName"]) ?> </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 col-12-mobilep" style="display: flex; align-items: center;">
                                    <h4 style="margin-right: 10px;">Player 2 </h4>
                                    <div>
                                        <select name="1player2" id="1player2" required>
                                            <?php foreach ($players as $row): ?>
                                            <option  value="<?= htmlspecialchars($row["id"])?>"><?= htmlspecialchars($row["username"])."  :  " . htmlspecialchars($row["fName"])." " . htmlspecialchars($row["lName"]) ?> </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 col-12-mobilep" style="display: flex; align-items: center;">
                                    <h4 style="margin-right: 10px;">Player 2 </h4>
                                    <div>
                                        <select name="2player2" id="2player2" required>
                                            <?php foreach ($players as $row): ?>
                                            <option  value="<?= htmlspecialchars($row["id"])?>"><?= htmlspecialchars($row["username"])."  :  " . htmlspecialchars($row["fName"])." " . htmlspecialchars($row["lName"]) ?> </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 col-12-mobilep" style="display: flex; align-items: center;">
                                    <h4 style="margin-right: 10px;">Player 3 </h4>
                                    <div>
                                        <select name="1player3" id="1player3" required>
                                            <?php foreach ($players as $row): ?>
                                            <option  value="<?= htmlspecialchars($row["id"])?>"><?= htmlspecialchars($row["username"])."  :  " . htmlspecialchars($row["fName"])." " . htmlspecialchars($row["lName"]) ?> </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-6 col-12-mobilep" style="display: flex; align-items: center;">
                                    <h4 style="margin-right: 10px;">Player 3 </h4>
                                    <div>
                                        <select name="2player3" id="2player3" required>
                                            <?php foreach ($players as $row): ?>
                                            <option  value="<?= htmlspecialchars($row["id"])?>"><?= htmlspecialchars($row["username"])."  :  " . htmlspecialchars($row["fName"])." " . htmlspecialchars($row["lName"]) ?> </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <textarea type="text" name="notes" id="notes" placeholder="Enter notes about the game" rows="4" maxlength=1023></textarea>
                                </div>

                                
                                
                                <div class="col-12">
                                    <input type="checkbox" id="terms" name="terms" required>
                                    <label for="terms">I verify that all information I put was correct and mislabeling will result in consequences, up to losing your profile</label>
                                </div>
                                <div class="col-12">
                                    <ul class="actions special">
                                        <li><input type="reset"  class="button alt"/></li>
                                        <li><input type="submit" id="submit" class="button" /></li>
                                    </ul>
                                </div>
                            </div>
                        </form>
                    </section>
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
    <script>
        document.getElementById('submit').addEventListener('submit', function(event) {
            const file = document.getElementById('image').files:inlineRefs{references="&#91;&#123;&quot;type&quot;&#58;&quot;inline_reference&quot;,&quot;start_index&quot;&#58;883,&quot;end_index&quot;&#58;886,&quot;number&quot;&#58;0,&quot;url&quot;&#58;&quot;https&#58;//www.geeksforgeeks.org/what-is-the-limit-file-format-when-using-input-typefile/&quot;,&quot;favicon&quot;&#58;&quot;https&#58;//imgs.search.brave.com/bGE5KD5Za34la_MeOAt7584d1aXRWEQopsXEQyAALPw/rs&#58;fit&#58;32&#58;32&#58;1&#58;0/g&#58;ce/aHR0cDovL2Zhdmlj/b25zLnNlYXJjaC5i/cmF2ZS5jb20vaWNv/bnMvYjBhOGQ3MmNi/ZWE5N2EwMmZjYzA1/ZTI0ZTFhMGUyMTE0/MGM0ZTBmMWZlM2Y2/Yzk2ODMxZTRhYTBi/NDdjYTE0OS93d3cu/Z2Vla3Nmb3JnZWVr/cy5vcmcv&quot;,&quot;snippet&quot;&#58;&quot;We&#32;can&#32;conclude&#32;that&#32;limiting&#32;the&#32;file&#32;formats&#32;in&#32;HTML&#32;requires&#32;only&#32;adding&#32;an&#32;&#32;tag&#32;and&#32;specifying&#32;the&#32;file&#32;format&#32;in&#32;the&#32;accept&#32;attribute&#32;(that&#32;restricts&#32;the&#32;users&#32;from&#32;browsing&#32;and&#32;selecting&#32;the&#32;files)…&quot;&#125;&#93;"};
            const maxSize = 20 * 1024 * 1024; // 20MB
            if (file && file.size > maxSize) {
                alert('File is too large. Please select a file smaller than 20MB.');
                event.preventDefault(); // Prevent form submission
            }
        });
        document.querySelector('form').addEventListener('submit', function(event) {
            const team1score = parseInt(document.getElementById('team1score').value, 10);
            const team2score = parseInt(document.getElementById('team2score').value, 10);
            if (team1score + team2score !== 9) {
                alert('The total score of Team 1 and Team 2 must equal 9.');
                event.preventDefault(); // Prevent form submission
            }
        });

        function myFunction() {
            document.getElementById("myDropdown").classList.toggle("show");
        }

        function filterFunction() {
            var input, filter, ul, li, a, i;
            input = document.getElementById("myInput");
            filter = input.value.toUpperCase();
            div = document.getElementById("myDropdown");
            a = div.getElementsByTagName("li");
            for (i = 0; i < a.length; i++) {
                txtValue = a[i].textContent || a[i].innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                a[i].style.display = "";
                } else {
                a[i].style.display = "none";
                }
            }
        }
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




