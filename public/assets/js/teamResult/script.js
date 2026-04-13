function openAddTestResultModal() {
    const modal = document.getElementById("addTestResultModal");
    if (modal) {
        modal.classList.add("active");
    }
}

function closeAddTestResultModal() {
    const modal = document.getElementById("addTestResultModal");
    const form = document.getElementById("addTestResultForm");

    if (modal) {
        modal.classList.remove("active");
    }

    if (form) {
        form.reset();
    }
}

function openAddMatchResultModal() {
    const modal = document.getElementById("addMatchResultModal");
    if (modal) {
        modal.classList.add("active");
    }
}

function closeAddMatchResultModal() {
    const modal = document.getElementById("addMatchResultModal");
    const form = document.getElementById("addMatchResultForm");

    if (modal) {
        modal.classList.remove("active");
    }

    if (form) {
        form.reset();
    }
}


document.addEventListener("DOMContentLoaded", function () {
    const openButtons = document.querySelectorAll(".add-result-btn");
    const closeButtons = document.querySelectorAll(".close-modal-btn");
    const testModal = document.getElementById("addTestResultModal");
    const matchModal = document.getElementById("addMatchResultModal");
    const searchBtn = document.getElementById("searchBtn");
    const searchResultInput = document.getElementById("playerSearch");

    openButtons.forEach(function (button) {
        const buttonText = button.textContent.trim().toLowerCase();

        if (buttonText.includes("match")) {
            button.addEventListener("click", openAddMatchResultModal);
        } else {
            button.addEventListener("click", openAddTestResultModal);
        }
    });

    closeButtons.forEach(function (button) {
        button.addEventListener("click", function () {
            if(button.closest("#addTestResultModal")) {
                closeAddTestResultModal();
            } else if(button.closest("#addMatchResultModal")) {
                closeAddMatchResultModal();}      
        });
    });
});