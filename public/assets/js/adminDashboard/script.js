// Admin Dashboard JavaScript

// Logout functionality
document.addEventListener('DOMContentLoaded', function() {
    const logoutBtn = document.getElementById('logoutBtn');
    
    if (logoutBtn) {
        logoutBtn.addEventListener('click', handleLogout);
    }
});

// Handle logout
function handleLogout() {
    // Show confirmation dialog
    if (confirm('Are you sure you want to logout?')) {
        // Clear session and redirect
        logout();
    }
}

// Logout function
async function logout() {
    try {
        // Call logout endpoint
        const response = await fetch('../login/logout', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        });

        if (response.ok) {
            // Redirect to landing page
            window.location.href = '../LandingPage';
        } else {
            // If logout endpoint doesn't exist, just redirect
            window.location.href = '../LandingPage';
        }
    } catch (error) {
        console.error('Logout error:', error);
        // Redirect anyway
        window.location.href = '../LandingPage';
    }
}

console.log('Admin Dashboard script loaded');
