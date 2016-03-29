<?php
    if($_GET['gameOver']) {
        echo '<h1>GAME OVER<h2>';
        echo '<p>You scored: ' . $_GET['points'] . ' points</p>'; 

        $dbhost = 'localhost';
        $dbuser = 'root';
        $dbpass = 'pass';
        $conn = mysql_connect($dbhost, $dbuser, $dbpass);

        if(!$conn) {
            die('Could not connect: ' . mysql_error());
        }

        $sql = 'INSERT INTO scoreboard ' .
            '(username, score) ' .
            'VALUES ("'.$_GET['user'].'", "'.$_GET['points'].'")';

        $db = mysql_select_db('ics321');

        if(!$db) {
            die('Could not connect to database: ' . mysql_error());
        }

        $retval = mysql_query($sql, $conn);

        if(!$retval) {
            die('Could not enter data: ' . mysql_error());
        }

        mysql_close($conn);
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Game Over</title>
    </head>
    <body>
        <ul>
            <li><a href="score.php?board=true&user=<?php echo $_GET['user']?>&score=0">High Scores</a></li>
            <li><a href="score.php?board=true&user=<?php echo $_GET['user']?>&score=1">My Scores</a></li>
        </ul>
    </body>
</html>
