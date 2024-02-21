<?php
include('z2.php');

if(isset($_SESSION["loggedin"])){
    header("location:pakketten.php");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inloggen</title>
    <link rel="icon" type="image/png" href="images/icon.png">
    <link rel="stylesheet" href="x1.css">
    <link rel="stylesheet" href="y1.css">
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

    <div class="forms">
        <form action="login.php" method="post">
            <div class="formitem">Email-adres<input type="text" name="email" value=""></div>
            <div class="formitem">Wachtwoord<input type="password" name="wachtwoord" value=""></div>
            <div class="formitem"><input class="groeneknop" type="submit" name="login" value="Log in"></div>
        </form>
        <a href="register.php">
            <p>Heb je nog geen account? Registreer je hier!</p>
        </a>
    </div>

    <!-- ----------------------------------------------------------------------------------------------------------- -->

    <footer>
    </footer>
    <script src="z1.js"></script>
</body>