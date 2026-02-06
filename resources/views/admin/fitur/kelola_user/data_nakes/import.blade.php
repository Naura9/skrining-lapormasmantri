<form action="{{ route('data-nakes.import_nakes') }}" method="POST" enctype="multipart/form-data" id="form-import">
    @csrf

    <div class="mb-3">
        <label class="block text-sm font-medium mb-1">
            Download Template
        </label>
        <a href="{{ asset('template_data_nakes.xlsx') }}"
            download
            class="inline-flex items-center gap-2 px-3 py-2 text-sm
                  bg-green-600 text-white rounded-lg
                  hover:bg-green-700 transition">
            <i class="fa-solid fa-file-excel"></i>
            Download
        </a>
    </div>

    <div>
        <label for="file_nakes" class="block text-sm font-medium mb-1">
            Pilih File
        </label>
        <input type="file"
            name="file_nakes"
            id="file_nakes"
            class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm
                      focus:outline-none focus:ring-2 focus:ring-[#61359C]/50">
        <p id="error-file_nakes" class="text-sm text-red-500 mt-1"></p>
    </div>

    <div class="flex justify-end gap-3 pt-4">
        <button type="button"
            id="importCancelBtn"
            class="px-4 py-2 rounded-lg bg-gray-400 text-white
                       hover:bg-gray-500 transition">
            Batal
        </button>

        <button type="submit"
            class="px-4 py-2 rounded-lg bg-[#61359C] text-white
                       hover:bg-[#61359C]/80 transition">
            Upload
        </button>
    </div>
</form>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const form = document.getElementById("form-import");

        form.addEventListener("submit", async (e) => {
            e.preventDefault();

            const fileInput = document.getElementById("file_nakes");
            const errorFile = document.getElementById("error-file_nakes");

            errorFile.textContent = "";

            if (!fileInput.files.length) {
                errorFile.textContent = "File wajib diisi";
                return;
            }

            const file = fileInput.files[0];
            if (!file.name.endsWith(".xlsx")) {
                errorFile.textContent = "File harus berformat .xlsx";
                return;
            }

            const formData = new FormData(form);

            try {
                const res = await fetch(form.action, {
                    method: "POST",
                    body: formData,
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    }
                });

                const data = await res.json();

                if (data.status) {
                    showSuccessToast(data.message || "Import berhasil");

                    document.getElementById("importNakesModal")
                        .classList.add("hidden");

                    fetchNakes();
                } else {
                    document.getElementById("importNakesModal")
                        .classList.add("hidden");

                    let errorText = "";

                    if (data.errors && Array.isArray(data.errors)) {
                        errorText = `
                            <ul class="text-left list-disc pl-5 space-y-1">
                                ${data.errors.map(err => `<li>${err}</li>`).join("")}
                            </ul>
                        `;
                    }

                    showErrorToast(
                        data.message || "Import gagal",
                        errorText
                    );
                }

            } catch (err) {
                console.error(err);
                showErrorToast("Terjadi kesalahan server");
            }
        });
    });
</script>