<?php
include('z2.php');
session_start();

if(!isset($_SESSION["loggedin"])){
    header("location:login.php");
}  

/*
if($_SESSION["rol"] !== 1){
    header("location:account.php");
}
*/
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voorraad</title>
    <link rel="icon" type="image/png" href="images/icon.png">
    <link rel="stylesheet" href="x2.css">
    <link rel="stylesheet" href="y2.css">
</head>

<body>
    <div class="navbar">
        <div class="navicon">
            <img class="icon" src="images/icon.png" width="45px" height="45px" style="padding: 10px;">
            <h2 class="icon">Maaskantje</h2>
        </div>
        <div class="navitems">
            <a href="leveranciers.php">
                <p class="knop">Leveranciers</p>
            </a>
            <a href="gebruikers.php">
                <p class="knop">Gebruikers</p>
            </a>
            <a href="voorraad.php">
                <p class="knop">Voorraad</p>
            </a>
            <a href="pakketten.php">
                <p class="knop">Pakketten</p>
            </a>
            <a href="account.php">
                <p class="knop">Account</p>
            </a>
        </div>
    </div>

    <!-- ----------------------------------------------------------------------------------------------------------- -->

    <p>Voorraad</p>

    <!-- ----------------------------------------------------------------------------------------------------------- -->

    <footer>
    </footer>
</body>