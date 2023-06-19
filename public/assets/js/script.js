document.addEventListener("DOMContentLoaded", function() {
    var saveButton = document.getElementById("saveButton");
    var deleteButton = document.getElementById("deleteButton");
    var deleteForm = document.getElementById("deleteForm")

    saveButton.addEventListener("click", function() {
        saveButton.classList.add("button-processing");
        saveButton.innerHTML = "Sauvegarde en cours...";
    });

    deleteForm.addEventListener("submit", function() {
        var confirmation = confirm("Voulez-vous vraiment supprimer cet element?");
        if (!confirmation){
            event.preventDefault();
            return;
        }
        deleteButton.classList.add("button-processing");
        deleteButton.innerHTML = "Suppression en cours...";
    });

    // Affichage du modal de modification
    var modal = new bootstrap.Modal(document.getElementById("primary-header-modal"));
    modal.show();
});


