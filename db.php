<?php

session_start();

$hostname = "localhost";
$username = "root";
$password = "";
$database = "maaskantje";

$mysqli = new mysqli($hostname, $username, $password, $database);

?>