document.addEventListener('DOMContentLoaded', function() {
    const cookieConsent = document.getElementById('cookieConsent');
    const acceptBtn = document.getElementById('acceptCookies');
    
    // Check if user has already made a choice
    if (!localStorage.getItem('cookieConsent')) {
        cookieConsent.style.setProperty('display', 'block', 'important');
        // Small delay to allow display:block to apply before adding show class for animation
        setTimeout(() => {
            cookieConsent.classList.add('show');
        }, 100);
    }
    
    // Accept button handler
    if (acceptBtn) {
        acceptBtn.addEventListener('click', function() {
            localStorage.setItem('cookieConsent', 'accepted');
            // Use Bootstrap's alert close method for consistent behavior
            const alert = bootstrap.Alert.getOrCreateInstance(cookieConsent);
            alert.close();
        });
    }
    
    // Handle dismiss via close button (X)
    cookieConsent.addEventListener('closed.bs.alert', function() {
        // Only set dismissed if not already set to accepted
        if (!localStorage.getItem('cookieConsent')) {
            localStorage.setItem('cookieConsent', 'dismissed');
        }
    });
});
