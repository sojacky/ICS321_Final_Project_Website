<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>ScoreBoard</title>
    </head>
    <body>
<?php
    if($_GET['board']) {
        $dbhost = 'localhost';
        $dbuser = 'root';
        $dbpass = 'pass';
        $conn = mysql_connect($dbhost, $dbuser, $dbpass);

        if(!$conn) {
            die('Could not connect: ' . mysql_error());
        }
        $drop = 'ALTER TABLE scoreboard ' . 
            'DROP COLUMN rank';

        $add = 'ALTER TABLE scoreboard ' . 
            'ADD COLUMN rank INT NOT NULL AUTO_INCREMENT PRIMARY KEY';

        $update = 'ALTER TABLE scoreboard ORDER BY score DESC';

        $scores = 'SELECT rank, username, score ' . 
            'FROM scoreboard ' . 
            'ORDER BY score DESC ' . 
            'LIMIT 10';

        $userscores = 'SELECT scoreboard.rank, accounts.username, scoreboard.score ' . 
            'FROM accounts, scoreboard ' .
            'WHERE accounts.username="'.$_GET['user'].'" AND ' . 
            'accounts.username=scoreboard.username ' .
            'ORDER BY score DESC ' . 
            'LIMIT 10'; 

        $db = mysql_select_db('ics321');

        if(!$db) {
            die('Could not connect to database: ' . mysql_error());
        }

        $reorder = mysql_query($drop);

        if(!$reorder) {
            die('Could not alter database: ' . mysql_error());
        }

        $reorder = mysql_query($update);

        if(!$reorder) {
            die('Could not alter database: ' . mysql_error());
        }

        $reorder = mysql_query($add);

        if(!$reorder) {
            die('Could not alter database: ' . mysql_error());
        }

        if($_GET['score'] == 0) {
            $home = "'index.html'";
            echo '<ul>';
            echo '<li><a href="game.php?user='.$_GET['user'].'">Home</a></li>';
            echo '<li>High Scores</li>';
            echo '<li><a href="score.php?board=true&user='.$_GET['user'].'&score=1">My Scores</a></li>';
            echo '</ul>';
            echo '<input type="button" value="Log Out" onclick="window.location = ' . $home . '"><br /><br />';

            $init = mysql_query($scores);

            echo '<table border="1">';
            echo '<caption>HIGH SCORES</caption>';
            echo '<tr><td>Ranking</td><td>Username</td><td>Score</td></tr>';

            while($row = mysql_fetch_array($init)) {
                echo '<tr><td>' . $row['rank'] . "</td><td>" . $row['username'] . 
                    "</td><td>" . $row['score'] . "</td><tr>";
            }

            echo '</table>';
        }

        else if($_GET['score'] == 1) {
            $home = "'index.html'";
            echo '<ul>';
            echo '<li><a href="game.php?user='.$_GET['user'].'">Home</a></li>';
            echo '<li><a href="score.php?board=true&user='.$_GET['user'].'&score=0">High Scores</a></li>';
            echo '<li>My Scores</li>';
            echo '</ul>';
            echo '<input type="button" value="Log Out" onclick="window.location = ' . $home . '"><br /><br />';

            $init = mysql_query($userscores);

            echo '<table border="1">';
            echo '<caption>MY SCORES</caption>';
            echo '<tr><td>Ranking</td><td>Username</td><td>Score</td></tr>';

            while($row = mysql_fetch_array($init)) {
                echo '<tr><td>' . $row['rank'] . "</td><td>" . $row['username'] . 
                    "</td><td>" . $row['score'] . "</td><tr>";
            }

            echo '</table>';
        }

        mysql_close($conn);
    }
?>

    </body>
</html>
