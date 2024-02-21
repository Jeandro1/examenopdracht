<?php
session_start();
$u = "sql11685675";
$p = "gnlHuRDwgY";
$hostname = "sql11.freesqldatabase.com";
$dbname = "sql11685675";

$conn = new PDO("mysql:host={$hostname};dbname={$dbname};port=3306", $u, $p);
?>