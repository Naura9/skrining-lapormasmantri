@extends('layouts.main')

@section('title', 'Data Kader')

@section('content')
<section class="px-4 sm:px-4 lg:px-6 py-2 mb-10">
    <h2 class="text-2xl font-bold mb-6 text-center sm:text-left">Data Kader</h2>

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-5 flex-wrap">
        <div class="flex flex-col sm:flex-row items-center gap-3 w-full sm:w-auto">
            <input id="searchInput" type="text"
                placeholder="Cari berdasarkan nama..."
                class="h-9 bg-white border border-[#00000033] rounded-lg px-3 text-sm
                   focus:outline-none focus:ring-2 focus:ring-[#61359C]/50 w-full sm:w-70">

            <x-dropdown
                label="Pilih status"
                :options="['Aktif', 'Nonaktif']"
                width="w-full sm:w-40 h-9"
                data-dropdown="filter"
                :selected="'Aktif'" />

            <button id="searchBtn"
                class="h-9 flex items-center justify-center bg-[#61359C] text-white
                   border border-[#00000033] px-3 rounded-lg text-sm
                   hover:bg-[#61359C]/80 transition w-full sm:w-auto">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </div>

        <div class="flex items-center gap-3 w-full lg:w-auto justify-end">
            <button id="btnImport"
                class="h-9 flex items-center gap-2 bg-[#61359C] text-white
                    text-sm px-4 rounded-lg hover:bg-[#61359C]/80
                    transition w-full sm:w-auto justify-center">
                <i class="fa-solid fa-file-excel"></i>
                Import Excel
            </button>
            <button
                class="h-9 flex items-center gap-2 bg-[#61359C] text-white
                   text-sm px-4 rounded-lg hover:bg-[#61359C]/80
                   transition w-full sm:w-auto justify-center">
                <i class="fa-solid fa-plus"></i>
                Tambah
            </button>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full border border-[#00000033] text-sm text-left text-gray-700 whitespace-nowrap">
            <thead class="bg-[#61359C] text-white text-center">
                <tr>
                    <th class="px-3 py-2 border border-[#00000033]">No</th>
                    <th class="px-3 py-2 border border-[#00000033]">Nama</th>
                    <th class="px-3 py-2 border border-[#00000033]">Kelurahan</th>
                    <th class="px-3 py-2 border border-[#00000033]">Posyandu</th>
                    <th class="px-3 py-2 border border-[#00000033]">Aksi</th>
                </tr>
            </thead>
            <tbody id="kaderTableBody"></tbody>
        </table>
    </div>

    <div id="modal" class="fixed inset-0 bg-slate-950/30 hidden items-center justify-center z-50 px-4">
        <div class="bg-white rounded-xl shadow-lg 
                w-full sm:w-11/12 md:w-10/12 lg:w-5/12 xl:w-4/12 
                max-w-3xl flex flex-col relative">

            <div class="w-full py-3 px-4">
                <h3 class="text-lg font-bold text-left">Detail Kader</h3>
            </div>

            <div class="px-4 py-2 w-full space-y-1 text-sm">
                <div class="grid grid-cols-[120px_10px_1fr] items-start">
                    <p class="font-medium">Nama Lengkap</p>
                    <p class="font-medium text-left">:</p>
                    <p id="modal-nama" class="text-left"></p>
                </div>

                <div class="grid grid-cols-[120px_10px_1fr] items-start">
                    <p class="font-medium">Username</p>
                    <p class="font-medium text-left">:</p>
                    <p id="modal-username" class="text-left"></p>
                </div>

                <div class="grid grid-cols-[120px_10px_1fr] items-start">
                    <p class="font-medium">Kelurahan</p>
                    <p class="font-medium text-left">:</p>
                    <p id="modal-kelurahan" class="text-left"></p>
                </div>

                <div class="grid grid-cols-[120px_10px_1fr] items-start">
                    <p class="font-medium">Posyandu</p>
                    <p class="font-medium text-left">:</p>
                    <p id="modal-posyandu" class="text-left"></p>
                </div>

                <div class="grid grid-cols-[120px_10px_1fr] items-start">
                    <p class="font-medium">No Telepon</p>
                    <p class="font-medium text-left">:</p>
                    <p id="modal-telepon" class="text-left"></p>
                </div>

                <div class="grid grid-cols-[120px_10px_1fr] items-start">
                    <p class="font-medium">Jenis Kelamin</p>
                    <p class="font-medium text-left">:</p>
                    <p id="modal-jenisKelamin" class="text-left"></p>
                </div>

                <div class="grid grid-cols-[120px_10px_1fr] items-start mt-3">
                    <p class="font-medium">Status</p>
                    <p class="font-medium text-left">:</p>
                    <div class="flex justify-start">
                        <button
                            id="modal-status"
                            class="flex items-center gap-2 text-white text-sm px-2 py-1 rounded-xl transition">
                            -
                        </button>
                    </div>
                </div>

                <div class="flex justify-center mt-4 sm:mt-6">
                    <button
                        id="closeModalBtn"
                        class="w-full bg-[#61359C] text-white text-sm font-semibold px-20 sm:px-44 py-2 rounded-lg hover:bg-[#61359C]/80 transition">
                        Tutup
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<x-modal id="kaderModalRef" size="md">
    <x-slot name="title">
        <h3 id="kaderModalTitle" class="text-lg font-bold">Tambah Data Kader</h3>
    </x-slot>

    @include('admin.fitur.kelola_user.data_kader.form')

    <x-slot name="footer">
        <button type="button" id="kaderCancelBtn"
            class="w-full px-6 py-2 rounded-lg bg-gray-400 text-white font-medium shadow hover:opacity-90 transition">
            Batal
        </button>
        <button type="submit" id="kaderSaveBtn" form="formEdit"
            class="w-full px-6 py-2 rounded-lg bg-[#61359C] text-white font-medium shadow hover:opacity-90 transition">
            Simpan
        </button>
    </x-slot>
</x-modal>

<x-modal id="importKaderModal" size="md">
    <x-slot name="title">
        <h3 class="text-lg font-bold">Import Data Kader</h3>
    </x-slot>

    @include('admin.fitur.kelola_user.data_kader.import')
</x-modal>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const tbody = document.getElementById("kaderTableBody");
        const modal = document.getElementById("modal");

        const modalNama = document.getElementById("modal-nama");
        const modalUsername = document.getElementById("modal-username");
        const modalKelurahan = document.getElementById("modal-kelurahan");
        const modalPosyandu = document.getElementById("modal-posyandu");
        const modalJenisKelamin = document.getElementById("modal-jenisKelamin");
        const modalTelepon = document.getElementById("modal-telepon");
        const modalStatus = document.getElementById("modal-status");

        const kaderModalRef = document.getElementById("kaderModalRef");
        const kaderModalTitle = document.getElementById("kaderModalTitle");
        const formEdit = document.getElementById("formEdit");

        const searchInput = document.getElementById("searchInput");
        const searchBtn = document.getElementById("searchBtn");
        const filterDropdown = document.querySelector('[data-dropdown="filter"]');

        let kaderList = [];

        async function fetchKader() {
            const keyword = searchInput.value.trim();

            let status = "";
            if (filterDropdown) {
                const selectedSpan = filterDropdown.querySelector('.dropdown-selected');
                status = selectedSpan ? selectedSpan.textContent.trim().toLowerCase() : "";
            }

            const params = new URLSearchParams();
            params.append("role", "kader");
            if (keyword) {
                params.append("keyword", keyword);
            }

            if (status) params.append("status", status.toLowerCase());

            try {
                const response = await fetch(`{{ url('api/users') }}?${params.toString()}`, {
                    headers: {
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    }
                });

                const result = await response.json();
                if (!result || !result.data) return;

                let users = result.data.list || [];

                if (keyword) {
                    const lowerKeyword = keyword.toLowerCase();
                    users = users.filter(user =>
                        (user.nama?.toLowerCase().includes(lowerKeyword)) ||
                        (user.kaderDetail?.nama_kelurahan?.toLowerCase().includes(lowerKeyword)) ||
                        (user.kaderDetail?.nama_posyandu?.toLowerCase().includes(lowerKeyword))
                    );
                }

                const kaderUsers = users.filter(user => user.role === 'kader');

                kaderList = kaderUsers;
                renderTable(kaderUsers);
            } catch (error) {
                showErrorToast.error("Gagal memuat data kader:", error);
                tbody.innerHTML = `<tr><td colspan="5" class="text-center text-red-500 py-4">Gagal memuat data</td></tr>`;
            }
        }

        function renderTable(list) {
            tbody.innerHTML = "";

            if (!list.length) {
                tbody.innerHTML = `<tr><td colspan="5" class="text-center text-gray-500 py-4">Tidak ada data kader.</td></tr>`;
                return;
            }

            list.reverse();

            list.forEach((item, index) => {
                const tr = document.createElement("tr");
                tr.className = "hover:bg-gray-50";

                tr.innerHTML = `
                    <td class="border border-[#00000033] text-center px-3 py-3">${index + 1}</td>
                    <td class="border border-[#00000033] px-3 py-3">${item.nama}</td>
                    <td class="border border-[#00000033] text-center px-3 py-3">${item.kaderDetail?.nama_kelurahan ?? '-'}</td>
                    <td class="border border-[#00000033] text-center px-3 py-3">${item.kaderDetail?.nama_posyandu ?? '-'}</td>
                    
                    <td class="border border-[#00000033] text-center px-3 py-3">
                        <div class="flex justify-center gap-2">
                            <button
                                class="px-3 py-1 text-xs rounded bg-green-600 text-white hover:bg-green-700 transition open-detail"
                                data-id="${item.id}">
                                Detail
                            </button>
                            <button
                                onclick="openKaderModal('edit', '${item.id}')"
                                class="px-3 py-1 text-xs rounded bg-yellow-500 text-white hover:bg-yellow-600 transition">
                                Edit
                            </button>
                            <button
                                onclick="openKaderModal('edit', '${item.id}')"
                                class="px-3 py-1 text-xs rounded bg-gray-500 text-white hover:bg-gray-600 transition">
                                Reset Password
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
                            const data = await fetch(`{{ url('api/users') }}/${id}`, {
                                method: "DELETE",
                                headers: {
                                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                                }
                            });
                            if (!data) return;

                            showSuccessToast("Data berhasil dihapus!");
                            await fetchKader();
                        } catch (error) {
                            showErrorToast.error("Gagal menghapus data:", error);
                            showErrorToast("Terjadi kesalahan pada server!");
                        }
                    });
                });
            });
        }

        formEdit.addEventListener("submit", async (e) => {
            e.preventDefault();

            ['name', 'username', 'password', 'jenis_kelamin', 'nama_kelurahan', 'nama_posyandu', 'no_telepon', 'status'].forEach(name => {
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
                let url = "{{ url('api/users') }}";
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
                    kaderModalRef.classList.add("hidden");
                    kaderModalRef.classList.remove("flex");
                    fetchKader();
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
                showErrorToast.error("Error:", err);
                alert("Terjadi kesalahan pada server!");
            }
        });

        function capitalizeFirstLetter(text) {
            if (!text || text === "-") return "-";
            return text.charAt(0).toUpperCase() + text.slice(1).toLowerCase();
        }

        tbody.addEventListener("click", (e) => {
            if (!e.target.classList.contains("open-detail")) return;

            const id = e.target.dataset.id;
            const item = kaderList.find(f => f.id == id);
            if (!item) return;

            modalNama.textContent = item.nama ?? "-";
            modalUsername.textContent = item.username ?? "-";
            modalKelurahan.textContent = item.kaderDetail?.nama_kelurahan ?? "-";
            modalPosyandu.textContent = item.kaderDetail?.nama_posyandu ?? "-";
            modalTelepon.textContent = item.kaderDetail?.no_telepon ?? "-";
            modalJenisKelamin.textContent = item.kaderDetail?.jenis_kelamin ?? "-";

            const rawStatus = item.kaderDetail?.status ?? "-";
            const status = capitalizeFirstLetter(rawStatus);

            modalStatus.textContent = status;

            modalStatus.classList.remove("bg-green-500", "bg-red-500");

            if (rawStatus && rawStatus.toLowerCase() === "aktif") {
                modalStatus.classList.add("bg-green-500");
            } else {
                modalStatus.classList.add("bg-red-500");
            }

            modal.classList.remove("hidden");
            modal.classList.add("flex");
            document.body.style.overflow = "hidden";
        });

        const openKaderModal = async (mode, id = null) => {
            kaderModalRef.classList.remove("hidden");
            kaderModalRef.classList.add("flex");

            if (mode === "edit" && id) {
                kaderModalTitle.textContent = "Edit Kader";
                try {
                    const data = await fetch(`{{ url('api/users') }}/${id}`);
                    const json = await data.json();

                    const item = json.data;
                    setFormData(item);

                    formEdit.setAttribute('data-mode', 'edit');
                    formEdit.setAttribute('data-id', id);
                } catch (err) {
                    showErrorToast.error("Gagal mengambil data kader:", err);
                }
            } else {
                kaderModalTitle.textContent = "Tambah Kader";
                setFormData(null);
                formEdit.removeAttribute('data-id');
                formEdit.setAttribute('data-mode', 'add');
            }
        };

        window.openKaderModal = openKaderModal;

        document.querySelectorAll('button').forEach(btn => {
            if (btn.textContent.includes("Tambah")) {
                btn.addEventListener("click", () => openKaderModal("add"));
            }
        });

        document.addEventListener("click", (e) => {
            if (e.target && e.target.textContent === "Edit") {
                const card = e.target.closest(".flex.flex-col");
                const itemName = card.querySelector("h3").textContent;
                const qty = card.querySelector("p.text-2xl").textContent;
                const img = card.querySelector("img").src;

                const id = e.target.dataset.id || null;

                openKaderModal("edit", id);
            }
        });

        document.getElementById("closeModalBtn").addEventListener("click", () => {
            modal.classList.add("hidden");
            modal.classList.remove("flex");
            document.body.style.overflow = "";
        });

        document.getElementById("kaderCancelBtn").addEventListener("click", () => {
            kaderModalRef.classList.add("hidden");
            kaderModalRef.classList.remove("flex");
        });

        searchBtn.addEventListener("click", fetchKader);
        searchInput.addEventListener("keydown", (e) => {
            if (e.key === "Enter") fetchKader();
        });

        if (filterDropdown) {
            const selectedSpan = filterDropdown.querySelector('.dropdown-selected');
            if (selectedSpan && !selectedSpan.textContent.trim()) {
                selectedSpan.textContent = "Aktif";
            }
        }

        const importModal = document.getElementById("importKaderModal");
        const btnImport = document.getElementById("btnImport");
        const importCancelBtn = document.getElementById("importCancelBtn");

        btnImport.addEventListener("click", () => {
            importModal.classList.remove("hidden");
            importModal.classList.add("flex");
            document.body.style.overflow = "hidden";
        });

        importCancelBtn.addEventListener("click", () => {
            importModal.classList.add("hidden");
            importModal.classList.remove("flex");
            document.body.style.overflow = "";
        });

        fetchKader();
    });
</script>
@endsection