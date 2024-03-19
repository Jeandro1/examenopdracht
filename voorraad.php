<?php
include('db.php');

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Product toevoegen
if (isset($_POST['toevoegen'])) {
    $product = $_POST['product'];
    $categorie = $_POST['categorie'];
    $aantal = $_POST['aantal'];
    $medewerker_id = 1; // Vervang 1 door de werkelijke ID van de medewerker

    // Controleer of het product al bestaat
    $check_query = "SELECT * FROM magazijn WHERE product = ?";
    $check_stmt = $mysqli->prepare($check_query);
    $check_stmt->bind_param("s", $product);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo "<script>alert('Dit product bestaat al.');</script>";
    } else {
        // Genereer een unieke EAN
        $EAN = generateUniqueEAN($mysqli);

        // Voeg het product toe
        $insert_query = "INSERT INTO magazijn (product, categorie, EAN, aantal) VALUES (?, ?, ?, ?)";
        $insert_stmt = $mysqli->prepare($insert_query);

        if (!$insert_stmt) {
            die("Error in SQL query: " . $mysqli->error);
        }

        if (!$insert_stmt->bind_param("sssi", $product, $categorie, $EAN, $aantal)) {
            die("Error binding parameters: " . $insert_stmt->error);
        }

        if (!$insert_stmt->execute()) {
            die("Error executing query: " . $insert_stmt->error);
        }

        $insert_stmt->close();
    }
}

// Functie om een unieke EAN te genereren
function generateUniqueEAN($mysqli) {
    $EAN = rand(9000000000001, 9999999999999);

    $check_query_EAN = "SELECT * FROM magazijn WHERE EAN = ?";
    $check_stmt_EAN = $mysqli->prepare($check_query_EAN);
    $check_stmt_EAN->bind_param("s", $EAN);
    $check_stmt_EAN->execute();
    $check_result_EAN = $check_stmt_EAN->get_result();
    $check_stmt_EAN->close();

    if ($check_result_EAN->num_rows > 0) {
        // EAN bestaat al, genereer een nieuwe
        $EAN = generateUniqueEAN($mysqli);
    }

    return $EAN;
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

function sortTable($columnName, $order, $result)
{
    // Array om de gegevens uit de database op te slaan
    $data = array();
    
    // Haal de gegevens op uit de database en sla ze op in de array
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    // Sorteer de array op basis van de opgegeven kolom en volgorde
    usort($data, function($a, $b) use ($columnName, $order) {
        if ($order === 'asc') {
            return $a[$columnName] <=> $b[$columnName];
        } else {
            return $b[$columnName] <=> $a[$columnName];
        }
    });

    return $data;
}

$search = isset($_GET['search']) ? $_GET['search'] : '';
$search_condition = '';
if (!empty($search)) {
    $search_condition = "WHERE product LIKE '%$search%'";
}

// Standaard sorteeropties
$columnName = isset($_GET['sort']) ? $_GET['sort'] : 'idmagazijn';
$order = isset($_GET['order']) ? $_GET['order'] : 'asc';


// Producten ophalen
$query = "SELECT * FROM magazijn";
$result = $mysqli->query($query);

$data = sortTable($columnName, $order, $result);

?>

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
        <form action="" method="post">
            <table>
                <tr>
                    <th>Naam</th>
                
                    <th>Aantal</th>
                
                    <th>Categorie</th>
                
                    <th>Product toevoegen</th>
                </tr>
                <td><input type="text" id="naam" name="product"></td>
                
            
                <td><input type="number" id="aantal" name="aantal" min="1"></td>
            
                <td><select name="categorie">
            
                <option value="Aardappelen, groente en fruit">Aardappelen, groente en fruit</option>
            
                <option value="Kaas en vleeswaren">Kaas en vleeswaren</option>
            
                <option value="Zuivel, plantaardig en eiere">Zuivel, plantaardig en eieren</option>
            
                <option value="Bakkerij en banket">Bakkerij en banket</option>
            
                <option value="Frisdrank, sappen, koffie en thee">Frisdrank, sappen, koffie en thee</option>
            
                <option value="Pasta, rijst en wereldkeuken">Pasta, rijst en wereldkeuken</option>
            
                <option value="Soepen, sauzen, kruiden en olie">Soepen, sauzen, kruiden en olie</option>
            
                <option value="Snoep, koek, chips en chocolade">Snoep, koek, chips en chocolade</option>
            
                <option value="Baby, verzorging en hygiëne">Baby, verzorging en hygiëne</option>
            
                </select></td>
            <td><input type="submit" value="Toevoegen" name="toevoegen"></td>
        </form>
    </div>
    

    <!-- Overzicht van producten -->
    <div class="overzicht">
        <table>
            <tr>
                <th><a href="?sort=idmagazijn&order=<?= ($columnName === 'idmagazijn' && $order === 'asc' ? 'desc' : 'asc') ?>">idmagazijn</a></th>
                <th><a href="?sort=EAN&order=<?= ($columnName === 'EAN' && $order === 'asc' ? 'desc' : 'asc') ?>">EAN</a></th>
                <th><a href="?sort=product&order=<?= ($columnName === 'product' && $order === 'asc' ? 'desc' : 'asc') ?>">Naam</a></th>
                <th><a href="?sort=aantal&order=<?= ($columnName === 'aantal' && $order === 'asc' ? 'desc' : 'asc') ?>">Aantal</a></th>
                <th><a href="?sort=categorie&order=<?= ($columnName === 'categorie' && $order === 'asc' ? 'desc' : 'asc') ?>">Categorie</a></th>
                <th>Zoeken</th>
            </tr>
            <tr>
                <!-- Voeg een zoekbalk toe -->
                <td colspan="5">
                    <form action="" method="get">
                        <input type="text" name="search" placeholder="Zoeken...">
                        <input type="submit" value="Zoeken">
                    </form>
                </td>
                <td></td>
            </tr>
            <?php
            foreach ($data as $row) {
                echo "<tr>";
                echo "<td>".$row['idmagazijn']."</td>";
                echo "<td>".$row['EAN']."</td>";
                echo "<td>".$row['product']."</td>";
                echo "<td>".$row['aantal']."</td>";
                echo "<td>".$row['categorie']."</td>";
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


    <?php


    
    ?>
    <!-- ----------------------------------------------------------------------------------------------------------- -->

    <footer>
    </footer>
</body>
</html>
