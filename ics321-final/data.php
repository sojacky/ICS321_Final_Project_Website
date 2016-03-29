<?php    
    if($_POST['submitted']) {
        $user = $_POST['username'];
        $pass = $_POST['password'];
        $login = $_POST['submitted'];

        /* Verify if user logged into account */
        if($login == false || $user == "" || $pass == "") {
            if($login == false) {
                echo '<script type="text/javascript">';
                echo 'alert("ERROR: Could not sign into user account")';
                echo '</script>';

                echo '<script type="text/javascript">';
                echo 'window.location.href="index.html";';
                echo '</script>';
            }
            else if($user == "" && $pass == "") {
                echo '<script type="text/javascript">';
                echo 'alert("ERROR: Please enter a username and password")';       
                echo '</script>';

                echo '<script type="text/javascript">';
                echo 'window.location.href="index.html";';
                echo '</script>';
            }
            else if($user == "" && $pass != "") {
                echo '<script type="text/javascript">';
                echo 'alert("ERROR: Please enter a username")';       
                echo '</script>';

                echo '<script type="text/javascript">';
                echo 'window.location.href="index.html";';
                echo '</script>';
            }
            else if($pass == "" && $user != "") {
                echo '<script type="text/javascript">';
                echo 'alert("ERROR: Please enter a password")';       
                echo '</script>';

                echo '<script type="text/javascript">';
                echo 'window.location.href="index.html";';
                echo '</script>';
            }
        }
        else {
            $dbhost = 'localhost';
            $dbuser = 'root';
            $dbpass = 'pass';
            $conn = mysql_connect($dbhost, $dbuser, $dbpass);

            if(!$conn) {
                die('Could not connect: ' . mysql_error());
            }

            $userexists = 'SELECT username ' .
                'FROM accounts ' .
                'WHERE username = "'.$user.'"';

            $passexists = 'SELECT username, password ' .
                'FROM accounts ' .
                'WHERE username = "'.$user.'" AND password = "'.$pass.'"';

            $db = mysql_select_db('ics321');

            if(!$db) {
                die('Could not connect to database: ' . mysql_error());
            }

            $userresult = mysql_query($userexists, $conn);

            if(!$userresult) {
                die('Could not retrieve data: ' . mysql_error());
            }

            else if($userresult && mysql_num_rows($userresult) > 0) {
                $passresult = mysql_query($passexists, $conn);

                if(!$passresult) {
                    die('Could not retrieve data: ' . mysql_error());
                }

                else if($passresult && mysql_num_rows($passresult) > 0) {
                    echo '<script type="text/javascript">';
                    echo 'alert("You are logged in as '.$user.'")';
                    echo '</script>';

                    echo '<script type="text/javascript">';
                    echo 'window.location.href="game.php?user='.$user.'"';
                    echo '</script>';
                }
                else {
                    echo '<script type="text/javascript">';
                    echo 'alert("Wrong password")';
                    echo '</script>';

                    echo '<script type="text/javascript">';
                    echo 'window.location.href="index.html"';
                    echo '</script>';                    
                }
            }
            else {
                echo '<script type="text/javascript">';
                echo 'alert("Account does not exist")';
                echo '</script>';

                echo '<script type="text/javascript">';
                echo 'window.location.href="index.html";';
                echo '</script>';
            }

            mysql_close($conn);
        }
    }

    else if($_POST['registered']){
        $reg = $_POST['registered'];
        $newuser = $_POST['newuser'];
        $newpass = $_POST['newpass'];
        $confirm = $_POST['confirm'];

        /* Verify if user created an account */
        if($reg == false || $newuser == "" || $newpass == "" || $confirm == "") {
            if($reg == false) {
                echo '<script type="text/javascript">';
                echo 'alert("ERROR: Could not create an account")';
                echo '</script>';

                echo '<script type="text/javascript">';
                echo 'window.location.href="register.html";';
                echo '</script>';
            }
            else if ($newuser == "") {
                echo '<script type="text/javascript">';
                echo 'alert("ERROR: A username is required")';       
                echo '</script>';

                echo '<script type="text/javascript">';
                echo 'window.location.href="register.html";';
                echo '</script>';
            }
            else if($newpass == "" && $newuser != "") {
                echo '<script type="text/javascript">';
                echo 'alert("ERROR: Please enter a password")';       
                echo '</script>';

                echo '<script type="text/javascript">';
                echo 'window.location.href="register.html";';
                echo '</script>';
            }
            else if($confirm == "" && $newpass != "") {
                echo '<script type="text/javascript">';
                echo 'alert("ERROR: Need to confirm password")';       
                echo '</script>';

                echo '<script type="text/javascript">';
                echo 'window.location.href="register.html";';
                echo '</script>';
            }
        }
        else if($confirm != $newpass) {
            echo '<script type="text/javascript">';
            echo 'alert("ERROR: Passwords do not match")';
            echo '</script>';

            echo '<script type="text/javascript">';
            echo 'window.location.href="register.html";';
            echo '</script>';
        }
        else {
            $dbhost = 'localhost';
            $dbuser = 'root';
            $dbpass = 'pass';
            $conn = mysql_connect($dbhost, $dbuser, $dbpass);

            if(!$conn) {
                die('Could not connect: ' . mysql_error());
            }

            $sql = 'INSERT INTO accounts ' .
                '(created_on, username, password) ' .
                'VALUES (NOW(), "'.$newuser.'", "'.$newpass.'")';

            $exists = 'SELECT username ' .
                'FROM accounts ' .
                'WHERE username = "'.$newuser.'"';

            $db = mysql_select_db('ics321');

            if(!$db) {
                die('Could not connect to database: ' . mysql_error());
            }

            $result = mysql_query($exists, $conn);

            if(!$result) {
                die('Could not retrieve data: ' . mysql_error());
            }

            else if($result && mysql_num_rows($result) > 0) {
                echo '<script text/javascript>';
                echo 'alert("Username already exists")';
                echo '</script>';

                echo '<script type="text/javascript">';
                echo 'window.location.href="register.html";';
                echo '</script>';
            }

            else {
                $retval = mysql_query($sql, $conn);

                if(!$retval) {
                    die('Could not enter data: ' . mysql_error());
                }

                echo '<script type="text/javascript">';
                echo 'alert("Welcome '.$newuser.'!")';
                echo '</script>';

                echo '<script type="text/javascript">';
                echo 'window.location.href="game.php?user='.$newuser.'";';
                echo '</script>';
            }

            mysql_close($conn);
        }
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Connecting...</title>
    </head>
    <body>
    </body>
</html>
