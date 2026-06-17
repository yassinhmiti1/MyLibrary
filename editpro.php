<?php
session_start();
ob_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once("config.php");

if (!isset($_SESSION["logged_in"]) || $_SESSION["logged_in"] !== true) {
  header("Location: Index.php");
  exit();
}

$id_user = intval($_SESSION["userid"]);
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["enregistrer"])) {
    $new_nom = trim($_POST['nom']);
    $new_prenom = trim($_POST['prenom']);
    $new_email = trim($_POST['email']);

    if (!empty($new_nom) && !empty($new_prenom) && !empty($new_email)) {
        $sqlUpdate = "UPDATE users SET NomUser = ?, PrenomUser = ?, EmailUser = ? WHERE id_user = ?";
        $stmtUp = $cnx->prepare($sqlUpdate);
        if ($stmtUp->execute([$new_nom, $new_prenom, $new_email, $id_user])) {
            echo "<script>alert('Profil mis à jour avec succès !'); window.location.href='editpro.php';</script>";
            exit();
        }
    } else {
        echo "<script>alert('Veuillez remplir tous les champs.');</script>";
    }
}

// updated data
$sql = "SELECT u.*, r.NameRole
        FROM users u
        LEFT JOIN role r ON u.id_role = r.id_role
        WHERE u.id_user = ?";

$stmt = $cnx->prepare($sql);
$stmt->execute([$id_user]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
  die("Utilisateur introuvable.");
}

$initials = strtoupper(substr($user['PrenomUser'], 0, 1) . substr($user['NomUser'], 0, 1));
$role = !empty($user['NameRole']) ? $user['NameRole'] : "Membre";

//L3adad dyal les emprunt

$sqlemp="Select count(id_emprunt) as nmb_emp from emprunt where id_user = ?";
$reque= $cnx->prepare($sqlemp);
$reque->execute([$id_user]);
$rowem = $reque->fetch(PDO::FETCH_BOTH);
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Edit Profile</title>
  <link rel="stylesheet" href="stylepro.css">
  <style>
    .action-buttons-container { display: none; gap: 10px; margin-top: 15px; }
    .btn-save { background-color: #2e6f40; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: bold; }
    .btn-cancel { background-color: #9ca3af; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: bold; }
    .logout-box { margin-top: 20px; text-align: center; }
    .logout-btn { background-color: #dc2626; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer; font-weight: bold; width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px; }
    .input-group input:not([readonly]) { border: 1px solid #2e6f40; background-color: #fff; }
  </style>
</head>
<body>
  <main class="maincontainer">
    <?php include('nav_lg.php'); ?>
    <div class="container">
      <form method="POST" action="editpro.php" class="profile-card">
        
        <div class="profile-header-bg">
          <button type="button" class="modifier-btn" id="edit-trigger-btn" onclick="enableEditMode()">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
              <path d="M12 20h9"></path>
              <path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5Z"></path>
            </svg>
            Modifier
          </button>
        </div>

        <div class="avatar-container">
          <div class="profile-avatar"><?php echo $initials; ?></div>
        </div>

        <div class="profile-main-info">
          <h2 class="user-name"><?php echo htmlspecialchars($user['PrenomUser'] . " " . $user['NomUser']); ?></h2>
          <div class="badge-row">
            <span class="role-badge"><?php echo $role; ?></span>
            <span class="membership-date">Membre depuis May 2026</span>
          </div>
        </div>

        <div class="stats-container">
          <div class="stat-box">
            <span class="stat-label">Livres empruntés</span>
            <span class="stat-value text-green"><?php echo($rowem["nmb_emp"]);?></span>
          </div>
          <div class="stat-box">
            <span class="stat-label">Statut</span>
            <span class="stat-value text-green"><?php echo ($role); ?></span>
          </div>
          <div class="stat-box">
            <span class="stat-label">Username</span>
            <span class="stat-value text-green"><?php echo (htmlspecialchars($_SESSION["loginusern"])); ?></span>
          </div>
        </div>

        <hr class="divider">

        <div class="info-section">
          <h3>Informations Personnelles</h3>

          <div class="input-group">
            <label><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg> Nom</label>
            <input type="text" name="nom" class="editable-input" value="<?php echo htmlspecialchars($user['NomUser']); ?>" readonly required>
          </div>

          <div class="input-group">
            <label><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg> Prénom</label>
            <input type="text" name="prenom" class="editable-input" value="<?php echo htmlspecialchars($user['PrenomUser']); ?>" readonly required>
          </div>

          <div class="input-group">
            <label><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg> Email</label>
            <input type="email" name="email" class="editable-input" value="<?php echo htmlspecialchars($user['EmailUser']); ?>" readonly required>
          </div>

          <div class="action-buttons-container" id="actions-container">
            <button type="submit" name="enregistrer" class="btn-save">Enregistrer</button>
            <button type="button" class="btn-cancel" onclick="disableEditMode()">Annuler</button>
          </div>
        </div>

        <hr class="divider">

        <div class="security-section">
          <h3>Sécurité</h3>
          <a href="changepass.php" style="text-decoration: none;">
            <button type="button" class="password-btn">Changer le mot de passe</button>
          </a>
        </div>

        <hr class="divider">

        <div class="logout-box">
          <a href="logout.php" style="text-decoration: none;">
            <button type="button" class="logout-btn">
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path><polyline points="16 17 21 12 16 7"></polyline><line x1='21' y1='12' x2='9' y2='12'></line></svg>
              Déconnexion
            </button>
          </a>
        </div>

      </form>
    </div>
  </main>

  <script>
    // JS magic bach i-switche l-inputs mechanical
    const inputs = document.querySelectorAll('.editable-input');
    const actionsContainer = document.getElementById('actions-container');
    const editBtn = document.getElementById('edit-trigger-btn');

    function enableEditMode() {
        inputs.forEach(input => input.removeAttribute('readonly'));
        actionsContainer.style.display = 'flex';
        editBtn.style.display = 'none';
    }

    function disableEditMode() {
        inputs.forEach(input => input.setAttribute('readonly', true));
        actionsContainer.style.display = 'none';
        editBtn.style.display = 'flex';
    }
  </script>
</body>
</html>
