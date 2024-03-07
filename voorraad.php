<<<<<<< HEAD
<?php
session_start();

// Databaseverbinding
$hostname = "localhost";
$username = "root";
$password = "";
$database = "maaskantje";

$mysqli = new mysqli($hostname, $username, $password, $database);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Product toevoegen
if(isset($_POST['toevoegen'])) {
    $idmagazijn = $_POST['idmagazijn'];
    $product = $_POST['product'];
    $categorie = $_POST['categorie'];
    $EAN = $_POST['EAN'];
    $aantal = $_POST['aantal'];
    $medewerker_id = 1; // Vervang 1 door de werkelijke ID van de medewerker

    // Controleer of het product al bestaat
    $check_query = "SELECT * FROM magazijn WHERE product = ?";
    $check_stmt = $mysqli->prepare($check_query);
    $check_stmt->bind_param("s", $product);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if($check_result->num_rows > 0) {
        echo "<script>alert('Dit product bestaat al.');</script>";
    } else {
        // Voeg het product toe als het nog niet bestaat
        $insert_query = "INSERT INTO magazijn (idmagazijn, product, categorie, EAN, aantal, mederwerker_idmederwerker) VALUES (?, ?, ?, ?, ?, ?)";
        $insert_stmt = $mysqli->prepare($insert_query);

        if (!$insert_stmt) {
            die("Error in SQL query: " . $mysqli->error);
        }

        if (!$insert_stmt->bind_param("isssii", $idmagazijn, $product, $categorie, $EAN, $aantal, $medewerker_id)) {
            die("Error binding parameters: " . $insert_stmt->error);
        }

        if (!$insert_stmt->execute()) {
            die("Error executing query: " . $insert_stmt->error);
        }

        $insert_stmt->close();
    }
}

// Product verwijderen
if(isset($_POST['verwijderen'])) {
    $idmagazijn = $_POST['idmagazijn'];

    $delete_query = "DELETE FROM magazijn WHERE idmagazijn = ?";
    $delete_stmt = $mysqli->prepare($delete_query);

    $delete_stmt->bind_param("i", $idmagazijn);
    $delete_stmt->execute();

    $delete_stmt->close();
}

// Aantal aanpassen
if(isset($_POST['aanpassen'])) {
    $idmagazijn = $_POST['idmagazijn'];
    $nieuw_aantal = $_POST['nieuw_aantal'];

    $update_query = "UPDATE magazijn SET aantal = ? WHERE idmagazijn = ?";
    $update_stmt = $mysqli->prepare($update_query);

    if (!$update_stmt) {
        die("Error in SQL query: " . $mysqli->error);
    }

    if (!$update_stmt->bind_param("ii", $nieuw_aantal, $idmagazijn)) {
        die("Error binding parameters: " . $update_stmt->error);
    }

    if (!$update_stmt->execute()) {
        die("Error executing query: " . $update_stmt->error);
    }

    $update_stmt->close();
}

// Producten ophalen
$query = "SELECT * FROM magazijn";
$result = $mysqli->query($query);
?>

=======
>>>>>>> 3f4feed15a92d112e96565d977d93d37342d496f
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Voorraad</title>
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

    <!-- Toevoegen van een product -->
    <div class="toevoegen">
        <h2>Product toevoegen</h2>
        <form action="" method="post">
            <label for="idmagazijn">id:</label><br>
            <input type="number" id="idmagazijn" name="idmagazijn"><br>
            <label for="naam">Naam:</label><br>
            <input type="text" id="naam" name="product"><br>
            <label for="categorie">categorie:</label><br>
            <textarea id="categorie" name="categorie"></textarea><br>
            <label for="EAN">EAN:</label><br>
            <input type="number" id="EAN" name="EAN" step="0.01"><br><br>
            <label for="aantal">Aantal:</label><br>
            <input type="number" id="aantal" name="aantal" min="1"><br><br>
            <input type="submit" value="Toevoegen" name="toevoegen">
        </form>
    </div>

    <!-- Overzicht van producten -->
    <div class="overzicht">
        <h2>Producten</h2>
        <table>
            <tr>
                <th>idmagazijn</th>
                <th>Naam</th>
                <th>Categorie</th>
                <th>EAN</th>
                <th>Aantal</th>
                <th>Actie</th>
            </tr>
            <?php
            while($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>".$row['idmagazijn']."</td>";
                echo "<td>".$row['product']."</td>";
                echo "<td>".$row['categorie']."</td>";
                echo "<td>".$row['EAN']."</td>";
                echo "<td>".$row['aantal']."</td>";
                echo "<td>
                        <form action='' method='post'>
                            <input type='hidden' name='idmagazijn' value='".$row['idmagazijn']."'>
                            <input type='number' name='nieuw_aantal' min='1' value='".$row['aantal']."'>
                            <input type='submit' value='Aanpassen' name='aanpassen'>
                            <input type='submit' value='Verwijderen' name='verwijderen'>
                        </form>
                      </td>";
                echo "</tr>";
            }
            ?>
        </table>
    </div>

    <!-- ----------------------------------------------------------------------------------------------------------- -->

    <footer>
    </footer>
</body>
<<<<<<< HEAD
</html>
=======
</html>
>>>>>>> 3f4feed15a92d112e96565d977d93d37342d496f
