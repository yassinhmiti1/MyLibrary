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
        // Hna verification: (ila knti m-hasher l-pass dirlha password_verify, ila simple gha t-gadd b ===)
        // Ghadi ndirlis verification standard standard, t9dr t-baddalha 3la hsab kfash m-stockih f l-DB
        if ($user['PasswordUser'] === $old_pass) {
            if ($new_pass === $conf_pass) {
                // 2. N-update l-mot de passe jdid
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
  <link rel="stylesheet" href="stylepro.css">
  <style>
    body { background-color: #edf4ee; font-family: sans-serif; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; }
    .card-pass { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); width: 100%; max-width: 400px; }
    .card-pass h2 { color: #2e6f40; margin-bottom: 20px; font-size: 1.5rem; text-align: center; }
    .form-group { margin-bottom: 15px; }
    .form-group label { display: block; margin-bottom: 5px; font-weight: 600; font-size: 0.9rem; }
    .form-group input { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px; box-sizing: border-box; }
    .btn-submit-pass { background-color: #2e6f40; color: white; border: none; padding: 12px; width: 100%; border-radius: 6px; font-weight: bold; cursor: pointer; margin-top: 10px; }
    .btn-submit-pass:hover { background-color: #1e4a2a; }
    .back-link { display: block; text-align: center; margin-top: 15px; color: #666; text-decoration: none; font-size: 0.9rem; }
  </style>
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
