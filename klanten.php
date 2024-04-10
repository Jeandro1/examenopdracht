<?php
include('db.php');

if(!isset($_SESSION['gebruikersnaam'])) {
    header("location:login.php");
    exit();
}

if($_SESSION['functie'] != "directie" && $_SESSION['functie'] != "vrijwilliger"){
    header("location:account.php");
    exit();
}

// medewerker toevoegen
if (isset($_POST['toevoegen'])) {
    $gezinsnaam = $_POST['gezinsnaam'];
    $adres = $_POST['adres'];
    $email = $_POST['email'];
    $telefoonnummer = $_POST['telefoonnummer'];
    $volwassenen = $_POST['volwassenen'];
    $kinderen = $_POST['kinderen'];
    $babys = $_POST['babys'];
    $allergieen = $_POST['allergieen'];

    if(isset($_POST['varkensvlees'])){
        $varkensvlees = true;
    }
    else{
        $varkensvlees = false;
    }
    if(isset($_POST['veganistisch'])){
        $veganistisch = true;
    }
    else{
        $veganistisch = false;
    }
    if(isset($_POST['vegetarisch'])){
        $vegetarisch = true;
    }
    else{
        $vegetarisch = false;
    }

    $check_query = "SELECT * FROM gezin WHERE adres = ?";
    $check_stmt = $mysqli->prepare($check_query);
    $check_stmt->bind_param("s", $adres);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            echo "<script>alert('Er staat al een gezin geregistreerd op dit adres!');</script>";
        } else {
            $insert_query = "INSERT INTO gezin (gezinsnaam, adres, email, telefoonnummer, volwassenen, kinderen, babys, varkensvlees, veganistisch, vegetarisch, allergieen) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $insert_stmt = $mysqli->prepare($insert_query);

            if (!$insert_stmt) {
                die("Error in SQL query: " . $mysqli->error);
            }

            if (!$insert_stmt->bind_param("sssiiiiiiis", $gezinsnaam, $adres, $email, $telefoonnummer, $volwassenen, $kinderen, $babys, $varkensvlees, $veganistisch, $vegetarisch, $allergieen)) {
                die("Error binding parameters: " . $insert_stmt->error);
            }

            if (!$insert_stmt->execute()) {
                die("Error executing query: " . $insert_stmt->error);
            }

            $insert_stmt->close();
    }
}

if(isset($_POST['aanpassen'])) {
    $idgezin = $_POST['idgezin'];
    $gezinsnaam = $_POST['gezinsnaam'];
    $adres = $_POST['adres'];
    $email = $_POST['email'];
    $telefoonnummer = $_POST['telefoonnummer'];
    $volwassenen = $_POST['volwassenen'];
    $kinderen = $_POST['kinderen'];
    $babys = $_POST['babys'];
    $allergieen = $_POST['allergieen'];
    if(isset($_POST['varkensvlees'])){
        $varkensvlees = true;
    }
    else{
        $varkensvlees = false;
    }
    if(isset($_POST['veganistisch'])){
        $veganistisch = true;
    }
    else{
        $veganistisch = false;
    }
    if(isset($_POST['vegetarisch'])){
        $vegetarisch = true;
    }
    else{
        $vegetarisch = false;
    }

    $update_query = "UPDATE gezin SET gezinsnaam=?, adres=?, email=?, telefoonnummer=?, volwassenen=?, kinderen=?, babys=?, varkensvlees=?, veganistisch=?, vegetarisch=?, allergieen=? WHERE idgezin=?";
    $update_stmt = $mysqli->prepare($update_query);
    $update_stmt->bind_param("sssiiiiiiisi", $gezinsnaam, $adres, $email, $telefoonnummer, $volwassenen, $kinderen, $babys, $varkensvlees, $veganistisch, $vegetarisch, $allergieen, $idgezin);
    $update_stmt->execute();

    $update_stmt->close();
}

if(isset($_POST['verwijderen'])) {
    $idgezin = $_POST['idgezin'];

    $delete_query = "DELETE FROM gezin WHERE idgezin = ?";
    $delete_stmt = $mysqli->prepare($delete_query);

    $delete_stmt->bind_param("i", $idgezin);
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
    $search_condition = "WHERE gezinsnaam LIKE '%$search%' OR adres LIKE '%$search%' OR email LIKE '%$search%' OR telefoonnummer LIKE '%$search%'";
}

$columnName = isset($_GET['sort']) ? $_GET['sort'] : 'idgezin';
$order = isset($_GET['order']) ? $_GET['order'] : 'asc';

$query = "SELECT * FROM gezin $search_condition";
$result = $mysqli->query($query);

$data = sortTable($columnName, $order, $result);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Klanten</title>
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
                    <th>Gezinsnaam</th>
                    <th>Adres</th>
                    <th>Email</th>
                    <th>Telefoonnummer</th>
                    <th>Volwassenen</th>
                    <th>Kinderen</th>
                    <th>Baby's</th>
                    <th>Geen varkensvlees</th>
                    <th>Veganistisch</th>
                    <th>Vegetarisch</th>
                    <th>Allergieën</th>
                    <th>Toevoegen</th>
                </tr>
                    <td><input type="text" name="gezinsnaam"></td>
                    <td><input type="text" name="adres"></td>
                    <td><input type="text" name="email"></td>
                    <td><input type="text" name="telefoonnummer"></td>
                    <td><input type="number" name="volwassenen"></td>
                    <td><input type="number" name="kinderen"></td>
                    <td><input type="number" name="babys"></td>
                    <td><input type="checkbox" name="varkensvlees"></td>
                    <td><input type="checkbox" name="veganistisch"></td>
                    <td><input type="checkbox" name="vegetarisch"></td>
                    <td><textarea name="allergieen"></textarea></td>
                <td><input type="submit" value="Toevoegen" name="toevoegen"></td>
            </table>
        </form>
    </div>
    
    <div class="overzicht">
        <table>
            <tr>
                <th><a href="?sort=gezinsnaam&order=<?= ($columnName === 'gezinsnaam' && $order === 'asc' ? 'desc' : 'asc') ?>">Gezinsnaam</a></th>
                <th><a href="?sort=adres&order=<?= ($columnName === 'adres' && $order === 'asc' ? 'desc' : 'asc') ?>">Adres</a></th>
                <th><a href="?sort=email&order=<?= ($columnName === 'email' && $order === 'asc' ? 'desc' : 'asc') ?>">Email</a></th>
                <th><a href="?sort=telefoonnummer&order=<?= ($columnName === 'telefoonnummer' && $order === 'asc' ? 'desc' : 'asc') ?>">Telefoonnummer</a></th>
                <th><a href="?sort=volwassenen&order=<?= ($columnName === 'volwassenen' && $order === 'asc' ? 'desc' : 'asc') ?>">Volwassenen</a></th>
                <th><a href="?sort=kinderen&order=<?= ($columnName === 'kinderen' && $order === 'asc' ? 'desc' : 'asc') ?>">Kinderen</a></th>
                <th><a href="?sort=babys&order=<?= ($columnName === 'babys' && $order === 'asc' ? 'desc' : 'asc') ?>">Baby's</a></th>
                <th><a href="?sort=varkensvlees&order=<?= ($columnName === 'varkensvlees' && $order === 'asc' ? 'desc' : 'asc') ?>">Geen varkensvlees</a></th>
                <th><a href="?sort=veganistisch&order=<?= ($columnName === 'veganistisch' && $order === 'asc' ? 'desc' : 'asc') ?>">Veganistisch</a></th>
                <th><a href="?sort=vegetarisch&order=<?= ($columnName === 'vegetarisch' && $order === 'asc' ? 'desc' : 'asc') ?>">Vegetarisch</a></th>
                <th>Allergieën</th>
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
        <form id='form_".$row['idgezin']."' action='' method='post' onsubmit='saveChangesGezin(event, ".$row['idgezin'].")'> <!-- Voeg onsubmit toe -->
            <input type='hidden' name='idgezin' value='". $row['idgezin']. "'>
            <td>
                <span id='gezinsnaam_".$row['idgezin']."' style='display: block;'>".$row['gezinsnaam']."</span>
                <input id='gezinsnaamInput_".$row['idgezin']."' type='text' name='gezinsnaam' value='". $row['gezinsnaam'] . "' style='display: none;'>
            </td>
            <td>
                <span id='adres_".$row['idgezin']."' style='display: block;'>".$row['adres']."</span>
                <input id='adresInput_".$row['idgezin']."' type='text' name='adres' value='". $row['adres'] . "' style='display: none;'>
            </td>
            <td>
                <span id='email_".$row['idgezin']."' style='display: block;'>".$row['email']."</span>
                <input id='emailInput_".$row['idgezin']."' type='text' name='email' value='". $row['email'] . "' style='display: none;'>
            </td>
            <td>
                <span id='telefoonnummer_".$row['idgezin']."' style='display: block;'>".$row['telefoonnummer']."</span>
                <input id='telefoonnummerInput_".$row['idgezin']."' type='text' name='telefoonnummer' value='". $row['telefoonnummer'] . "' style='display: none;'>
            </td>
            <td>
                <span id='volwassenen_".$row['idgezin']."' style='display: block;'>".$row['volwassenen']."</span>
                <input id='volwassenenInput_".$row['idgezin']."' type='number' name='volwassenen' value='". $row['volwassenen'] . "' style='display: none;'>
            </td>
            <td>
                <span id='kinderen_".$row['idgezin']."' style='display: block;'>".$row['kinderen']."</span>
                <input id='kinderenInput_".$row['idgezin']."' type='number' name='kinderen' value='". $row['kinderen'] . "' style='display: none;'>
            </td>
            <td>
                <span id='babys_".$row['idgezin']."' style='display: block;'>".$row['babys']."</span>
                <input id='babysInput_".$row['idgezin']."' type='number' name='babys' value='". $row['babys'] . "' style='display: none;'>
            </td>
            <td>
                <span id='varkensvlees_".$row['idgezin']."' style='display: block;'>"; if($row['varkensvlees'] == 0){echo "nee";}else{echo "ja";} echo "</span>
                <input id='varkensvleesInput_".$row['idgezin']."' type='checkbox' name='varkensvlees' ". ($row['varkensvlees'] ? 'checked' : '') . " style='display: none;'>
            </td>
            <td>
                <span id='veganistisch_".$row['idgezin']."' style='display: block;'>"; if($row['veganistisch'] == 0){echo "nee";}else{echo "ja";} echo "</span>
                <input id='veganistischInput_".$row['idgezin']."' type='checkbox' name='veganistisch' ". ($row['veganistisch'] ? 'checked' : '') . " style='display: none;'>
            </td>
            <td>
                <span id='vegetarisch_".$row['idgezin']."' style='display: block;'>"; if($row['vegetarisch'] == 0){echo "nee";}else{echo "ja";} echo "</span>
                <input id='vegetarischInput_".$row['idgezin']."' type='checkbox' name='vegetarisch' ". ($row['vegetarisch'] ? 'checked' : '') . " style='display: none;'>
            </td>
            <td>
                <span id='allergieen_".$row['idgezin']."' style='display: block;'>".$row['allergieen']."</span>
                <textarea id='allergieenInput_".$row['idgezin']."' name='allergieen' style='display: none;'>".$row['allergieen']."</textarea>
            </td>
            <td>
                <button id='aanpassenButton_".$row['idgezin']."' type='button' onclick='openFormGezin(".$row['idgezin'].")'>Aanpassen</button>
                <input id='saveButton_". $row['idgezin']."' type='submit' value='Opslaan' name='aanpassen' style='display: none;'>
                <input id='deleteButton_".$row['idgezin']."' type='submit' value='Verwijderen' name='verwijderen' style='display: none;'>
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
