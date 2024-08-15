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

if(isset($_POST['afgegeven'])) {
    $idpakket = $_POST['idpakket'];

    $update_query = "UPDATE pakket SET datum_uitgifte=curdate() WHERE idpakket=?";
    $update_stmt = $mysqli->prepare($update_query);
    $update_stmt->bind_param("i", $idpakket);
    $update_stmt->execute();

    $update_stmt->close();
}

function sortTable($columnName, $order, $result){
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
    $search_condition = "WHERE idpakket LIKE '%$search%' OR datum_samenstelling LIKE '%$search%' OR datuum_uitgifte LIKE '%$search%' OR gezinsnaam LIKE '%$search%' OR adres LIKE '%$search%'";
}

$columnName = isset($_GET['sort']) ? $_GET['sort'] : 'idpakket';
$order = isset($_GET['order']) ? $_GET['order'] : 'asc';

$query = "SELECT pakket.idpakket, pakket.datum_samenstelling, pakket.datum_uitgifte, gezin.gezinsnaam, gezin.adres, GROUP_CONCAT(CONCAT(product.product, ':', pakket_has_product.product_aantal) SEPARATOR '<br>') AS producten_aantallen
          FROM pakket
          LEFT JOIN gezin ON pakket.gezin_idgezin = gezin.idgezin
          LEFT JOIN pakket_has_product ON pakket.idpakket = pakket_has_product.pakket_idpakket
          LEFT JOIN product ON pakket_has_product.product_idproduct = product.idproduct
          GROUP BY pakket.idpakket";
$result = $mysqli->query($query);

$data = sortTable($columnName, $order, $result);

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
    <script src="functions.js"></script>
</head>

<body>
    <?php navbar(); ?>  
    <div>
        <table>
            <tr>
                <th><a href="?sort=idpakket&order=<?= ($columnName === 'idpakket' && $order === 'asc' ? 'desc' : 'asc') ?>">Pakket nummer</a></th>
                <th><a href="?sort=datum_samenstelling&order=<?= ($columnName === 'datum_samenstelling' && $order === 'asc' ? 'desc' : 'asc') ?>">Datum samenstelling</a></th>
                <th><a href="?sort=datum_uitgifte&order=<?= ($columnName === 'datum_uitgifte' && $order === 'asc' ? 'desc' : 'asc') ?>">Datum uitgifte</a></th>
                <th><a href="?sort=gezinsnaam&order=<?= ($columnName === 'gezinsnaam' && $order === 'asc' ? 'desc' : 'asc') ?>">Gezinsnaam</a></th>
                <th><a href="?sort=adres&order=<?= ($columnName === 'adres' && $order === 'asc' ? 'desc' : 'asc') ?>">Adres</a></th>
                <th>
                </th>
            </tr>
            <?php
               foreach ($data as $row) {
                echo "<tr>";
                echo "<td>".$row['idpakket']."</td>";
                echo "<td>".$row['datum_samenstelling']."</td>";
                if(empty($row['datum_uitgifte'])){
                    echo "<td> 
                        <form action='' method='post'>
                            <input type='hidden' name='idpakket' value='". $row['idpakket']. "'>
                            <input type='submit' value='Afgegeven' name='afgegeven'>
                        </form>
                       </td>";
                }
                else{
                    echo "<td>".$row['datum_uitgifte']."</td>";
                }
                echo "<td>".$row['gezinsnaam']."</td>";
                echo "<td>".$row['adres']."</td>";
                echo "<td>".$row['producten_aantallen']."</td>";
                echo "</tr>";
                }
            ?>
        </table>
    </div>
</body>
</html>
