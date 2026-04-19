document.addEventListener("DOMContentLoaded", function () {
    var cards = document.querySelectorAll(".store-card");

    cards.forEach(function (card, index) {
        card.style.opacity = "0";
        card.style.transform = "translateY(10px)";

        setTimeout(function () {
            card.style.transition = "opacity 260ms ease, transform 260ms ease";
            card.style.opacity = "1";
            card.style.transform = "translateY(0)";
        }, index * 60);
    });
});
