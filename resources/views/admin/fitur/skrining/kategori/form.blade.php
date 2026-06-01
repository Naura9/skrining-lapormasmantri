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
    <input type="hidden" id="target_skrining" name="target_skrining">
</form>

<script>
    const targetSkriningDropdown = document.getElementById('targetSkriningDropdown');

    const tSInput = document.getElementById('target_skrining');

    const formModel = {
        id: "",
        nama_kategori: "",
        target_skrining: "",
    };

    window.setFormData = (item) => {
        if (item) {
            formEdit.querySelector('[name="nama_kategori"]').value = item.nama_kategori ?? '';
            document.getElementById('target_skrining').value = 'nik';
        } else {
            formModel.id = "";
            formModel.nama_kategori = "";
            formModel.target_skrining = "";
            
            formEdit.reset();
            document.getElementById('target_skrining').value = 'nik';
        }
    };
</script>