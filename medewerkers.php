<?php
include('db.php');

unset($_POST);

if(!isset($_SESSION['gebruikersnaam'])) {
    header("location:login.php");
    exit();
}

if($_SESSION['functie'] != "directie"){
    header("location:account.php");
    exit();
}

// medewerker toevoegen
if (isset($_POST['toevoegen'])) {
    $voornaam = $_POST['voornaam'];
    $achternaam = $_POST['achternaam'];
    $gebruikersnaam = $_POST['gebruikersnaam'];
    $wachtwoord = $_POST['wachtwoord'];
    $herhaalwachtwoord = $_POST['herhaalwachtwoord'];
    $functie = $_POST['functie'];

    if($wachtwoord !== $herhaalwachtwoord){
        header("location:medewerkers.php");
        echo "<script>alert('Wachtwoorden komen niet overeen!');</script>";
        exit();
    }
    else {
        $check_query = "SELECT * FROM medewerker WHERE gebruikersnaam = ?";
        $check_stmt = $mysqli->prepare($check_query);
        $check_stmt->bind_param("s", $gebruikersnaam);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            echo "<script>alert('Gebruikersnaam is al in gebruik!');</script>";
        } else {
            $autoincrement_query = "ALTER TABLE medewerker AUTO_INCREMENT = 1";
            $autoincrement = $mysqli->prepare($autoincrement_query);
            $autoincrement->execute();
            $autoincrement->close();

            $hashedwachtwoord = password_hash($wachtwoord, PASSWORD_DEFAULT);
            $insert_query = "INSERT INTO medewerker (voornaam, achternaam, gebruikersnaam, wachtwoord, functie) VALUES (?, ?, ?, ?, ?)";
            $insert_stmt = $mysqli->prepare($insert_query);

            if (!$insert_stmt) {
                die("Error in SQL query: " . $mysqli->error);
            }

            if (!$insert_stmt->bind_param("sssss", $voornaam, $achternaam, $gebruikersnaam, $hashedwachtwoord, $functie)) {
                die("Error binding parameters: " . $insert_stmt->error);
            }

            if (!$insert_stmt->execute()) {
                die("Error executing query: " . $insert_stmt->error);
            }

            $insert_stmt->close();
        }
    }
}

if(isset($_POST['aanpassen'])) {
    $idmedewerker = $_POST['idmedewerker'];
    $voornaam = $_POST['voornaam'];
    $achternaam = $_POST['achternaam'];
    $gebruikersnaam = $_POST['gebruikersnaam'];
    $functie = $_POST['functie'];

    $update_query = "UPDATE medewerker SET voornaam=?, achternaam=?, gebruikersnaam=?, functie=? WHERE idmedewerker=?";
    $update_stmt = $mysqli->prepare($update_query);
    $update_stmt->bind_param("ssssi", $voornaam, $achternaam, $gebruikersnaam, $functie, $idmedewerker);
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
    $search_condition = "WHERE voornaam LIKE '%$search%' OR achternaam LIKE '%$search%' OR gebruikersnaam LIKE '%$search%' OR functie LIKE '%$search%'";
}

$columnName = isset($_GET['sort']) ? $_GET['sort'] : 'idmedewerker';
$order = isset($_GET['order']) ? $_GET['order'] : 'asc';

$query = "SELECT * FROM medewerker $search_condition";
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
    <script src="functions.js"></script>
</head>

<body>
    <?php navbar(); ?>  

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
                    <option value="geblokkeerd">Geblokkeerd</option>          
                    </select></td>
                <td><input type="submit" value="Toevoegen" name="toevoegen"></td>
            </table>
        </form>
    </div>
    
    <div class="overzicht">
        <table>
            <tr>
                <th><a href="?sort=voornaam&order=<?= ($columnName === 'voornaam' && $order === 'asc' ? 'desc' : 'asc') ?>">Voornaam</a></th>
                <th><a href="?sort=achternaam&order=<?= ($columnName === 'achternaam' && $order === 'asc' ? 'desc' : 'asc') ?>">Achternaam</a></th>
                <th><a href="?sort=gebruikersnaam&order=<?= ($columnName === 'gebruikersnaam' && $order === 'asc' ? 'desc' : 'asc') ?>">Gebruikersnaam</a></th>
                <th><a href="?sort=functie&order=<?= ($columnName === 'functie' && $order === 'asc' ? 'desc' : 'asc') ?>">Functie</a></th>
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
    echo "
        <form id='form_".$row['idmedewerker']."' action='' method='post' onsubmit='saveChangesMedewerkers(event, ".$row['idmedewerker'].")'> <!-- Voeg onsubmit toe -->
            <input type='hidden' name='idmedewerker' value='". $row['idmedewerker']. "'>
            <td>
                <span id='voornaam_".$row['idmedewerker']."' style='display: block;'>".$row['voornaam']."</span>
                <input id='voornaamInput_".$row['idmedewerker']."' type='text' name='voornaam' value='". $row['voornaam'] . "' style='display: none;'>
            </td>
            <td>
                <span id='achternaam_".$row['idmedewerker']."' style='display: block;'>".$row['achternaam']."</span>
                <input id='achternaamInput_".$row['idmedewerker']."' type='text' name='achternaam' value='". $row['achternaam'] . "' style='display: none;'>
            </td>
            <td>
                <span id='gebruikersnaam_".$row['idmedewerker']."' style='display: block;'>".$row['gebruikersnaam']."</span>
                <input id='gebruikersnaamInput_".$row['idmedewerker']."' type='text' name='gebruikersnaam' value='". $row['gebruikersnaam'] . "' style='display: none;'>
            </td>
            <td>
                <span id='functie_".$row['idmedewerker']."' style='display: block;'>".$row['functie']."</span>
                <select id='functieSelect_".$row['idmedewerker']."' name='functie' style='display: none;'>          
                    <option value='vrijwilliger'>Vrijwilliger</option>          
                    <option value='magazijn'>Magazijn</option>        
                    <option value='directie'>Directie</option>
                    <option value='geblokkeerd'>Geblokkeerd</option>          
                </select>
            </td>
            <td>
                <button id='aanpassenButton_".$row['idmedewerker']."' type='button' onclick='openFormMedewerkers(".$row['idmedewerker'].")'>Aanpassen</button>
                <input id='saveButton_".$row['idmedewerker']."' type='submit' value='Opslaan' name='aanpassen' style='display: none;'>
                <input id='deleteButton_".$row['idmedewerker']."' type='submit' value='Verwijderen' name='verwijderen' style='display: none;'>
            </td>
        </form>
    ";
    echo "</tr>";
}
?>
        </table>
    </div>
</body>
</html>
