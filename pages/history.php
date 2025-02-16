<?php
include '../database/connect.php';
session_start();

// Check if connection is successful
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Corrected SQL query
$result = $conn->query("SELECT * FROM games ORDER BY id DESC");
$players = $conn->query("SELECT id, username, fName, lName, profilepic FROM users");
$ids=[];
while ($row = $players->fetch_assoc()) {
    $ids[$row['id']] = $row;
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
                <h2>History</h2>
                <p>See past games.</p>
            </header>
            <div class="row">
                <div class="col-12">
                    <section class="box">
                        <h3>History</h3>
                        <div class="table-wrapper">
                            <?php if ($result && $result->num_rows > 0): ?>
                                <table>
                                    <tr>
                                        <th style="vertical-align: text-bottom;">Date</th>
                                        <th style="vertical-align: text-bottom;">Team 1</th>
                                        <th style="vertical-align: text-bottom;">Sets 1</th>
                                        <th style="vertical-align: text-bottom;">Sets 2</th>
                                        <th style="vertical-align: text-bottom;">Team 2</th>
                                    </tr>
                                    <?php while ($row = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td>
                                                <?= htmlspecialchars($row["date"]) ?> 
                                            </td>
                                            <td>
                                                <table style="width: 50%;">
                                                    <tr>
                                                        <td>
                                                            <a href="profile.php?profile=<?= htmlspecialchars($ids[$row["2player1"]]['username']) ?>">
                                                                <img src="<?= htmlspecialchars($ids[$row["2player1"]]['profilepic']) ?>" width="32" height="32" style="vertical-align: middle" alt="Profile Picture">
                                                            </a>
                                                        <td>
                                                            <a href="profile.php?profile=<?= htmlspecialchars($ids[$row["2player1"]]['username']) ?>">
                                                                <?= htmlspecialchars($ids[$row["2player1"]]['username']) ?>
                                                            </a>
                                                        </td>
                                                        <td><?= htmlspecialchars($ids[$row["2player1"]]['fName']) . " ". htmlspecialchars($ids[$row["2player1"]]['lName']) ?></td>
                                                        <td><?= htmlspecialchars($row["2player1T"]) ?></td>
                                                        
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <a href="profile.php?profile=<?= htmlspecialchars($ids[$row["2player2"]]['username']) ?>">
                                                                <img src="<?= htmlspecialchars($ids[$row["2player2"]]['profilepic']) ?>" width="32" height="32" style="vertical-align: middle" alt="Profile Picture">
                                                            </a>
                                                        <td>
                                                            <a href="profile.php?profile=<?= htmlspecialchars($ids[$row["2player2"]]['username']) ?>">
                                                                <?= htmlspecialchars($ids[$row["2player2"]]['username']) ?>
                                                            </a>
                                                        </td>
                                                        <td><?= htmlspecialchars($ids[$row["2player2"]]['fName']) . " ". htmlspecialchars($ids[$row["2player2"]]['lName']) ?></td>
                                                        <td><?= htmlspecialchars($row["2player2T"]) ?></td>
                                                        
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <a href="profile.php?profile=<?= htmlspecialchars($ids[$row["2player3"]]['username']) ?>">
                                                                <img src="<?= htmlspecialchars($ids[$row["2player3"]]['profilepic']) ?>" width="32" height="32" style="vertical-align: middle" alt="Profile Picture">
                                                            </a>
                                                        <td>
                                                            <a href="profile.php?profile=<?= htmlspecialchars($ids[$row["2player3"]]['username']) ?>">
                                                                <?= htmlspecialchars($ids[$row["2player3"]]['username']) ?>
                                                            </a>
                                                        </td>
                                                        <td><?= htmlspecialchars($ids[$row["2player3"]]['fName']) . " ". htmlspecialchars($ids[$row["2player3"]]['lName']) ?></td>
                                                        <td><?= htmlspecialchars($row["2player3T"]) ?></td>
                                                        
                                                    </tr> 
                                                </table>
                                                
                                            </td>
                                            <td><?= htmlspecialchars($row["1score"])?></td>
                                            <td><?= htmlspecialchars($row["2score"])?></td>
                                            <td>
                                                <table style="width: 50%;">
                                                    <tr>
                                                        <td>
                                                            <a href="profile.php?profile=<?= htmlspecialchars($ids[$row["1player1"]]['username']) ?>">
                                                                <img src="<?= htmlspecialchars($ids[$row["1player1"]]['profilepic']) ?>" width="32" height="32" style="vertical-align: middle" alt="Profile Picture">
                                                            </a>
                                                        <td>
                                                            <a href="profile.php?profile=<?= htmlspecialchars($ids[$row["1player1"]]['username']) ?>">
                                                                <?= htmlspecialchars($ids[$row["1player1"]]['username']) ?>
                                                            </a>
                                                        </td>
                                                        <td><?= htmlspecialchars($ids[$row["1player1"]]['fName']) . " ". htmlspecialchars($ids[$row["1player1"]]['lName']) ?></td>
                                                        <td><?= htmlspecialchars($row["1player1T"]) ?></td>
                                                        
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <a href="profile.php?profile=<?= htmlspecialchars($ids[$row["1player2"]]['username']) ?>">
                                                                <img src="<?= htmlspecialchars($ids[$row["1player2"]]['profilepic']) ?>" width="32" height="32" style="vertical-align: middle" alt="Profile Picture">
                                                            </a>
                                                        <td>
                                                            <a href="profile.php?profile=<?= htmlspecialchars($ids[$row["1player2"]]['username']) ?>">
                                                                <?= htmlspecialchars($ids[$row["1player2"]]['username']) ?>
                                                            </a>
                                                        </td>
                                                        <td><?= htmlspecialchars($ids[$row["1player2"]]['fName']) . " ". htmlspecialchars($ids[$row["1player2"]]['lName']) ?></td>
                                                        <td><?= htmlspecialchars($row["1player2T"]) ?></td>
                                                        
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <a href="profile.php?profile=<?= htmlspecialchars($ids[$row["1player3"]]['username']) ?>">
                                                                <img src="<?= htmlspecialchars($ids[$row["1player3"]]['profilepic']) ?>" width="32" height="32" style="vertical-align: middle" alt="Profile Picture">
                                                            </a>
                                                        <td>
                                                            <a href="profile.php?profile=<?= htmlspecialchars($ids[$row["1player3"]]['username']) ?>">
                                                                <?= htmlspecialchars($ids[$row["1player3"]]['username']) ?>
                                                            </a>
                                                        </td>
                                                        <td><?= htmlspecialchars($ids[$row["1player3"]]['fName']) . " ". htmlspecialchars($ids[$row["1player3"]]['lName']) ?></td>
                                                        <td><?= htmlspecialchars($row["1player3T"]) ?></td>
                                                        
                                                    </tr> 
                                                </table>
                                                
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </table>
                            <?php else: ?>
                                <p>No results found.</p>
                            <?php endif; ?>
                        </div>
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
    <script src="assets/js/jquery.min.js"></script>
    <script src="assets/js/jquery.dropotron.min.js"></script>
    <script src="assets/js/jquery.scrollex.min.js"></script>
    <script src="assets/js/browser.min.js"></script>
    <script src="assets/js/breakpoints.min.js"></script>
    <script src="assets/js/util.js"></script>
    <script src="assets/js/main.js"></script>
</body>
</html>
