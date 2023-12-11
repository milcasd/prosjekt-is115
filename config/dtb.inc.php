<?php
define("DB_VERT", "localhost");
define("DB_BRUKER", "root");
define("DB_PASS", "");
define("DB_NAVN", "jobquest");

try {
    $pdo = new PDO("mysql:host=". DB_VERT . ";dbname=" . DB_NAVN, DB_BRUKER, DB_PASS);
    //echo "Tilkobling til databasen er utført.";
} catch (PDOException $e) {
    //echo "Feil ved tilkoblingen til databasen: " . $e->getMessage();
}
?>