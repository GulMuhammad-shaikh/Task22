document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const submitBtn = loginForm.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        // Show loading state
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing In...';
        submitBtn.disabled = true;
        
        // Submit form
        const formData = new FormData(loginForm);
        
        fetch('php/authenticate.php', {
            method: 'GET',
            body: formData
        })
        .then(response => response.text())
        .then(data => {
            if (data === 'success') {
                window.location.href = 'dashboard.php';
            } else {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
                alert('Invalid username or password');
            }
        })
        .catch(error => {
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
            alert('An error occurred. Please try again.');
            console.error('Error:', error);
        });
    });
});