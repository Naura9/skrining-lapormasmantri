<form id="formEditSection" class="space-y-4">
    <input type="hidden" name="kategori_id" id="kategori_id">
    <input type="hidden" name="no_urut" id="no_urut">

    <div class="text-left w-full">
        <label for="judul_section" class="block text-sm font-semibold mb-1">
            Judul Section
        </label>
        <input type="text" id="judul_section" name="judul_section"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                   focus:outline-none focus:ring-2 focus:ring-[#61359C]/50"
            placeholder="Masukkan nama kategori">
        <p class="text-red-500 text-xs mt-1 hidden" id="error-judul_section"></p>
    </div>
</form>

<script>
    const formModelSection = {
        id: "",
        kategori_id: "",
        judul_section: "",
        no_urut: "",
    };

    window.setFormSectionData = (item) => {
        const form = document.getElementById("formEditSection");

        if (item) {
            form.querySelector('[name="judul_section"]').value = item.judul_section ?? '';
            form.querySelector('[name="kategori_id"]').value = item.kategori_id ?? '';
            form.querySelector('[name="no_urut"]').value = item.no_urut ?? '';
        }
    };
</script>