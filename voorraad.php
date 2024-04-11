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

// Product toevoegen
if (isset($_POST['toevoegen'])) {
    $product = $_POST['product'];
    $categorie = $_POST['categorie'];
    $aantal = $_POST['aantal'];

    $check_query = "SELECT * FROM product WHERE product = ?";
    $check_stmt = $mysqli->prepare($check_query);
    $check_stmt->bind_param("s", $product);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo "<script>alert('Dit product bestaat al!');</script>";
    } else {
        $autoincrement_query = "ALTER TABLE product AUTO_INCREMENT = 1";
        $autoincrement = $mysqli->prepare($autoincrement_query);
        $autoincrement->execute();
        $autoincrement->close();
        
        $EAN = generateUniqueEAN($mysqli);
        $insert_query = "INSERT INTO product (product, categorie, EAN, aantal) VALUES (?, ?, ?, ?)";
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

// Categorie toevoegen
if (isset($_POST['categorietoevoegen'])) {
    $nieuwecategorie = $_POST['nieuwecategorie'];

    $check_query = "SELECT * FROM categorie WHERE categorie = ?";
    $check_stmt = $mysqli->prepare($check_query);
    $check_stmt->bind_param("s", $nieuwecategorie);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo "<script>alert('Deze categorie bestaat al!');</script>";
    } else {
        
        $insert_query = "INSERT INTO categorie (categorie) VALUES (?)";
        $insert_stmt = $mysqli->prepare($insert_query);

        if (!$insert_stmt) {
            die("Error in SQL query: " . $mysqli->error);
        }

        if (!$insert_stmt->bind_param("s", $nieuwecategorie)) {
            die("Error binding parameters: " . $insert_stmt->error);
        }

        if (!$insert_stmt->execute()) {
            die("Error executing query: " . $insert_stmt->error);
        }

        $insert_stmt->close();
    }
}


function generateUniqueEAN($mysqli) {
    $EAN = rand(9000000000001, 9999999999999);

    $check_query_EAN = "SELECT * FROM product WHERE EAN = ?";
    $check_stmt_EAN = $mysqli->prepare($check_query_EAN);
    $check_stmt_EAN->bind_param("s", $EAN);
    $check_stmt_EAN->execute();
    $check_result_EAN = $check_stmt_EAN->get_result();
    $check_stmt_EAN->close();

    if ($check_result_EAN->num_rows > 0) {
        $EAN = generateUniqueEAN($mysqli);
    }

    return $EAN;
}

// Product aanpassen / verwijderen
if(isset($_POST['verwijderen'])) {
    $idproduct = $_POST['idproduct'];

    $delete_query = "DELETE FROM product WHERE idproduct = ?";
    $delete_stmt = $mysqli->prepare($delete_query);

    $delete_stmt->bind_param("i", $idproduct);
    $delete_stmt->execute();

    $delete_stmt->close();
}

if(isset($_POST['aanpassen'])) {
    $idproduct = $_POST['idproduct'];
    $product = $_POST['product'];
    $categorie = $_POST['categorie'];
    $aantal = $_POST['aantal'];

    $update_query = "UPDATE product SET product=?, categorie=?, aantal=? WHERE idproduct=?";
    $update_stmt = $mysqli->prepare($update_query);
    $update_stmt->bind_param("sssi", $product, $categorie, $aantal, $idproduct);
    $update_stmt->execute();

    $update_stmt->close();
}

// Categorie aanpassen / verwijderen
if(isset($_POST['categorieaanpassen'])) {
    $idcategorie = $_POST['idcategorie'];
    $gewijzigdecategorie = $_POST['gewijzigdecategorie'];

    $update_query = "UPDATE categorie SET categorie=? WHERE idcategorie=?";
    $update_stmt = $mysqli->prepare($update_query);
    $update_stmt->bind_param("si", $gewijzigdecategorie, $idcategorie);
    $update_stmt->execute();

    $update_stmt->close();
}

if(isset($_POST['categorieverwijderen'])) {
    $idcategorie = $_POST['idcategorie'];

    $delete_query = "DELETE FROM categorie WHERE idcategorie = ?";
    $delete_stmt = $mysqli->prepare($delete_query);

    $delete_stmt->bind_param("i", $idcategorie);
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
    $search_condition = "WHERE EAN LIKE '%$search%' OR product LIKE '%$search%' OR aantal LIKE '%$search%' OR categorie LIKE '%$search%'";
}

$columnName = isset($_GET['sort']) ? $_GET['sort'] : 'product';
$order = isset($_GET['order']) ? $_GET['order'] : 'asc';

$query = "SELECT * FROM product $search_condition";
$result = $mysqli->query($query);

$data = sortTable($columnName, $order, $result);

$resultcategoriequery = "SELECT * FROM categorie";
$resultcategorie = $mysqli->query($resultcategoriequery);

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
    <script src="functions.js"></script>
</head>

<body>
    <?php navbar(); ?>  

    <div class="toevoegen">
        <form action="" method="post">
            <table>
                <tr>
                    <th>Naam</th>
                    <th>Aantal</th>
                    <th>Categorie</th>
                    <th>Product toevoegen</th>
                </tr>
                <tr>
                    <td><input type="text" name="product"></td>
                    <td><input type="number" name="aantal" min="1"></td>
                    <td><select name="categorie">
                        <?php
                        foreach($resultcategorie as $row){
                            echo "<option value='" . $row['categorie'] . "'>" . $row['categorie'] . "</option>";
                        }
                        ?>  
                    </select></td>
                    <td><input type="submit" value="Toevoegen" name="toevoegen"></td>
                </tr>
            </table>
        </form>
    </div>

    <div class="categorietoevoegen">
        <form action="" method="post">
            <table>
                <tr>
                    <th>Categorie</th>
                    <th>Toevoegen</th>
                    <th>Categorie</th>
                    <th>Nieuwe categorie omschrijving</th>
                    <th>Aanpassen</th>
                    <th>Verwijderen</th>
                </tr>
                <tr>
                    <td><input type="text" name="nieuwecategorie"></td>
                    <td><input type="submit" value="Toevoegen" name="categorietoevoegen"></td>
                    <td><select name="idcategorie">
                        <?php
                        foreach($resultcategorie as $row){
                            echo "<option value='" . $row['idcategorie'] . "'>" . $row['categorie'] . "</option>";
                        }
                        ?>  
                    </select></td>
                    <td><input type="text" name="gewijzigdecategorie"></td>
                    <td><input type="submit" value="Aanpassen" name="categorieaanpassen"></td>
                    <td><input type="submit" value="Verijderen" name="categorieverwijderen"></td>
                </tr>
            </table>
        </form>
    </div>
    
    <div class="overzicht">
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
    echo "
        <form id='form_".$row['idproduct']."' action='' method='post' onsubmit='return saveChangesProduct(event, ".$row['idproduct'].")'> <!-- Voeg onsubmit toe -->
            <input type='hidden' name='idproduct' value='". $row['idproduct']. "'>
            <td>
                ".$row['EAN']."
            </td>
            <td>
                <span id='product_".$row['idproduct']."' style='display: block;'>".$row['product']."</span>
                <input id='productInput_".$row['idproduct']."' type='text' name='product' value='". $row['product'] . "' style='display: none;'>
            </td>
            <td>
                <span id='aantal_".$row['idproduct']."' style='display: block;'>".$row['aantal']."</span>
                <input id='aantalInput_".$row['idproduct']."' type='text' name='aantal' value='". $row['aantal'] . "' style='display: none;' required>
            </td>
            <td>
                <span id='categorie_".$row['idproduct']."' style='display: block;'>".$row['categorie']."</span>
                <select id='categorieSelect_".$row['idproduct']."' name='categorie' style='display: none;'>";

                    foreach($resultcategorie as $categorieRow) {
                        echo "<option value='".$categorieRow['categorie']."'>".$categorieRow['categorie']."</option>";
                    }

                echo "</select>
            </td>
            <td>
                <button id='aanpassenButton_".$row['idproduct']."' type='button' onclick='openFormProduct(".$row['idproduct'].")'>Aanpassen</button>
                <input id='saveButton_".$row['idproduct']."' type='submit' value='Opslaan' name='aanpassen' style='display: none;'>
                <input id='deleteButton_".$row['idproduct']."' type='submit' value='Verwijderen' name='verwijderen' style='display: none;'>
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
