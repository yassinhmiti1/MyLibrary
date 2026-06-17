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
    <link rel="stylesheet" href="style.css">
    <style>
        :root {
            --brand-color: #2e6f40;
            --brand-color-hover: #1e4a2a;
            --bg-light: #edf4ee;
            --text-dark: #2b3a2e;
        }

        body {
            background-color: var(--bg-light);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: var(--text-dark);
            margin: 0;
            padding: 20px;
        }

        .emprunt-container {
            max-width: 900px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.05);
            display: grid;
            grid-template-columns: 1fr 1.2fr;
            overflow: hidden;
        }

        @media (max-width: 768px) {
            .emprunt-container {
                grid-template-columns: 1fr;
            }
        }

        .book-preview {
            background: #f8faf8;
            padding: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border-right: 1px solid #eef2ee;
            text-align: center;
        }

        .book-preview img {
            width: 180px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .book-preview h2 {
            margin: 10px 0 5px 0;
            font-size: 1.5rem;
        }

        .book-preview p {
            color: #666;
            margin: 0;
        }

        .badge-dispo {
            background: #e1f5fe;
            color: #0288d1;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: bold;
            margin-top: 15px;
        }

        .form-section {
            padding: 40px;
        }

        .form-section h1 {
            font-size: 1.8rem;
            margin-bottom: 25px;
            color: var(--brand-color);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #cccccc;
            border-radius: 8px;
            box-sizing: border-box;
            font-size: 1rem;
            transition: border-color 0.3s;
        }

        .form-group input:focus {
            border-color: var(--brand-color);
            outline: none;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        .btn-submit {
            background-color: var(--brand-color);
            color: #ffffff;
            border: none;
            padding: 14px 20px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: bold;
            width: 100%;
            cursor: pointer;
            transition: background 0.3s;
        }

        .btn-submit:hover {
            background-color: var(--brand-color-hover);
        }

        .btn-cancel {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #666;
            text-decoration: none;
            font-size: 0.9rem;
        }
    </style>
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
