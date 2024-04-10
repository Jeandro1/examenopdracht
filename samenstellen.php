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

if (isset($_POST['selecteer'])) {
    $idgezin = $_POST['idgezin'];
}

if (isset($_POST['toevoegen'])) {
    // product toevoegen aan pakket overzicht
}

if(isset($_POST['verstuur'])) {
    // pakket koppelen aan gezin en aantal van producten aanpassen(het aantal producten in de voorraad - het aantal van de producten die in het pakket zijn toegevoegd)
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
    $search_condition = "WHERE EAN LIKE '%$search%' OR product LIKE '%$search%' OR aantal LIKE '%$search%' OR categorie LIKE '%$search%'";
}

$searchGezin = isset($_GET['searchGezin']) ? $_GET['searchGezin'] : '';
$search_condition_gezin = '';
if (!empty($searchGezin)) {
    $search_condition_gezin = "WHERE gezinsnaam LIKE '%$searchGezin%'";
}

$columnName = isset($_GET['sort']) ? $_GET['sort'] : 'product';
$order = isset($_GET['order']) ? $_GET['order'] : 'asc';
$query = "SELECT * FROM product $search_condition";
$result = $mysqli->query($query);

$data = sortTable($columnName, $order, $result);

$query_gezin = "SELECT * FROM gezin $search_condition_gezin";
$result_gezin = $mysqli->query($query_gezin);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Samenstellen</title>
    <link rel="icon" type="image/png" href="images/icon.png">
    <link rel="stylesheet" href="styling2.css">
    <link rel="stylesheet" href="navbar.css">
    <script src="functions.js"></script>
</head>

<body>
    <?php navbar(); ?>  

    <div class="overzichtgezin">
        <table>
            <tr>
                <th>Gezinsnaam</th>
                <th>Volwassenen</th>
                <th>Kinderen</th>
                <th>Baby's</th>
                <th>Geen varkensvlees</th>
                <th>Veganistisch</th>
                <th>Vegetarisch</th>
                <th>Allergieën</th>
                <th>
                    <form action="" method="get">
                        <input type="text" name="searchGezin" placeholder="Zoeken...">
                        <input type="submit" value="Zoeken">
                    </form>
                </th>
            </tr>
            <?php
               foreach ($result_gezin as $row) {
                echo "<tr>"; 
                echo "<td>".$row['gezinsnaam']."</td>"; 
                echo "<td>".$row['volwassenen']."</td>"; 
                echo "<td>".$row['kinderen']."</td>"; 
                echo "<td>".$row['babys']."</td>"; 
                echo "<td>"; if($row['varkensvlees'] == 0){echo "nee";}else{echo "ja";} echo "</td>"; 
                echo "<td>"; if($row['veganistisch'] == 0){echo "nee";}else{echo "ja";} echo "</td>"; 
                echo "<td>"; if($row['vegetarisch'] == 0){echo "nee";}else{echo "ja";} echo "</td>";
                echo "<td>".$row['allergieen']."</td>";
                echo "<td> 
                        <form action='' method='post'>
                            <input type='hidden' name='idgezin' value='". $row['idgezin']. "'>
                            <button id='selecteer' type='button' onclick='selecteerGezin'>Selecteer</button>
                        </form>
                       </td>";
                echo "</tr>";
                }
            ?>
        </table>
    </div>

    <div class="overzichtpakket">
        <form action="" method="post">
            <table>
                <tr>
                    <th>EAN</th>
                    <th>Naam</th>
                    <th>Aantal</th>
                    <th><input type="submit" value="Verstuur samenstelling" name="=verstuur"></th>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </table>
        </form>
    </div>
    
    <div class="overzichtvoorraad">
        <table>
            <tr>
                <th><a href="?sort=EAN&order=<?= ($columnName === 'EAN' && $order === 'asc' ? 'desc' : 'asc') ?>">EAN</a></th>
                <th><a href="?sort=product&order=<?= ($columnName === 'product' && $order === 'asc' ? 'desc' : 'asc') ?>">Naam</a></th>
                <th><a href="?sort=aantal&order=<?= ($columnName === 'aantal' && $order === 'asc' ? 'desc' : 'asc') ?>">Aantal</a></th>
                <th><a href="?sort=categorie&order=<?= ($columnName === 'categorie' && $order === 'asc' ? 'desc' : 'asc') ?>">Categorie</a></th>
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
                echo "<td>".$row['EAN']."</td>"; 
                echo "<td>".$row['product']."</td>"; 
                echo "<td>".$row['aantal']."</td>"; 
                echo "<td>".$row['categorie']."</td>"; 
                echo "<td> 
                        <form action='' method='post'>
                            <input type='number' name='aantal' min='1' value='1'> 
                            <input type='submit' value='Toevoegen' name='toevoegen'> 
                        </form>
                       </td>";
                echo "</tr>";
                }
            ?>
        </table>
    </div>
</body>
</html>
