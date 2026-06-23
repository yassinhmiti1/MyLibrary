<?php

$srv = "192.168.1.6";
$db = "bibiotique_102";
$us = "root";
$pass = "root";

$cnx = new PDO("mysql:host=$srv; dbname=$db", $us, $pass);
?>