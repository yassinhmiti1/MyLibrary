<?php
require_once("config.php");

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    $sql = "SELECT l.*, t.intituleTheme, a.NomAuteur, e.NomEditeur 
            FROM livre l 
            JOIN theme t ON l.NumTheme = t.NumTheme 
            JOIN Auteur a ON l.NumAuteur = a.NumAuteur 
            JOIN editeur e ON l.NumEditeur = e.NumEditeur 
            WHERE l.NumLivre = ?";
            
    $stmt = $cnx->prepare($sql);
    $stmt->execute([$id]);
    $book = $stmt->fetch(PDO::FETCH_BOTH);

    if ($book) {
        $titre = $book['TitreLivre'];
        $annee = $book['AnneeEdition'];
        $auteur = $book['NomAuteur'];
        $editeur = $book['NomEditeur'];
        $theme = $book['intituleTheme'];
        $nbr_ex = intval($book['nbr_exmp']);
        
        $is_available = ($nbr_ex > 0);
        
        echo "
        <button class='close-dialog-btn' onclick='closeBookDetails()'>&times;</button>
        
        <div class='dialog-scrollable-body'>
            <div class='dialog-img-container'>
                <img src='getimage.php?id={$id}' alt='{$titre}'>
            </div>
            
            <div class='dialog-info-container'>
                <span class='dialog-genre'>{$theme}</span>
                <h2 class='dialog-title'>{$titre}</h2>
                <p class='dialog-author'>par {$auteur}</p>
                
                
                <table class='dialog-meta-table'>
                    <tr><td>Éditeur:</td><td class='meta-value'>{$editeur}</td></tr>
                    <tr><td>Année:</td><td class='meta-value'>{$annee}</td></tr>
                </table>";

                
                if ($is_available) {
                    echo "
                    <div class='dialog-status-box badge-dispo'>
                        <div class='status-title'>
                            <svg width='18' height='18' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2.5'><path d='M22 11.08V12a10 10 0 1 1-5.93-9.14'></path><polyline points='22 4 12 14.01 9 11.01'></polyline></svg>
                            Disponible
                        </div>
                        <p class='status-desc'>{$nbr_ex} copies restantes</p>
                    </div>";
                } else {
                
                    echo "
                    <div class='status-soldout'>
                        <div class='soldout-title'>
                            <svg width='20' height='20' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2.5'><circle cx='12' cy='12' r='10'></circle><line x1='15' y1='9' x2='9' y2='15'></line><line x1='9' y1='9' x2='15' y2='15'></line></svg>
                            Non Disponible
                        </div>
                        <p class='soldout-desc'>0 copie restante</p>
                    </div>";
                }
                
                echo "
                <div class='dialog-quantity-section'>
                    <label>Quantité</label>
                    <input type='number' value='" . ($is_available ? "1" : "0") . "' min='" . ($is_available ? "1" : "0") . "' max='{$nbr_ex}' class='quantity-input' " . ($is_available ? "" : "disabled") . ">
                </div>
                
                <a href='emprunter.php?id={$id}' class='btn-emprunter-link'>
<button type='button' class='btn-emprunter' " . ($is_available ? "" : "disabled style='background-color:#9ca3af; cursor:not-allowed;'") . "><svg width='18' height='18' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2'><circle cx='9' cy='21' r='1'></circle><circle cx='20' cy='21' r='1'></circle><path d='M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6'></path></svg> Emprunter</button>
</a>

            </div>
        </div>";
    } else {
        echo "<p style='padding:20px; text-align:center;'>Livre introuvable.</p>";
    }
}
?>
