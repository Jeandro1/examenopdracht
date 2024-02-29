<?php
include('z2.php');

if(isset($_SESSION["loggedin"])){
    header("location:pakketten.php");
}

//$statement = $conn->prepare("SELECT * FROM users WHERE email = :email AND wachtwoord = :wachtwoord");
//$statement->execute(array('email' => $_POST["email"], 'wachtwoord' => $_POST["wachtwoord"]));
//$count = $statement->rowCount();

$count = 1;
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inloggen</title>
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

    <div class="forms">
        <form action="" method="post">
            <?php
                if(isset($_POST["login"])){
                    if(empty($_POST["email"]) || empty($_POST["wachtwoord"])){
                        echo "Alle velden moeten worden ingevuld!";
                    } 
                    elseif($count > 0){
                        $_SESSION["loggedin"] = true;
                        $_SESSION["email"] = $_POST["email"];
                        echo "Succesvol!";
                        header("location:account.php");
                    }
                    else{
                        $count++;
                        //echo "Verkeerde email of wachtwoord ingevuld!";
                    }    
                }
            ?>
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