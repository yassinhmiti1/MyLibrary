<?php
session_start();
ob_start();
require_once("config.php");

if (!isset($_SESSION["logged_in"]) || $_SESSION["logged_in"] !== true) {
  header("Location: Index.php");
  exit();
}

$id_user = intval($_SESSION["userid"]);

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["change_password"])) {
    $old_pass = $_POST['old_password'];
    $new_pass = $_POST['new_password'];
    $conf_pass = $_POST['conf_password'];

    // 1. N-verify_w l-mot de passe l-9dim men l-database
    $sql = "SELECT PasswordUser FROM users WHERE id_user = ?";
    $stmt = $cnx->prepare($sql);
    $stmt->execute([$id_user]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        if ($user['PasswordUser'] === $old_pass) {
            if ($new_pass === $conf_pass) {
                $sqlUpdate = "UPDATE users SET PasswordUser = ? WHERE id_user = ?";
                $stmtUp = $cnx->prepare($sqlUpdate);
                $stmtUp->execute([$new_pass, $id_user]);

                echo "<script>alert('Mot de passe changé avec succès !'); window.location.href='Index.php';</script>";
                exit();
            } else {
                echo "<script>alert('Les nouveaux mots de passe ne correspondent pas !');</script>";
            }
        } else {
            echo "<script>alert('L\'ancien mot de passe est incorrect !');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Changer le mot de passe</title>
  <link rel="stylesheet" href="stylepass.css">
  <script src="scriptpas.js" defer></script>
</head>
<body>

  <div class="card-pass">
    <h2>Changer le mot de passe</h2>
    <form method="POST">
      <div class="form-group">
        <label>Ancien mot de passe :</label>
        <input type="password" name="old_password" required placeholder="Saisir l'ancien code">
      </div>
      
      <div class="form-group">
        <label>Nouveau mot de passe :</label>
        <input type="password" name="new_password" required placeholder="Minimum 6 caractères">
      </div>

      <div class="form-group">
        <label>Confirmer le nouveau mot de passe :</label>
        <input type="password" name="conf_password" required placeholder="Ressaisir le nouveau code">
      </div>

      <button type="submit" name="change_password" class="btn-submit-pass">Valider le changement</button>
      <a href="editpro.php" class="back-link">Retour au profil</a>
    </form>
  </div>

</body>
</html>
