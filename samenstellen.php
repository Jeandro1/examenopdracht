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
    $_SESSION['idgezin'] = $_POST['idgezin'];
    $selectedgezin = $_SESSION['idgezin'];
}

if (isset($_POST['deselecteer'])) {
    unset($_SESSION['idgezin']);
}

if (isset($_POST['toevoegen'])) {
    if(!isset($_SESSION['idgezin'])) {
        echo "<script>alert('Selecteer een gezin!');</script>";
    }
    else{

        $productExists = false;
        $_SESSION['idgezin'] = $_SESSION['idgezin'];
        $selectedgezin = $_SESSION['idgezin'];
        if(isset($_SESSION['pakketinhoud'])) {
        foreach ($_SESSION['pakketinhoud'] as $key => $value) {
            if ($value['EAN'] == $_POST['EAN']) {
                $_SESSION['pakketinhoud'][$key]['aantal'] = $_POST['aantal'];
                $productExists = true;
                break;
            }
        }
        }
    if (!$productExists) {
        $_SESSION['pakketinhoud'][] = array(
            'idproduct' => $_POST['idproduct'],
            'EAN' => $_POST['EAN'],
            'product' => $_POST['product'],
            'aantal' => $_POST['aantal']
        );
    }
    }
    
    
}

if (isset($_POST['verwijderen'])) {
    $_SESSION['idgezin'] = $_SESSION['idgezin'];
    $index = array_search($_POST['idproduct'], array_column($_SESSION['pakketinhoud'], 'idproduct'));

    if ($index !== false) {
        unset($_SESSION['pakketinhoud'][$index]);
        $_SESSION['pakketinhoud'] = array_values($_SESSION['pakketinhoud']);
    }
}

if(isset($_POST['verstuur'])) {

    $autoincrement_query = "ALTER TABLE pakket AUTO_INCREMENT = 1";
    $autoincrement = $mysqli->prepare($autoincrement_query);
    $autoincrement->execute();
    $autoincrement->close();

    $idgezin = $_SESSION['idgezin'];
    $insert_query = "INSERT INTO pakket (datum_samenstelling, gezin_idgezin) VALUES (curdate(), ?)";
    $insert_stmt = $mysqli->prepare($insert_query);
    $insert_stmt->bind_param("i", $idgezin);
    $insert_stmt->execute();
    $insert_stmt->close();

    $pakket_idpakket = $mysqli->insert_id;

    foreach ($_SESSION['pakketinhoud'] as $row) {

        $aantal_pakket = $row['aantal'];
        $idproduct_pakket = $row['idproduct'];
        $query_aantal = "SELECT aantal FROM product WHERE idproduct = $idproduct_pakket";
        $result_aantal = $mysqli->query($query_aantal);
        $row_aantal = $result_aantal->fetch_assoc();
        $nieuw_aantal = $row_aantal['aantal'] - $row['aantal'];
 
        $insert_producten_query = "INSERT INTO pakket_has_product (pakket_idpakket, product_idproduct, product_aantal) VALUES (?, ?, ?)";
        $insert_producten_stmt = $mysqli->prepare($insert_producten_query);
        $insert_producten_stmt->bind_param("iii", $pakket_idpakket, $idproduct_pakket, $aantal_pakket);
        $insert_producten_stmt->execute();
        $insert_producten_stmt->close();

        $update_query = "UPDATE product SET aantal=? WHERE idproduct=?";
        $update_stmt = $mysqli->prepare($update_query);
        $update_stmt->bind_param("ii", $nieuw_aantal, $idproduct_pakket);
        $update_stmt->execute();
        $update_stmt->close();

    }

    unset($_SESSION['idgezin']);
    unset($_SESSION['pakketinhoud']);
    header("location:pakketten.php");
    exit();
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
            if(!isset($_SESSION['idgezin'])) {
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
                            <input type='submit' value='Selecteer' name='selecteer'>
                        </form>
                       </td>";
                echo "</tr>";
                }
            }
            else{
                $selectedgezin = $_SESSION['idgezin'];
                $query_selected = "SELECT * FROM gezin WHERE idgezin = $selectedgezin";
                $result_selected = $mysqli->query($query_selected);
                foreach ($result_selected as $row) {
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
                            <input type='submit' value='Deselecteer' name='deselecteer'>
                        </form>
                       </td>";
                echo "</tr>";
                }
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
                    <th><input type="submit" value="Verstuur pakketsamenstelling" name="verstuur"></th>
                </tr>
                <?php
                if(!isset($_SESSION['pakketinhoud'])) {
                    echo "<tr><td>X</td><td>X</td><td>X</td><td>X</td></tr>";
                }
                else{
                    foreach ($_SESSION['pakketinhoud'] as $row) {
                    echo "<tr>";
                    echo "<td>" . $row['EAN'] . "</td>";
                    echo "<td>" . $row['product'] . "</td>";
                    echo "<td>" . $row['aantal'] . "</td>";
                    echo "<td>
                            <form action='' method='post'>
                                <input type='hidden' name='idproduct' value='".$row['idproduct']."'> 
                                <input type='hidden' name='EAN' value='" . $row['EAN'] . "'>
                                <input type='hidden' name='product' value='" . $row['product'] . "'>
                                <input type='hidden' name='aantal' value='" . $row['aantal'] . "'>
                                <input type='submit' value='Verwijderen' name='verwijderen'>
                            </form>
                           </td>";
                    echo "</tr>";
                    }     
                
                }
                ?>
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
                            <input type='hidden' name='idproduct' value='".$row['idproduct']."'> 
                            <input type='hidden' name='EAN' value='".$row['EAN']."'> 
                            <input type='hidden' name='product' value='".$row['product']."'> 
                            <input type='hidden' name='aantal' value='".$row['aantal']."'> 
                            <input type='number' name='aantal' min='1' max='".$row['aantal']."' value='1'> 
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
