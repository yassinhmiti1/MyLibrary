<?php

require_once("config.php");

$id = $_GET["id"];

$sql = "SELECT photo FROM livre WHERE NumLivre = ?";
$stmt = $cnx->prepare($sql);
$stmt->execute([$id]);

$row = $stmt->fetch(PDO::FETCH_ASSOC);

header("Content-Type: image/jpeg");

echo $row["photo"];