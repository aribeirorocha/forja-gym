<?php 

function getDatabaseConnection() : PDO {
    $db = new PDO("sqlite:../database/gym.db");
    $db->exec('PRAGMA foreign_keys = ON;');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $db;
}

?>