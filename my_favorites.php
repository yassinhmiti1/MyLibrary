<?php
require_once("config.php");
session_start();

if (!isset($_SESSION['userid'])) {
    header("Location: Index.php");
    exit;
}

$id_user = $_SESSION['userid'];


$limit = 20; 
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;


$count_sql = "SELECT COUNT(*) FROM favoris WHERE id_user = ?";
$stmt_count = $cnx->prepare($count_sql);
$stmt_count->execute([$id_user]);
$total_favs = $stmt_count->fetchColumn();

$total_pages = ceil($total_favs / $limit);

$sql = "SELECT livre.*, theme.intituleTheme, Auteur.NomAuteur 
        FROM favoris 
        JOIN livre ON favoris.id_livre = livre.NumLivre
        JOIN theme ON livre.NumTheme = theme.NumTheme 
        JOIN Auteur ON livre.NumAuteur = Auteur.NumAuteur 
        WHERE favoris.id_user = ?
        LIMIT $limit OFFSET $offset";

$requete = $cnx->prepare($sql);
$requete->execute([$id_user]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Favorite Books</title>
  <link rel="stylesheet" href="stylehome.css">
  <style>
    .img_box { position: relative; }
    
    .btn-fav-remove {
        position: absolute;
        top: 10px;
        left: 10px;
        background: rgba(220, 20, 60, 0.9);
        border: none;
        border-radius: 50%;
        width: 35px;
        height: 35px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10;
        transition: transform 0.2s, background 0.3s;
    }
    .btn-fav-remove:hover { 
        transform: scale(1.1);
        background: rgb(180, 10, 40);
    }
    .btn-fav-remove svg { 
        fill: #fff; 
        width: 20px; 
        height: 20px; 
    }
    .box {
        transition: opacity 0.4s ease, transform 0.4s ease;
    }
    .box.fade-out {
        opacity: 0;
        transform: scale(0.8);
    }

    .pagination { 
        display: flex; 
        justify-content: center; 
        gap: 10px; 
        margin: 40px 0; 
        grid-column: 1 / -1; 
    }
    .pagination a, .pagination span { 
        padding: 8px 16px; 
        border: 1px solid #ccc; 
        text-decoration: none; 
        color: #333; 
        border-radius: 4px; 
        font-weight: bold;
        transition: background 0.2s;
    }
    .pagination a:hover {
        background-color: #f0f0f0;
    }
    .pagination .active { 
        background-color: #346739; 
        color: white; 
        border-color: #346739; 
    }
  </style>
</head>
<body>
  <main class="maincontainer">
    <?php include("nav_lg.php"); ?>
    <div class="container">
      <div class="header">
        <h1><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="m480-120-58-52q-101-91-167-157T150-447.5Q111-500 95.5-544T80-634q0-94 63-157t157-63q52 0 99 22t81 62q34-40 81-62t99-22q94 0 157 63t63 157q0 46-15.5 90T810-447.5Q771-395 705-329T538-172l-58 52Zm0-108q96-86 158-147.5t98-107q36-45.5 50-81t14-70.5q0-60-40-100t-100-40q-47 0-87 26.5T518-680h-76q-15-41-55-67.5T300-774q-60 0-100 40t-40 100q0 35 14 70.5t50 81q36 45.5 98 107T480-228Zm0-273Z"/></svg> My Favorite Books</h1>
        <p>There when you found all of your favorite books</p>
      </div>

      <div class="box-container" id="favoritesContainer">
        <?php
        if ($total_favs == 0) {
            echo "<p id='noBooksMsg' style='grid-column: 1/-1; text-align:center; padding:50px;'>No books were found in your favorites.</p>";
        }

        while ($rowl = $requete->fetch(PDO::FETCH_ASSOC)) {
          $id_livre = $rowl['NumLivre'];
          $titre = $rowl['TitreLivre'];
          $auteur = $rowl['NomAuteur'] ?? "Unknown Author";
          $theme_nom = $rowl['intituleTheme'] ?? "General";
          
          echo "
          <div class='box' id='book-card-{$id_livre}'>
              <div class='img_box'>
                  <button class='btn-fav-remove' onclick='removeFavorite({$id_livre})' title='Remove from favorites'>
                      <svg xmlns='http://www.w3.org/2000/svg' viewBox='0 -960 960 960'><path d='m480-120-58-52q-101-91-167-153T150-447.5Q114-510 100-562t-14-102q0-102 69-171t171-69q57 0 108 26t86 70q35-44 86-70t108-26q102 0 171 69t69 171q0 50-14 102t-50 114.5Q744-325 678-263t-167 153l-58 52Z'/></svg>
                  </button>
                  <img src='getimage.php?id={$id_livre}' alt='".htmlspecialchars($titre)."'>
              </div>
              <div class='info_box'>
                  <span class='genre-badge'>{$theme_nom}</span>
                  <h3 class='book-title'>".htmlspecialchars($titre)."</h3>
                  <p class='book-author'>".htmlspecialchars($auteur)."</p>
              </div>
          </div>";
        }
        ?>

        <?php if ($total_pages > 1): ?>
          <div class="pagination" id="paginationBlock">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
              <a href="?page=<?php echo $i; ?>" class="<?php echo ($page == $i) ? 'active' : ''; ?>">
                <?php echo $i; ?>
              </a>
            <?php endfor; ?>
          </div>
        <?php endif; ?>

      </div>
    </div>
  </main>

  <script>
    function removeFavorite(bookId) {
        fetch('add_favorite.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id_livre=' + bookId
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'removed' || data.status === 'success') {
                const card = document.getElementById('book-card-' + bookId);
                if (card) {
                    card.classList.add('fade-out'); 
                    setTimeout(() => {
                        card.remove(); 
                        
                        const container = document.getElementById('favoritesContainer');
                        const remainingCards = container.querySelectorAll('.box');
                        
                        if (remainingCards.length === 0) {
                            const urlParams = new URLSearchParams(window.location.search);
                            let currentPage = parseInt(urlParams.get('page')) || 1;
                            
                            if (currentPage > 1) {
                                window.location.href = '?page=' + (currentPage - 1);
                            } else {
                                container.innerHTML = "<p id='noBooksMsg' style='grid-column: 1/-1; text-align:center; padding:50px;'>No books were found in your favorites.</p>";
                                const pagBlock = document.getElementById('paginationBlock');
                                if(pagBlock) pagBlock.remove();
                            }
                        }
                    }, 400);
                }
            } else {
                alert(data.message || "Erreur lors de la suppression.");
            }
        })
        .catch(err => {
            console.error(err);
            alert("Une erreur s'est produite.");
        });
    }
  </script>
</body>
</html>
