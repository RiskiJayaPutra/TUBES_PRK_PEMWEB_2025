document.addEventListener('DOMContentLoaded', function() {

    const toggles = document.querySelectorAll(".toggle-password");

    toggles.forEach(toggle => {
        toggle.addEventListener("click", function () {
            const target = this.getAttribute("data-target");
            const input = document.getElementById(target);

            if (!input) return;

            if (input.type === "password") {
                input.type = "text";
                this.classList.remove("fa-eye");
                this.classList.add("fa-eye-slash");
            } else {
                input.type = "password";
                this.classList.remove("fa-eye-slash");
                this.classList.add("fa-eye");
            }
        });
    });

    // VALIDASI FORM INFORMASI AKUN
    const form = document.getElementById('formEditProfile');
    const namaInput = document.getElementById('nama');
    const usernameInput = document.getElementById('username');

    form.addEventListener('submit', function(e) {
        if (!namaInput.value.trim() || !usernameInput.value.trim()) {
            alert("Nama Lengkap dan Username tidak boleh kosong!");
            e.preventDefault();
        }
    });

});
