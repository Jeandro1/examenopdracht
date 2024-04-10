<?php

session_start();

$hostname = "localhost";
$username = "root";
$password = "";
$dbname = "maaskantje";

$mysqli = new mysqli($hostname, $username, $password, $dbname);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}
function navbar(){
    echo "<div class='navbar2'>
            <a href='index.php'>
               <img class='navicon' src='images/icon.png' href='index.php'>
            </a>
          <div class='navitems'>";
    if($_SESSION['functie'] == "directie"){
        echo '<a href="medewerkers.php">
                <p class="knop">Medewerkers</p>
              </a>';
    }
    if($_SESSION['functie'] == "directie" || $_SESSION['functie'] == "magazijn"){
        echo '<a href="leveranciers.php">
                <p class="knop">Leveranciers</p>
              </a>
              <a href="voorraad.php">
                <p class="knop">Voorraad</p>
            </a>';
            }
            if($_SESSION['functie'] == "directie" || $_SESSION['functie'] == "vrijwilliger"){
                echo '<a href="klanten.php">
                    <p class="knop">Klanten</p>
                     </a>
                    <a href="pakketten.php">
                     <p class="knop">Pakketten</p>
                   </a>
                   <a href="samenstellen.php">
                     <p class="knop">Samenstellen</p>
                   </a>';
            }
            if(!empty($_SESSION['functie'])){
                echo '<a href="account.php">
                <p class="knop">Account</p>
            </a>';
            }
            echo "</div></div>";
}

?>