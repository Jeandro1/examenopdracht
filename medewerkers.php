<?php
include('db.php');

if(!isset($_SESSION['gebruikersnaam'])) {
    header("location:login.php");
}

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// medewerker toevoegen
if (isset($_POST['toevoegen'])) {
    $voornaam = $_POST['voornaam'];
    $achternaam = $_POST['achternaam'];
    $gebruikersnaam = $_POST['gebruikersnaam'];
    $wachtwoord = $_POST['wachtwoord'];
    $functie = $_POST['functie'];

    $check_query = "SELECT * FROM medewerker WHERE gebruikersnaam = ?";
    $check_stmt = $mysqli->prepare($check_query);
    $check_stmt->bind_param("s", $gebruikersnaam);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo "<script>alert('Gebruikersnaam is al in gebruik.');</script>";
    } else {

        $insert_query = "INSERT INTO medewerker (voornaam, achternaam, gebruikersnaam, wachtwoord, functie) VALUES (?, ?, ?, ?, ?)";
        $insert_stmt = $mysqli->prepare($insert_query);

        if (!$insert_stmt) {
            die("Error in SQL query: " . $mysqli->error);
        }

        if (!$insert_stmt->bind_param("sssss", $voornaam, $achternaam, $gebruikersnaam, $wachtwoord, $functie)) {
            die("Error binding parameters: " . $insert_stmt->error);
        }

        if (!$insert_stmt->execute()) {
            die("Error executing query: " . $insert_stmt->error);
        }

        $insert_stmt->close();
    }
}

if(isset($_POST['aanpassen'])) {
    $idmedewerker = $_POST['idmedewerker'];
    $voornaam = $_POST['voornaam'];
    $achternaam = $_POST['achternaam'];
    $gebruikersnaam = $_POST['gebruikersnaam'];
    $wachtwoord = $_POST['wachtwoord'];
    $functie = $_POST['functie'];

    $update_query = "UPDATE medewerker SET voornaam=?, achternaam=?, gebruikersnaam=?, wachtwoord=?, functie=? WHERE idmedewerker=?";
    $update_stmt = $mysqli->prepare($update_query);
    $update_stmt->bind_param("sssssi", $voornaam, $achternaam, $gebruikersnaam, $wachtwoord, $functie, $idmedewerker);
    $update_stmt->execute();

    $update_stmt->close();
}

if(isset($_POST['verwijderen'])) {
    $idmedewerker = $_POST['idmedewerker'];

    $delete_query = "DELETE FROM medewerker WHERE idmedewerker = ?";
    $delete_stmt = $mysqli->prepare($delete_query);

    $delete_stmt->bind_param("i", $idmedewerker);
    $delete_stmt->execute();

    $delete_stmt->close();
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
    $search_condition = "WHERE voornaam LIKE '%$search%'";
}

$columnName = isset($_GET['sort']) ? $_GET['sort'] : 'idmedewerker';
$order = isset($_GET['order']) ? $_GET['order'] : 'asc';

$query = "SELECT * FROM medewerker";
$result = $mysqli->query($query);

$data = sortTable($columnName, $order, $result);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medewerkers</title>
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

    <div class="gebruikersinvoegen">
        <form action="" method="post">
            <table>
                <tr>
                    <th>Voornaam</th>
                    <th>Achternaam</th>
                    <th>Gebruikersnaam</th>
                    <th>Wachtwoord</th>
                    <th>Herhaal wachtwoord</th>
                    <th>Functie</th>
                    <th>Toevoegen</th>
                </tr>
                    <td><input type="text" name="voornaam"></td>
                    <td><input type="text" name="achternaam"></td>
                    <td><input type="text" name="gebruikersnaam"></td>
                    <td><input type="text" name="wachtwoord"></td>
                    <td><input type="text" name="herhaalwachtwoord"></td>
                    <td><select name="functie">          
                    <option value="vrijwilliger">Vrijwilliger</option>          
                    <option value="magazijn">Magazijn</option>        
                    <option value="directie">Directie</option>        
                    </select></td>
                <td><input type="submit" value="Toevoegen" name="toevoegen"></td>
            </table>
        </form>
    </div>

    <div class="gebruikersinvoegen">
        <form action="" method="post">
            <table>
                <tr>
                    <th>idMedewerker</th>
                    <th>Voornaam</th>
                    <th>Achternaam</th>
                    <th>Gebruikersnaam</th>
                    <th>Wachtwoord</th>
                    <th>Functie</th>
                    <th>Aanpassen</th>
                </tr>
                <tr>
                    <td><input type="text" name="idmedewerker"></td>
                    <td><input type="text" name="voornaam"></td>
                    <td><input type="text" name="achternaam"></td>
                    <td><input type="text" name="gebruikersnaam"></td>
                    <td><input type="text" name="wachtwoord"></td>
                    <td><select name="functie">          
                    <option value="vrijwilliger">Vrijwilliger</option>          
                    <option value="magazijn">Magazijn</option>        
                    <option value="directie">Directie</option>        
                    </select></td>
                    <td><input type="submit" value="Aanpassen" name="aanpassen"></td>
                </tr>
            </table>
        </form>
    </div>
    
    <div class="overzicht">
        <table>
            <tr>
                <th><a href="?sort=idmedewerker&order=<?= ($columnName === 'idmedewerker' && $order === 'asc' ? 'desc' : 'asc') ?>">idmedewerker</a></th>
                <th><a href="?sort=voornaam&order=<?= ($columnName === 'voornaam' && $order === 'asc' ? 'desc' : 'asc') ?>">Voornaam</a></th>
                <th><a href="?sort=achternaam&order=<?= ($columnName === 'achternaam' && $order === 'asc' ? 'desc' : 'asc') ?>">Achternaam</a></th>
                <th><a href="?sort=gebruikersnaam&order=<?= ($columnName === 'gebruikersnaam' && $order === 'asc' ? 'desc' : 'asc') ?>">Gebruikersnaam</a></th>
                <th><a href="?sort=functie&order=<?= ($columnName === 'functie' && $order === 'asc' ? 'desc' : 'asc') ?>">Functie</a></th>
                <th>Wachtwoord</th>
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
                echo "<td>".$row['idmedewerker']."</td>";
                echo "<td>".$row['voornaam']."</td>";
                echo "<td>".$row['achternaam']."</td>";
                echo "<td>".$row['gebruikersnaam']."</td>";
                echo "<td>".$row['functie']."</td>";
                echo "<td>".$row['wachtwoord']."</td>";
                echo "<td>
                        <form action='' method='post'>
                            <input type='hidden' name='idmedewerker' value='".$row['idmedewerker']."'>
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
