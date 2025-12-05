document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    const passwordInput = document.getElementById('password');
    const togglePassword = document.getElementById('togglePassword');

    // Lihat password
    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        // Ganti icon mata
        this.classList.toggle('fa-eye');
        this.classList.toggle('fa-eye-slash');
    });

    // Validasi form
    loginForm.addEventListener('submit', function(e) {
        e.preventDefault();
        let isValid = true;

        // Hapus error lama
        resetErrors();

        // Cek username
        const username = document.getElementById('username');
        if (!username.value.trim()) {
            showError(username, 'Username wajib diisi');
            isValid = false;
        }

        // Cek password
        if (!passwordInput.value.trim()) {
            showError(passwordInput, 'Password wajib diisi');
            isValid = false;
        }

        if (isValid) {
            // Animasi loading
            const btn = this.querySelector('button[type="submit"]');
            const originalContent = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-circle-notch fa-spin"></i> Memproses...';
            btn.classList.add('opacity-75', 'cursor-not-allowed');

            // Kirim data
            setTimeout(() => {
                // Contoh submit
                console.log('Form submitted');
                
                // Reset tombol
                btn.disabled = false;
                btn.innerHTML = originalContent;
                btn.classList.remove('opacity-75', 'cursor-not-allowed');
                
                // Submit
                 loginForm.submit(); 
            }, 1000);
        }
    });

    function showError(input, message) {
        // Tambah border merah
        input.classList.add('border-red-500', 'focus:ring-red-200', 'focus:border-red-500');
        input.classList.remove('border-slate-200', 'focus:ring-primary/20', 'focus:border-primary');
        
        // Tampilkan pesan error
        const errorMsg = input.parentElement.querySelector('.error-message');
        if (errorMsg) {
            errorMsg.textContent = message;
            errorMsg.classList.remove('hidden');
        }
    }

    function resetErrors() {
        const inputs = document.querySelectorAll('input');
        inputs.forEach(input => {
            // Reset style border
            input.classList.remove('border-red-500', 'focus:ring-red-200', 'focus:border-red-500');
            input.classList.add('border-slate-200', 'focus:ring-primary/20', 'focus:border-primary');
            
            // Sembunyikan pesan error
            const errorMsg = input.parentElement.querySelector('.error-message');
            if (errorMsg) {
                errorMsg.classList.add('hidden');
            }
        });
    }
});
