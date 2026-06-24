<?php
ob_start();
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once("config.php");

// 1. Vérification de l'ID du livre
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: home.php");
    exit();
}

// 2. Vérification de l'authentification
if (!isset($_SESSION["logged_in"]) || $_SESSION["logged_in"] !== true || !isset($_SESSION["userid"])) {
    header("Location: Index.php"); 
    exit();
}

$id_user = $_SESSION["userid"];

// 3. Récupération des infos de l'utilisateur
$sqlus = "SELECT * FROM users WHERE id_user = ?"; // Semicolon dima hna!
$req = $cnx->prepare($sqlus);
$req->execute([$id_user]);
$user = $req->fetch(PDO::FETCH_BOTH);

// 4. Récupération des infos du livre
$id_livre = (int)$_GET['id'];
$sql = "SELECT l.*, a.NomAuteur FROM livre l JOIN Auteur a ON l.NumAuteur = a.NumAuteur WHERE l.NumLivre = ?";
$stmt = $cnx->prepare($sql);
$stmt->execute([$id_livre]);
$livre = $stmt->fetch();

if (!$livre) {
    echo "<script>alert('Livre introuvable !'); window.location.href='home.php';</script>";
    exit();
}

// 5. Traitement du formulaire POST
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["confirmer_emprunt"])) {
    $nom = $_POST['nom_complet'] ?? '';
    $email = $_POST['email'] ?? '';
    $tel = $_POST['telephone'] ?? '';
    $date_emp = $_POST['date_emprunt'] ?? '';
    $date_ret = $_POST['date_retour'] ?? '';
    $qty = (int)($_POST['quantite'] ?? 1);

    if (!empty($nom) && !empty($email) && !empty($tel) && !empty($date_emp) && !empty($date_ret)) {
      
        if ($livre['nbr_exmp'] >= $qty) {
            try {
                $cnx->beginTransaction();

                // Insertion de l'emprunt
                $sqlInsert = "INSERT INTO emprunt (id_user, NumLivre, nom_complet, email, telephone, date_emprunt, date_retour_prevu, quantite) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
                $stmtIns = $cnx->prepare($sqlInsert);
                $stmtIns->execute([$id_user, $id_livre, $nom, $email, $tel, $date_emp, $date_ret, $qty]);

                // Mise à jour du stock
                $sqlUpdateStock = "UPDATE livre SET nbr_exmp = nbr_exmp - ? WHERE NumLivre = ?";
                $stmtUp = $cnx->prepare($sqlUpdateStock);
                $stmtUp->execute([$qty, $id_livre]);

                $cnx->commit();
                echo "<script>alert('Votre demande d emprunt a été validée avec succès !'); window.location.href='home.php';</script>";
                exit();
            } catch (Exception $e) {
                $cnx->rollBack();
                echo "<script>alert('Erreur lors de la validation: " . addslashes($e->getMessage()) . "');</script>";
            }
        } else {
            echo "<script>alert('Désolé, le stock est insuffisant pour cette quantité.');</script>";
        }
    } else {
        echo "<script>alert('Veuillez remplir tous les champs obligatoires.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmer l'Emprunt - MyLibrary</title>
    <link rel="stylesheet" href="styleemp1.css">
  <script src="scriptemp1.js" defer></script>
</head>
<body>

    <div class="emprunt-container">
        <div class="book-preview">
            <img src="getimage.php?id=<?php echo (int)$livre['NumLivre']; ?>" alt="Cover">
            <h2><?php echo htmlspecialchars($livre['TitreLivre'] ?? ''); ?></h2>
            <p>par <?php echo htmlspecialchars($livre['NomAuteur'] ?? ''); ?></p>
            <div class="badge-dispo"><?php echo (int)$livre['nbr_exmp']; ?> exemplaires restants</div>
        </div>

        <div class="form-section">
            <h1>Formulaire d'Emprunt</h1>
            <form method="post">
                <div class="form-group">
                    <label>Nom Complet :</label>
                    <input type="text" name="nom_complet" required placeholder="Entrez votre nom complet">
                </div>

                <div class="form-group">
                    <label>Adresse Email :</label>
                    <input type="email" name="email" required value="<?php echo htmlspecialchars($user['EmailUser'] ?? ''); ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Numéro de Téléphone :</label>
                    <input type="tel" name="telephone" required placeholder="+212 600000000">
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Date d'Emprunt :</label>
                        <input type="date" name="date_emprunt" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Date Retour Prévu :</label>
                        <input type="date" name="date_retour" value="<?php echo date('Y-m-d', strtotime('+14 days')); ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Quantité :</label>
                    <input type="number" name="quantite" value="1" min="1" max="<?php echo (int)$livre['nbr_exmp']; ?>" required>
                </div>

                <button type="submit" name="confirmer_emprunt" class="btn-submit">Confirmer l'Emprunt</button>
                <a href="home.php" class="btn-cancel">Retour à l'accueil</a>
            </form>
        </div>
    </div>

</body>
</html>
