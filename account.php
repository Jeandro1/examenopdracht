<?php
include('db.php');

if(!isset($_SESSION['gebruikersnaam'])) {
    header("location:login.php");
    exit();
}

if (isset($_POST['aanpassen'])) {
    $username = $_SESSION['gebruikersnaam'];
    $wachtwoord = $_POST['wachtwoord'];
    $nieuwwachtwoord = $_POST['nieuwwachtwoord'];
    $herhaalwachtwoord = $_POST['herhaalwachtwoord'];
    $idmedewerker = $_SESSION['idmedewerker'];

    if($nieuwwachtwoord !== $herhaalwachtwoord){
        echo "<script>alert('Wachtwoorden komen niet overeen!');</script>";
    }
    else {
        $query = "SELECT * FROM medewerker WHERE gebruikersnaam = ?";
        $stmt = $mysqli->prepare($query);

        if (!$stmt->bind_param("s", $username)) {
            die("Error binding parameters: " . $stmt->error);
        }

        if (!$stmt->execute()) {
            die("Error executing query: " . $stmt->error);
        }

        $result = $stmt->get_result();

        if ($row = $result->fetch_assoc()) {
            if(password_verify($wachtwoord, $row['wachtwoord'])) {
                $hashedwachtwoord = password_hash($nieuwwachtwoord, PASSWORD_DEFAULT);
                $update_query = "UPDATE medewerker SET wachtwoord=? WHERE idmedewerker=?";
                $update_stmt = $mysqli->prepare($update_query);
                $update_stmt->bind_param("si", $hashedwachtwoord, $idmedewerker);
                $update_stmt->execute();
                $update_stmt->close();
                echo "<script>alert('Wachtwoord is succesvol gewijzigd!');</script>";
            }
            else{
                echo "<script>alert('Oud wachtwoord incorrect!');</script>";
            }
        }  
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account</title>
    <link rel="icon" type="image/png" href="images/icon.png">
    <link rel="stylesheet" href="styling2.css">
    <link rel="stylesheet" href="navbar.css">
</head>

<body>
    <?php navbar(); ?>  

    <!-- ----------------------------------------------------------------------------------------------------------- -->

    <div>
            <table>
                <tr>
                    <th>Voornaam</th>
                    <th>Achternaam</th>
                    <th>Gebruikersnaam</th>
                    <th>Functie</th>
                </tr>

    <?php
        echo "<tr>";
        echo "<td>".$_SESSION['voornaam']."</td>";
        echo "<td>".$_SESSION['achternaam']."</td>";
        echo "<td>".$_SESSION['gebruikersnaam']."</td>";
        echo "<td>".$_SESSION['functie']."</td>";
        echo "</tr>";
    ?>
</table>

<div class="aanpassen">
        <form action="" method="post">
            <table>
                <tr>
                    <th>Wachtwoord aanpassen</th>
                    <th><input type="submit" value="Aanpassen" name="aanpassen"></th>
                </tr>
                <tr>
                    <td>Oud wachtwoord:</td>
                    <td><input type="text" name="wachtwoord"></td>
                </tr>
                <tr>
                    <td>Nieuw wachtwoord:</td>
                    <td><input type="text" name="nieuwwachtwoord"></td>
                </tr>
                <tr>
                    <td>Herhaal nieuw wachtwoord:</td>
                    <td><input type="text" name="herhaalwachtwoord"></td>
                </tr>
            </table>
        </form>
</div>

</body>
</html>