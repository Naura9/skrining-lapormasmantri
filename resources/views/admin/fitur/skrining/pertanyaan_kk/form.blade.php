<form id="formEdit" class="space-y-4">
    <div class="text-left w-full">
        <label for="nama_kategori" class="block text-sm font-semibold mb-1">
            Nama Kategori
        </label>
        <input type="text" id="nama_kategori" name="nama_kategori"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                   focus:outline-none focus:ring-2 focus:ring-[#61359C]/50"
            placeholder="Masukkan nama kategori">
        <p class="text-red-500 text-xs mt-1 hidden" id="error-nama_kategori"></p>
    </div>

    <div class="text-left">
        <label for="target_skrining" class="block text-sm font-semibold mb-1">
            Target Skrining
        </label>
        <x-dropdown
            id="targetSkriningDropdown"
            label="Pilih Target Skrining"
            :options="['KK', 'NIK']"
            width="w-full"
            data-dropdown="filter" />
        <p class="text-red-500 text-xs mt-1 hidden" id="error-target_skrining"></p>
        <input type="hidden" name="target_skrining" id="target_skrining">
    </div>
</form>

<script>
    const targetSkriningDropdown = document.getElementById('targetSkriningDropdown');

    const tSDropdown = document.getElementById('targetSkriningDropdown');
    const tSInput = document.getElementById('target_skrining');

    if (tSDropdown) {
        tSDropdown.addEventListener('dropdown-changed', (e) => {
            const label = e.detail.value;

            if (label === 'KK') {
                tSInput.value = 'kk';
            } else if (label === 'NIK') {
                tSInput.value = 'nik';
            }
        });
    }

    const formModel = {
        id: "",
        nama_kategori: "",
        target_skrining: "",
    };

    window.setFormData = (item) => {
        if (item) {
            formEdit.querySelector('[name="nama_kategori"]').value = item.nama_kategori ?? '';
            document.getElementById('target_skrining').value = item.target_skrining ?? '';

            const targetSkriningDropdown = document.querySelector('[data-dropdown="filter"]');
            if (targetSkriningDropdown) {
                const label = targetSkriningDropdown.querySelector('.dropdown-selected');
                if (label) {
                    label.textContent =
                        item.target_skrining === 'kk' ?
                        'KK' :
                        item.target_skrining === 'nik' ?
                        'NIK' :
                        'Pilih Target Skrining';
                }
            }
        } else {
            formModel.id = "";
            formModel.nama_kelurahan = "";
            formModel.target_skrining = "";

            formEdit.reset();

            const targetSkriningDropdown = document.querySelector('[data-dropdown="filter"]');
            if (targetSkriningDropdown) {
                const label = targetSkriningDropdown.querySelector('.dropdown-selected');
                if (label) label.textContent = 'Pilih Target Skrining';
            }
        }
    };
</script>