///// AFFICHER et CACHER les filtres/////


//Tout le DOM doit être chargé
document.addEventListener("DOMContentLoaded", () => {
  //On récupère les éléments par leur ID
    const filter = document.getElementById("filter");
    const openBtn = document.getElementById("openBtn");
    const closeBtn = document.getElementById("closeBtn");

    //Vérification que les trois élément existent
    if (filter && openBtn && closeBtn) {
        openBtn.addEventListener("click", (e) => {
            e.preventDefault(); // Empêche le comportement par défaut du lien (recharger la page)
            filter.style.display = "block";  //Affichage du bloc filtre
            openBtn.style.display = "none";  //Cacher openBtn
            closeBtn.style.display = "inline-block"; //Affiche Filtre-
        });

        closeBtn.addEventListener("click", (e) => {
            e.preventDefault();
            filter.style.display = "none";
            closeBtn.style.display = "none";
            openBtn.style.display = "inline-block";
        });
    }
});
