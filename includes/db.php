<?php
// includes/db.php


$conf = 0;

if ($conf == 1) {

    $host = 'localhost';
    $db = 'dbuzwvgswctwj0';
    $user = 'uakgcogyqodis';
    $pass = '(Aw5$c2b1+*|';
    $charset = 'utf8mb4'; 
    
} else {

    $host = 'localhost';
    $db = 'ofm';
    $user = 'root';
    $pass = '';
    $charset = 'utf8mb4';
    
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=$charset", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Error en la conexión a la base de datos: " . $e->getMessage());
}
?>
