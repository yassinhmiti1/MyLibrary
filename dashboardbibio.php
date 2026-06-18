<?php
ob_start();
session_start();
require_once("config.php");

if (!isset($_SESSION["logged_in"]) || $_SESSION["logged_in"] !== true) {
  header("Location: index.php");
  exit();
}
if ((int)$_SESSION["loginrole"] !== 1) {
  header("Location: index.php");
  exit();
}

error_reporting(E_ALL);
ini_set('display_errors', 1);

$row = null; $op_dia_edit = false;
$rowused = null; $op_dia_us_edit = false;
$row_aut = null; $op_dia_aut_edit = false;
$row_edi = null; $op_dia_edi_edit = false;
$row_thm = null; $op_dia_thm_edit = false;

// ---  LIVRES ---
if (isset($_POST["id_edit"])) {
  $edid = $_POST["id_edit"];
  $sqled = "SELECT * FROM livre WHERE NumLivre = ?";
  $requete = $cnx->prepare($sqled);
  $requete->execute([$edid]);
  $row = $requete->fetch(PDO::FETCH_BOTH);
  if ($row) $op_dia_edit = true;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["update_livre_btn"])) {
  $id_livre = $_POST["old_id"];
  $titre = $_POST["ednomlivre"] ?? '';
  $auteur = $_POST["ednauteur"] ?? '';
  $editeur = $_POST["ednediteur"] ?? '';
  $theme = $_POST["edntheme"] ?? '';
  $annee = $_POST["edannee"] ?? '';
  $nbr_ex = $_POST["ednbr_exmp"] ?? '';

  if (!empty($id_livre) && !empty($titre) && !empty($auteur) && !empty($editeur) && !empty($theme) && !empty($annee) && !empty($nbr_ex)) {
    try {
      if (isset($_FILES["edphotoup"]) && $_FILES["edphotoup"]["error"] == 0) {
        $image = $_FILES["edphotoup"]["tmp_name"];
        $imgContent = file_get_contents($image);
        $sqlUp = "UPDATE livre SET TitreLivre=?, AnneeEdition=?, NumAuteur=?, NumEditeur=?, NumTheme=?, photo=? , nbr_exmp=? WHERE NumLivre=?";
        $stmtUp = $cnx->prepare($sqlUp);
        $stmtUp->execute([$titre, $annee, $auteur, $editeur, $theme, $imgContent, $nbr_ex, $id_livre]);
      } else {
        $sqlUp = "UPDATE livre SET TitreLivre=?, AnneeEdition=?, NumAuteur=?, NumEditeur=?, NumTheme=? , nbr_exmp=? WHERE NumLivre=?";
        $stmtUp = $cnx->prepare($sqlUp);
        $stmtUp->execute([$titre, $annee, $auteur, $editeur, $theme, $nbr_ex, $id_livre]);
      }
      echo "<script>alert('Livre modifié avec succès !'); window.location.href='dashboardbibio.php';</script>";
      exit();
    } catch (PDOException $e) {
      echo "<script>alert('Erreur Update Livre: " . addslashes($e->getMessage()) . "');</script>";
    }
  }
}

if (isset($_POST["supp-btn"])) {
  $id = $_POST['id_supp'];
  $sqlsup = "DELETE FROM livre WHERE NumLivre=?";
  $request = $cnx->prepare($sqlsup);
  if ($request->execute([$id])) {
    echo "<script>alert('Livre a été supprimé'); window.location.href='dashboardbibio.php';</script>";
    exit();
  }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["add_livre_btn"])) {
  $titre = $_POST["nomlivre"] ?? '';
  $auteur = $_POST["nauteur"] ?? '';
  $editeur = $_POST["nediteur"] ?? '';
  $theme = $_POST["ntheme"] ?? '';
  $annee = $_POST["annee"] ?? '';
  $nbr_ex = $_POST["nbr_exmp"] ?? '';
  $imgContent = null;
  if (isset($_FILES["photoup"]) && $_FILES["photoup"]["error"] == 0) {
    $imgContent = file_get_contents($_FILES["photoup"]["tmp_name"]);
  }
  if (!empty($titre) && !empty($auteur) && !empty($editeur) && !empty($theme) && !empty($annee) && !empty($nbr_ex)) {
    try {
      $sql = "INSERT INTO livre (TitreLivre, AnneeEdition, NumAuteur, NumEditeur, NumTheme, photo, nbr_exmp) VALUES (?, ?, ?, ?, ?, ?, ?)";
      $stmt = $cnx->prepare($sql);
      $stmt->execute([$titre, $annee, $auteur, $editeur, $theme, $imgContent, $nbr_ex]);
      echo "<script>alert('Livre ajouté avec succès !'); window.location.href='dashboardbibio.php';</script>";
      exit();
    } catch (PDOException $e) {
      echo "<script>alert('Erreur: " . addslashes($e->getMessage()) . "');</script>";
    }
  }
}

// --- USERS ---
if (isset($_POST["id_edit_us"])) {
  $edusid = $_POST["id_edit_us"];
  $sqledus = "SELECT * FROM users WHERE id_user = ?";
  $requete = $cnx->prepare($sqledus);
  $requete->execute([$edusid]);
  $rowused = $requete->fetch(PDO::FETCH_BOTH);
  if ($rowused) $op_dia_us_edit = true;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["edus_btn"])) {
  $id_user = $_POST["old_us_id"];
  $nu = $_POST["ednomus"] ?? '';
  $pu = $_POST["edprenomus"] ?? '';
  $uu = $_POST["edusername"] ?? '';
  $eu = $_POST["edemus"] ?? '';
  $pau = $_POST["edpassus"] ?? '';
  $ru = $_POST["edroleus"] ?? '';

  if (!empty($id_user) && !empty($nu) && !empty($pu) && !empty($uu) && !empty($eu) && !empty($ru)) {
    try {
      if (!empty($pau)) {
        $sqlUp = "UPDATE users SET NomUser=?, PrenomUser=?, LoginUser=?, EmailUser=?, PasswordUser=?, id_role=? WHERE id_user=?";
        $stmtUp = $cnx->prepare($sqlUp);
        $stmtUp->execute([$nu, $pu, $uu, $eu, $pau, $ru, $id_user]);
      } else {
        $sqlUp = "UPDATE users SET NomUser=?, PrenomUser=?, LoginUser=?, EmailUser=?, id_role=? WHERE id_user=?";
        $stmtUp = $cnx->prepare($sqlUp);
        $stmtUp->execute([$nu, $pu, $uu, $eu, $ru, $id_user]);
      }
      echo "<script>alert('User modifié avec succès !'); window.location.href='dashboardbibio.php';</script>";
      exit();
    } catch (PDOException $e) {
      echo "<script>alert('Erreur Update User: " . addslashes($e->getMessage()) . "');</script>";
    }
  }
}

if (isset($_POST["supp-us-btn"])) {
  $idu = $_POST['id_supp_us'];
  $sqlsupus = "DELETE FROM users WHERE id_user=?";
  $requestus = $cnx->prepare($sqlsupus);
  if ($requestus->execute([$idu])) {
    echo "<script>alert('User a été supprimé'); window.location.href='dashboardbibio.php';</script>";
    exit();
  }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["addus_btn"])) {
  $nu = $_POST["nomus"] ?? '';
  $pu = $_POST["prenomus"] ?? '';
  $uu = $_POST["username"] ?? '';
  $eu = $_POST["emus"] ?? '';
  $pau = $_POST["passus"] ?? '';
  $ru = $_POST["roleus"] ?? '';
  if (!empty($nu) && !empty($pu) && !empty($uu) && !empty($eu) && !empty($pau) && !empty($ru)) {
    try {
      $sql = "INSERT INTO users(NomUser, PrenomUser, LoginUser, EmailUser, PasswordUser, id_role) VALUES (?,?,?,?,?,?)";
      $stmt = $cnx->prepare($sql);
      $stmt->execute([$nu, $pu, $uu, $eu, $pau, $ru]);
      echo "<script>alert('User ajouté avec succès !'); window.location.href='dashboardbibio.php';</script>";
      exit();
    } catch (PDOException $e) {
      echo "<script>alert('Erreur: " . addslashes($e->getMessage()) . "');</script>";
    }
  }
}

// --- AUTEURS ---
if (isset($_POST["id_edit_aut"])) {
  $sqled = "SELECT * FROM Auteur WHERE NumAuteur = ?";
  $requete = $cnx->prepare($sqled);
  $requete->execute([$_POST["id_edit_aut"]]);
  $row_aut = $requete->fetch(PDO::FETCH_BOTH);
  if ($row_aut) $op_dia_aut_edit = true;
}
if (isset($_POST["add_aut_btn"])) {
  $nom = $_POST["nomaut"] ?? ''; $adr = $_POST["adraut"] ?? '';
  if (!empty($nom)) {
    $cnx->prepare("INSERT INTO Auteur (NomAuteur, AdresseAuteur) VALUES (?,?)")->execute([$nom, $adr]);
    header("Location: dashboardbibio.php"); exit();
  }
}
if (isset($_POST["update_aut_btn"])) {
  $cnx->prepare("UPDATE Auteur SET NomAuteur=?, AdresseAuteur=? WHERE NumAuteur=?")->execute([$_POST["ednomaut"], $_POST["edadraut"], $_POST["old_aut_id"]]);
  header("Location: dashboardbibio.php"); exit();
}
if (isset($_POST["supp-aut-btn"])) {
  $cnx->prepare("DELETE FROM Auteur WHERE NumAuteur=?")->execute([$_POST['id_supp_aut']]);
  header("Location: dashboardbibio.php"); exit();
}

// --- EDITEURS ---
if (isset($_POST["id_edit_edi"])) {
  $sqled = "SELECT * FROM editeur WHERE NumEditeur = ?";
  $requete = $cnx->prepare($sqled);
  $requete->execute([$_POST["id_edit_edi"]]);
  $row_edi = $requete->fetch(PDO::FETCH_BOTH);
  if ($row_edi) $op_dia_edi_edit = true;
}
if (isset($_POST["add_edi_btn"])) {
  $nom = $_POST["nomedi"] ?? ''; $adr = $_POST["adredi"] ?? '';
  if (!empty($nom)) {
    $cnx->prepare("INSERT INTO editeur (NomEditeur, AdresseEditeur) VALUES (?,?)")->execute([$nom, $adr]);
    header("Location: dashboardbibio.php"); exit();
  }
}
if (isset($_POST["update_edi_btn"])) {
  $cnx->prepare("UPDATE editeur SET NomEditeur=?, AdresseEditeur=? WHERE NumEditeur=?")->execute([$_POST["ednomedi"], $_POST["edadredi"], $_POST["old_edi_id"]]);
  header("Location: dashboardbibio.php"); exit();
}
if (isset($_POST["supp-edi-btn"])) {
  $cnx->prepare("DELETE FROM editeur WHERE NumEditeur=?")->execute([$_POST['id_supp_edi']]);
  header("Location: dashboardbibio.php"); exit();
}

// --- THEMES ---
if (isset($_POST["id_edit_thm"])) {
  $sqled = "SELECT * FROM theme WHERE NumTheme = ?";
  $requete = $cnx->prepare($sqled);
  $requete->execute([$_POST["id_edit_thm"]]);
  $row_thm = $requete->fetch(PDO::FETCH_BOTH);
  if ($row_thm) $op_dia_thm_edit = true;
}
if (isset($_POST["add_thm_btn"])) {
  $intitule = $_POST["intitulethm"] ?? '';
  if (!empty($intitule)) {
    $cnx->prepare("INSERT INTO theme (intituleTheme) VALUES (?)")->execute([$intitule]);
    header("Location: dashboardbibio.php"); exit();
  }
}
if (isset($_POST["update_thm_btn"])) {
  $cnx->prepare("UPDATE theme SET intituleTheme=? WHERE NumTheme=?")->execute([$_POST["edintitulethm"], $_POST["old_thm_id"]]);
  header("Location: dashboardbibio.php"); exit();
}
if (isset($_POST["supp-thm-btn"])) {
  $cnx->prepare("DELETE FROM theme WHERE NumTheme=?")->execute([$_POST['id_supp_thm']]);
  header("Location: dashboardbibio.php"); exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Bibliothèque</title>
  <link rel="stylesheet" href="style.css">
  <style>
    .pagination-container {
      margin-top: 20px;
      display: flex;
      justify-content: center;
      gap: 8px;
    }
    .pagination-container a {
      padding: 8px 14px;
      border: 1px solid #ccc;
      text-decoration: none;
      color: var(--second-text-color);
      border-radius: 4px;
      font-weight: 500;
      transition: all 0.3s ease;
    }
    .pagination-container a:hover {
      background-color: #3d7a5a;
      color: white;
      border-color: #3d7a5a;
    }
    .pagination-container a.active {
      background-color: #2e5942;
      color: white;
      border-color: #2e5942;
      pointer-events: none;
    }
  </style>
  <script src="script.js" defer></script>
</head>
<body>
  <main class="maincontainer">

    <?php include("nav_lg.php"); ?>
    <?php include("menu.php"); ?>

    <div class="container">
      <div id="livre">
        <div class="header">
          <section id="Livres">
            <h1>Livres</h1>
          </section>
          <button id="add" onclick="openDialogAdd()">Add Livre</button>
        </div>
        <br>
        <div class="table_con">
          <table id="table-livre"></table>
        </div>
        <div class="pagination-container" id="pagination-livre"></div>

        <dialog class="adddia" id="adddia">
          <form method="post" enctype="multipart/form-data">
            <h2>Add Livre</h2><br>
            <label>Nom Livre:</label><input type="text" name="nomlivre">
            <label>Année:</label>
            <select name="annee"><option value="">-- select --</option>
              <?php for ($i = 1970; $i <= 2026; $i++) echo "<option value='$i'>$i</option>"; ?>
            </select>
            <label>Auteur:</label>
            <select name="nauteur">
              <?php $res = $cnx->query("SELECT * FROM Auteur"); while ($r = $res->fetch()) echo "<option value='{$r[0]}'>{$r[1]}</option>"; ?>
            </select>
            <label>Editeur:</label>
            <select name="nediteur">
              <?php $res = $cnx->query("SELECT * FROM editeur"); while ($r = $res->fetch()) echo "<option value='{$r[0]}'>{$r[1]}</option>"; ?>
            </select>
            <label>Theme:</label>
            <select name="ntheme">
              <?php $res = $cnx->query("SELECT * FROM theme"); while ($r = $res->fetch()) echo "<option value='{$r[0]}'>{$r[1]}</option>"; ?>
            </select>
            <label>Photo:</label><input type="file" name="photoup">
            <label>Nombre des exemplaire:</label><input type="number" name="nbr_exmp">
            <br>
            <div class="buttons">
              <button type="submit" name="add_livre_btn" id="submit-btn">Submit</button>
              <button type="button" onclick="closeDialogAdd()">Close</button>
            </div>
          </form>
        </dialog>

        <dialog class="adddia" id="edit_livre">
          <?php if ($row): ?>
          <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="old_id" value="<?php echo $row[0]; ?>">
            <h2>Edit Livre</h2><br>
            <label>Nom Livre:</label><input type="text" name="ednomlivre" value="<?php echo htmlspecialchars($row[1]); ?>">
            <label>Année:</label>
            <select name="edannee">
              <?php for ($i = 1970; $i <= 2026; $i++) {
                $s = ($i == $row["AnneeEdition"])?'selected':''; echo "<option value='$i' $s>$i</option>";
              } ?>
            </select>
            <label>Auteur:</label>
            <select name="ednauteur">
              <?php $res = $cnx->query("SELECT * FROM Auteur"); while ($r = $res->fetch()) {
                $s = ($r[0] == $row["NumAuteur"])?'selected':''; echo "<option value='{$r[0]}' $s>{$r[1]}</option>";
              } ?>
            </select>
            <label>Editeur:</label>
            <select name="ednediteur">
              <?php $res = $cnx->query("SELECT * FROM editeur"); while ($r = $res->fetch()) {
                $s = ($r[0] == $row["NumEditeur"])?'selected':''; echo "<option value='{$r[0]}' $s>{$r[1]}</option>";
              } ?>
            </select>
            <label>Theme:</label>
            <select name="edntheme">
              <?php $res = $cnx->query("SELECT * FROM theme"); while ($r = $res->fetch()) {
                $s = ($r[0] == $row["NumTheme"])?'selected':'';
                echo "<option value='{$r[0]}' $s>{$r[1]}</option>";
              } ?>
            </select>
            <img src='getimage.php?id=<?php echo $row[0]; ?>' width='50'>
            <label>Photo:</label><input type="file" name="edphotoup">
            <label>Nombre des exemplaire:</label><input type="number" name="ednbr_exmp" value="<?php echo htmlspecialchars($row[7]); ?>">
            <br>
            <div class="buttons">
              <button type="submit" name="update_livre_btn" id="submit-btn">Update</button>
              <button type="button" onclick="window.location.href='dashboardbibio.php'">Close</button>
            </div>
          </form>
          <?php endif; ?>
        </dialog>
      </div>

      <div id="user" class="hidden">
        <div class="header">
          <section id="Users">
            <h1>Users</h1>
          </section>
          <button id="add" onclick="openDialogAddUser()">Add User</button>
        </div>
        <br>
        <div class="table_con">
          <table id="table-user"></table>
        </div>
        <div class="pagination-container" id="pagination-user"></div>

        <dialog class="adddia" id="add_user">
          <form method="post">
            <h1>Add User</h1><br>
            <label>Nom:</label><input type="text" name="nomus">
            <label>Prenom:</label><input type="text" name="prenomus">
            <label>Username:</label><input type="text" name="username">
            <label>Email:</label><input type="email" name="emus">
            <label>Password:</label><input type="password" name="passus">
            <label>Role:</label>
            <select name="roleus">
              <?php $res = $cnx->query("SELECT * FROM role"); while ($r = $res->fetch()) echo "<option value='{$r[0]}'>{$r[1]}</option>"; ?>
            </select>
            <br>
            <div class="buttons">
              <button type="submit" name="addus_btn" id="submit-btn">Submit</button>
              <button type="button" onclick="closeDialogAddUser()">Close</button>
            </div>
          </form>
        </dialog>

        <dialog class="adddia" id="edit_user">
          <?php if ($rowused): ?>
          <form method="post">
            <input type="hidden" name="old_us_id" value="<?php echo $rowused[0]; ?>">
            <h1>Edit User</h1><br>
            <label>Nom:</label><input type="text" name="ednomus" value="<?php echo htmlspecialchars($rowused[1]); ?>">
            <label>Prenom:</label><input type="text" name="edprenomus" value="<?php echo htmlspecialchars($rowused[2]); ?>">
            <label>Username:</label><input type="text" name="edusername" value="<?php echo htmlspecialchars($rowused[3]); ?>">
            <label>Email:</label><input type="email" name="edemus" value="<?php echo htmlspecialchars($rowused[4]); ?>">
            <label>Password:</label><input type="password" name="edpassus">
            <label>Role:</label>
            <select name="edroleus">
              <?php $res = $cnx->query("SELECT * FROM role"); while ($r = $res->fetch()) {
                $s = ($r[0] == $rowused["id_role"])?'selected':''; echo "<option value='{$r[0]}' $s>{$r[1]}</option>";
              } ?>
            </select>
            <br>
            <div class="buttons">
              <button type="submit" name="edus_btn" id="submit-btn">Update</button>
              <button type="button" onclick="window.location.href='dashboardbibio.php'">Close</button>
            </div>
          </form>
          <?php endif; ?>
        </dialog>
      </div>

      <div id="auteur" class="hidden">
        <div class="header">
          <section id="Auteurs">
            <h1>Auteurs</h1>
          </section>
          <button id="add" onclick="openDialogAddAut()">Add Auteur</button>
        </div>
        <br>
        <div class="table_con">
          <table id="table-auteur"></table>
        </div>
        <div class="pagination-container" id="pagination-auteur"></div>

        <dialog class="adddia" id="add_aut">
          <form method="post">
            <h2>Add Auteur</h2><br>
            <label>Nom Auteur:</label><input type="text" name="nomaut" required>
            <label>Adresse:</label><input type="text" name="adraut">
            <br>
            <div class="buttons">
              <button type="submit" name="add_aut_btn" id="submit-btn">Submit</button>
              <button type="button" onclick="closeDialogAddAut()">Close</button>
            </div>
          </form>
        </dialog>

        <dialog class="adddia" id="edit_aut">
          <?php if ($row_aut): ?>
          <form method="post">
            <input type="hidden" name="old_aut_id" value="<?php echo $row_aut[0]; ?>">
            <h2>Edit Auteur</h2><br>
            <label>Nom:</label><input type="text" name="ednomaut" value="<?php echo htmlspecialchars($row_aut[1]); ?>" required>
            <label>Adresse:</label><input type="text" name="edadraut" value="<?php echo htmlspecialchars($row_aut[2]); ?>">
            <br>
            <div class="buttons">
              <button type="submit" name="update_aut_btn" id="submit-btn">Update</button>
              <button type="button" onclick="window.location.href='dashboardbibio.php'">Close</button>
            </div>
          </form>
          <?php endif; ?>
        </dialog>
      </div>

      <div id="editeur" class="hidden">
        <div class="header">
          <section id="Editeurs">
            <h1>Editeurs</h1>
          </section>
          <button id="add" onclick="openDialogAddEdi()">Add Editeur</button>
        </div>
        <br>
        <div class="table_con">
          <table id="table-editeur"></table>
        </div>
        <div class="pagination-container" id="pagination-editeur"></div>

        <dialog class="adddia" id="add_edi">
          <form method="post">
            <h2>Add Editeur</h2><br>
            <label>Nom Editeur:</label><input type="text" name="nomedi" required>
            <label>Adresse:</label><input type="text" name="adredi">
            <br>
            <div class="buttons">
              <button type="submit" name="add_edi_btn" id="submit-btn">Submit</button>
              <button type="button" onclick="closeDialogAddEdi()">Close</button>
            </div>
          </form>
        </dialog>

        <dialog class="adddia" id="edit_edi">
          <?php if ($row_edi): ?>
          <form method="post">
            <input type="hidden" name="old_edi_id" value="<?php echo $row_edi[0]; ?>">
            <h2>Edit Editeur</h2><br>
            <label>Nom:</label><input type="text" name="ednomedi" value="<?php echo htmlspecialchars($row_edi[1]); ?>" required>
            <label>Adresse:</label><input type="text" name="edadredi" value="<?php echo htmlspecialchars($row_edi[2]); ?>">
            <br>
            <div class="buttons">
              <button type="submit" name="update_edi_btn" id="submit-btn">Update</button>
              <button type="button" onclick="window.location.href='dashboardbibio.php'">Close</button>
            </div>
          </form>
          <?php endif; ?>
        </dialog>
      </div>

      <div id="theme" class="hidden">
        <div class="header">
          <section id="Theme">
            <h1>Themes</h1>
          </section>
          <button id="add" onclick="openDialogAddThm()">Add Theme</button>
        </div>
        <br>
        <div class="table_con">
          <table id="table-theme"></table>
        </div>
        <div class="pagination-container" id="pagination-theme"></div>

        <dialog class="adddia" id="add_thm">
          <form method="post">
            <h2>Add Theme</h2><br>
            <label>Intitulé Theme:</label><input type="text" name="intitulethm" required>
            <br>
            <div class="buttons">
              <button type="submit" name="add_thm_btn" id="submit-btn">Submit</button>
              <button type="button" onclick="closeDialogAddThm()">Close</button>
            </div>
          </form>
        </dialog>

        <dialog class="adddia" id="edit_thm">
          <?php if ($row_thm): ?>
          <form method="post">
            <input type="hidden" name="old_thm_id" value="<?php echo $row_thm[0]; ?>">
            <h2>Edit Theme</h2><br>
            <label>Intitulé:</label><input type="text" name="edintitulethm" value="<?php echo htmlspecialchars($row_thm[1]); ?>" required>
            <br>
            <div class="buttons">
              <button type="submit" name="update_thm_btn" id="submit-btn">Update</button>
              <button type="button" onclick="window.location.href='dashboardbibio.php'">Close</button>
            </div>
          </form>
          <?php endif; ?>
        </dialog>
      </div>
      <div id="emprunt" class="hidden">
        <div class="header">
          <section id="Emprunts">
            <h1>Gestion des Emprunts</h1>
          </section>
        </div>
        <br>
        <div class="boxs">
          <div class="box">
            <p>Total des emprunts</p>
            <h4><?php 
            $sqle1 = "select count(id_emprunt) as nbr_emp from emprunt";
            $requ = $cnx->query($sqle1);
            $row = $requ->fetch(PDO::FETCH_BOTH);
            echo($row["nbr_emp"]);
            ?></h4>
          </div>
          <br>
          <div class="box">
            <p>En Cours</p>
            <h4><?php
            $sql2 = "select count(id_emprunt) as nbr_emc from emprunt where status='en_cours'";
            $req2 = $cnx->query($sql2);
            $row1 = $req2->fetch(PDO::FETCH_BOTH);
            echo($row1["nbr_emc"]);
            ?></h4>
          </div>
          <br>
          <div class="box">
            <p>En Retard</p>
            <h4><?php
            $sql2 = "select count(id_emprunt) as nbr_emc from emprunt where status='en retard'";
            $req2 = $cnx->query($sql2);
            $row1 = $req2->fetch(PDO::FETCH_BOTH);
            echo($row1["nbr_emc"]);
            ?></h4>
          </div>
          <br>
          <div class="box">
            <p>Retourne</p>
            <h4><?php
            $sql2 = "select count(id_emprunt) as nbr_emc from emprunt where status='retourne'";
            $req2 = $cnx->query($sql2);
            $row1 = $req2->fetch(PDO::FETCH_BOTH);
            echo($row1["nbr_emc"]);
            ?></h4>
          </div>
        </div>
        <br>
        
        <div class="table_con">
          <table id="table-emprunt"></table>
        </div>
        <div class="pagination-container" id="pagination-emprunt"></div>

      </div>
    </div>
  </main>

  <?php if ($op_dia_edit): ?><script>
    document.addEventListener("DOMContentLoaded", function() {
      document.getElementById("edit_livre").showModal();
    });
  </script>
  <?php endif; ?>
  <?php if ($op_dia_us_edit): ?><script>
    document.addEventListener("DOMContentLoaded", function() {
      document.getElementById("edit_user").showModal();
    });
  </script>
  <?php endif; ?>
  <?php if ($op_dia_aut_edit): ?><script>
    document.addEventListener("DOMContentLoaded", function() {
      document.getElementById("edit_aut").showModal();
    });
  </script>
  <?php endif; ?>
  <?php if ($op_dia_edi_edit): ?><script>
    document.addEventListener("DOMContentLoaded", function() {
      document.getElementById("edit_edi").showModal();
    });
  </script>
  <?php endif; ?>
  <?php if ($op_dia_thm_edit): ?><script>
    document.addEventListener("DOMContentLoaded", function() {
      document.getElementById("edit_thm").showModal();
    });
  </script>
  <?php endif; ?>
</body>
</html>