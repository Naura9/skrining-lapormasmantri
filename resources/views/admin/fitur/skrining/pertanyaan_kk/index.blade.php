@extends('layouts.main')

@section('title', 'Pertanyaan Skrining KK')

@section('content')
<section class="px-4 sm:px-4 lg:px-6 py-2 mb-10">
    <h2 class="text-2xl font-bold mb-6 text-center sm:text-left">Pertanyaan Skrining KK</h2>

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-3">
        <div class="flex flex-col sm:flex-row items-stretch gap-4 mb-3 w-full sm:w-auto">
            <button
                id="btnTambahPertanyaanKk"
                class="flex items-center gap-2 bg-[#61359C] text-white text-sm px-4 py-2 rounded-lg
                   hover:bg-[#61359C]/80 transition w-full sm:w-auto justify-center">
                <i class="fa-solid fa-plus"></i>
                Tambah
            </button>
        </div>

        <div class="flex justify-end w-full sm:w-auto">
            <button
                id=""
                class="flex items-center gap-2 bg-yellow-500 text-white text-sm px-4 py-2 rounded-lg
                   hover:bg-yellow-600 transition w-full sm:w-auto justify-center">
                Edit
            </button>
        </div>

    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full border border-[#00000033] text-sm text-left text-gray-700">
            <thead class="bg-[#61359C] text-white text-center">
                <tr>
                    <th class="px-3 py-2 border border-[#00000033] w-[5%]">No</th>
                    <th class="px-3 py-2 border border-[#00000033] w-[95%]">Pertanyaan</th>
                </tr>
            </thead>
            <tbody id="pertanyaanKkTableBody"></tbody>
        </table>
    </div>
</section>

<x-modal id="pertanyaanKkModalRef" size="md">
    <x-slot name="title">
        <h3 id="pertanyaanKkModalTitle" class="text-lg font-bold">Tambah Pertanyaan Skrining KK</h3>
    </x-slot>

    @include('admin.fitur.skrining.pertanyaan_kk.form')

    <x-slot name="footer">
        <button type="button" id="pertanyaanKkCancelBtn"
            class="w-full px-6 py-2 rounded-lg bg-gray-400 text-white font-medium shadow hover:opacity-90 transition">
            Batal
        </button>
        <button type="submit" id="pertanyaanKkSaveBtn" form="formEdit"
            class="w-full px-6 py-2 rounded-lg bg-[#0B6CF4] text-white font-medium shadow hover:opacity-90 transition">
            Simpan
        </button>
    </x-slot>
</x-modal>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const tbody = document.getElementById("pertanyaanKkTableBody");

        const pertanyaanKkModalRef = document.getElementById("pertanyaanKkModalRef");
        const pertanyaanKkModalTitle = document.getElementById("pertanyaanKkModalTitle");
        const formEdit = document.getElementById("formEdit");

        async function fetchPertanyaanKk() {
            try {
                const response = await fetch(`{{ url('api/pertanyaan') }}`, {
                    headers: {
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    }
                });

                const result = await response.json();
                if (!result || !result.data) return;

                const list = result.data.list || [];

                renderTable(list);

            } catch (error) {
                console.error("Gagal memuat data pertanyaan:", error);
                tbody.innerHTML = `
            <tr>
                <td colspan="2" class="text-center text-red-500 py-4">
                    Gagal memuat data
                </td>
            </tr>
        `;
            }
        }

        function renderTable(list) {
            tbody.innerHTML = "";

            const filtered = list.filter(item =>
                item.target_skrining?.toLowerCase() === 'kk'
            );

            if (!filtered.length) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="2" class="text-center text-gray-500 py-4">
                            Tidak ada pertanyaan skrining KK.
                        </td>
                    </tr>
                `;
                return;
            }

            let currentSection = null;
            let no = 0;

            filtered.forEach((item) => {

                if (currentSection !== item.judul_section) {
                    currentSection = item.judul_section;

                    no = 1;

                    const sectionRow = document.createElement("tr");
                    sectionRow.innerHTML = `
                        <td colspan="2"
                            class="border border-[#00000033] px-3 py-2 bg-gray-100 font-bold">
                            ${currentSection}
                        </td>
                    `;
                    tbody.appendChild(sectionRow);
                }

                const tr1 = document.createElement("tr");
                tr1.innerHTML = `
                    <td rowspan="2"
                        class="border border-[#00000033] text-center align-middle px-3 py-3 font-semibold">
                        ${no++}
                    </td>
                    <td class="border border-[#00000033] px-3 py-3 font-semibold">
                        ${item.pertanyaan}
                    </td>
                `;
                tbody.appendChild(tr1);

                let opsiHtml = '';

                switch (item.jenis_pertanyaan) {

                    case 'radio':
                        opsiHtml = item.opsi_jawaban?.length ?
                            item.opsi_jawaban.map(opt =>
                                `<div class="flex items-center gap-2">
                                    <i class="fa-regular fa-circle-dot text-gray-500"></i>
                                    <span>${opt}</span>
                                </div>`
                            ).join('') :
                            '-';
                        break;

                    case 'checkbox':
                        opsiHtml = item.opsi_jawaban?.length ?
                            item.opsi_jawaban.map(opt =>
                                `<div class="flex items-center gap-2">
                                    <i class="fa-regular fa-square-check text-gray-500"></i>
                                    <span>${opt}</span>
                                </div>`
                            ).join('') :
                            '-';
                        break;

                    case 'select':
                        opsiHtml = `
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-caret-down text-gray-500"></i>
                                <span>Dropdown:</span>
                            </div>
                            <div class="ml-6">
                                ${item.opsi_jawaban?.join(', ') ?? '-'}
                            </div>
                        `;
                        break;

                    case 'text':
                        opsiHtml = `
                            <div class="flex items-center gap-2 text-gray-500">
                                <i class="fa-solid fa-i-cursor"></i>
                                Jawaban pendek
                            </div>
                        `;
                        break;

                    case 'textarea':
                        opsiHtml = `
                            <div class="flex items-center gap-2 text-gray-500">
                                <i class="fa-solid fa-align-left"></i>
                                Paragraf
                            </div>
                        `;
                        break;

                    case 'date':
                        opsiHtml = `
                            <div class="flex items-center gap-2 text-gray-500">
                                <i class="fa-solid fa-calendar"></i>
                                Date
                            </div>
                        `;
                        break;

                    default:
                        opsiHtml = '-';
                }


                const tr2 = document.createElement("tr");
                tr2.innerHTML = `
                    <td class="border border-[#00000033] px-3 py-2 text-sm text-gray-600">
                        ${opsiHtml}
                    </td>
                `;

                tbody.appendChild(tr2);
            });


            document.querySelectorAll(".delete-btn").forEach(btn => {
                btn.addEventListener("click", async () => {
                    const id = btn.dataset.id;

                    showDeleteConfirmToast("Apakah Anda yakin ingin menghapus data ini?", async () => {
                        try {
                            const data = await fetch(`{{ url('api/pertanyaanKk') }}/${id}`, {
                                method: "DELETE",
                                headers: {
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                }
                            });
                            if (!data) return;

                            showSuccessToast("Data berhasil dihapus!");
                            await fetchPertanyaanKk();
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

            ['nama_pertanyaanKk', 'target_skrining'].forEach(name => {
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
                let url = "{{ url('api/pertanyaanKk') }}";
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
                    pertanyaanKkModalRef.classList.add("hidden");
                    pertanyaanKkModalRef.classList.remove("flex");
                    fetchPertanyaanKk();
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

        const openPertanyafetchPertanyaanKkModal = async (mode, id = null) => {
            pertanyaanKkModalRef.classList.remove("hidden");
            pertanyaanKkModalRef.classList.add("flex");

            if (mode === "edit" && id) {
                pertanyaanKkModalTitle.textContent = "Edit Pertanyaan Skrining KK";
                try {
                    const data = await fetch(`{{ url('api/pertanyaanKk') }}/${id}`);
                    const json = await data.json();

                    const item = json.data;
                    setFormData(item);

                    formEdit.setAttribute('data-mode', 'edit');
                    formEdit.setAttribute('data-id', id);
                } catch (err) {
                    console.error("Gagal mengambil data pertanyaanKk:", err);
                }
            } else {
                pertanyaanKkModalTitle.textContent = "Tambah Pertanyaan Skrining KK";
                setFormData(null);
                formEdit.removeAttribute('data-id');
                formEdit.setAttribute('data-mode', 'add');
            }
        };

        window.openPertanyafetchPertanyaanKkModal = openPertanyafetchPertanyaanKkModal;

        document.querySelectorAll('button').forEach(btn => {
            if (btn.textContent.includes("Tambah")) {
                btn.addEventListener("click", () => openPertanyafetchPertanyaanKkModal("add"));
            }
        });

        document.getElementById("pertanyaanKkCancelBtn").addEventListener("click", () => {
            pertanyaanKkModalRef.classList.add("hidden");
            pertanyaanKkModalRef.classList.remove("flex");
        });

        fetchPertanyaanKk();
    });
</script>
@endsection