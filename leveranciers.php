<?php
include('db.php');

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// gegevens toevoegen
if (isset($_POST['toevoegen'])) {
    $bedrijfsnaam = $_POST['bedrijfsnaam'];
    $adres= $_POST['adres'];
    $naam = $_POST['naam'];
    $email	 = $_POST['email'];
    $telefoonnummer = $_POST['telefoonnummer'];
    $volgendelevering = $_POST['volgende_levering'];

    $check_query = "SELECT * FROM leverancier WHERE bedrijfsnaam = ?";
    $check_stmt = $mysqli->prepare($check_query);
    $check_stmt->bind_param("s", $bedrijfsnaam);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo "<script>alert('Deze levering bestaat al.');</script>";
    } else {
    

        $insert_query = "INSERT INTO leverancier (bedrijfsnaam, adres, naam, email, telefoonnummer,  volgende_levering) VALUES (?, ?, ?, ?, ?, ?)";
        $insert_stmt = $mysqli->prepare($insert_query);

        if (!$insert_stmt) {
            die("Error in SQL query: " . $mysqli->error);
        }

        if (!$insert_stmt->bind_param("ssssis", $bedrijfsnaam, $adres, $naam, $email, $telefoonnummer, $volgendelevering )) {
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
    $idleverancier = $_POST['idleverancier'];

    $delete_query = "DELETE FROM leverancier WHERE idleverancier = ?";
    $delete_stmt = $mysqli->prepare($delete_query);

    $delete_stmt->bind_param("i", $idleverancier);
    $delete_stmt->execute();

    $delete_stmt->close();
}

// Aantal aanpassen
if(isset($_POST['aanpassen'])) {
    $idleverancier = $_POST['idleverancier'];
    $nieuw_volgende_levering = $_POST[' $nieuw_volgende_levering'];

    $update_query = "UPDATE volgende_levering SET date = ? WHERE idleverancier = ?";
    $update_stmt = $mysqli->prepare($update_query);

    if (!$update_stmt) {
        die("Error in SQL query: " . $mysqli->error);
    }

    if (!$update_stmt->bind_param("ii", $ $nieuw_volgende_levering, $idleverancier)) {
        die("Error binding parameters: " . $update_stmt->error);
    }

    if (!$update_stmt->execute()) {
        die("Error executing query: " . $update_stmt->error);
    }

    $update_stmt->close();
}

function sortTable($columnName, $order, $result)
{
    $data = array();
    
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

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
    $search_condition = "WHERE leverancier LIKE '%$search%'";
}

$columnName = isset($_GET['sort']) ? $_GET['sort'] : 'idleverancier';
$order = isset($_GET['order']) ? $_GET['order'] : 'asc';

$query = "SELECT * FROM leverancier";
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
            <a href="klanten.php">
                <p class="knop">Klanten</p>
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

    <div class="gebruikersinvoegen">
        <form action="" method="post">
            <table>
                <tr>
                    <th>bedrijfsnaam</th>
                    <th>adres</th>
                    <th>naam</th>
                    <th>email</th>
                    <th>telefoonnummer</th>
                    <th>volgende levering</th>
                    <th>toevoegen</th>
                </tr>
                <td><input type="text" id="bedrijfsnaam" name="bedrijfsnaam"></td>
                <td><input type="text" id="adres" name="adres" ></td>
                <td><input type="text" id="naam" name="naam"></td>
                <td><input type="text" id="email" name="email"></td>
                <td><input type="text" id="telefoonnummer" name="telefoonnummer" max="9"></td>
                <td><input type="date" id="volgende_levering" name="volgende_levering"></td>
            <td><input type="submit" value="Toevoegen" name="toevoegen"></td>
        </form>
    </div>
    
    <div class="overzicht">
        <table>
            <tr>
                <th><a href="?sort=idleverancier&order=<?= ($columnName === 'idleverancier' && $order === 'asc' ? 'desc' : 'asc') ?>">idleverancier</a></th>
                <th><a href="?sort=bedrijfsnaam&order=<?= ($columnName === 'bedrijfsnaam' && $order === 'asc' ? 'desc' : 'asc') ?>">bedrijfsnaam</a></th>
                <th><a href="?sort=adres&order=<?= ($columnName === 'adres' && $order === 'asc' ? 'desc' : 'asc') ?>">adres</a></th>
                <th><a href="?sort=naam&order=<?= ($columnName === 'naam' && $order === 'asc' ? 'desc' : 'asc') ?>">Naam</a></th>
                <th><a href="?sort=email&order=<?= ($columnName === 'email' && $order === 'asc' ? 'desc' : 'asc') ?>">email</a></th>
                <th><a href="?sort=telefoonnummer&order=<?= ($columnName === 'telefoonnummer' && $order === 'asc' ? 'desc' : 'asc') ?>">telefoonnummer</a></th>
                <th><a href="?sort=volgende_levering&order=<?= ($columnName === 'volgende_levering' && $order === 'asc' ? 'desc' : 'asc') ?>">volgende levering</a></th>
                <th>
                    <form action="" method="get">
                        <input type="text" name="search" placeholder="Zoeken...">
                        <input type="submit" value="Zoeken">
                    </form>
                </th>
            </tr>
            <?php
            foreach ($data as $row) {
                echo "<tr>";
                echo "<td>".$row['idleverancier']."</td>";
                echo "<td>".$row['bedrijfsnaam']."</td>";
                echo "<td>".$row['adres']."</td>";
                echo "<td>".$row['naam']."</td>";
                echo "<td>".$row['email']."</td>";
                echo "<td>".$row['telefoonnummer']."</td>";
                echo "<td>".$row['volgende_levering']."</td>";
                echo "<td>
                        <form action='' method='post'>
                            <input type='hidden' name='idleverancier' value='".$row['idleverancier']."'>
                            <input type='date' name='nieuw_volgende_levering' value='".$row['volgende_levering']."'>
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
