<script>
    /**
     * Pratinjau gambar sebelum form dikirim.
     * Dipakai oleh form produk (foto) dan form toko (logo).
     */
    function pratinjauGambar(input, boxId) {
        const box = document.getElementById(boxId);
        const info = document.getElementById(boxId + '-info');

        if (!box || !input.files || !input.files[0]) {
            return;
        }

        const file = input.files[0];

        if (!file.type.startsWith('image/')) {
            if (info) {
                info.textContent = 'Berkas yang dipilih bukan gambar.';
                info.className = 'mt-2 text-[11px] text-red-600';
            }
            return;
        }

        const url = URL.createObjectURL(file);

        box.innerHTML = '';
        const img = document.createElement('img');
        img.src = url;
        img.alt = 'Pratinjau gambar';
        img.className = 'w-full h-full object-cover';
        img.onload = function () {
            URL.revokeObjectURL(url);
        };
        box.appendChild(img);

        if (info) {
            const kb = Math.max(1, Math.round(file.size / 1024));
            info.textContent = file.name + ' · ' + kb + ' KB';
            info.className = 'mt-2 text-[11px] ' + (file.size > 2048 * 1024 ? 'text-red-600' : 'text-gray-500');
        }
    }
</script>
