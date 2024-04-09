<?php
include('db.php');

if(!isset($_SESSION['gebruikersnaam'])) {
    header("location:login.php");
    exit();
}

if($_SESSION['functie'] != "directie" && $_SESSION['functie'] != "magazijn"){
    header("location:account.php");
    exit();
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
        echo "<script>alert('Deze levering bestaat al!');</script>";
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

if(isset($_POST['aanpassen'])) {
    $idleverancier = $_POST['idleverancier'];
    $bedrijfsnaam = $_POST['bedrijfsnaam'];
    $adres = $_POST['adres'];
    $naam = $_POST['naam'];
    $email = $_POST['email'];
    $telefoonnummer = $_POST['telefoonnummer'];
    $volgendelevering = $_POST['volgende_levering'];

    $update_query = "UPDATE leverancier SET bedrijfsnaam=?, adres=?, naam=?, email=?, telefoonnummer=?, volgende_levering=? WHERE idleverancier=?";
    $update_stmt = $mysqli->prepare($update_query);
    $update_stmt->bind_param("ssssisi", $bedrijfsnaam, $adres, $naam, $email, $telefoonnummer, $volgendelevering, $idleverancier);
    $update_stmt->execute();

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

$columnName = isset($_GET['sort']) ? $_GET['sort'] : 'bedrijfsnaam';
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
    <?php navbar(); ?>  

    <div class="gebruikersinvoegen">
        <form action="" method="post">
            <table>
                <tr>
                    <th>Bedrijfsnaam</th>
                    <th>Adres</th>
                    <th>Naam contact</th>
                    <th>Email contact</th>
                    <th>Telefoonnummer</th>
                    <th>Volgende levering</th>
                    <th>Toevoegen</th>
                </tr>
                <tr>
                   <td><input type="text" name="bedrijfsnaam"></td>
                   <td><input type="text" name="adres" ></td>
                   <td><input type="text" name="naam"></td>
                   <td><input type="text" name="email"></td>
                   <td><input type="text" name="telefoonnummer" max="9"></td>
                   <td><input type="datetime-local" name="volgende_levering"></td>
                   <td><input type="submit" value="Toevoegen" name="toevoegen"></td>
                </tr>   
            </table>
        </form>
    </div>
    
    <div class="overzicht">
        <table>
            <tr>
                <th><a href="?sort=bedrijfsnaam&order=<?= ($columnName === 'bedrijfsnaam' && $order === 'asc' ? 'desc' : 'asc') ?>">Bedrijfsnaam</a></th>
                <th><a href="?sort=adres&order=<?= ($columnName === 'adres' && $order === 'asc' ? 'desc' : 'asc') ?>">Adres</a></th>
                <th><a href="?sort=naam&order=<?= ($columnName === 'naam' && $order === 'asc' ? 'desc' : 'asc') ?>">Naam</a></th>
                <th><a href="?sort=email&order=<?= ($columnName === 'email' && $order === 'asc' ? 'desc' : 'asc') ?>">Email</a></th>
                <th><a href="?sort=telefoonnummer&order=<?= ($columnName === 'telefoonnummer' && $order === 'asc' ? 'desc' : 'asc') ?>">Telefoonnummer</a></th>
                <th><a href="?sort=volgende_levering&order=<?= ($columnName === 'volgende_levering' && $order === 'asc' ? 'desc' : 'asc') ?>">Volgende levering</a></th>
                <th>
                    <form action="" method="get">
                        <input type="text" name="search" placeholder="Zoeken...">
                        <input type="submit" value="Zoeken">
                    </form>
            </tr>
            <?php
            foreach ($data as $row) {
                echo "<tr>";
                echo "<td>".$row['bedrijfsnaam']."</td>";
                echo "<td>".$row['adres']."</td>";
                echo "<td>".$row['naam']."</td>";
                echo "<td>".$row['email']."</td>";
                echo "<td>".$row['telefoonnummer']."</td>";
                echo "<td>".$row['volgende_levering']."</td>";
                echo "<td>
                <form action='' method='post'>
                    <input type='hidden' name='idleverancier' value='". $row['idleverancier']. "'>
                    <input type='text' name='bedrijfsnaam' value='". $row['bedrijfsnaam'] . "'>
                    <input type='text' name='adres' value='". $row['adres'] . "'>
                    <input type='text' name='naam' value='". $row['naam'] . "'>
                    <input type='text' name='email' value='" . $row['email'] . "'>
                    <input type='text' name='telefoonnummer' value='" . $row['telefoonnummer'] . "'>
                    <input type='datetime-local' name='volgende_levering' value='". $row['volgende_levering'] . "'>
                    <input type='submit' value='Opslaan' name='aanpassen'>
                    <input type='submit' value='Verwijderen' name='verwijderen'>
                  </form>
               </td>";
            }
            ?>
            </tr>
        </table>
    </div>
  
    <?php


    
    ?>
    <!-- ----------------------------------------------------------------------------------------------------------- -->

    <footer>
    </footer>
</body>
</html>
