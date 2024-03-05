

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voedselbank Maaskantje</title>
    <link rel="icon" type="image/png" href="images/icon.png">
    <link rel="stylesheet" href="styling1.css">
    <link rel="stylesheet" href="navbar1.css">
</head>

<body class="backgroundimage">

    <div class="navbar">
        <div class="navicon">
            <img class="icon" src="images/icon.png" width="45px" height="45px" style="padding: 10px;">
            <h2 class="icon">Maaskantje</h2>
        </div>
        <div class="navitems">
            <a href="index.php">
                <p class="knop">Home</p>
            </a>
            <a href="register.php">
                <p class="knop">Registreren</p>
            </a>
            <a href="login.php">
                <p class="groeneknop">Inloggen</p>
            </a>
        </div>
        <div class="dropdown">
            <div class="linegroup" onclick="dropdownFunction()">
                <div class="line"></div>
                <div class="line"></div>
                <div class="line"></div>
            </div>
            <div id="dropdownItems" class="dropdown-content">
                <a href="index.php">Home</a>
                <a href="register.php">Registreren</a>
                <a href="login.php">Inloggen</a>
            </div>
        </div>
    </div>

    <!-- ----------------------------------------------------------------------------------------------------------- -->

    <div class="center">
        <h1>Welkom bij Voedselbank Maaskantje</h1>
        <h3>Waar iedereen een goede maaltijd verdient</h3>
        <a href="login.php">
            <p class="centerknop">Stel een pakket samen</p>
        </a>
    </div>

    <!-- ----------------------------------------------------------------------------------------------------------- -->

    <footer>
    </footer>
    <script src="z1.js"></script>
</body>