<?php
    
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title>Meteor Fall</title>
		<script type="text/javascript" src="https://code.createjs.com/createjs-2015.05.21.min.js"></script>
    </head>
    <body>
<ul>
    <li>Home</li>
    <li><a href="score.php?board=true&user=<?php echo $_GET['user']?>&score=0">High Scores</a></li>
    <li><a href="score.php?board=true&user=<?php echo $_GET['user']?>&score=1">My Scores</a></li>
</ul>

<input type="button" value="Log Out" onclick="window.location = 'index.html'"><br />

<canvas id="ctx" style="border: 1px solid #000000;"></canvas>

<script type="text/javascript">
    var user = "<?php echo $_GET['user'] ?>";
    var WIDTH = 800;
    var HEIGHT = 600;
    var stage;
    var queue;
    var meteorXPos = Math.floor((Math.random() * 670) + 50);
    var meteorYPos = -25;
    var meteorXPos2 = Math.floor((Math.random() * 670) + 50);
    var meteorYPos2 = -25;
    var meteorYSpeed = Math.floor((Math.random() * 5) + 3);
    var meteorYSpeed2 = Math.floor((Math.random() * 5) + 3);
    var playerSpeed = 20;
    var playerXPos = 350;
    var playerYPos = 500;
    var ballXPos;
    var ballYPos = 500;
    var score = 0;
    var scoreText;
    var numLives = 3;
    var lifeText;
    var gameOver;

    window.onload = function () {
        /*
        *  Set up the Canvas with Width and Height
        *
        */
        var ctx = document.getElementById("ctx").getContext("2d");
        ctx.canvas.width = WIDTH;
        ctx.canvas.height = HEIGHT;
        ctx.canvas.border = 1;
        stage = new createjs.Stage("ctx");

        /*
        *  Set up the Asset Queue and load sounds
        *
        */
        queue = new createjs.LoadQueue(false);
        queue.installPlugin(createjs.Sound);
        queue.on("complete", queueLoaded, this);
        createjs.Sound.alternateExtensions = ["ogg"];

        /*
        *  Create a load manifest for all assets
        *
        */
        queue.loadManifest([
                       { id: 'backgroundImage', src: 'assets/background.jpg' },
                       { id: 'sorakaRight', src: 'assets/sorakaRight.png' },
                       { id: 'sorakaLeft', src: 'assets/sorakaLeft.png' },
                       { id: 'bgm', src: 'assets/snowDown.mp3' },
                       { id: 'meteor', src: 'assets/meteor.png' },
                       { id: 'meteorSpritesheet', src: 'assets/meteorSheet.png' }
                   ]);
        queue.load();

    }

    function queueLoaded(event) {
        // Add background image
        var backgroundImage = new createjs.Bitmap(queue.getResult("backgroundImage"));
        stage.addChild(backgroundImage);

        // Add Score
        scoreText = new createjs.Text("Score: " + score.toString(), "22px Arial", "#FFF");
        scoreText.x = 10;
        scoreText.y = 10;
        stage.addChild(scoreText);

        // Add Lives
        lifeText = new createjs.Text("Life: " + numLives.toString(), "22px Arial", "#FFF");
        lifeText.x = 720;
        lifeText.y = 10;
        stage.addChild(lifeText);

        // Play background music
        createjs.Sound.play("bgm", { loop: -1 });

        // Create meteor spritesheet
        meteorRock = new createjs.SpriteSheet({
            "images": [queue.getResult('meteor')],
            "frames": { "width": 100, "height": 100 },
            "animations": { "rock": [0] }
        });

        explodeMeteor = new createjs.SpriteSheet({
            "images": [queue.getResult('meteorSpritesheet')],
            "frames": { "width": 100, "height": 100 },
            "animations": { "explode": [0, 12, false, 1] }
        });

        // Create SorakaRight spritesheet
        sorakaRightSprite = new createjs.SpriteSheet({
            "images": [queue.getResult('sorakaRight')],
            "frames": { "width": 102, "height": 105 },
            "animations": { "walkRight": [0, 4] }
        });

        // Create SorakaLeft spritesheet
        sorakaLeftSprite = new createjs.SpriteSheet({
            "images": [queue.getResult('sorakaLeft')],
            "frames": { "width": 102, "height": 105 },
            "animations": { "walkLeft": [0, 4] }
        });

        // Create meteor sprite
        createMeteor();
        createMeteor2();

        // Create player
        createPlayerRight();

        // Add ticker
        createjs.Ticker.setFPS(15);
        createjs.Ticker.addEventListener('tick', stage);
        createjs.Ticker.addEventListener('tick', tickEvent);
        createjs.Ticker.addEventListener('tick', playerEvent);

        window.onmousedown = handleMouseDown;
    }

    function createMeteor() {
        dropRock = new createjs.Sprite(meteorRock, "rock");
        dropRock.x = meteorXPos;
        dropRock.y = meteorYPos;
        dropRock.gotoAndPlay("rock");
        stage.addChildAt(dropRock, 1);
    }

    function createMeteor2() {
        dropRock2 = new createjs.Sprite(meteorRock, "rock");
        dropRock2.x = meteorXPos2;
        dropRock2.y = meteorYPos2;
        dropRock2.gotoAndPlay("rock");
        stage.addChildAt(dropRock2, 1);
    }

    function blowUp() {
        boom = new createjs.Sprite(explodeMeteor, "explode");
        boom.x = meteorXPos;
        boom.y = meteorYPos;
        boom.gotoAndPlay("explode");
        stage.addChild(boom);
    }

    function blowUp2() {
        boom2 = new createjs.Sprite(explodeMeteor, "explode");
        boom2.x = meteorXPos2;
        boom2.y = meteorYPos2;
        boom2.gotoAndPlay("explode");
        stage.addChild(boom2);
    }

    function createPlayerRight() {
        moveRight = new createjs.Sprite(sorakaRightSprite, "walkRight");
        moveRight.x = playerXPos;
        moveRight.y = playerYPos;
        moveRight.gotoAndPlay("walkRight");
        stage.addChildAt(moveRight, 1);
    }

    function createPlayerLeft() {
        moveLeft = new createjs.Sprite(sorakaLeftSprite, "walkLeft");
        moveLeft.x = playerXPos;
        moveLeft.y = playerYPos;
        moveLeft.gotoAndPlay("walkLeft");
        stage.addChildAt(moveLeft, 1);
    }

    function tickEvent() {
        // Keep track of meteors
        if (stage.contains(dropRock)) {
            if (meteorYPos < (HEIGHT - 60) && meteorYPos > -30) {
                meteorYPos += meteorYSpeed;
                dropRock.y = meteorYPos;
            }
            else {
                stage.removeChild(dropRock);
                blowUp();
                if (numLives > 0) {
                    numLives -= 1;
                    lifeText.text = "Life: " + numLives.toString();
                    if(numLives == 0) {
                        gameOver = true;
                    }
                    else {
                        gameOver = false;
                    }
                }
                else {
                    if(gameOver == true) {
                        window.location.href = "gameOver.php?points=" + score.toString() + "&gameOver=true&user=" + user;
                        gameOver = false;
                    }
                }
                meteorXPos = Math.floor((Math.random() * 670) + 50);
                meteorYPos = -25;
                meteorYSpeed = Math.floor((Math.random() * 5) + 3);
                var timeToCreate = Math.floor((Math.random() * 3000) + 1);
                setTimeout(createMeteor, timeToCreate);
            }
        }

        if (stage.contains(dropRock2)) {            
            if (meteorYPos2 < (HEIGHT - 60) && meteorYPos2 > -30) {
                meteorYPos2 += meteorYSpeed2;
                dropRock2.y = meteorYPos2;
            }
            else {
                stage.removeChild(dropRock2);
                blowUp2();
                meteorXPos2 = Math.floor((Math.random() * 670) + 50);
                meteorYPos2 = -25;
                if (numLives > 0) {
                    numLives -= 1;
                    lifeText.text = "Life: " + numLives.toString();
                    if(numLives == 0) {
                        gameOver = true;
                    }
                    else {
                        gameOver = false;
                    }
                }
                else {                    
                    if(gameOver == true) {
                        window.location.href = "gameOver.php?points=" + score.toString() + "&gameOver=true&user=" + user;
                        gameOver = false;
                    }
                }
                meteorYSpeed2 = Math.floor((Math.random() * 5) + 3)
                var timeToCreate = Math.floor((Math.random() * 3000) + 1);
                setTimeout(createMeteor2, timeToCreate);
            }
        }
    }

    function playerEvent() {
        if (stage.contains(moveRight) || stage.contains(moveLeft)) {
            if (playerXPos < WIDTH - 80 && playerXPos > -25) {
                playerXPos += playerSpeed;
            }
            else {
                playerSpeed = playerSpeed * (-1);
                playerXPos += playerSpeed;
                if (stage.contains(moveRight)) {
                    stage.removeChild(moveRight);
                    createPlayerLeft();
                }
                else if (stage.contains(moveLeft)) {
                    stage.removeChild(moveLeft);
                    createPlayerRight();
                }
            }
            if (stage.contains(moveRight)) {
                moveRight.x = playerXPos;
            }
            else if (stage.contains(moveLeft)) {
                moveLeft.x = playerXPos;
            }
        }
    }

    function handleMouseDown(event) {
        if(gameOver) {
            window.location.href = "gameOver.php?points=" + score.toString() + "&gameOver=true&user=" + user;
            gameOver = false;
        }
        clickX = Math.round(event.clientX - 40);
        clickY = Math.round(event.clientY - 40);

        spriteX = Math.round(dropRock.x);
        spriteY = Math.round(dropRock.y);
        spriteX2 = Math.round(dropRock2.x);
        spriteY2 = Math.round(dropRock2.y);

        if (clickX <= spriteX + 25 && clickX >= spriteX - 25) {
            if (clickY <= spriteY + 25 && clickY >= spriteY - 25) {
                if (stage.contains(dropRock)) {
                    stage.removeChild(dropRock);
                    blowUp();
                    if(numLives != 0) {
                        score += 100;
                        scoreText.text = "Score: " + score.toString();
                    }
                    meteorXPos = Math.floor((Math.random() * 670) + 50);
                    meteorYPos = -25;
                    meteorYSpeed = Math.floor((Math.random() * 5) + 3);
                    var timeToCreate = Math.floor((Math.random() * 3000) + 1);
                    setTimeout(createMeteor, timeToCreate);
                }
            }
        }
        if (clickX <= spriteX2 + 25 && clickX >= spriteX2 - 25) {
            if (clickY <= spriteY2 + 25 && clickY >= spriteY2 - 25) {
                if (stage.contains(dropRock2)) {
                    stage.removeChild(dropRock2);
                    blowUp2();
                    if(numLives != 0) {
                        score += 100;
                        scoreText.text = "Score: " + score.toString();
                    }
                    meteorXPos2 = Math.floor((Math.random() * 670) + 50);
                    meteorYPos2 = -25;
                    meteorYSpeed2 = Math.floor((Math.random() * 5) + 3);
                    var timeToCreate = Math.floor((Math.random() * 3000) + 1);
                    setTimeout(createMeteor2, timeToCreate);
                }
            }
        }
    }

</script>
    </body>
</html>