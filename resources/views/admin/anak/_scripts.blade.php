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