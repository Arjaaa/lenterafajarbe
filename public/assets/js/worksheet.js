function previewMedia(url, type) {
    const container = document.getElementById("mediaContainer");

    // Tampilkan efek loading spinner Boxicons saat memuat media
    container.innerHTML = `
        <div class="p-5 text-muted">
            <i class="bx bx-loader-alt bx-spin bx-md mb-2"></i>
            <p class="mb-0">Memuat media dari Cloudinary...</p>
        </div>
    `;

    // Cek apakah file berupa gambar atau dokumen/PDF
    if (type === "image" || url.match(/\.(jpeg|jpg|gif|png|webp)$/i) != null) {
        // Jika gambar, render tag <img> dengan batasan tinggi layar agar pas di pop-up
        container.innerHTML = `<img src="${url}" class="img-fluid" style="max-height: 75vh; width: 100%; object-fit: contain; padding: 10px;" alt="Lampiran Worksheet">`;
    } else {
        // Jika dokumen/PDF, render tag <iframe> agar bisa dibaca langsung di pop-up
        container.innerHTML = `<iframe src="${url}" width="100%" height="600px" style="border: none;"></iframe>`;
    }
}
