<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
$current_page = basename($_SERVER["PHP_SELF"]);
?>
<nav class="nav_lg">
  <div class="logo">
    <img src="image/Untitled130_20260518214254.png" alt="Logo">
    <h1>MyLibrary</h1>
  </div>
  <div class="btn_nav">
    <ul class="main-menu">
      <?php if (isset($_SESSION["loginrole"]) && (int) $_SESSION["loginrole"] === 1): ?>
        <li class="<?php echo $current_page == "dashboardbibio.php" ? "active" : ""; ?>">
          <a href="dashboardbibio.php">
            <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"><path d="M520-600v-240h320v240H520ZM120-440v-400h320v400H120Zm400 320v-400h320v400H520Zm-400 0v-240h320v240H120Zm80-400h160v-240H200v240Zm400 320h160v-240H600v240Zm0-480h160v-80H600v80ZM200-200h160v-80H200v80Zm160-320Zm240-160Zm0 240ZM360-280Z" /></svg> 
            <span>Dashboard</span>
          </a>
        </li>
      <?php endif; ?>
      
      <li class="<?php echo $current_page == "home.php" ? "active" : ""; ?>">
        <a href="home.php">
          <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"><path d="M240-200h120v-240h240v240h120v-360L480-740 240-560v360Zm-80 80v-480l320-240 320 240v480H520v-240h-80v240H160Zm320-350Z" /></svg> 
          <span>Home</span>
        </a>
      </li>
      
      <li class="<?php echo $current_page == "my_favorites.php" ? "active" : ""; ?>">
        <a href="my_favorites.php">
          <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"><path d="m480-120-58-52q-101-91-167-157T150-447.5Q111-500 95.5-544T80-634q0-94 63-157t157-63q52 0 99 22t81 62q34-40 81-62t99-22q94 0 157 63t63 157q0 46-15.5 90T810-447.5Q771-395 705-329T538-172l-58 52Zm0-108q96-86 158-147.5t98-107q36-45.5 50-81t14-70.5q0-60-40-100t-100-40q-47 0-87 26.5T518-680h-76q-15-41-55-67.5T300-774q-60 0-100 40t-40 100q0 35 14 70.5t50 81q36 45.5 98 107T480-228Zm0-273Z"/></svg> 
          <span>Favorite</span>
        </a>
      </li>
      
      <li class="<?php echo $current_page == "mes_emprunts.php" ? "active" : ""; ?>">
        <a href="mes_emprunts.php">
          <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"><path d="M480-40 192-256q-15-11-23.5-28t-8.5-36v-480q0-33 23.5-56.5T240-880h480q33 0 56.5 23.5T800-800v480q0 19-8.5 36T768-256L480-40Zm0-100 240-180v-480H240v480l240 180Zm-42-220 226-226-56-58-170 170-84-84-58 56 142 142Zm42-440H240h480-240Z"/></svg> 
          <span>Empruntes</span>
        </a>
      </li>
      
      <li class="<?php echo $current_page == "editpro.php" ? "active" : ""; ?>">
        <a href="editpro.php">
          <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"><path d="M367-527q-47-47-47-113t47-113q47-47 113-47t113 47q47 47 47 113t-47 113q-47 47-113 47t-113-47ZM160-160v-112q0-34 17.5-62.5T224-378q62-31 126-46.5T480-440q66 0 130 15.5T736-378q29 15 46.5 43.5T800-272v112H160Zm80-80h480v-32q0-11-5.5-20T700-306q-54-27-109-40.5T480-360q-56 0-111 13.5T260-306q-9 5-14.5 14t-5.5 20v32Zm296.5-343.5Q560-607 560-640t-23.5-56.5Q513-720 480-720t-56.5 23.5Q400-673 400-640t23.5 56.5Q447-560 480-560t56.5-23.5ZM480-640Zm0 400Z" /></svg> 
          <span>Profile</span>
        </a>
      </li>
      
      <li>
        <button id="theme-btn" class="btn-theme">
          <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"><path d="M480-120q-150 0-255-105T120-480q0-150 105-255t255-105q14 0 27.5 1t26.5 3q-41 29-65.5 75.5T444-660q0 90 63 153t153 63q55 0 101-24.5t75-65.5q2 13 3 26.5t1 27.5q0 150-105 255T480-120Zm0-80q88 0 158-48.5T740-375q-20 5-40 8t-40 3q-123 0-209.5-86.5T364-660q0-20 3-40t8-40q-78 32-126.5 102T200-480q0 116 82 198t198 82Zm-10-270Z"/></svg>
        </button>
      </li>
      
      <li class="humberger-item" id="humberger-item">
        <button id="humberger-btn">
          <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px"><path d="M240-400q-33 0-56.5-23.5T160-480q0-33 23.5-56.5T240-560q33 0 56.5 23.5T320-480q0 33-23.5 56.5T240-400Zm240 0q-33 0-56.5-23.5T400-480q0-33 23.5-56.5T480-560q33 0 56.5 23.5T560-480q0 33-23.5 56.5T480-400Zm240 0q-33 0-56.5-23.5T640-480q0-33 23.5-56.5T720-560q33 0 56.5 23.5T800-480q0 33-23.5 56.5T720-400Z"/></svg>
        </button>
        <ul id="humberger-layout" class="humberger-layout">
          </ul>
      </li>
    </ul>
  </div>
</nav>

<style>
  .nav_lg {
    grid-area: navlg;
    display: flex;
    background-color: var(--surface-color, whitesmoke);
    height: 70px;
    width: 100%;
    justify-content: space-between;
    align-items: center;
    position: sticky;
    top: 0;
    left: 0;
    z-index: 1000;
  }
  .logo {
    display: flex;
    justify-content: center;
    align-items: center;
    padding: 7px 20px;
    font-size: 1em;
    color: var(--text-color, #333);
  }
  .logo img {
    width: 50px;
    height: 50px;
  }
  .btn_nav {
    display: flex;
    align-items: center;
  }
  .btn_nav ul.main-menu {
    display: flex;
    padding-right: 20px;
    list-style: none;
    gap: 10px;
    align-items: center;
    margin: 0;
  }
  .btn_nav ul li {
    border-radius: .5em;
    display: flex;
    justify-content: center;
    align-items: center;
    white-space: nowrap; 
  }
  .btn_nav ul li span {
    display: none;
  }
  .btn_nav ul a {
    display: flex;
    justify-content: center;
    align-items: center;
    text-decoration: none;
    padding: 10px 20px;
    color: var(--text-color, #333);
    gap: 10px;
    border-radius: 5px;
    font-size: 0.95rem;
    font-weight: 600;
  }
  .btn_nav ul li svg {
    fill: var(--text-color, #333);
  }
  
  .btn_nav ul li.active {
    background-color: var(--brand-color, #007bff);
    span{
      display: block !important;
    }
  }
  .btn_nav ul li.active a { color: whitesmoke; }
  .btn_nav ul li.active svg { fill: whitesmoke; }

  .btn_nav ul li:hover:not(.humberger-item) {
    background-color: var(--seconde-brand-color, #0056b3);
  }
  .btn_nav ul li:hover:not(.humberger-item) a { color: whitesmoke; }
  .btn_nav ul li:hover:not(.humberger-item) svg { fill: whitesmoke; }

  .btn-theme {
    padding: 10px;
    border: none;
    background: none;
    cursor: pointer;
  }

  .humberger-item {
    display: none !important;
    position: relative;
  }
  .humberger-item.show-btn {
    display: flex !important;
  }

  #humberger-btn {
    background-color: var(--surface-color, #eee);
    border: none;
    padding: 7px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
  }
  #humberger-btn svg {
    fill: var(--text-color, #333);
  }

  .humberger-layout {
    display: none; 
    position: absolute;
    width: 200px;
    top: 55px;
    right: 0;
    border-radius: 10px;
    background-color: var(--surface-color, whitesmoke);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    padding: 10px !important;
    list-style: none;
    flex-direction: column;
    gap: 10px;
    z-index: 2000;
  }

  .humberger-layout.open {
    display: flex !important;
  }

  .humberger-layout li {
    width: 100%;
  }
  .humberger-layout li a {
    justify-content: flex-start !important;
    width: 100%;
  }
</style>

<script>
document.addEventListener("DOMContentLoaded", function() {
  const nav = document.querySelector(".btn_nav");
  const mainMenu = document.querySelector(".main-menu");
  const humitem = document.getElementById("humberger-item");
  const humlayout = document.getElementById("humberger-layout");
  const themeLi = document.getElementById("theme-btn").closest("li");

  const originalLinks = Array.from(mainMenu.querySelectorAll("li:not(.humberger-item):not(:nth-last-child(2))"));

  function adjustResponsiveMenu() {
    humlayout.classList.remove("open"); 
    
    originalLinks.forEach(link => mainMenu.insertBefore(link, themeLi));
    humlayout.innerHTML = "";
    humitem.classList.remove("show-btn");

    if (mainMenu.scrollWidth > nav.offsetWidth) {
      humitem.classList.add("show-btn");

      for (let i = originalLinks.length - 1; i >= 0; i--) {
        if (mainMenu.scrollWidth > nav.offsetWidth) {
          humlayout.insertBefore(originalLinks[i], humlayout.firstChild);
        } else {
          break;
        }
      }
    }
  }

  // تشغيل الحساب مع تحميل الصفحة ومع تغيير الحجم
  adjustResponsiveMenu();
  window.addEventListener("resize", adjustResponsiveMenu);

  // الفتح والغلق عند الضغط
  humitem.addEventListener("click", function (e) {
    e.stopPropagation();
    humlayout.classList.toggle("open");
  });

  // غلق القائمة عند الضغط في أي مكان آخر
  document.addEventListener("click", function () {
    humlayout.classList.remove("open");
  });
});


let themebtn; 

function toggleth() {
  const isDark = document.body.classList.toggle('darkmode');
  localStorage.setItem('theme', isDark ? 'dark' : 'light');
  themebtn.innerHTML = isDark ? 
    '<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M565-395q35-35 35-85t-35-85q-35-35-85-35t-85 35q-35 35-35 85t35 85q35 35 85 35t85-35Zm-226.5 56.5Q280-397 280-480t58.5-141.5Q397-680 480-680t141.5 58.5Q680-563 680-480t-58.5 141.5Q563-280 480-280t-141.5-58.5ZM200-440H40v-80h160v80Zm720 0H760v-80h160v80ZM440-760v-160h80v160h-80Zm0 720v-160h80v160h-80ZM256-650l-101-97 57-59 96 100-52 56Zm492 496-97-101 53-55 101 97-57 59Zm-98-550 97-101 59 57-100 96-56-52ZM154-212l101-97 55 53-97 101-59-57Zm326-268Z"/></svg>' : 
    '<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M480-120q-150 0-255-105T120-480q0-150 105-255t255-105q14 0 27.5 1t26.5 3q-41 29-65.5 75.5T444-660q0 90 63 153t153 63q55 0 101-24.5t75-65.5q2 13 3 26.5t1 27.5q0 150-105 255T480-120Zm0-80q88 0 158-48.5T740-375q-20 5-40 8t-40 3q-123 0-209.5-86.5T364-660q0-20 3-40t8-40q-78 32-126.5 102T200-480q0 116 82 198t198 82Zm-10-270Z"/></svg>';
}

function appliedThemeStored() {
  if (localStorage.getItem('theme') === 'dark') {
    document.body.classList.add('darkmode');
    if(themebtn) {
      themebtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 -960 960 960" width="24px" fill="#e3e3e3"><path d="M565-395q35-35 35-85t-35-85q-35-35-85-35t-85 35q-35 35-35 85t35 85q35 35 85 35t85-35Zm-226.5 56.5Q280-397 280-480t58.5-141.5Q397-680 480-680t141.5 58.5Q680-563 680-480t-58.5 141.5Q563-280 480-280t-141.5-58.5ZM200-440H40v-80h160v80Zm720 0H760v-80h160v80ZM440-760v-160h80v160h-80Zm0 720v-160h80v160h-80ZM256-650l-101-97 57-59 96 100-52 56Zm492 496-97-101 53-55 101 97-57 59Zm-98-550 97-101 59 57-100 96-56-52ZM154-212l101-97 55 53-97 101-59-57Zm326-268Z"/></svg>';
    }
  }
}

document.addEventListener('DOMContentLoaded', function() {
  themebtn = document.getElementById("theme-btn"); 
  appliedThemeStored();
  if(themebtn) themebtn.addEventListener('click', toggleth);
});
</script>
