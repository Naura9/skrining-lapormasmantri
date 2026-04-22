@extends('layouts.main')

@section('title', 'Data Kader')

@section('content')
<section class="p-2 mb-10">
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
        <table class="min-w-full border border-[#00000033] text-sm text-left text-gray-700">
            <thead class="bg-[#61359C] text-white text-center">
                <tr>
                    <th class="px-3 py-2 border border-[#00000033] w-[5%]">No</th>
                    <th class="px-3 py-2 border border-[#00000033] w-[28%]">Nama</th>
                    <th class="px-3 py-2 border border-[#00000033] w-[17%]">Kelurahan</th>
                    <th class="px-3 py-2 border border-[#00000033] w-[17%]">Posyandu</th>
                    <th class="px-3 py-2 border border-[#00000033] w-[33%]">Aksi</th>
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
                            class="flex items-center gap-2 text-white text-sm px-2 py-0.5 rounded-lg transition">
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

<x-modal id="resetPasswordModal" size="md">
    <x-slot name="title">
        <h3 class="text-lg font-bold">Reset Password</h3>
    </x-slot>

    <form id="formResetPassword" class="space-y-4 px-2">
        <input type="hidden" id="reset_user_id">

        <div class="text-left w-full relative">
            <label class="block text-sm font-medium mb-1">Password Baru</label>
            <input type="password" id="reset_password"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 pr-10 text-sm focus:outline-none focus:ring-2 focus:ring-[#61359C]/50"
                placeholder="Masukkan password baru">
            <button type="button"
                onclick="togglePassword()"
                class="absolute right-3 top-[30px] text-gray-500 hover:text-gray-700">
                <i id="eye-icon" class="fa-solid fa-eye-slash"></i>
            </button>
            <p id="error-reset_password" class="text-red-500 text-xs mt-1 hidden"></p>
        </div>
    </form>

    <x-slot name="footer">
        <button type="button" id="resetCancelBtn"
            class="w-full px-6 py-2 rounded-lg bg-gray-400 text-white font-medium hover:opacity-90">
            Batal
        </button>
        <button type="submit" form="formResetPassword"
            class="w-full px-6 py-2 rounded-lg bg-[#61359C] text-white font-medium hover:opacity-90">
            Simpan
        </button>
    </x-slot>
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

        window.fetchKader = async function() {
            const keyword = searchInput.value.trim();

            let status = "";
            if (filterDropdown) {
                const selectedSpan = filterDropdown.querySelector('.dropdown-selected');
                status = selectedSpan ? selectedSpan.textContent.trim().toLowerCase() : "";
            }

            const params = new URLSearchParams();
            params.append("role", "kader");
            if (keyword) params.append("keyword", keyword);
            if (status) params.append("status", status.toLowerCase());

            try {
                const result = await fetchWithAuth(`{{ url('api/users') }}?${params.toString()}`, {
                    headers: {
                        "Accept": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}"
                    }
                });

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
                showErrorToast("Gagal memuat data kader");
            }
        };

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
                                onclick="openResetPasswordModal('${item.id}')"
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
                            const result = await fetchWithAuth(`{{ url('api/users') }}/${id}`, {
                                method: "DELETE"
                            });

                            if (!result) return;

                            showSuccessToast("Data berhasil dihapus!");
                            await fetchKader();
                        } catch (error) {
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

                const result = await fetchWithAuth(url, {
                    method: method,
                    body: formData
                });

                if (result?.status_code === 422) {
                    Object.keys(result.errors).forEach(key => {
                        const el = document.getElementById("error-" + key);
                        if (el) {
                            el.textContent = result.errors[key][0];
                            el.classList.remove("hidden");
                        }
                    });
                    return;
                }

                if (result?.status_code !== 200) return;

                showSuccessToast("Data berhasil disimpan!");
                kaderModalRef.classList.add("hidden");
                kaderModalRef.classList.remove("flex");

                fetchKader();

            } catch (err) {
                showErrorToast("Terjadi kesalahan pada server");
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
                    const result = await fetchWithAuth(`{{ url('api/users') }}/${id}`);
                    const item = result.data;

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

        const resetModal = document.getElementById("resetPasswordModal");

        document.getElementById("resetCancelBtn").addEventListener("click", () => {
            resetModal.classList.add("hidden");
            resetModal.classList.remove("flex");
        });

        window.openResetPasswordModal = function(id) {
            document.getElementById("reset_user_id").value = id;
            document.getElementById("reset_password").value = "";

            resetModal.classList.remove("hidden");
            resetModal.classList.add("flex");
        };

        window.togglePassword = function() {
            const input = document.getElementById("reset_password");
            const icon = document.getElementById("eye-icon");

            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove("fa-eye-slash");
                icon.classList.add("fa-eye");
            } else {
                input.type = "password";
                icon.classList.remove("fa-eye");
                icon.classList.add("fa-eye-slash");
            }
        };

        document.getElementById("formResetPassword")
            .addEventListener("submit", async (e) => {
                e.preventDefault();

                const id = document.getElementById("reset_user_id").value;
                const password = document.getElementById("reset_password").value;
                const errorEl = document.getElementById("error-reset_password");

                errorEl.textContent = "";
                errorEl.classList.add("hidden");

                if (!password) {
                    errorEl.textContent = "Password wajib diisi";
                    errorEl.classList.remove("hidden");
                    return;
                }

                if (password.length < 6) {
                    errorEl.textContent = "Password minimal 6 karakter";
                    errorEl.classList.remove("hidden");
                    return;
                }

                try {
                    const result = await fetchWithAuth(
                        `{{ url('api/users') }}/${id}/reset-password`, {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json"
                            },
                            body: JSON.stringify({
                                password
                            })
                        }
                    );

                    if (!result) return;

                    if (!result.status) {
                        showErrorToast(result.message || "Gagal reset password");
                        return;
                    }

                    showSuccessToast("Password berhasil direset!");

                    resetModal.classList.add("hidden");
                    resetModal.classList.remove("flex");

                } catch (err) {
                    showErrorToast("Terjadi kesalahan pada server");
                }
            });

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