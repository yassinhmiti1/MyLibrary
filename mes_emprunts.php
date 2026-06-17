<?php
session_start();
require_once("config.php");
error_reporting(E_ALL);
ini_set('display_errors', 1);


if (!isset($_SESSION["logged_in"]) || $_SESSION["logged_in"] !== true) {
  header("Location: Index.php");
  exit();
}


$id_user_connected = $_SESSION["userid"] ?? null;

if (!$id_user_connected) {
  die("Utilisateur non identifié.");
}


$cnx->query("UPDATE emprunt SET status = 'en retard' WHERE status = 'en_cours' AND date_retour_prevu < CURDATE() AND id_user = " . (int)$id_user_connected);


$stmt = $cnx->prepare("
    SELECT e.*, l.TitreLivre, l.NumLivre, a.NomAuteur
    FROM emprunt e
    LEFT JOIN livre l ON e.NumLivre = l.NumLivre
    LEFT JOIN Auteur a ON l.NumAuteur = a.NumAuteur
    WHERE e.id_user = ?
    ORDER BY e.date_emprunt DESC
  ");
$stmt->execute([$id_user_connected]);
$mes_emprunts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Mes Emprunts - MyLibrary</title>
  <link rel="stylesheet" href="styleemp.css">
</head>
<body>

  <main class="maincontainer">
    <?php include("nav_lg.php") ?>


    <div class="container">
      <div class="header">
        <h1><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M480-40 192-256q-15-11-23.5-28t-8.5-36v-480q0-33 23.5-56.5T240-880h480q33 0 56.5 23.5T800-800v480q0 19-8.5 36T768-256L480-40Zm0-100 240-180v-480H240v480l240 180Zm-42-220 226-226-56-58-170 170-84-84-58 56 142 142Zm42-440H240h480-240Z" /></svg> Mes Livres Empruntés</h1>
        <br>
        <p>This is where you'll find all the books you've borrowed</p>
      </div>
      <br>

      <div class="cards-grid">
        <?php if (count($mes_emprunts) > 0): ?>
        <?php foreach ($mes_emprunts as $row):
        $id_emprunt = $row['id_emprunt'];
        $num_livre = $row['NumLivre'];
        $titre = $row['TitreLivre'] ?? 'Inconnu';
        $auteur = $row['NomAuteur'] ?? 'Auteur Inconnu';
        $status = $row['status'];

        $date_emp = date('d/m/Y', strtotime($row['date_emprunt']));
        $date_prevu = date('d/m/Y', strtotime($row['date_retour_prevu']));
        ?>
        <div class="book-card">
          <div class="cover-container">
            <img src="getimage.php?id=<?= $num_livre ?>" class="book-cover" alt="Cover">
          </div>

          <div class="card-content">
            <div class="genre-badge">
              Roman
            </div>
            <div class="book-title">
              <?= htmlspecialchars($titre) ?>
            </div>
            <div class="book-author">
              <?= htmlspecialchars($auteur) ?>
            </div>

            <div class="info-dates">
              📅 Emprunté le: <b><?= $date_emp ?></b><br>
              🕒 Retour prévu: <b><?= $date_prevu ?></b>
            </div>

            <div class="card-footer">
              <?php if ($status === 'retourne'): ?>
              <span class="status-badge status-retourne">Retourné</span>
              <span class="no-action">Aucune action</span>
              <?php elseif ($status === 'en retard'): ?>
              <span class="status-badge status-retard">En Retard</span>
              <form method="post" action="update_status.php">
                <input type="hidden" name="id_emprunt_render" value="<?= $id_emprunt ?>">
                <button type="submit" name="btn_rendre" class="btn-rendre btn-red">Rendre</button>
              </form>
              <?php else : ?>
              <span class="status-badge status-encours">En Cours</span>
              <form method="post" action="update_status.php">
                <input type="hidden" name="id_emprunt_render" value="<?= $id_emprunt ?>">
                <button type="submit" name="btn_rendre" class="btn-rendre btn-green">Rendre</button>
              </form>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
        <?php else : ?>
        <p style="grid-column: 1/-1; text-align: center; color: #555;">
          Vous n'avez aucun livre emprunté actuellement.
        </p>
        <?php endif; ?>
      </div>
    </div>
  </main>
</body>
</html>