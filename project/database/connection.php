<?php 

function getDatabaseConnection() : PDO {
    $db = new PDO("sqlite:../database/gym.db");
    return $db;
}

?>