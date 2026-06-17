function openDialogAdd() {
    document.getElementById("adddia").showModal();
}
function closeDialogAdd() {
    document.getElementById("adddia").close();
}
function openDialogAddUser() {
    document.getElementById("add_user").showModal();
}
function closeDialogAddUser() {
    document.getElementById("add_user").close();
}
function openDialogAddAut() {
    document.getElementById("add_aut").showModal();
}
function closeDialogAddAut() {
    document.getElementById("add_aut").close();
}
function openDialogAddEdi() {
    document.getElementById("add_edi").showModal();
}
function closeDialogAddEdi() {
    document.getElementById("add_edi").close();
}
function openDialogAddThm() {
    document.getElementById("add_thm").showModal();
}
function closeDialogAddThm() {
    document.getElementById("add_thm").close();
}

function switchActiveTab(clickedLink) {
    const allLinks = [tolivre, touser, toauteur, toediteur, totheme];

    allLinks.forEach(link => {
        if (link) {
            link.classList.remove("active");
            if (link.parentElement) {
                link.parentElement.classList.remove("active");
            }
        }
    });

    if (clickedLink) {
        clickedLink.classList.add("active");
        if (clickedLink.parentElement) {
            clickedLink.parentElement.classList.add("active");
        }
    }
}

const tolivre = document.getElementById("tolivre");
const touser = document.getElementById("touser");
const toauteur = document.getElementById("toauteur");
const toediteur = document.getElementById("toediteur");
const totheme = document.getElementById("totheme");
const toemp = document.getElementById("toemp");

const livre = document.getElementById("livre");
const user = document.getElementById("user");
const auteur = document.getElementById("auteur");
const editeur = document.getElementById("editeur");
const theme = document.getElementById("theme");
const emprunt = document.getElementById("emprunt");

if (tolivre) {
    tolivre.addEventListener("click", function (e) {
        e.preventDefault();
        switchActiveTab(this);
        window.location.hash = "Livres";
        livre.classList.remove("hidden");
        user.classList.add("hidden");
        auteur.classList.add("hidden");
        editeur.classList.add("hidden");
        theme.classList.add("hidden");
        emprunt.classList.add("hidden");
    });
}
if (touser) {
    touser.addEventListener("click", function (e) {
        e.preventDefault();
        switchActiveTab(this);
        window.location.hash = "Users";
        livre.classList.add("hidden");
        user.classList.remove("hidden");
        auteur.classList.add("hidden");
        editeur.classList.add("hidden");
        theme.classList.add("hidden");
        emprunt.classList.add("hidden");
    });
}

if (toauteur) {
    toauteur.addEventListener("click", function (e) {
        e.preventDefault();
        switchActiveTab(this);
        window.location.hash = "Auteurs";
        livre.classList.add("hidden");
        user.classList.add("hidden");
        auteur.classList.remove("hidden");
        editeur.classList.add("hidden");
        theme.classList.add("hidden");
        emprunt.classList.add("hidden");
    });
}

if (toediteur) {
    toediteur.addEventListener("click", function (e) {
        e.preventDefault();
        switchActiveTab(this);
        window.location.hash = "Editeurs";
        livre.classList.add("hidden");
        user.classList.add("hidden");
        auteur.classList.add("hidden");
        editeur.classList.remove("hidden");
        theme.classList.add("hidden");
        emprunt.classList.add("hidden");
    });
}

if (totheme) {
    totheme.addEventListener("click", function (e) {
        e.preventDefault();
        switchActiveTab(this);
        window.location.hash = "Theme";
        livre.classList.add("hidden");
        user.classList.add("hidden");
        auteur.classList.add("hidden");
        editeur.classList.add("hidden");
        theme.classList.remove("hidden");
        emprunt.classList.add("hidden");
    });
}

if (toemp) {
    toemp.addEventListener("click", function (e) {
        e.preventDefault();
        switchActiveTab(this);
        window.location.hash = "Emprunts";
        livre.classList.add("hidden");
        user.classList.add("hidden");
        auteur.classList.add("hidden");
        editeur.classList.add("hidden");
        theme.classList.add("hidden");
        emprunt.classList.remove("hidden");
    });
}

window.addEventListener("DOMContentLoaded", () => {
    const hash = window.location.hash;

    livre.classList.add("hidden");
    user.classList.add("hidden");
    auteur.classList.add("hidden");
    editeur.classList.add("hidden");
    theme.classList.add("hidden");
    emprunt.classList.add("hidden");

    if (hash === "#Users") {
        user.classList.remove("hidden");
        switchActiveTab(touser);
    } else if (hash === "#Auteurs") {
        auteur.classList.remove("hidden");
        switchActiveTab(toauteur);
    } else if (hash === "#Editeurs") {
        editeur.classList.remove("hidden");
        switchActiveTab(toediteur);
    } else if (hash === "#Theme") {
        theme.classList.remove("hidden");
        switchActiveTab(totheme);
    } else if (hash === "#Emprunts") {
        emprunt.classList.remove("hidden");
        switchActiveTab(toemp);
    } else {
        livre.classList.remove("hidden");
        switchActiveTab(tolivre);
    }
});

function loadPage(section, page, event) {
    if (event) event.preventDefault();

    fetch(`get_table_data.php?section=${section}&page=${page}`)
        .then(response => {
            if (!response.ok) throw new Error("Network status error");
            return response.json();
        })
        .then(data => {
            var tableContainer = document.getElementById(`table-${section}`);
            var paginationContainer = document.getElementById(
                `pagination-${section}`
            );
            if (tableContainer) tableContainer.innerHTML = data.table;
            if (paginationContainer)
                paginationContainer.innerHTML = data.pagination;
        })
        .catch(error =>
            console.error("Erreur AJAX section " + section + ":", error)
        );
}

document.addEventListener("DOMContentLoaded", function () {
    loadPage("livre", 1, null);
    loadPage("user", 1, null);
    loadPage("auteur", 1, null);
    loadPage("editeur", 1, null);
    loadPage("theme", 1, null);
    loadPage("emprunt", 1, null);
});

