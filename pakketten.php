<?php

include('db.php');

if(!isset($_SESSION['gebruikersnaam'])) {
    header("location:login.php");
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "maaskantje";

// Verbinding maken met de database
$conn = new mysqli($servername, $username, $password, $dbname);

// Controleren op fouten bij de verbinding
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['submit'])) {
        // Controleren of alle vereiste velden zijn ingevuld
        if (isset($_POST['pakket_idpakket'], $_POST['product_idproduct'], $_POST['aantal'])) {
            $pakket_idpakket = $_POST['pakket_idpakket'];
            $product_idproduct = $_POST['product_idproduct'];
            $aantal = $_POST['aantal'];
            $selectedproducts = isset($_POST['product']) ? $_POST['product'] : [];

            // Voorbereiden van de query om gegevens in te voegen
            $stmt = $conn->prepare("INSERT INTO pakket_has_product (pakket_idpakket, product_idproduct, aantal, naam) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("iii", $pakket_idpakket, $product_idproduct, $aantal);
            $stmt->execute();

            // Loop over geselecteerde producten en voeg ze toe aan de relatie
            foreach ($selectedproducts as $selectedproduct) {
                $stmt = $conn->prepare("INSERT INTO pakket_has_product (product_idproduct, pakket_idpakket) VALUES (?, ?)");
                $stmt->bind_param("ii", $selectedproduct, $pakket_idpakket);
                $stmt->execute();
            }
        }
    }

    // Voeg nieuwe pakketten toe
    if (isset($_POST['addpakket'])) {
        if (isset($_POST['newpakket'])) {
            $newpakket = $_POST['newpakket'];
            $stmt = $conn->prepare("INSERT INTO pakket_has_product (pakket_idpakket) VALUES (?)");
            $stmt->bind_param("i", $newpakket);
            $stmt->execute();
        } else {
            echo "Nieuw pakket veld is niet ingevuld.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pakketten</title>
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

    <div class="aanmaakpagina">
        <form class="formsborder" method="post" action="" enctype="multipart/form-data">
            Naam: <br><input type="text" name="naam" required><br>
            Aantal: <br><input type="number" name="aantal" required><br><br>
            Producten:<br>
            <?php
            // Haal producten op uit de database
            $result = $conn->query("SELECT * FROM product");
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<input type='checkbox' name='product[]' value='" . $row['idproduct'] . "'>" . $row['product'] . "<br>";
                }
            } else {
                echo "Geen producten gevonden.";
            }
            ?>
            <br><input class="formsbutton" type="submit" name="submit" value="Toevoegen">
            <?php if (isset($_POST['submit'])) {echo "Product toegevoegd!"; } ?>
        </form>

        <form class="formsborder" method="post" action="">
            Nieuwe pakket: <input type="text" name="newpakket" required>
            <input class="formsbutton" type="submit" name="addpakket" value="Toevoegen">
        </form>
    </div>

    <footer>
    </footer>
</body>

</html>
