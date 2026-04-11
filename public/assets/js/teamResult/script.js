function openAddResultModal() {
    const modal = document.getElementById("addResultModal");
    if (modal) {
        modal.classList.add("active");
    }
}

function closeAddResultModal() {
    const modal = document.getElementById("addResultModal");
    const form = document.getElementById("addResultForm");

    if (modal) {
        modal.classList.remove("active");
    }

    if (form) {
        form.reset();
    }
}

document.addEventListener("DOMContentLoaded", function () {
    const openBtn = document.querySelector(".add-result-btn");
    const modal = document.getElementById("addResultModal");
    const searchBtn = document.getElementById("searchBtn");
    const searchInput = document.getElementById("playerSearch");

    function performResultSearch() {
        if (!searchInput) {
            return;
        }

        const query = searchInput.value.trim().toLowerCase();
        const rows = document.querySelectorAll(".results-container .result");

        rows.forEach(function (row) {
            const firstCell = row.querySelector("p");
            const rowText = firstCell ? firstCell.textContent.trim().toLowerCase() : "";
            row.style.display = rowText.includes(query) ? "grid" : "none";
        });
    }

    if (openBtn) {
        openBtn.addEventListener("click", openAddResultModal);
    }

    if (modal) {
        modal.addEventListener("click", function (event) {
            if (event.target === modal) {
                closeAddResultModal();
            }
        });
    }

    if (searchBtn) {
        searchBtn.addEventListener("click", performResultSearch);
    }

    if (searchInput) {
        searchInput.addEventListener("keydown", function (event) {
            if (event.key === "Enter") {
                event.preventDefault();
                performResultSearch();
            }
        });
    }
});