// Footer interactions
document.addEventListener('DOMContentLoaded', () => {
    const socialLinks = document.querySelectorAll('.social-link');
    const sponsorLogos = document.querySelectorAll('.footer-sponsor');
    const footerLogos = document.querySelectorAll('.footer-logo');

    // Social links interactions
    socialLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            const platform = link.querySelector('img').alt;
            if (typeof showNotification === 'function') {
                showNotification(`📱 ${platform} clicked! This would open our ${platform} page.`);
            }
        });
    });

    // Sponsor logo interactions
    sponsorLogos.forEach(logo => {
        logo.addEventListener('click', () => {
            const sponsor = logo.alt;
            if (typeof showNotification === 'function') {
                showNotification(`🤝 Thank you ${sponsor} for supporting UOC Football!`);
            }
        });
    });

    // Footer logo interactions
    footerLogos.forEach(logo => {
        logo.addEventListener('click', () => {
            const logoName = logo.alt;
            if (typeof showNotification === 'function') {
                showNotification(`🏫 ${logoName} clicked! This would navigate to the university homepage.`);
            }
        });
    });
});