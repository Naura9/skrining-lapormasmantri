@extends('layouts.main')

@section('title', 'Kategori Skrining')

@section('content')
<section class="px-4 sm:px-4 lg:px-6 py-2 mb-10">
    <h2 class="text-2xl font-bold mb-6 text-center sm:text-left">Kategori Skrining</h2>

    <div class="flex flex-col sm:flex-row sm:items-center justify-end gap-4 mb-3 flex-wrap">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 mb-3 flex-wrap">
            <div class="flex items-center gap-3 w-full lg:w-auto justify-end">
                <button
                    class="flex items-center gap-2 bg-[#61359C] text-white text-sm px-4 py-2 rounded-lg
                       hover:bg-[#61359C]/80 transition w-full sm:w-auto justify-center">
                    <i class="fa-solid fa-plus"></i>
                    Tambah
                </button>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto px-30">
        <table class="min-w-full border border-[#00000033] text-sm text-left text-gray-700">
            <thead class="bg-[#61359C] text-white text-center">
                <tr>
                    <th class="px-3 py-2 border border-[#00000033] w-[5%]">No</th>
                    <th class="px-3 py-2 border border-[#00000033] w-[35%]">Nama Kategori</th>
                    <th class="px-3 py-2 border border-[#00000033] w-[30%]">Skrining</th>
                    <th class="px-3 py-2 border border-[#00000033] w-[30%]">Aksi</th>
                </tr>
            </thead>
            <tbody id="kategoriTableBody"></tbody>
        </table>
    </div>
</section>

<x-modal id="kategoriModalRef" size="md">
    <x-slot name="title">
        <h3 id="kategoriModalTitle" class="text-lg font-bold">Tambah Kategori Skrining</h3>
    </x-slot>

    @include('admin.fitur.skrining.kategori.form')

    <x-slot name="footer">
        <button type="button" id="kategoriCancelBtn"
            class="w-full px-6 py-2 rounded-lg bg-gray-400 text-white font-medium shadow hover:opacity-90 transition">
            Batal
        </button>
        <button type="submit" id="kategoriSaveBtn" form="formEdit"
            class="w-full px-6 py-2 rounded-lg bg-[#61359C] text-white font-medium shadow hover:opacity-90 transition">
            Simpan
        </button>
    </x-slot>
</x-modal>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const tbody = document.getElementById("kategoriTableBody");

        const kategoriModalRef = document.getElementById("kategoriModalRef");
        const kategoriModalTitle = document.getElementById("kategoriModalTitle");
        const formEdit = document.getElementById("formEdit");

        async function fetchKategori() {
            try {
                const response = await fetch(`{{ url('api/kategori') }}`, {
                    headers: {
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    }
                });

                const result = await response.json();
                if (!result || !result.data) return;

                const kategori = result.data.list || [];

                renderTable(kategori);
            } catch (error) {
                console.error("Gagal memuat data kategori skrining:", error);
                tbody.innerHTML = `<tr><td colspan="5" class="text-center text-red-500 py-4">Gagal memuat data</td></tr>`;
            }
        }

        function renderTable(list) {
            tbody.innerHTML = "";

            if (!list.length) {
                tbody.innerHTML = `<tr><td colspan="5" class="text-center text-gray-500 py-4">Tidak ada data kategori skrining.</td></tr>`;
                return;
            }

            list.reverse();

            list.forEach((item, index) => {
                const tr = document.createElement("tr");
                tr.className = "hover:bg-gray-50";

                tr.innerHTML = `
                    <td class="border border-[#00000033] text-center px-3 py-3">${index + 1}</td>
                    <td class="border border-[#00000033] px-3 py-3">${item.nama_kategori}</td>
                    <td class="border border-[#00000033] px-3 py-3 text-center">
                        ${item.target_skrining?.toUpperCase() ?? '-'}
                    </td>
                    
                    <td class="border border-[#00000033] text-center px-3 py-3">
                        <div class="flex justify-center gap-2">
                            <button
                                onclick="openKategoriModal('edit', '${item.id}')"
                                class="px-3 py-1 text-xs rounded bg-yellow-500 text-white hover:bg-yellow-600 transition">
                                Edit
                            </button>

                            <button
                                class="px-3 py-1 text-xs rounded bg-red-600 text-white hover:bg-red-700 transition delete-btn"
                                data-id="${item.id}">
                                Hapus
                            </button>
                        </div>
                    </td>
                `;
                tbody.appendChild(tr);
            });

            document.querySelectorAll(".delete-btn").forEach(btn => {
                btn.addEventListener("click", async () => {
                    const id = btn.dataset.id;

                    showDeleteConfirmToast("Apakah Anda yakin ingin menghapus data ini?", async () => {
                        try {
                            const data = await fetch(`{{ url('api/kategori') }}/${id}`, {
                                method: "DELETE",
                                headers: {
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                }
                            });
                            if (!data) return;

                            showSuccessToast("Data berhasil dihapus!");
                            await fetchKategori();
                        } catch (error) {
                            console.error("Gagal menghapus data:", error);
                            showErrorToast("Terjadi kesalahan pada server!");
                        }
                    });
                });
            });
        }

        formEdit.addEventListener("submit", async (e) => {
            e.preventDefault();

            ['nama_kategori', 'target_skrining'].forEach(name => {
                const el = document.getElementById("error-" + name);
                if (el) {
                    el.textContent = "";
                    el.classList.add("hidden");
                }
            });

            const mode = formEdit.getAttribute("data-mode");
            const id = formEdit.getAttribute("data-id");
            const formData = new FormData(formEdit);

            if (mode === "edit" && id) {
                formData.append("id", id);
            }

            try {
                let url = "{{ url('api/kategori') }}";
                let method = "POST";

                if (mode === "edit" && id) {
                    formData.append("_method", "PUT");
                }

                const res = await fetch(url, {
                    method: method,
                    body: formData
                });
                const data = await res.json();

                if (!data.errors) {
                    showSuccessToast("Data berhasil disimpan!");
                    kategoriModalRef.classList.add("hidden");
                    kategoriModalRef.classList.remove("flex");
                    fetchKategori();
                } else {
                    if (data.errors) {
                        Object.keys(data.errors).forEach(key => {
                            const el = document.getElementById("error-" + key);
                            if (el) {
                                el.textContent = data.errors[key][0];
                                el.classList.remove("hidden");
                            }
                        });
                    } else {
                        showErrorToast("Gagal menyimpan data!");
                    }
                }
            } catch (err) {
                console.error("Error:", err);
                alert("Terjadi kesalahan pada server!");
            }
        });

        const openKategoriModal = async (mode, id = null) => {
            kategoriModalRef.classList.remove("hidden");
            kategoriModalRef.classList.add("flex");

            if (mode === "edit" && id) {
                kategoriModalTitle.textContent = "Edit Kategori Skrining";
                try {
                    const data = await fetch(`{{ url('api/kategori') }}/${id}`);
                    const json = await data.json();

                    const item = json.data;
                    setFormData(item);

                    formEdit.setAttribute('data-mode', 'edit');
                    formEdit.setAttribute('data-id', id);
                } catch (err) {
                    console.error("Gagal mengambil data kategori:", err);
                }
            } else {
                kategoriModalTitle.textContent = "Tambah Kategori Skrining";
                setFormData(null);
                formEdit.removeAttribute('data-id');
                formEdit.setAttribute('data-mode', 'add');
            }
        };

        window.openKategoriModal = openKategoriModal;

        document.querySelectorAll('button').forEach(btn => {
            if (btn.textContent.includes("Tambah")) {
                btn.addEventListener("click", () => openKategoriModal("add"));
            }
        });

        document.getElementById("kategoriCancelBtn").addEventListener("click", () => {
            kategoriModalRef.classList.add("hidden");
            kategoriModalRef.classList.remove("flex");
        });

        fetchKategori();
    });
</script>
@endsection