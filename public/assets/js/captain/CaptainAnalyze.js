/* ---------- NAVIGATION ---------- */
// document.querySelectorAll('.nav-link').forEach(link => {
//     link.addEventListener('click', function (e) {
//         e.preventDefault();

//         // Active nav
//         document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('active'));
//         this.classList.add('active');

//         // Pages
//         document.querySelectorAll('.page-content').forEach(p => p.classList.remove('active'));
//         const page = this.dataset.page;
//         const target = document.getElementById(`${page}-page`);
//         if (target) target.classList.add('active');

//         if (page === 'meal-plan') {
//             applyMealFilterFromActiveTab();
//         }
//     });
// });