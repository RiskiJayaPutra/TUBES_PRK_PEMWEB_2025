document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const passwordInput = document.getElementById('password');
    const togglePassword = document.getElementById('togglePassword');

    // Toggle Password Visibility
    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        // Toggle icon class
        this.classList.toggle('fa-eye');
        this.classList.toggle('fa-eye-slash');
    });

    // Form Validation
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        let isValid = true;

        // Reset previous errors
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.style.display = 'none');

        // Validate Username
        const username = document.getElementById('username');
        if (!username.value.trim()) {
            showError(username, 'Username tidak boleh kosong');
            isValid = false;
        }

        // Validate Password
        if (!passwordInput.value.trim()) {
            showError(passwordInput, 'Password tidak boleh kosong');
            isValid = false;
        }

        if (isValid) {
            // Simulate loading state
            const btn = this.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Memproses...';

            // Simulate server request (Replace with actual AJAX/Form submission later)
            setTimeout(() => {
                // For now, just show success and reload/redirect
                // In real app, remove this timeout and allow form submission or use fetch()
                
                // Demo success
                // Swal.fire({
                //     icon: 'success',
                //     title: 'Login Berhasil',
                //     text: 'Mengalihkan ke dashboard...',
                //     timer: 1500,
                //     showConfirmButton: false
                // }).then(() => {
                //     // window.location.href = 'dashboard.php';
                // });
                
                // Since backend logic isn't ready, we just submit the form naturally if we were using PHP action
                // But for this demo, we'll just log it.
                console.log('Form submitted');
                
                // Re-enable for demo purposes
                btn.disabled = false;
                btn.innerHTML = originalText;
                
                // Actual submission would happen here
                 loginForm.submit(); 
            }, 1000);
        }
    });

    function showError(input, message) {
        input.classList.add('is-invalid');
        const feedback = input.parentElement.parentElement.querySelector('.invalid-feedback');
        if (feedback) {
            feedback.textContent = message;
            feedback.style.display = 'block';
        }
    }

    // Input Focus Effects (Optional enhancement)
    const inputs = document.querySelectorAll('.form-control');
    inputs.forEach(input => {
        input.addEventListener('focus', () => {
            input.parentElement.querySelector('.input-icon').style.color = '#3b82f6';
        });
        input.addEventListener('blur', () => {
            if (!input.value) {
                input.parentElement.querySelector('.input-icon').style.color = '#94a3b8';
            }
        });
    });
});
