document.addEventListener('DOMContentLoaded', function() {
    const logoutBtn = document.getElementById('logout-btn');

    if (logoutBtn) {
        logoutBtn.addEventListener('click', async (e) => {
            e.preventDefault();
            
            // This is a simple logout. In a real app, you'd call an API endpoint.
            // For now, we'll just redirect to a non-existent PHP script to clear the session.
            window.location.href = 'logout.php';
        });
    }
}); 