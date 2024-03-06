<?php
include('db.php');

if(isset($_SESSION["loggedin"])){
    header("location:pakketten.php");
    exit;
}

// Check the connection
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

if (isset($_GET["loguit"])) {
    $_SESSION = array();
    session_destroy();
}

if (isset($_POST['groeneknop'])) {
    $username = $_POST["login"];
    $password = $_POST["pwd"];

    // Prepare the SQL statement
    $query = "SELECT * FROM mederwerker WHERE gebruikersnaam = ? AND wachtwoord = ?";
    $stmt = $mysqli->prepare($query);

    if (!$stmt) {
        die("Error in SQL query: " . $mysqli->error);
    }

    // Bind parameters and execute the statement
    if (!$stmt->bind_param("ss", $username, $password)) {
        die("Error binding parameters: " . $stmt->error);
    }

    // Execute the statement
    if (!$stmt->execute()) {
        die("Error executing query: " . $stmt->error);
    }

    // Get the result
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // User found in the database
        $row = $result->fetch_assoc();
        $_SESSION["gebruikersnaam"] = array(
            "gebruikersnaam" => $row["gebruikersnaam"],
            "wachtwoord" => $row["wachtwoord"],
            "functie" => $row["functie"]
        );

        $message = "Welcome " . $_SESSION["gebruikersnaam"]["functie"] . " with the role " . $_SESSION["gebruikersnaam"]["functie"];

        if ($_SESSION["gebruikersnaam"]["functie"] == "medewerker") {
            // Redirect to the admin page
            header("Location: voorraad.php");
            exit;
        }
    } else {
        // User not found in the database or password is incorrect
        $message = "Login failed";
    }
}

// Close the database connection
$mysqli->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inloggen</title>
    <link rel="icon" type="image/png" href="images/icon.png">
    <link rel="stylesheet" href="styling1.css">
    <link rel="stylesheet" href="navbar1.css">
</head>

<body class="backgroundimage">

    <div class="navbar">
        <div class="navicon">
            <img class="icon" src="images/icon.png" width="45px" height="45px" style="padding: 10px;">
            <h2 class="icon">Maaskantje</h2>
        </div>
        <div class="navitems">
            <a href="index.php">
                <p class="knop">Home</p>
            </a>
            <a href="register.php">
                <p class="knop">Registreren</p>
            </a>
            <a href="login.php">
                <p class="groeneknop">Inloggen</p>
            </a>
        </div>
        <div class="dropdown">
            <div class="linegroup" onclick="dropdownFunction()">
                <div class="line"></div>
                <div class="line"></div>
                <div class="line"></div>
            </div>
            <div id="dropdownItems" class="dropdown-content">
                <a href="index.php">Home</a>
                <a href="register.php">Registreren</a>
                <a href="login.php">Inloggen</a>
            </div>
        </div>
    </div>

    <!-- ----------------------------------------------------------------------------------------------------------- -->

    <div class="forms">
        <form action="" method="post">
            <?php
                if(isset($_POST["login"])){
                    if(empty($_POST["login"]) || empty($_POST["pwd"])){
                        echo "Alle velden moeten worden ingevuld!";
                    } else {
                        // Do something when login is pressed
                    }    
                }
            ?>
            <div class="formitem">gebruikersnaam<input type="text" name="login" value=""></div>
            <div class="formitem">Wachtwoord<input type="password" name="pwd" value=""></div>
            <div class="formitem"><input class="groeneknop" type="submit" name="groeneknop" value="Log in"></div>
        </form>
        <a href="register.php">
            <p>Heb je nog geen account? Registreer je hier!</p>
        </a>
    </div>

    <!-- ----------------------------------------------------------------------------------------------------------- -->

    <footer>
    </footer>
    <script src="functions.js"></script>
</body>
</html>
