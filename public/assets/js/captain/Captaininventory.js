document.addEventListener("DOMContentLoaded", () => {

    new Chart(document.getElementById("equipmentUsageChart"), {
        type: "bar",
        data: {
            labels: ["Equipment", "Kits", "Balls", "Accessories"],
            datasets: [
                {
                    label: "In Use",
                    data: [45, 32, 28, 40],
                    backgroundColor: "#f97316"
                },
                {
                    label: "Available",
                    data: [25, 18, 22, 20],
                    backgroundColor: "#22c55e"
                }
            ]
        }
    });

    new Chart(document.getElementById("statusDistributionChart"), {
        type: "pie",
        data: {
            labels: ["In Use", "Available", "Damaged"],
            datasets: [{
                data: [142, 89, 17],
                backgroundColor: ["#f97316", "#22c55e", "#ef4444"]
            }]
        }
    });

});
