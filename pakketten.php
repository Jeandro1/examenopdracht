<?php
include('db.php');

// Check if user is logged in
if (!isset($_SESSION['gebruikersnaam'])) {
    header("location:login.php");
    exit();
}

// Check user role
if ($_SESSION['functie'] != "directie" && $_SESSION['functie'] != "vrijwilliger") {
    header("location:account.php");
    exit();
}

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
    <script>
        function toevoegenAanOverzicht(idproduct) {
            // Zoek de geselecteerde elementen
            var ean = document.getElementById('EAN_' + idproduct).innerText;
            var naam = document.getElementById('product_' + idproduct).innerText;
            var aantal = document.getElementById('aantal_' + idproduct).innerText;
            var categorie = document.getElementById('categorie_' + idproduct).innerText;

            // Maak een nieuwe rij voor het overzicht
            var newRow = "<tr id='rij_" + idproduct + "'>";
            newRow += "<td>" + ean + "</td>";
            newRow += "<td>" + naam + "</td>";
            newRow += "<td>" + aantal + "</td>";
            newRow += "<td>" + categorie + "</td>";
            newRow += "<td><button onclick='verwijderUitOverzicht(" + idproduct + ")'>Verwijder</button></td>";
            newRow += "</tr>";

            // Voeg de nieuwe rij toe aan het overzicht
            document.getElementById('overzichtTable').innerHTML += newRow;

            // Toon de knop voor het verwijderen van producten
            document.getElementById('verwijderButton').style.display = 'inline';
        }

        function verwijderUitOverzicht(idproduct) {
            // Zoek de rij die verwijderd moet worden
            var row = document.getElementById('rij_' + idproduct);
            // Verwijder de rij uit de tabel
            row.parentNode.removeChild(row);

            // Verberg de knop voor het verwijderen van producten als er geen rijen meer zijn
            if (document.getElementById('overzichtTable').getElementsByTagName('tr').length === 1) {
                document.getElementById('verwijderButton').style.display = 'none';
            }
        }
    </script>
</head>

<body>
    <?php navbar(); 
    $columnName = isset($_GET['sort']) ? $_GET['sort'] : 'product';
$order = isset($_GET['order']) ? $_GET['order'] : 'asc';

$query = "SELECT * FROM product";
$result = $mysqli->query($query);
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
$data = sortTable($columnName, $order, $result);
?>
                <div class="overzicht">
                <table>
                    <tr>
                        <th><a href="?sort=EAN&order=<?= ($columnName === 'EAN' && $order === 'asc' ? 'desc' : 'asc') ?>">EAN</a></th>
                        <th><a href="?sort=product&order=<?= ($columnName === 'product' && $order === 'asc' ? 'desc' : 'asc') ?>">Naam</a></th>
                        <th><a href="?sort=aantal&order=<?= ($columnName === 'aantal' && $order === 'asc' ? 'desc' : 'asc') ?>">Aantal</a></th>
                        <th><a href="?sort=categorie&order=<?= ($columnName === 'categorie' && $order === 'asc' ? 'desc' : 'asc') ?>">Categorie</a></th>
                        <th><a>verzend</a></th>
                    </tr>
                    <?php
                       foreach ($data as $row) {
                            echo "<tr>";
                            echo "
                                <td><span id='EAN_" . $row['idproduct'] . "'>" . $row['EAN'] . "</span></td>
                                <td><span id='product_" . $row['idproduct'] . "'>" . $row['product'] . "</span></td>
                                <td><span id='aantal_" . $row['idproduct'] . "'>" . $row['aantal'] . "</span></td>
                                <td><span id='categorie_" . $row['idproduct'] . "'>" . $row['categorie'] . "</span></td>
                                <td><button onclick='toevoegenAanOverzicht(" . $row['idproduct'] . ")'>verzend</button></td>
                            ";
                            echo "</tr>";
                        }
                    ?>
                </table>
            </div>
<div class="productoverzicht">
        <table id="overzichtTable">
            <tr>
                <th>EAN</th>
                <th>Naam</th>
                <th>Aantal</th>
                <th>Categorie</th>
                <th>Verwijder</th>
            </tr>
        </table>
        <ul>
  
        </ul>

    </div>


   

 
       
    </div>

    <footer>
    </footer>
</body>

</html>
