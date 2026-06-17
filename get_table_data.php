<?php
session_start();
require_once("config.php");
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION["logged_in"]) || $_SESSION["logged_in"] !== true || (int)$_SESSION["loginrole"] !== 1) {
    header('Content-Type: application/json');
    echo json_encode(['table' => '<tr><td colspan="10">Accès refusé</td></tr>', 'pagination' => '']);
    exit();
}

$cnx->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

$limit = 20;
$section = $_GET['section'] ?? 'livre';
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $limit;

$rows_html = "";
$pagination_html = "";
$pages = 1;

try {
    // --- 1. HANDLING LIVRES ---
    if ($section === 'livre') {
        $total = $cnx->query("SELECT COUNT(*) FROM livre")->fetchColumn();
        $pages = ceil($total / $limit);

        $stmt = $cnx->prepare("SELECT NumLivre, TitreLivre, AnneeEdition, NumAuteur, NumEditeur, NumTheme, nbr_exmp FROM livre LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        $livres = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $rows_html = "<tr id='titre_ta'><td>Id</td><td>Nom livre</td><td>Année</td><td>Auteur</td><td>Editeur</td><td>Theme</td><td>Nombre exemplair</td><td>Cover</td><td>Edit</td><td>Supprimer</td></tr>";

        if (count($livres) > 0) {
            foreach ($livres as $row) {
                $id_livre  = $row['NumLivre'] ?? $row['num_livre'] ?? $row['id_livre'] ?? array_values($row)[0];
                $titre     = $row['TitreLivre'] ?? $row['titre_livre'] ?? $row['titre'] ?? 'Inconnu';
                $annee     = $row['AnneeEdition'] ?? $row['annee_edition'] ?? $row['annee'] ?? '';
                $nbr_exmp  = $row['nbr_exmp'] ?? $row['nbr_exemplaire'] ?? 0;
                
                $id_auteur  = $row['NumAuteur'] ?? $row['num_auteur'] ?? null;
                $id_editeur = $row['NumEditeur'] ?? $row['num_editeur'] ?? null;
                $id_theme   = $row['NumTheme'] ?? $row['num_theme'] ?? null;

                $nom_auteur = 'Inconnu';
                if ($id_auteur) {
                    $q = $cnx->prepare("SELECT NomAuteur FROM Auteur WHERE NumAuteur = ?");
                    $q->execute([$id_auteur]);
                    $nom_auteur = $q->fetchColumn() ?: 'Inconnu';
                }

                $nom_editeur = 'Inconnu';
                if ($id_editeur) {
                    $q = $cnx->prepare("SELECT NomEditeur FROM editeur WHERE NumEditeur = ?");
                    $q->execute([$id_editeur]);
                    $nom_editeur = $q->fetchColumn() ?: 'Inconnu';
                }

                $nom_theme = 'Inconnu';
                if ($id_theme) {
                    $q = $cnx->prepare("SELECT intituleTheme FROM theme WHERE NumTheme = ?");
                    $q->execute([$id_theme]);
                    $nom_theme = $q->fetchColumn() ?: 'Inconnu';
                }

                $rows_html .= "<tr>
                    <td>{$id_livre}</td>
                    <td>" . htmlspecialchars($titre) . "</td>
                    <td>" . htmlspecialchars($annee) . "</td>
                    <td>" . htmlspecialchars($nom_auteur) . "</td>
                    <td>" . htmlspecialchars($nom_editeur) . "</td>
                    <td>" . htmlspecialchars($nom_theme) . "</td>
                    <td>{$nbr_exmp}</td>
                    <td><img src='getimage.php?id={$id_livre}' width='50'></td>
                    <td><form method='post'><input type='hidden' value='{$id_livre}' name='id_edit'><button type='submit' id='edit-btn'>edit</button></form></td>
                    <td><form method='post'><input type='hidden' value='{$id_livre}' name='id_supp'><button type='submit' id='supp-btn' name='supp-btn'>Supprimer</button></form></td>
                </tr>";
            }
        } else {
            $rows_html .= "<tr><td colspan='10' style='text-align:center;'>Aucun livre trouvé dans la base de données.</td></tr>";
        }
    }

    // --- 2. HANDLING USERS ---
    elseif ($section === 'user') {
        $total = $cnx->query("SELECT COUNT(*) FROM users")->fetchColumn();
        $pages = ceil($total / $limit);

        $stmt = $cnx->prepare("SELECT u.id_user, u.NomUser, u.PrenomUser, u.LoginUser, u.EmailUser, r.NameRole FROM users u LEFT JOIN role r ON r.id_role = u.id_role LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();

        $rows_html = "<tr id='titre_ta'><td>Id User</td><td>Nom</td><td>Prenom</td><td>Username</td><td>Email</td><td>Role</td><td>Edit</td><td>Supprimer</td></tr>";
        while ($rowusl = $stmt->fetch()) {
            $rows_html .= "<tr>
                <td>{$rowusl[0]}</td><td>" . htmlspecialchars($rowusl[1]) . "</td><td>" . htmlspecialchars($rowusl[2]) . "</td><td>" . htmlspecialchars($rowusl[3]) . "</td><td>" . htmlspecialchars($rowusl[4]) . "</td><td>" . htmlspecialchars($rowusl[5] ?? 'Aucun') . "</td>
                <td><form method='post'><input type='hidden' value='{$rowusl[0]}' name='id_edit_us'><button type='submit' id='edit-btn'>edit</button></form></td>
                <td><form method='post'><input type='hidden' value='{$rowusl[0]}' name='id_supp_us'><button type='submit' id='supp-btn' name='supp-us-btn'>Supprimer</button></form></td>
            </tr>";
        }
    }

    // --- 3. HANDLING AUTEURS ---
    elseif ($section === 'auteur') {
        $total = $cnx->query("SELECT COUNT(*) FROM Auteur")->fetchColumn();
        $pages = ceil($total / $limit);

        $stmt = $cnx->prepare("SELECT * FROM Auteur LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();

        $rows_html = "<tr id='titre_ta'><td>Id Auteur</td><td>Nom</td><td>Adresse</td><td>Edit</td><td>Supprimer</td></tr>";
        while ($r = $stmt->fetch()) {
            $rows_html .= "<tr>
                <td>{$r[0]}</td><td>" . htmlspecialchars($r[1]) . "</td><td>" . htmlspecialchars($r[2]) . "</td>
                <td><form method='post'><input type='hidden' value='{$r[0]}' name='id_edit_aut'><button type='submit' id='edit-btn'>edit</button></form></td>
                <td><form method='post'><input type='hidden' value='{$r[0]}' name='id_supp_aut'><button type='submit' id='supp-btn' name='supp-aut-btn'>Supprimer</button></form></td>
            </tr>";
        }
    }

    // --- 4. HANDLING EDITEURS ---
    elseif ($section === 'editeur') {
        $total = $cnx->query("SELECT COUNT(*) FROM editeur")->fetchColumn();
        $pages = ceil($total / $limit);

        $stmt = $cnx->prepare("SELECT * FROM editeur LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();

        $rows_html = "<tr id='titre_ta'><td>Id Editeur</td><td>Nom</td><td>Adresse</td><td>Edit</td><td>Supprimer</td></tr>";
        while ($r = $stmt->fetch()) {
            $rows_html .= "<tr>
                <td>{$r[0]}</td><td>" . htmlspecialchars($r[1]) . "</td><td>" . htmlspecialchars($r[2]) . "</td>
                <td><form method='post'><input type='hidden' value='{$r[0]}' name='id_edit_edi'><button type='submit' id='edit-btn'>edit</button></form></td>
                <td><form method='post'><input type='hidden' value='{$r[0]}' name='id_supp_edi'><button type='submit' id='supp-btn' name='supp-edi-btn'>Supprimer</button></form></td>
            </tr>";
        }
    }

    // --- 5. HANDLING THEMES ---
    elseif ($section === 'theme') {
        $total = $cnx->query("SELECT COUNT(*) FROM theme")->fetchColumn();
        $pages = ceil($total / $limit);

        $stmt = $cnx->prepare("SELECT * FROM theme LIMIT :limit OFFSET :offset");
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();

        $rows_html = "<tr id='titre_ta'><td>Id Theme</td><td>Intitulé Theme</td><td>Edit</td><td>Supprimer</td></tr>";
        while ($r = $stmt->fetch()) {
            $rows_html .= "<tr>
                <td>{$r[0]}</td><td>" . htmlspecialchars($r[1]) . "</td>
                <td><form method='post'><input type='hidden' value='{$r[0]}' name='id_edit_thm'><button type='submit' id='edit-btn'>edit</button></form></td>
                <td><form method='post'><input type='hidden' value='{$r[0]}' name='id_supp_thm'><button type='submit' id='supp-btn' name='supp-thm-btn'>Supprimer</button></form></td>
            </tr>";
        }
    }

        // --- 6. HANDLING EMPRUNTS
    elseif ($section === 'emprunt') {
        $cnx->query("UPDATE emprunt SET status = 'en retard' WHERE status = 'en_cours' AND date_retour_prevu < CURDATE()");

        $total = $cnx->query("SELECT COUNT(*) FROM emprunt")->fetchColumn();
        $pages = ceil($total / $limit);

        $stmt = $cnx->prepare("
            SELECT e.*, l.TitreLivre 
            FROM emprunt e 
            LEFT JOIN livre l ON e.NumLivre = l.NumLivre 
            LIMIT :limit OFFSET :offset
        ");
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        $emprunts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $rows_html = "<tr id='titre_ta'>
            <td>Livre</td>
            <td>Utilisateur</td>
            <td>Date Emprunt</td>
            <td>Retour Prévu</td>
            <td>Status</td>
        </tr>";

        if (count($emprunts) > 0) {
            foreach ($emprunts as $row) {
                $id_emprunt = $row['id_emprunt'];
                $num_livre = $row['NumLivre'];
                $titre_livre = $row['TitreLivre'] ?? 'Inconnu';
                $nom_user = $row['nom_complet'];
                $email_user = $row['email'];
                
                $date_emp = date('d/m/Y', strtotime($row['date_emprunt']));
                $date_prevu = date('d/m/Y', strtotime($row['date_retour_prevu']));
                
                $status = $row['status'];
                $status_badge = "";

                if ($status === 'retourne') {
                    $status_badge = "<span style='background: #e1f5fe; color: #0288d1; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 13px;'>Retourné</span>";
                } elseif ($status === 'en retard') {
                    $status_badge = "<span style='background: #ffebee; color: #c62828; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 13px;'>En Retard</span>";
                } else { 
                    $status_badge = "<span style='background: #e8f5e9; color: #2e7d32; padding: 4px 8px; border-radius: 4px; font-weight: bold; font-size: 13px;'>En Cours</span>";
                }

                $rows_html .= "<tr>
                    <td>
                        <div style='display: flex; align-items: center; gap: 10px;'>
                            <img src='getimage.php?id={$num_livre}' width='35' height='50' style='border-radius:4px; object-fit: cover;'>
                            <div>
                                <b style='font-size: 14px;'> " . htmlspecialchars($titre_livre) . "</b><br>
                                <span style='font-size: 11px; color: gray;'>ID: {$num_livre}</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <b style='font-size: 14px;'>" . htmlspecialchars($nom_user) . "</b><br>
                        <span style='font-size: 11px; color: gray;'>" . htmlspecialchars($email_user) . "</span>
                    </td>
                    <td style='font-size: 13px; color: #333;'>📅 {$date_emp}</td>
                    <td style='font-size: 13px; color: #333;'>🕒 {$date_prevu}</td>
                    <td>{$status_badge}</td>
                </tr>";
            }
        } else {
            $rows_html .= "<tr><td colspan='5' style='text-align:center; padding: 20px;'>Aucun emprunt trouvé.</td></tr>";
        }
    }


    // Generation HTML pagination
    if ($pages > 1) {
        if ($page > 1) {
            $pagination_html .= "<a href='#' onclick='loadPage(\"{$section}\", " . ($page - 1) . ", event)'>&laquo; Précédent</a>";
        }
        for ($i = 1; $i <= $pages; $i++) {
            $active = ($i == $page) ? 'active' : '';
            $pagination_html .= "<a href='#' onclick='loadPage(\"{$section}\", {$i}, event)' class='{$active}'>{$i}</a>";
        }
        if ($page < $pages) {
            $pagination_html .= "<a href='#' onclick='loadPage(\"{$section}\", " . ($page + 1) . ", event)'>Suivant &raquo;</a>";
        }
    }

    header('Content-Type: application/json');
    echo json_encode([
        'table' => $rows_html,
        'pagination' => $pagination_html
    ]);
    exit();

} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'table' => "<tr><td colspan='10'>Erreur SQL: " . addslashes($e->getMessage()) . "</td></tr>",
        'pagination' => ''
    ]);
    exit();
}
