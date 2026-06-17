<?php
require_once("config.php");
session_start();

header('Content-Type: application/json');

$session_key = isset($_SESSION['userid']) ? 'userid' : (isset($_SESSION['id_user']) ? 'id_user' : null);

if (!$session_key) {
    echo json_encode([
        'status' => 'auth_required', 
        'message' => 'You must log in first!'
    ]);
    exit;
}

if (!isset($_POST['id_livre'])) {
    echo json_encode(['status' => 'error', 'message' => 'Book code invalid!']);
    exit;
}

$id_user = $_SESSION[$session_key]; 
$id_livre = intval($_POST['id_livre']);

try {
    
    $check = $cnx->prepare("SELECT * FROM favoris WHERE id_user = ? AND id_livre = ?");
    $check->execute([$id_user, $id_livre]);
    
    if ($check->rowCount() > 0) {
        
        $del = $cnx->prepare("DELETE FROM favoris WHERE id_user = ? AND id_livre = ?");
        $del->execute([$id_user, $id_livre]);
        
        
        echo json_encode(['status' => 'removed', 'message' => 'Removed from Favorites!']);
    } else {
        $ins = $cnx->prepare("INSERT INTO favoris (id_user, id_livre) VALUES (?, ?)");
        $ins->execute([$id_user, $id_livre]);
        
        echo json_encode(['status' => 'added', 'message' => 'Added to favorites successfully!']);
    }
} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' =>'An error occurred on the server' . $e->getMessage()]);
}
?>
