<script>
    // 1. Fungsi untuk mengatur Tab (Ortu Lama vs Ortu Baru)
    function setOrtuStatus(status) {
        document.getElementById('ortu_status').value = status;

        let isLama = (status === 'lama');

        // Atur required dinamis untuk form Ortu Lama
        document.getElementById('name_lama').required = isLama;
        document.getElementById('parent_id').required = isLama;

        // Atur required dinamis untuk form Ortu Baru
        document.getElementById('name_baru').required = !isLama;
        document.getElementById('father_name').required = !isLama;
        document.getElementById('mother_name').required = !isLama;
        document.getElementById('parent_phone').required = !isLama;
        document.getElementById('parent_email').required = !isLama;
        document.getElementById('parent_password').required = !isLama;
    }

    // 2. Fungsi untuk Validasi Ukuran Foto & Preview (UX Level Pro)
    document.addEventListener("DOMContentLoaded", function () {

        function setupImageInput(inputId, previewContainerId, previewImageId, errorMsgId) {
            const inputElement = document.getElementById(inputId);
            const previewContainer = document.getElementById(previewContainerId);
            const previewImage = document.getElementById(previewImageId);
            const errorElement = document.getElementById(errorMsgId);

            if (!inputElement) return;

            inputElement.addEventListener('change', function () {
                const file = this.files[0];
                const maxSize = 2 * 1024 * 1024; // Maksimal 2MB

                // Reset tampilan setiap kali user ganti foto
                if (previewContainer) previewContainer.classList.add('d-none');
                if (previewImage) previewImage.src = '';
                if (errorElement) errorElement.classList.add('d-none');

                if (file) {
                    // SATPAM: Tolak kalau foto lebih dari 2MB
                    if (file.size > maxSize) {
                        if (errorElement) errorElement.classList.remove('d-none'); // Munculkan teks merah
                        this.value = ''; // Kosongkan inputan
                        return; // Berhenti di sini
                    }

                    // FOTOGRAFER: Kalau ukuran aman, munculkan preview gambarnya
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        if (previewImage) previewImage.src = e.target.result;
                        if (previewContainer) previewContainer.classList.remove('d-none');
                    }
                    reader.readAsDataURL(file);
                }
            });
        }

        // Pasang fungsi ke Form Tambah (Tab Ortu Lama & Baru)
        setupImageInput('photo_lama', 'preview_container_lama', 'preview_lama', 'error_photo_lama');
        setupImageInput('photo_baru', 'preview_container_baru', 'preview_baru', 'error_photo_baru');

        // Pasang fungsi ke SEMUA Form Edit yang ada di tabel
        @foreach($students as $student)
            setupImageInput(
                'photo_edit_{{ $student->id }}',
                'preview_container_edit_{{ $student->id }}',
                'preview_edit_{{ $student->id }}',
                'error_photo_edit_{{ $student->id }}'
            );
        @endforeach
    });
</script>

{{-- JIKA ADA DATA KREDENSIAL ORANG TUA SETELAH UPDATE PASSWORD --}}
@if(session('parent_credentials'))
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            Swal.fire({
                title: 'Berhasil Update Password!',
                html: `
                        <div style="text-align: left; background: #f8f9fa; padding: 15px; border-radius: 10px; border: 1px dashed #d9dee3;">
                            <p class="mb-2 text-dark">Silakan screenshot data ini untuk dikirimkan ke Orang Tua / Wali Kelas:</p>
                            <hr class="my-2">
                            <p class="mb-1"><strong>Email:</strong> <span class="text-primary">{{ session('parent_credentials')['email'] }}</span></p>
                            <p class="mb-0"><strong>Password Baru:</strong> <span class="text-success">{{ session('parent_credentials')['password'] }}</span></p>
                        </div>
                    `,
                icon: 'info',
                confirmButtonText: 'Tutup & Paham',
                confirmButtonColor: '#5b9cf6',
                allowOutsideClick: false // Mencegah popup tertutup tidak sengaja sebelum di-screenshot
            });
        });
    </script>
@endif