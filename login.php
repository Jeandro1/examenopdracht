<?php
include('db.php');

if(isset($_SESSION['gebruikersnaam'])) {
    header("location:account.php");
}

$message = "";

if (isset($_POST['loginknop'])) {
    $username = $_POST["user"];
    $password = $_POST["pwd"];

    $query = "SELECT * FROM medewerker WHERE gebruikersnaam = ? OR wachtwoord = ?";
    $stmt = $mysqli->prepare($query);

    if (!$stmt->bind_param("ss", $username, $password)) {
        die("Error binding parameters: " . $stmt->error);
    }

    if (!$stmt->execute()) {
        die("Error executing query: " . $stmt->error);
    }

    $result = $stmt->get_result();

    if ($row = mysqli_fetch_assoc($result)) {
        $pwdCheck = password_verify($password, $row['wachtwoord']);
        if($pwdCheck == true) {
            $_SESSION["gebruikersnaam"] = array(
                "idmedewerker" => $row["idmedewerker"],
                "voornaam" => $row["voornaam"],
                "achternaam" => $row["achternaam"],
                "gebruikersnaam" => $row["gebruikersnaam"],
                "wachtwoord" => $row["wachtwoord"],
                "functie" => $row["functie"]
            );
            header("Location: account.php");
            exit();
        } else {
            $message = "Incorrecte gebruikersnaam of wachtwoord";
        }
    } else {
        $message = "Gebruiker niet gevonden";
    }
}

$mysqli->close();
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
    <link rel="stylesheet" href="navbar.css">
</head>

<body>
    <div class="navbar1">
        <img class="navicon" src="images/icon.png" href="index.php">
        <div class="navitems">
            <a href="login.php">
                <p class="groeneknop">Inloggen</p>
            </a>
        </div>
    </div>

    <!-- ----------------------------------------------------------------------------------------------------------- -->

    <h4 class="center">Let op! Deze pagina is alleen voor medewerkers.</h4>

    <div class="forms">
        <form action="" method="post">
            <?php
                if(isset($_POST["loginknop"])){
                    if(empty($_POST["login"]) || empty($_POST["pwd"])){
                        echo "Alle velden moeten worden ingevuld!";
                    }  
                }
            ?>
            <div class="formitem">gebruikersnaam<input type="text" name="user" value=""></div>
            <div class="formitem">Wachtwoord<input type="password" name="pwd" value=""></div>
            <div class="formitem"><input class="groeneknop" type="submit" name="loginknop" value="Log in"></div>
        </form>
    </div>

    <!-- ----------------------------------------------------------------------------------------------------------- -->

    <footer>
    </footer>
</body>
</html>
