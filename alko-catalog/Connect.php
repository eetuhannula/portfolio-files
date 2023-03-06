<?php

// DATABASE TAULUN NIMI:
$tablename = "products";

// YHTEYS DATABASEEN "catalog"
function connect() {
    $servername = "localhost";
    $username = "root";
    $password = "eetuh";
    $db = "catalog";
    
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$db", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        //echo "CONNECTION SUCCESFULL <br>";
        return $conn;
    } catch (PDOException $e) {
        echo "CONNECTION FAILED:  ". $e->getMessage();
    }
}


