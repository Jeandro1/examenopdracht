<?php
include('db.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account</title>
    <link rel="icon" type="image/png" href="images/icon.png">
    <link rel="stylesheet" href="styling2.css">
    <link rel="stylesheet" href="navbar.css">
</head>

<body>
    <div class="navbar2">
        <a href="index.php">
            <img class="navicon" src="images/icon.png" href="index.php">
        </a>
        <div class="navitems">
            <?php 
            if($_SESSION['gebruikersnaam']['functie'] == "directie"){
                echo '<a href="medewerkers.php">
                        <p class="knop">Medewerkers</p>
                    </a>';
            }
            if($_SESSION['gebruikersnaam']['functie'] == "directie" || $_SESSION['gebruikersnaam']['functie'] == "magazijn"){
                echo '<a href="leveranciers.php">
                        <p class="knop">Leveranciers</p>
                    </a>
                      <a href="voorraad.php">
                        <p class="knop">Voorraad</p>
                    </a>';
            }
            if($_SESSION['gebruikersnaam']['functie'] == "directie" || $_SESSION['gebruikersnaam']['functie'] == "vrijwilliger"){
                echo '<a href="klanten.php">
                    <p class="knop">Klanten</p>
                     </a>
                   <a href="pakketten.php">
                     <p class="knop">Pakketten</p>
                   </a>';
            }
            if(!empty($_SESSION['gebruikersnaam']['functie'])){
                echo '<a href="account.php">
                <p class="knop">Account</p>
            </a>';
            }

            
            ?>
        </div>
    </div>

    <!-- ----------------------------------------------------------------------------------------------------------- -->

    <div class="gebruikersinvoegen">
            <table>
                <tr>
                    <th>Voornaam</th>
                    <th>Achternaam</th>
                    <th>Gebruikersnaam</th>
                    <th>Wachtwoord</th>
                    <th>Herhaal wachtwoord</th>
                    <th>Functie</th>
                </tr>

    <?php
            if(isset($_SESSION['gebruikersnaam'])) {
                echo "<tr>";
                echo "<td>".$_SESSION['gebruikersnaam']['idmedewerker']."</td>";
                echo "<td>".$_SESSION['gebruikersnaam']['voornaam']."</td>";
                echo "<td>".$_SESSION['gebruikersnaam']['achternaam']."</td>";
                echo "<td>".$_SESSION['gebruikersnaam']['gebruikersnaam']."</td>";
                echo "<td>".$_SESSION['gebruikersnaam']['wachtwoord']."</td>";
                echo "<td>".$_SESSION['gebruikersnaam']['functie']."</td>";
                echo "</tr>";
            } else {
                header("location:login.php");
            }
    ?>
</table>

<div class="aanpassen">
        <form action="" method="post">
            <table>
                <tr>
                    <th>Wachtwoord aanpassen</th>
                    <th><input type="submit" value="Aanpassen" name="aanpassen"></th>
                </tr>
                <tr>
                    <td>Oud wachtwoord:</td>
                    <td><input type="text" name="wachtwoord"></td>
                </tr>
                <tr>
                    <td>Nieuw wachtwoord:</td>
                    <td><input type="text" name="wachtwoord"></td>
                </tr>
                <tr>
                    <td>Herhaal nieuw wachtwoord:</td>
                    <td><input type="text" name="wachtwoord"></td>
                </tr>
        </form>
    </div>
</div>

    <!-- ----------------------------------------------------------------------------------------------------------- -->

    <footer>
    </footer>
</body>
</html>