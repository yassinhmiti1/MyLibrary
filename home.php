<?php
require_once("config.php");
session_start();

$limit = 12;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$current_theme = isset($_REQUEST["id_theme"]) ? $_REQUEST["id_theme"] : 'all';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>Home Page</title>
  <link rel="stylesheet" href="stylehome.css">
  <style>
    
    
  </style>
</head>
<body>
  <main class="maincontainer">
    <?php include("nav_lg.php"); ?>
    <div class="container">
      <div class="header">
        <h1><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M440-278v-394q-41-24-87-36t-93-12q-36 0-71.5 7T120-692v396q35-12 69.5-18t70.5-6q47 0 91.5 10.5T440-278Zm40 118q-48-38-104-59t-116-21q-42 0-82.5 11T100-198q-21 11-40.5-1T40-234v-482q0-11 5.5-21T62-752q46-24 96-36t102-12q74 0 126 17t112 52q11 6 16.5 14t5.5 21v418q44-21 88.5-31.5T700-320q36 0 70.5 6t69.5 18v-481q15 5 29.5 11t28.5 14q11 5 16.5 15t5.5 21v482q0 23-19.5 35t-40.5 1q-37-20-77.5-31T700-240q-60 0-116 21t-104 59Zm140-240v-440l120-40v440l-120 40Zm-340-99Z" /></svg> Library Colections</h1>
        <br>
        <p>Discover and explore our extensive collection of books</p>
      </div>
      <br>
      
      <div class="filter_box">
        <h2><svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M440-160q-17 0-28.5-11.5T400-200v-240L168-736q-15-20-4.5-42t36.5-22h560q26 0 36.5 22t-4.5 42L560-440v240q0 17-11.5 28.5T520-160h-80Zm40-308 198-252H282l198 252Zm0 0Z" /></svg> Filter By Theme</h2>
        <div class="filters">
          <?php
          $all_active = ($current_theme === 'all') ? "active" : "";
          echo "
          <form method='post' style='display:inline;'>
              <input type='hidden' name='id_theme' value='all'>
              <button type='submit' class='filter-value {$all_active}'>All</button>
          </form>";

          $sqlth = "Select * from theme";
          $rep = $cnx->query($sqlth);
          while ($row = $rep->fetch(PDO::FETCH_BOTH)) {
            $is_active = ($current_theme == $row[0]) ? "active" : "";
            echo "
            <form method='post' style='display:inline;'>
                <input type='hidden' name='id_theme' value='".$row[0]."'>
                <button type='submit' class='filter-value {$is_active}'>".$row[1]."</button>
            </form>";
          }
          ?>
        </div>
      </div>
      <br><br>

      <div class="box-container">
        <?php
        if ($current_theme && $current_theme !== 'all') {
          $count_sql = "SELECT COUNT(*) FROM livre WHERE NumTheme = ?";
          $stmt_count = $cnx->prepare($count_sql);
          $stmt_count->execute([$current_theme]);
          $total_books = $stmt_count->fetchColumn();

          $sql = "SELECT livre.*, theme.intituleTheme, Auteur.NomAuteur FROM livre JOIN theme ON livre.NumTheme = theme.NumTheme Join Auteur on livre.NumAuteur= Auteur.NumAuteur WHERE livre.NumTheme = ? LIMIT $limit OFFSET $offset";
          $requete = $cnx->prepare($sql);
          $requete->execute([$current_theme]);
        } else {
          // حساب المجموع لكل الكتب
          $total_books = $cnx->query("SELECT COUNT(*) FROM livre")->fetchColumn();

          // جلب كل الكتب بـ LIMIT
          $sql = "SELECT livre.*, theme.intituleTheme, Auteur.NomAuteur FROM livre JOIN theme ON livre.NumTheme = theme.NumTheme Join Auteur on livre.NumAuteur= Auteur.NumAuteur LIMIT $limit OFFSET $offset";
          $requete = $cnx->query($sql);
        }

        $total_pages = ceil($total_books / $limit);

        while ($rowl = $requete->fetch(PDO::FETCH_BOTH)) {
          $id_livre = $rowl[0];
          $titre = $rowl[1];
          $auteur = isset($rowl['NomAuteur']) ? $rowl['NomAuteur'] : "Unknown Author";
          $theme_nom = isset($rowl['intituleTheme']) ? $rowl['intituleTheme'] : "General";
          $nbr_exmp = isset($rowl['nbr_exmp']) ? intval($rowl['nbr_exmp']) : intval($rowl[7]);

          if ($nbr_exmp > 0) {
            $status_class = "available";
            $status_text = "Available";
            $icon_path = "m382-320 284-284-56-56-228 228-114-114-56 56 170 170ZM480-80q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Z";
          } else {
            $status_class = "borrowed";
            $status_text = "Borrowed";
            $icon_path = "m336-280 144-144 144 144 56-56-144-144 144-144-56-56-144 144-144-144-56 56 144 144-144 144 56 56ZM480-80q-83 0-156-31.5T197-197q-54-54-85.5-127T80-480q0-83 31.5-156T197-763q54-54 127-85.5T480-880q83 0 156 31.5T763-763q54 54 85.5 127T880-480q0 83-31.5 156T763-197q-54 54-127 85.5T480-80Z";
          }

          echo "
          <div class='box'>
              <div class='img_box'>
                  <button class='btn-fav' onclick='addToFavorite({$id_livre})' title='Add to favorites'>
                      <svg xmlns='http://www.w3.org/2000/svg' viewBox='0 -960 960 960'><path d='m480-120-58-52q-101-91-167-153T150-447.5Q114-510 100-562t-14-102q0-102 69-171t171-69q57 0 108 26t86 70q35-44 86-70t108-26q102 0 171 69t69 171q0 50-14 102t-50 114.5Q744-325 678-263t-167 153l-58 52Z'/></svg>
                  </button>
                  
                  <img src='getimage.php?id={$id_livre}' alt='{$titre}'>
                  <span class='status-badge {$status_class}'>
                      <svg xmlns='http://www.w3.org/2000/svg' height='16px' viewBox='0 -960 960 16px' width='16px' fill='#fff'><path d='{$icon_path}'/></svg>
                      {$status_text}
                  </span>
              </div>
              <div class='info_box'>
                  <span class='genre-badge'>{$theme_nom}</span>
                  <h3 class='book-title'>{$titre}</h3>
                  <p class='book-author'>{$auteur}</p>
                  <button class='btn-get' onclick='openBookDetails({$id_livre})'>Get Livre</button>
              </div>
          </div>";
        }
        ?>
      </div>

      <?php if ($total_pages > 1): ?>
        <div class="pagination">
          <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?php echo $i; ?>&id_theme=<?php echo urlencode($current_theme); ?>" class="<?php echo ($page == $i) ? 'active' : ''; ?>">
              <?php echo $i; ?>
            </a>
          <?php endfor; ?>
        </div>
      <?php endif; ?>

      <dialog id="bookDetailsDialog" class="book-dialog">
        <div class="dialog-content" id="dialogDynamicContent">
            <p style="padding: 20px; text-align:center;">Loading details...</p>
        </div>
      </dialog>

    </div>
  </main>

  <script>
    const dialog = document.getElementById('bookDetailsDialog');

    function openBookDetails(bookId) {
        dialog.showModal();
        document.getElementById('dialogDynamicContent').innerHTML = '<p style="padding: 20px; text-align:center;">Loading...</p>';

        fetch('get_book_details.php?id=' + bookId)
            .then(response => response.text())
            .then(data => {
                document.getElementById('dialogDynamicContent').innerHTML = data;
            })
            .catch(err => {
                document.getElementById('dialogDynamicContent').innerHTML = '<p style="color:red; padding:20px;">Error loading details.</p>';
            });
    }

    function closeBookDetails() {
        dialog.close();
    }

    dialog.addEventListener('click', (e) => {
        if (e.target === dialog) dialog.close();
    });

    // add favorite
    function addToFavorite(bookId) {
        fetch('add_favorite.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'id_livre=' + bookId
        })
        .then(response => response.json())
        .then(data => {
            alert(data.message);
        })
        .catch(err => {
            console.error(err);
            alert("l'ajout a le favorite a echoue!");
        });
    }
  </script>
</body>
</html>
