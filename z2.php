<?php
session_start();
$u = "localhost";
$p = "localhost";
$hostname = "localhost";
$dbname = "localhost";

$conn = new PDO("mysql:host={$hostname};dbname={$dbname};port=3306", $u, $p);
?>