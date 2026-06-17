<?php
session_start();
require_once("config.php");

if (isset($_POST['btn_rendre']) && isset($_POST['id_emprunt_render'])) {
    $id_emprunt = (int)$_POST['id_emprunt_render'];

    try {
        $stmt = $cnx->prepare("UPDATE emprunt SET status = 'retourne', date_retour = CURDATE() WHERE id_emprunt = ?");
        $stmt->execute([$id_emprunt]);
        
        header("Location: mes_emprunts.php"); 
        exit();
    } catch (Exception $e) {
        echo "Erreur: " . $e->getMessage();
    }
}
?>
